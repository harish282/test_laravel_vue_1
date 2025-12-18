<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderUpdated;
use App\Events\OrderMatched;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');

        $query = Order::open();

        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        $orders = $query->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'symbol' => $order->symbol,
                'side' => $order->side,
                'price' => $order->price,
                'amount' => $order->amount,
                'created_at' => $order->created_at,
            ];
        });

        return response()->json($orders);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'symbol' => $order->symbol,
                'side' => $order->side,
                'price' => $order->price,
                'amount' => $order->amount,
                'status' => $order->status,
            ];
        });

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'symbol' => 'required|string',
            'side' => 'required|in:buy,sell',
            'price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = $request->user();
        $symbol = $request->symbol;
        $side = $request->side;
        $price = $request->price;
        $amount = $request->amount;

        DB::transaction(function () use ($user, $symbol, $side, $price, $amount) {
            if ($side === 'buy') {
                // Check if users.balance >= amount * price
                $totalCost = bcmul($price, $amount, 2); // balance is decimal 15,2
                if ($user->balance < $totalCost) {
                    throw ValidationException::withMessages(['amount' => 'Insufficient USD balance']);
                }

                // Deduct from users.balance
                $user->decrement('balance', $totalCost);
            } else {
                // Check if assets.amount >= amount
                $asset = Asset::firstOrCreate(
                    ['user_id' => $user->id, 'symbol' => $symbol],
                    ['amount' => 0, 'locked_amount' => 0]
                );

                if ($asset->amount < $amount) {
                    throw ValidationException::withMessages(['amount' => 'Insufficient ' . $symbol . ' balance']);
                }

                // Move amount into assets.locked_amount
                $asset->decrement('amount', $amount);
                $asset->increment('locked_amount', $amount);
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'side' => $side,
                'price' => $price,
                'amount' => $amount,
                'status' => 1, // open
            ]);

            // Broadcast order created
            broadcast(new OrderUpdated($order, 'created'));

            // Match order
            $this->matchOrder($order);
        });

        return response()->json(['message' => 'Order created successfully']);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->where('status', 1)->firstOrFail();

        DB::transaction(function () use ($order) {
            if ($order->side === 'buy') {
                // Add back to users.balance
                $totalCost = bcmul($order->price, $order->amount, 2);
                $order->user->increment('balance', $totalCost);
            } else {
                // Move from locked_amount back to amount
                $asset = Asset::where('user_id', $order->user_id)->where('symbol', $order->symbol)->first();
                if ($asset) {
                    $asset->increment('amount', $order->amount);
                    $asset->decrement('locked_amount', $order->amount);
                }
            }

            $order->update(['status' => 3]); // cancelled

            // Broadcast cancelled
            broadcast(new OrderUpdated($order, 'cancelled'));
        });

        return response()->json(['message' => 'Order cancelled successfully']);
    }

    private function matchOrder(Order $order): void
    {
        $counterSide = $order->side === 'buy' ? 'sell' : 'buy';

        $counterOrder = Order::open()
            ->where('symbol', $order->symbol)
            ->where('side', $counterSide)
            ->where('amount', '>=', $order->amount)
            ->when($order->side === 'buy', fn($q) => $q->where('price', '<=', $order->price))
            ->when($order->side === 'sell', fn($q) => $q->where('price', '>=', $order->price))
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$counterOrder) {
            return; // No full match, leave open
        }

        // Full match
        $matchAmount = $order->amount;

        // Create trade
        $trade = Trade::create([
            'buyer_id' => $order->side === 'buy' ? $order->user_id : $counterOrder->user_id,
            'seller_id' => $order->side === 'sell' ? $order->user_id : $counterOrder->user_id,
            'symbol' => $order->symbol,
            'price' => $counterOrder->price,
            'amount' => $matchAmount,
        ]);

        // Broadcast match
        broadcast(new OrderMatched($trade));

        // Update balances
        $buyerId = $order->side === 'buy' ? $order->user_id : $counterOrder->user_id;
        $sellerId = $order->side === 'sell' ? $order->user_id : $counterOrder->user_id;

        $buyer = \App\Models\User::find($buyerId);
        $seller = \App\Models\User::find($sellerId);

        // Buyer gets asset
        $buyerAsset = Asset::firstOrCreate(['user_id' => $buyerId, 'symbol' => $order->symbol], ['amount' => 0, 'locked_amount' => 0]);
        $buyerAsset->increment('amount', $matchAmount);

        // Seller gets USD minus commission
        $cost = bcmul($counterOrder->price, $matchAmount, 2);
        $commission = bcmul($cost, '0.015', 2); // 1.5%
        $seller->increment('balance', bcsub($cost, $commission, 2));

        // Deduct commission from buyer
        $buyer->decrement('balance', $commission);

        // Seller releases asset
        $sellerAsset = Asset::firstOrCreate(['user_id' => $sellerId, 'symbol' => $order->symbol], ['amount' => 0, 'locked_amount' => 0]);
        $sellerAsset->decrement('locked_amount', $matchAmount);

        // Mark orders as filled
        $order->update(['status' => 2, 'amount' => 0]);
        $counterOrder->update(['status' => 2, 'amount' => 0]);
    }
}
