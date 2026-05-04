<?php

declare(strict_types=1);

namespace IsGibberish\Config;

/**
 * Class DetectorConfig
 * @package IsGibberish\Config
 *
 * Stores thresholds, weights and keyboard layouts used by the detector.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final readonly class DetectorConfig
{
    /**
     * Create a new immutable detector configuration.
     *
     * @param array<string, float> $weights
     * @param list<string> $keyboardRows
     */
    public function __construct(
        private float $threshold = 60.0,
        private array $weights = [],
        private array $keyboardRows = ['qwertyuiop', 'asdfghjkl', 'zxcvbnm', 'qwertzuiop', 'yxcvbnm'],
    ) {
    }

    /**
     * Create the default detector configuration for the first package version.
     */
    public static function default(): self
    {
        return new self(
            threshold: 45.0,
            weights: [
                'special_character_density' => 30.0,
                'repetition' => 30.0,
                'keyboard_pattern' => 50.0,
                'vowel_consonant_balance' => 20.0,
                'character_distribution' => 20.0,
            ],
        );
    }

    /**
     * Get the minimum score required to classify input as gibberish.
     */
    public function threshold(): float
    {
        return $this->threshold;
    }

    /**
     * Get the configured weight for a heuristic or a fallback value.
     */
    public function weight(string $heuristicName, float $fallback = 0.0): float
    {
        return $this->weights[$heuristicName] ?? $fallback;
    }

    /**
     * Get the configured keyboard rows used for keyboard-smash detection.
     *
     * @return list<string>
     */
    public function keyboardRows(): array
    {
        return $this->keyboardRows;
    }

    /**
     * Return a cloned configuration with a different score threshold.
     */
    public function withThreshold(float $threshold): self
    {
        return new self($threshold, $this->weights, $this->keyboardRows);
    }

    /**
     * Return a cloned configuration with updated heuristic weights.
     *
     * @param array<string, float> $weights
     */
    public function withWeights(array $weights): self
    {
        return new self($this->threshold, $weights, $this->keyboardRows);
    }
}
