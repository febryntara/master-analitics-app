<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatchProcessingFinished implements ShouldBroadcast
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $projectId,
        public int $taskLogId
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('project.' . $this->projectId);
    }

    public function broadcastAs()
    {
        return 'batch.finished';
    }
}
