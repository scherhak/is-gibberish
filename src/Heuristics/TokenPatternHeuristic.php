<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\CharacterClassifier;
use IsGibberish\Support\InputProfile;
use IsGibberish\Support\Unicode;

/**
 * Class TokenPatternHeuristic
 * @package IsGibberish\Heuristics
 *
 * Detects inputs made up of many individually suspicious token fragments.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class TokenPatternHeuristic implements HeuristicInterface
{
    /**
     * Get the internal heuristic identifier.
     */
    public function name(): string
    {
        return 'token_pattern';
    }

    /**
     * Analyze whitespace-delimited token fragments for suspicious composition.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult
    {
        $tokens = preg_split('/\s+/u', trim($profile->original()), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($tokens) < 4) {
            return new HeuristicResult(0.0);
        }

        $substantiveTokens = array_values(array_filter(
            $tokens,
            static fn (string $token): bool => count(Unicode::characters($token)) >= 3,
        ));

        if (count($substantiveTokens) < 4) {
            return new HeuristicResult(0.0);
        }

        $suspiciousCount = 0;

        foreach ($substantiveTokens as $token) {
            if ($this->isSuspiciousToken($token, $config)) {
                $suspiciousCount++;
            }
        }

        $ratio = $suspiciousCount / count($substantiveTokens);
        $weight = $config->weight($this->name(), 35.0);

        if ($ratio >= 0.7) {
            return new HeuristicResult($weight, ['Most token fragments look structurally implausible.']);
        }

        if ($ratio >= 0.5) {
            return new HeuristicResult($weight * 0.7, ['Many token fragments look structurally implausible.']);
        }

        if ($suspiciousCount >= 6 && $ratio >= 0.4) {
            return new HeuristicResult($weight * 0.7, ['The input contains many individually suspicious token fragments.']);
        }

        return new HeuristicResult(0.0);
    }

    /**
     * Determine whether a single token fragment looks suspicious in isolation.
     */
    private function isSuspiciousToken(string $token, DetectorConfig $config): bool
    {
        $characters = Unicode::characters($token);
        $length = count($characters);

        if ($length < 3) {
            return false;
        }

        $letters = '';
        $letterCount = 0;
        $vowelCount = 0;
        $digitCount = 0;
        $specialCount = 0;

        foreach ($characters as $character) {
            if (CharacterClassifier::isLetter($character)) {
                $letterCount++;
                $letters .= Unicode::lower($character);

                if (CharacterClassifier::isVowel($character)) {
                    $vowelCount++;
                }

                continue;
            }

            if (CharacterClassifier::isDigit($character)) {
                $digitCount++;
                continue;
            }

            $specialCount++;
        }

        if ($letterCount >= 5 && $vowelCount === 0) {
            return true;
        }

        if ($letterCount >= 6 && ($vowelCount / $letterCount) < 0.2) {
            return true;
        }

        if (
            $digitCount === 0
            && $specialCount === 0
            && $letterCount >= 4
            && ($vowelCount / $letterCount) <= 0.25
        ) {
            return true;
        }

        if ($digitCount > 0 && $specialCount > 0 && $letterCount > 0) {
            return true;
        }

        if ($specialCount >= 3 && $specialCount / $length >= 0.3) {
            return true;
        }

        if ($letters !== '' && $this->containsKeyboardRun($letters, $config)) {
            return true;
        }

        if (preg_match('/(.{2,4})\1{1,}/u', Unicode::lower($token)) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the token letters contain a suspicious keyboard-row run.
     */
    private function containsKeyboardRun(string $letters, DetectorConfig $config): bool
    {
        foreach ($config->keyboardRows() as $row) {
            if ($this->longestContainedRun($letters, $row) >= 3) {
                return true;
            }

            if ($this->longestContainedRun($letters, strrev($row)) >= 3) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the longest substring from the input that appears in a keyboard row.
     */
    private function longestContainedRun(string $needle, string $haystack): int
    {
        $max = min(strlen($needle), strlen($haystack));

        for ($length = $max; $length >= 3; $length--) {
            for ($offset = 0; $offset <= strlen($needle) - $length; $offset++) {
                if (str_contains($haystack, substr($needle, $offset, $length))) {
                    return $length;
                }
            }
        }

        return 0;
    }
}
