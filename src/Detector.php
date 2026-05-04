<?php

declare(strict_types=1);

namespace IsGibberish;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Heuristics\CharacterDistributionHeuristic;
use IsGibberish\Heuristics\HeuristicInterface;
use IsGibberish\Heuristics\KeyboardPatternHeuristic;
use IsGibberish\Heuristics\RepetitionHeuristic;
use IsGibberish\Heuristics\SpecialCharacterDensityHeuristic;
use IsGibberish\Heuristics\VowelConsonantHeuristic;
use IsGibberish\Support\InputProfile;

/**
 * Class Detector
 * @package IsGibberish
 *
 * Coordinates the heuristic pipeline and exposes the public detection API.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class Detector
{
    /**
     * Create a detector with optional custom configuration and heuristics.
     *
     * @param list<HeuristicInterface>|null $heuristics
     */
    public function __construct(
        private readonly ?DetectorConfig $config = null,
        private readonly ?array $heuristics = null,
    ) {
    }

    /**
     * Check whether the given input should be treated as gibberish.
     */
    public function isGibberish(string $input): bool
    {
        return $this->analyze($input)->isGibberish();
    }

    /**
     * Analyze the given input and return the full detection result.
     */
    public function analyze(string $input): AnalysisResult
    {
        $config = $this->config ?? DetectorConfig::default();
        $profile = InputProfile::from($input);

        $score = 0.0;
        $breakdown = [];
        $reasons = [];

        foreach ($this->heuristics ?? $this->defaultHeuristics() as $heuristic) {
            $result = $heuristic->analyze($profile, $config);
            $score += $result->score();
            $breakdown[$heuristic->name()] = $result->score();

            foreach ($result->reasons() as $reason) {
                $reasons[] = $reason;
            }
        }

        return new AnalysisResult(
            gibberish: $score >= $config->threshold(),
            score: $score,
            threshold: $config->threshold(),
            breakdown: $breakdown,
            reasons: array_values(array_unique($reasons)),
        );
    }

    /**
     * Build the default set of heuristics used by the detector.
     *
     * @return list<HeuristicInterface>
     */
    private function defaultHeuristics(): array
    {
        return [
            new SpecialCharacterDensityHeuristic(),
            new RepetitionHeuristic(),
            new KeyboardPatternHeuristic(),
            new VowelConsonantHeuristic(),
            new CharacterDistributionHeuristic(),
        ];
    }
}
