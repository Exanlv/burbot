<?php

namespace Exan\Burbot;

use Ragnarok\Fenrir\Constants\Events;
use Ragnarok\Fenrir\Discord;
use Ragnarok\Fenrir\Gateway\Events\VoiceStateUpdate;
use React\EventLoop\Loop;

class Burbot
{
    private array $timers = [];

    public function __construct(
        private readonly Discord $discord
    ) {
        $discord->gateway->events->on(Events::VOICE_STATE_UPDATE, function (VoiceStateUpdate $event) {
            $userId = &$event->member->user->id;
            if (!$event->deaf) {
                if (isset($this->timers[$userId])) {
                    Loop::get()->cancelTimer($this->timers[$userId]);
                    unset($this->timers[$userId]);
                }

                return;
            }

            $this->timers[$userId] = Loop::get()->addTimer(300, function () use ($event, $userId) {
                $this->discord->rest->guild->modifyMember(
                    $event->guild_id,
                    $userId,
                    ['nick' => 'Bur']
                )->otherwise(function () {
                    // oops
                })->done();
            });
        });
    }
}
