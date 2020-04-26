<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Realtime;
use App\Stock;

class RealtimeStockEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stocks;
    public $action;
    public $room = "default";
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($esGuardarVenta, $stock = null, $eliminar = false)
    {
        if($eliminar){
            $this->action = "delete";
        }else{
            $this->action = "add";
        }
        //Cuando se dispara el evento desde guardarventa entonces debo retornar los stocks
        //que estan en guardados en la tabla realtime, de lo contrario pues retorno el stock recibido
        if($esGuardarVenta){
            $realtime = Realtime::whereRetornado(0)->get();
            $realtimeIdAfectado = collect($realtime)->map(function($d){
                return $d['idAfectado'];
            });
            $realtimeId = collect($realtime)->map(function($d){
                return $d['id'];
            });
            Realtime::whereIn('id', $realtimeId)->update(['retornado' => 1]);
            $this->stocks = Stock::whereIn('id', $realtimeIdAfectado)->get();
        }else{
            $this->stocks = [$stock];
        }

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
