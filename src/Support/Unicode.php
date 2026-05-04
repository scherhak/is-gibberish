<?php

declare(strict_types=1);

namespace IsGibberish\Support;

/**
 * Class Unicode
 * @package IsGibberish\Support
 *
 * Provides lightweight Unicode helper methods without requiring runtime dependencies.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class Unicode
{
    private const LOWERCASE_MAP = [
        'Ä' => 'ä',
        'Ö' => 'ö',
        'Ü' => 'ü',
        'ẞ' => 'ß',
        'Á' => 'á',
        'À' => 'à',
        'Â' => 'â',
        'Ã' => 'ã',
        'Å' => 'å',
        'Æ' => 'æ',
        'Ç' => 'ç',
        'É' => 'é',
        'È' => 'è',
        'Ê' => 'ê',
        'Ë' => 'ë',
        'Í' => 'í',
        'Ì' => 'ì',
        'Î' => 'î',
        'Ï' => 'ï',
        'Ł' => 'ł',
        'Ñ' => 'ñ',
        'Ó' => 'ó',
        'Ò' => 'ò',
        'Ô' => 'ô',
        'Õ' => 'õ',
        'Ø' => 'ø',
        'Œ' => 'œ',
        'Ś' => 'ś',
        'Ú' => 'ú',
        'Ù' => 'ù',
        'Û' => 'û',
        'Ý' => 'ý',
        'Ÿ' => 'ÿ',
        'Ź' => 'ź',
        'Ż' => 'ż',
    ];

    /**
     * Split a UTF-8 string into a list of individual characters.
     *
     * @return list<string>
     */
    public static function characters(string $input): array
    {
        if ($input === '') {
            return [];
        }

        return preg_split('//u', $input, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /**
     * Convert a UTF-8 string to lowercase using a lightweight character map.
     */
    public static function lower(string $input): string
    {
        return strtolower(strtr($input, self::LOWERCASE_MAP));
    }
}
