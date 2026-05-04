<?php

declare(strict_types=1);

namespace IsGibberish\Tests;

use IsGibberish\Detector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class DetectorTest
 * @package IsGibberish\Tests
 *
 * Verifies the detector behavior for obvious gibberish and plausible human input.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final class DetectorTest extends TestCase
{
    /**
     * Ensure that obvious gibberish samples are classified correctly.
     */
    #[DataProvider('gibberishProvider')]
    public function testItDetectsObviousGibberish(string $input): void
    {
        $detector = new Detector();

        self::assertTrue($detector->isGibberish($input), sprintf('Expected "%s" to be flagged as gibberish.', $input));
    }

    /**
     * Ensure that plausible human input is not classified as gibberish.
     */
    #[DataProvider('validInputProvider')]
    public function testItAllowsPlausibleHumanInput(string $input): void
    {
        $detector = new Detector();

        self::assertFalse($detector->isGibberish($input), sprintf('Expected "%s" to be allowed.', $input));
    }

    /**
     * Provide sample inputs that should be flagged as gibberish.
     *
     * @return iterable<string, array{0: string}>
     */
    public static function gibberishProvider(): iterable
    {
        yield 'keyboard smash' => ['asdfghjkl'];
        yield 'random symbols' => ['kfkJHzgHjb6)?)7)'];
        yield 'repeated characters' => ['aaaaaaaaaa'];
        yield 'keyboard row variant' => ['qwertzuiop'];
        yield 'mixed repetition' => ['12121212'];
    }

    /**
     * Provide sample inputs that should be treated as plausible human input.
     *
     * @return iterable<string, array{0: string}>
     */
    public static function validInputProvider(): iterable
    {
        yield 'simple name' => ['Max Mustermann'];
        yield 'german long word' => ['Donaudampfschifffahrt'];
        yield 'umlaut text' => ['Straßenüberführung'];
        yield 'accented name' => ['José Alvarez'];
        yield 'real sentence' => ['Bitte rufen Sie mich morgen an.'];
    }
}
