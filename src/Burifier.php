<?php

namespace Exan\Burbot;

use Exan\Burbot\Exceptions\UnableToBurifyException;

class Burifier
{
    private array $accents = [
        'e' => ['e', 'ë', 'é', 'è', 'ê'],
        'y' => ['y', 'ÿ', 'y', 'y', 'y'],
        'u' => ['u', 'ü', 'ú', 'ù', 'û'],
        'i' => ['i', 'ï', 'í', 'ì', 'î'],
        'o' => ['õ', 'ö', 'ó', 'ò', 'ô'],
        'a' => ['ã', 'ä', 'á', 'à', 'â'],
    ];

    public function __construct()
    {
        foreach ($this->accents as $letter => $accented) {
            $this->accents[$letter] = array_filter($accented, fn ($accentedLetter) => $accentedLetter !== $letter);
        }
    }

    /**
     * @throws UnableToBurifyException
     */
    public function burify(string $original)
    {
        // Remove accents
        $username = $this->removeAccents($original);

        $patterns = [
            '/(b[e|y|i|o|a]r)/i',
            '/(bu[q|w|t|p|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m])/i',
            '/([q|w|t|p|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m]ur)/i',
            '/\bb[e|y|i|o|a](?=[q|w|t|p|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m])/i',
            '/\bbe(?=e)/i',
            '/\bby(?=y)/i',
            '/\bbu(?=u)/i',
            '/\bbi(?=i)/i',
            '/\bbo(?=o)/i',
            '/\bba(?=a)/i',
            '/(br)/i',
            '/([q|w|t|p|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m][e|y|i|o|a]r)/i',
            '/([q|w|t|p|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m]r)/i',
        ];

        $replacements = 0;
        while ($replacements < 1 && count($patterns) > 0) {
            $replaced = preg_replace(
                array_shift($patterns),
                'bur',
                $username,
                1,
                $replacements
            );
        }

        if ($replacements === 0) {
            throw new UnableToBurifyException();
        }

        if (mb_strlen($original) + 1 === mb_strlen($replaced)) {
            $burPosition = strpos($replaced, 'bur');
            $burEnd = $burPosition + 2;

            $original = self::mb_substr_replace($original, ' ', $burEnd, 0);
            $username = self::mb_substr_replace($username, ' ', $burEnd, 0);
        }

        $accented = self::retainAccents($original, $username, $replaced);
        $cased = self::retainCasing($original, $accented);

        return $cased;
    }

    private function retainCasing($original, $burified): string
    {
        $splitOriginal = mb_str_split($original);
        $splitBurified = mb_str_split($burified);
        $final = [];

        foreach ($splitOriginal as $key => $letter) {
            $final[] = ctype_upper($letter) ? strtoupper($splitBurified[$key]) : $splitBurified[$key];
        }


        return implode('', $final);
    }

    private function retainAccents(string $original, string $accentLess, string $burified): string
    {
        $splitOriginal = mb_str_split($original);
        $splitUnaccented = mb_str_split($accentLess);
        $splitBurified = mb_str_split($burified);

        $splitAccented = [];

        foreach ($splitUnaccented as $key => $unaccentedLetter) {
            $originalLetter = &$splitOriginal[$key];
            $burifiedLetter = &$splitBurified[$key];

            if (isset($this->accents[$unaccentedLetter])) {
                $accentedLetters = $this->accents[$unaccentedLetter];

                $position = array_search(strtolower($originalLetter), $accentedLetters, true);

                if ($position !== false) { // The letter was accented originally
                    $splitAccented[] = $burifiedLetter === $unaccentedLetter // If the letter wasn't changed in burifying process
                        ? $accentedLetters[$position] ?? $originalLetter
                        : $this->accents['u'][$position];

                    continue;
                }
            }

            $splitAccented[] = $splitBurified[$key];
        }

        return implode('', $splitAccented);
    }

    private function removeAccents(string $name)
    {
        $needles = array_merge(...array_values($this->accents));

        $replacements = '';
        foreach ($this->accents as $letter => $accents) {
            $replacements .= str_repeat($letter, count($accents));
        }

        return str_replace($needles, mb_str_split($replacements), $name);
    }

    /**
     * @see https://gist.github.com/JBlond/942f17f629f22e810fe3
     */
    private static function mb_substr_replace($string, $replacement, $start, $length = NULL)
    {
        if (is_array($string)) {
            $num = count($string);

            $replacement = is_array($replacement)
                ? array_slice($replacement, 0, $num)
                : array_pad(array($replacement), $num, $replacement);

            if (is_array($start)) {
                $start = array_slice($start, 0, $num);
                foreach ($start as $key => $value)
                    $start[$key] = is_int($value) ? $value : 0;
            } else {
                $start = array_pad(array($start), $num, $start);
            }

            if (!isset($length)) {
                $length = array_fill(0, $num, 0);
            } elseif (is_array($length)) {
                $length = array_slice($length, 0, $num);
                foreach ($length as $key => $value)
                    $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
            } else {
                $length = array_pad(array($length), $num, $length);
            }

            return array_map(__FUNCTION__, $string, $replacement, $start, $length);
        }

        preg_match_all('/./us', (string)$string, $smatches);
        preg_match_all('/./us', (string)$replacement, $rmatches);

        if ($length === NULL) $length = mb_strlen($string);

        array_splice($smatches[0], $start, $length, $rmatches[0]);

        return join($smatches[0]);
    }
}
