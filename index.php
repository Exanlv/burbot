<?php

use Dotenv\Dotenv;
use Exan\Burbot\Burbot;
use Psr\Log\NullLogger;
use Ragnarok\Fenrir\Bitwise\Bitwise;
use Ragnarok\Fenrir\Discord;
use Ragnarok\Fenrir\Enums\Intent;

require './vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function env(string $key, mixed $default = null) {
    $var = isset($_ENV[$key]) ? $_ENV[$key] : getenv($key);

    return $var === false ? $default : $var;
}

$discord = (new Discord(
    env('TOKEN'),
    new NullLogger()
))->withGateway(
    new Bitwise(Intent::GUILD_VOICE_STATES->value),
)->withRest();

$burbot = new Burbot($discord);

$discord->gateway->open();
