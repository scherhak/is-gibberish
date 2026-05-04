<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\InputProfile;

/**
 * Class CharacterDistributionHeuristic
 * @package IsGibberish\Heuristics
 *
 * Detects structurally suspicious inputs based on overall character mix.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class CharacterDistributionHeuristic implements HeuristicInterface
{
    /**
     * Get the internal heuristic identifier.
     */
    public function name(): string
    {
        return 'character_distribution';
    }

    /**
     * Analyze character variety and category ratios for suspicious structure.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult
    {
        if ($profile->length() < 5) {
            return new HeuristicResult(0.0);
        }

        $weight = $config->weight($this->name(), 20.0);
        $score = 0.0;
        $reasons = [];

        $uniqueRatio = $profile->uniqueCharacterRatio();
        $dominantRatio = $profile->dominantCharacterRatio();
        $letterRatio = $profile->letterRatio();
        $digitRatio = $profile->digitRatio();

        if ($dominantRatio >= 0.6) {
            $score = max($score, $weight);
            $reasons[] = 'A single character dominates the input.';
        } elseif ($uniqueRatio <= 0.35) {
            $score = max($score, $weight * 0.6);
            $reasons[] = 'Character variety is unusually low.';
        }

        if ($digitRatio >= 0.75 && $uniqueRatio <= 0.4) {
            $score = max($score, $weight);
            $reasons[] = 'Numeric input looks mechanically repetitive.';
        }

        if ($profile->length() >= 10 && $letterRatio >= 0.45 && $digitRatio >= 0.1 && $profile->specialCharRatio() >= 0.15) {
            $score = max($score, $weight);
            $reasons[] = 'Letter, digit and symbol mix looks structurally unnatural.';
        }

        if ($profile->length() >= 8 && $letterRatio > 0.0 && $digitRatio > 0.2 && $profile->specialCharRatio() > 0.1) {
            $score = max($score, $weight);
            $reasons[] = 'Contains an unusually mixed character distribution.';
        }

        return new HeuristicResult($score, $reasons);
    }
}
