<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlocksgeneralsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $blocksgenerals;
    public $action;
    public $room = "default";
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($blocksgenerals, $eliminar = false)
    {
        if($eliminar){
            $this->action = "delete";
        }else{
            $this->action = "add";
        }
        $this->blocksgenerals = [$blocksgenerals];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
        return ['blocksgenerals'];
    }
}
