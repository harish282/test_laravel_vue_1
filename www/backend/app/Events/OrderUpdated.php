<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $action, // 'created', 'matched', 'cancelled'
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('orders.' . $this->order->symbol),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'symbol' => $this->order->symbol,
                'side' => $this->order->side,
                'price' => $this->order->price,
                'amount' => $this->order->amount,
                'status' => $this->order->status,
                'created_at' => $this->order->created_at,
            ],
            'action' => $this->action,
        ];
    }
}
