<?php

declare(strict_types=1);

namespace IsGibberish\Support;

/**
 * Class CharacterClassifier
 * @package IsGibberish\Support
 *
 * Provides low-level character classification helpers for Unicode-aware checks.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class CharacterClassifier
{
    private const VOWELS = 'aeiouyäöüáàâãåæéèêëíìîïóòôõøœúùûýÿ';

    /**
     * Determine whether the given character is a Unicode letter.
     */
    public static function isLetter(string $character): bool
    {
        return preg_match('/^\p{L}$/u', $character) === 1;
    }

    /**
     * Determine whether the given character is a Unicode digit.
     */
    public static function isDigit(string $character): bool
    {
        return preg_match('/^\p{N}$/u', $character) === 1;
    }

    /**
     * Determine whether the given character is whitespace.
     */
    public static function isWhitespace(string $character): bool
    {
        return preg_match('/^\s$/u', $character) === 1;
    }

    /**
     * Determine whether the given character should be treated as a vowel.
     */
    public static function isVowel(string $character): bool
    {
        $lower = Unicode::lower($character);

        return str_contains(self::VOWELS, $lower);
    }
}
