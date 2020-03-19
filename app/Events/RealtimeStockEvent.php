<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RealtimeStockEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stocks;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $realtimeIdAfectado = Realtime::whereRetornado(0)->get();
        $realtimeIdAfectado = collect($realtimeIdAfectado)->map(function($d){
            return $d['idAfectado'];
        });
        Realtime::whereIn('idAfectado', $realtimeIdAfectado)->update(['retornado' => 1]);
        $this->stocks = Stock::whereIn('id', $realtimeIdAfectado)->get();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
        return ['realtime-stock'];
    }
}
