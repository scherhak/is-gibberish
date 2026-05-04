<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\InputProfile;

/**
 * Class KeyboardPatternHeuristic
 * @package IsGibberish\Heuristics
 *
 * Detects sequences that resemble keyboard-smash patterns on common layouts.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class KeyboardPatternHeuristic implements HeuristicInterface
{
    /**
     * Get the internal heuristic identifier.
     */
    public function name(): string
    {
        return 'keyboard_pattern';
    }

    /**
     * Analyze whether the input resembles a keyboard row sequence.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult
    {
        $input = $profile->lettersOnlyLowered();

        if (strlen($input) < 5) {
            return new HeuristicResult(0.0);
        }

        $weight = $config->weight($this->name(), 35.0);
        $bestPartialRun = 0;

        foreach ($config->keyboardRows() as $row) {
            if (str_contains($row, $input) || str_contains(strrev($row), $input)) {
                return new HeuristicResult($weight, ['Matches a strong keyboard row pattern.']);
            }

            $bestPartialRun = max(
                $bestPartialRun,
                $this->longestContainedRun($input, $row),
                $this->longestContainedRun($input, strrev($row)),
            );
        }

        if ($bestPartialRun >= 6) {
            return new HeuristicResult($weight * 0.8, ['Contains a long keyboard row sequence.']);
        }

        if ($bestPartialRun >= 5) {
            return new HeuristicResult($weight * 0.65, ['Contains a keyboard-like character run.']);
        }

        return new HeuristicResult(0.0);
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
