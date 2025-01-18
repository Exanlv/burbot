<?php

use Exan\Burbot\Burbot;
use Exan\Burbot\Burifier;
use Exan\Burbot\Exceptions\UnableToBurifyException;

require './vendor/autoload.php';

$burifier = new Burifier();

$names = [
    'Violint',
    'KaasKip',
    'Guitarmender',
    'Bread',
    'Burbot',
    'FreadBoat',
    'Henry Heteluchtoven',
    'Lyngvi',
    'AmicisServerIsDood',
    'BoomerWrld',
    'BulBassaur',
    'Exan',
    'Gerrie Gietvloer',
    'Hello Kitty Drum Set',
    'Hello Kitty Fiets',
    'Hello Kitty Guitar',
    'Hello Kitty quad',
    'Koen',
    'Lisanne',
    'Pieter Parketvloer',
    'Snarelex',
    'Vladimir',
    'Wortelnet',
    'Pikacheese',
    'Wops',
    'Jarat the cheese eating mouse',
    'Trans-table',
    'Veenanas',
    'Originele Burbot',
];

foreach ($names as $name) {
    try {
        $burified = $burifier->burify($name);
    } catch (UnableToBurifyException) {
        $burified = 'Bur';
    }

    echo $name, ' => ', $burified, PHP_EOL;
}
