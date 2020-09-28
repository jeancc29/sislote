<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlocksdirtygeneralsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $blocksdirtygenerals;
    public $action;
    public $room = "default";
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($blocksdirtygenerals, $eliminar = false)
    {
        $this->room = $blocksdirtygenerals->getConnectionName();
        if($eliminar){
            $this->action = "delete";
        }else{
            $this->action = "add";
        }
        $this->blocksdirtygenerals = [$blocksdirtygenerals];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
        return ['blocksdirtygenerals'];
    }
}