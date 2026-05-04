<?php

declare(strict_types=1);

namespace IsGibberish;

/**
 * Class AnalysisResult
 * @package IsGibberish
 *
 * Represents the final detector outcome including score details and reasons.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final readonly class AnalysisResult
{
    /**
     * Create a new immutable analysis result instance.
     *
     * @param array<string, float> $breakdown
     * @param list<string> $reasons
     */
    public function __construct(
        private bool $gibberish,
        private float $score,
        private float $threshold,
        private array $breakdown,
        private array $reasons,
    ) {
    }

    /**
     * Determine whether the analyzed input was classified as gibberish.
     */
    public function isGibberish(): bool
    {
        return $this->gibberish;
    }

    /**
     * Get the aggregated score produced by all heuristics.
     */
    public function score(): float
    {
        return $this->score;
    }

    /**
     * Get the threshold that was used for the final classification.
     */
    public function threshold(): float
    {
        return $this->threshold;
    }

    /**
     * Get the individual scores contributed by each heuristic.
     *
     * @return array<string, float>
     */
    public function breakdown(): array
    {
        return $this->breakdown;
    }

    /**
     * Get the human-readable reasons that contributed to the classification.
     *
     * @return list<string>
     */
    public function reasons(): array
    {
        return $this->reasons;
    }
}
