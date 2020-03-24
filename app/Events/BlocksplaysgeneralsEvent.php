<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlocksplaysgeneralsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $blocksplaysgenerals;
    public $action;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($blocksplaysgenerals, $eliminar = false)
    {
        if($eliminar){
            $this->action = "delete";
        }else{
            $this->action = "add";
        }
        $this->blocksplaysgenerals = [$blocksplaysgenerals];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('blocksplaysgenerals');
        return ['blocksplaysgenerals'];
    }
}
