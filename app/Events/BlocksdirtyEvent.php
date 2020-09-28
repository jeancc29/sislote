<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlocksdirtyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $blocksdirty;
    public $action;
    public $room = "default";
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($blocksdirty, $eliminar = false)
    {
        $this->room = $blocksdirty->getConnectionName();
        if($eliminar){
            $this->action = "delete";
        }else{
            $this->action = "add";
        }
        $this->blocksdirty = [$blocksdirty];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
        return ['blocksdirty'];
    }
}