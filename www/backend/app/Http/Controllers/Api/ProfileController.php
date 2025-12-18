<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get USD balance from users table
        $usdBalance = $user->balance;

        // Get other asset balances
        $assets = Asset::where('user_id', $user->id)->get()->map(function ($asset) {
            return [
                'symbol' => $asset->symbol,
                'amount' => $asset->amount,
                'locked_amount' => $asset->locked_amount,
            ];
        });

        return response()->json([
            'id' => $user->id,
            'usd_balance' => $usdBalance,
            'assets' => $assets,
        ]);
    }
}
