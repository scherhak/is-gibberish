<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\InputProfile;

/**
 * Class RepetitionHeuristic
 * @package IsGibberish\Heuristics
 *
 * Detects suspicious repetition of characters or short character blocks.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class RepetitionHeuristic implements HeuristicInterface
{
    /**
     * Get the internal heuristic identifier.
     */
    public function name(): string
    {
        return 'repetition';
    }

    /**
     * Analyze the input for repeated character and repeated block patterns.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult
    {
        $weight = $config->weight($this->name(), 25.0);
        $reasons = [];
        $score = 0.0;
        $input = $profile->loweredWithoutWhitespace();

        if ($input === '') {
            return new HeuristicResult(0.0);
        }

        if (preg_match('/(.)\1{4,}/u', $input) === 1) {
            $score = max($score, $weight);
            $reasons[] = 'Contains excessive repeated characters.';
        }

        if (preg_match('/^(.{1,4})\1{2,}$/u', $input) === 1) {
            $score = max($score, $weight);
            $reasons[] = 'Input is dominated by a repeated short block.';
        } elseif (preg_match('/(.{2,4})\1{2,}/u', $input) === 1) {
            $score = max($score, $weight * 0.8);
            $reasons[] = 'Contains repeated short character blocks.';
        }

        return new HeuristicResult($score, $reasons);
    }
}
