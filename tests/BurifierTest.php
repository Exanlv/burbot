<?php

namespace Tests\Burbot;

use Exan\Burbot\Burifier;
use PHPUnit\Framework\TestCase;

class BurifierTest extends TestCase
{
    /**
     * @dataProvider burDataProvider
     */
    public function testItBursNames(string $input, string $output)
    {
        $burifier = new Burifier();

        $this->assertEquals($output, $burifier->burify($input));
    }

    public static function burDataProvider(): array
    {
        return [
            'It replaces the 3rd letter if possible' => [
                'input' => 'buhman',
                'output' => 'burman',
            ],
            'It replaces the 2nd letter if possible' => [
                'input' => 'berbaan',
                'output' => 'burbaan',
            ],
            'It replaces the 1st letter if possible' => [
                'input' => 'kurbob',
                'output' => 'burbob',
            ],

            'It burifies bulbassaur' => [
                'input' => 'bulbassaur',
                'output' => 'burbassaur',
            ],

            'It prioritizes 2nd letter replacements' => [
                'input' => 'borbuhmur',
                'output' => 'burbuhmur',
            ],
            'It prioritizes 3rd letter replacement over 2nd' => [
                'input' => 'buhmur',
                'output' => 'burmur',
            ],

            'It doesnt matter where in string burn\'t occurs for 3rd letter replacement' => [
                'input' => 'prefixbuhbut',
                'output' => 'prefixburbut',
            ],
            'It doesnt matter where in string burn\'t occurs for 2nd letter replacement' => [
                'input' => 'prefixberbaan',
                'output' => 'prefixburbaan',
            ],
            'It doesnt matter where in string burn\'t occurs for 1st letter replacement' => [
                'input' => 'prefixkurbob',
                'output' => 'prefixburbob',
            ],

            'It retains umlauts for 3rd letter replacement' => [
                'input' => 'büh',
                'output' => 'bür',
            ],
            'It retains umlauts for 2nd letter replacement' => [
                'input' => 'bár',
                'output' => 'búr',
            ],
            'It retains umlauts for 1st letter replacement' => [
                'input' => 'hùr',
                'output' => 'bùr',
            ],

            'It retains umlauts for 3rd letter replacement with prëfïx' => [
                'input' => 'prëfïxbüh',
                'output' => 'prëfïxbür',
            ],
            'It retains umlauts for 2nd letter replacement with prëfïx' => [
                'input' => 'prëfïxbár',
                'output' => 'prëfïxbúr',
            ],
            'It retains umlauts for 1st letter replacement with prëfïx' => [
                'input' => 'prëfïxhùr',
                'output' => 'prëfïxbùr',
            ],
            'It doesnt matter where in string burn\'t occurs for 2nd letter replacement' => [
                'input' => 'bërbaan',
                'output' => 'bürbaan',
            ],
            'It doesnt matter where in string burn\'t occurs for 1st letter replacement' => [
                'input' => 'bük',
                'output' => 'bür',
            ],

            'It can insert letters to force a bur' => [
                'input' => 'biker',
                'output' => 'burker',
            ],
            'It can insert letters to force a bur v2' => [
                'input' => 'boder',
                'output' => 'burder',
            ],

            'It can insert letters to force a bur if duplicate vowels follow a b' => [
                'input' => 'baader',
                'output' => 'burader',
            ],
            'It can insert letters to force a bur if duplicate vowels follow a b v2' => [
                'input' => 'booder',
                'output' => 'buroder',
            ],
            'It doesnt insert letters to force a bur if non-duplicate vowels follow a b' => [
                'input' => 'boader',
                'output' => 'boader',
            ],


            'It can insert letters to force a bur with some degree of casing' => [
                'input' => 'BiKeR',
                'output' => 'BurKeR',
            ],
            'It can insert letters to force a bur with some degree of casing v2' => [
                'input' => 'BoDeR',
                'output' => 'BurDeR',
            ],

            'It can insert letters to force a bur if duplicate vowels follow a b with a degree of casing' => [
                'input' => 'BaAdER',
                'output' => 'BurAdER',
            ],
            'It can insert letters to force a bur if duplicate vowels follow a b with a degree of casing v2' => [
                'input' => 'BOOder',
                'output' => 'BUrOder',
            ],

            'It can insert letters to force a bur while keeping accents' => [
                'input' => 'BïKeR',
                'output' => 'BürKeR',
            ],
            'It can insert letters to force a bur while keeping accents v2' => [
                'input' => 'BóDeR',
                'output' => 'BúrDeR',
            ],

            'It replaces the 3rd letter if possible while keeping capitalization' => [
                'input' => 'bUHmAN',
                'output' => 'bURmAN',
            ],
            'It replaces the 2nd letter if possible while keeping capitalization' => [
                'input' => 'BErBaAn',
                'output' => 'BUrBaAn',
            ],
            'It replaces the 1st letter if possible while keeping capitalization' => [
                'input' => 'KURbOB',
                'output' => 'BURbOB',
            ],
        ];
    }
}
