<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\InputProfile;

/**
 * Class SpecialCharacterDensityHeuristic
 * @package IsGibberish\Heuristics
 *
 * Flags inputs with an unusually high share of special characters.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class SpecialCharacterDensityHeuristic implements HeuristicInterface
{
    /**
     * Get the internal heuristic identifier.
     */
    public function name(): string
    {
        return 'special_character_density';
    }

    /**
     * Analyze the ratio of special characters in the input.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult
    {
        if ($profile->length() < 4) {
            return new HeuristicResult(0.0);
        }

        $ratio = $profile->specialCharRatio();
        $weight = $config->weight($this->name(), 30.0);
        $score = 0.0;
        $reasons = [];

        if ($ratio >= 0.45) {
            $score = $weight;
            $reasons[] = 'Very high special character density.';
        } elseif ($ratio >= 0.25) {
            $score = $weight * 0.6;
            $reasons[] = 'Elevated special character density.';
        }

        return new HeuristicResult($score, $reasons);
    }
}
