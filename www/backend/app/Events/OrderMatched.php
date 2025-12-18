<?php

namespace App\Events;

use App\Models\Trade;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderMatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Trade $trade,
    ) {
        Log::info('OrderMatched event created', ['trade_id' => $trade->id]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->trade->buyer_id),
            new PrivateChannel('user.' . $this->trade->seller_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.matched';
    }

    public function broadcastWith(): array
    {
        return [
            'trade' => [
                'id' => $this->trade->id,
                'buyer_id' => $this->trade->buyer_id,
                'seller_id' => $this->trade->seller_id,
                'symbol' => $this->trade->symbol,
                'price' => $this->trade->price,
                'amount' => $this->trade->amount,
                'created_at' => $this->trade->created_at,
            ],
        ];
    }
}
