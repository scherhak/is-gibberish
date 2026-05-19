<?php

declare(strict_types=1);

namespace IsGibberish;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Heuristics\CharacterDistributionHeuristic;
use IsGibberish\Heuristics\HeuristicInterface;
use IsGibberish\Heuristics\KeyboardPatternHeuristic;
use IsGibberish\Heuristics\RepetitionHeuristic;
use IsGibberish\Heuristics\SpecialCharacterDensityHeuristic;
use IsGibberish\Heuristics\TokenPatternHeuristic;
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
     *
     * The optional options array allows per-call overrides without creating a
     * dedicated DetectorConfig instance.
     *
     * @param array{
     *     threshold?: float,
     *     weights?: array<string, float>,
     *     keyboard_rows?: list<string>
     * } $options
     */
    public function isGibberish(string $input, array $options = []): bool
    {
        return $this->analyze($input, $options)->isGibberish();
    }

    /**
     * Analyze the given input and return the full detection result.
     *
     * The optional options array allows per-call overrides without changing the
     * detector's base configuration.
     *
     * @param array{
     *     threshold?: float,
     *     weights?: array<string, float>,
     *     keyboard_rows?: list<string>
     * } $options
     */
    public function analyze(string $input, array $options = []): AnalysisResult
    {
        $config = $this->resolveConfig($options);
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
            new TokenPatternHeuristic(),
        ];
    }

    /**
     * Resolve the effective detector configuration for a single analysis call.
     *
     * Per-call options are layered on top of the detector's configured base
     * settings or the package defaults when no custom configuration is set.
     *
     * @param array{
     *     threshold?: float,
     *     weights?: array<string, float>,
     *     keyboard_rows?: list<string>
     * } $options
     */
    private function resolveConfig(array $options): DetectorConfig
    {
        $config = $this->config ?? DetectorConfig::default();

        if (array_key_exists('threshold', $options)) {
            $config = $config->withThreshold($options['threshold']);
        }

        if (!empty($options['weights'])) {
            $config = $config->withMergedWeights($options['weights']);
        }

        if (!empty($options['keyboard_rows'])) {
            $config = $config->withKeyboardRows($options['keyboard_rows']);
        }

        return $config;
    }
}
