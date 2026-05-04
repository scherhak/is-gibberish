<?php

declare(strict_types=1);

namespace IsGibberish\Heuristics;

use IsGibberish\Config\DetectorConfig;
use IsGibberish\Support\InputProfile;

/**
 * Interface HeuristicInterface
 * @package IsGibberish\Heuristics
 *
 * Defines the contract for all heuristic analyzers used by the detector.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
interface HeuristicInterface
{
    /**
     * Get the stable identifier of the heuristic.
     */
    public function name(): string;

    /**
     * Analyze the prepared input profile and return a heuristic result.
     */
    public function analyze(InputProfile $profile, DetectorConfig $config): HeuristicResult;
}
