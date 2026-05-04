<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\InputProfile;

/**
 * Class VowelConsonantHeuristic
 * @package IsGibberish\Heuristics
 *
 * Evaluates whether the vowel-to-consonant balance looks unnatural.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class VowelConsonantHeuristic implements HeuristicInterface
{
    /**
     * Get the internal heuristic identifier.
     */
    public function name(): string
    {
        return 'vowel_consonant_balance';
    }

    /**
     * Analyze the letter distribution for unusual vowel and consonant ratios.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult
    {
        $letters = $profile->letterCount();

        if ($letters < 5) {
            return new HeuristicResult(0.0);
        }

        $vowels = $profile->vowelCount();
        $weight = $config->weight($this->name(), 20.0);
        $score = 0.0;
        $reasons = [];

        if ($vowels === 0 && $letters >= 6) {
            $score = $weight;
            $reasons[] = 'Contains a long letter sequence without vowels.';
        } else {
            $ratio = $vowels / $letters;

            if ($letters >= 8 && ($ratio < 0.15 || $ratio > 0.85)) {
                $score = $weight * 0.65;
                $reasons[] = 'Has an unnatural vowel to consonant balance.';
            }
        }

        return new HeuristicResult($score, $reasons);
    }
}
