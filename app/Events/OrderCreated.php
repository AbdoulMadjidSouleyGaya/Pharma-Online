<?php

namespace App\Events;

use App\Models\CustomerOrder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La commande créée.
     */
    public CustomerOrder $order;

    /**
     * ID de la pharmacie concernée.
     */
    public int $pharmacy_id;

    /**
     * Crée une nouvelle instance d'événement.
     */
    public function __construct(CustomerOrder $order)
    {
        $this->order = $order;
        $this->pharmacy_id = (int) $order->pharmacy_id;
    }

    /**
     * Canal de diffusion (privé) : pharmacies.{id}.orders
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('pharmacies.' . $this->pharmacy_id . '.orders');
    }

    /**
     * Nom de l'événement côté JS (Echo).
     * On écoute ".order.created" dans le JS.
     */
    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * Payload envoyé côté JS (e.xxx)
     */
    public function broadcastWith(): array
    {
        return [
            'id'            => (string) $this->order->id,
            'number'        => (int) $this->order->number,
            'status'        => (string) $this->order->status,
            'pharmacy_id'   => $this->pharmacy_id,
            'pharmacy_name' => optional($this->order->pharmacy)->name,
            'created_at'    => optional($this->order->created_at)->toIso8601String(),
        ];
    }
}
