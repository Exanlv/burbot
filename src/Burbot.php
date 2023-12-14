<?php

namespace Exan\Burbot;

use Ragnarok\Fenrir\Constants\Events;
use Ragnarok\Fenrir\Discord;
use Ragnarok\Fenrir\Gateway\Events\VoiceStateUpdate;
use Ragnarok\Fenrir\Parts\GuildMember;
use React\EventLoop\Loop;
use Throwable;

class Burbot
{
    private readonly Burifier $burifier;
    private array $timers = [];

    public function __construct(
        private readonly Discord $discord
    ) {
        $this->burifier = new Burifier();

        $discord->gateway->events->on(Events::VOICE_STATE_UPDATE, function (VoiceStateUpdate $event) {
            $userId = &$event->member->user->id;
            if (!$event->deaf && !$event->self_deaf) {
                if (isset($this->timers[$userId])) {
                    Loop::get()->cancelTimer($this->timers[$userId]);
                    unset($this->timers[$userId]);
                }

                return;
            }

            $this->timers[$userId] = Loop::get()->addTimer(300, function () use ($event, $userId) {
                $this->discord->rest->guild->getMember($event->guild_id, $userId)->then(function (GuildMember $member) use ($event, $userId) {
                    $displayName = $member->nick ?? $member->user->username ?? $member->user->global_name;
                    $burifiedName = $this->burifier->burify($displayName);

                    if (!str_contains($burifiedName, 'bur') || $displayName === $burifiedName) {
                        $burifiedName = 'Bur';
                    }

                    $this->discord->rest->guild->modifyMember(
                        $event->guild_id,
                        $userId,
                        ['nick' => 'Bur']
                    )->otherwise(function (Throwable $e) {
                        echo $e->getMessage();
                    })->done();
                });
            });
        });
    }
}
