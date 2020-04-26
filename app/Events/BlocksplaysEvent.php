<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlocksplaysEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $blocksplays;
    public $action;
    public $room = "default";
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($blocksplays, $eliminar = false)
    {
        if($eliminar){
            $this->action = "delete";
        }else{
            $this->action = "add";
        }
        $this->blocksplays = [$blocksplays];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('blocksplays');
        return ['blocksplays'];
    }
}
