<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

/**
 * Class HeuristicResult
 * @package IsGibberish\Heuristics
 *
 * Encapsulates the score and reasons produced by a single heuristic.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final readonly class HeuristicResult
{
    /**
     * Create a new heuristic result instance.
     *
     * @param list<string> $reasons
     */
    public function __construct(
        private float $score,
        private array $reasons = [],
    ) {
    }

    /**
     * Get the score contributed by the heuristic.
     */
    public function score(): float
    {
        return $this->score;
    }

    /**
     * Get the human-readable reasons emitted by the heuristic.
     *
     * @return list<string>
     */
    public function reasons(): array
    {
        return $this->reasons;
    }
}
