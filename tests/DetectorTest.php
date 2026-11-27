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
     * Ensure that the threshold can be overridden for a single analysis call.
     */
    public function testItAcceptsPerCallThresholdOverride(): void
    {
        $detector = new Detector();
        $result = $detector->analyze('qwertzuiop', [
            'threshold' => 55.0,
        ]);

        self::assertFalse($result->isGibberish());
        self::assertSame(55.0, $result->threshold());
        self::assertSame(50.0, $result->score());
    }

    /**
     * Ensure that heuristic weights can be overridden for a single call.
     */
    public function testItAcceptsPerCallWeightOverrides(): void
    {
        $detector = new Detector();
        $result = $detector->analyze('qwertzuiop', [
            'weights' => [
                'keyboard_pattern' => 70.0,
            ],
        ]);

        self::assertTrue($result->isGibberish());
        self::assertSame(70.0, $result->score());
        self::assertSame(70.0, $result->breakdown()['keyboard_pattern']);
    }

    /**
     * Ensure that the boolean API also accepts per-call overrides.
     */
    public function testItAcceptsOverridesInBooleanApi(): void
    {
        $detector = new Detector();

        self::assertFalse($detector->isGibberish('qwertzuiop', [
            'threshold' => 55.0,
        ]));
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
        yield 'short multi token keyboard fragments' => ['sdfa sdfas df sadf asdfas asdf'];
        yield 'issue 2 keyboard fragments mixed with short tokens' => ['asdf 24qf waefasdf arg aerg ergeara asd'];
        yield 'multi token gibberish mix' => ['asdf 24qf waefasdf arg aerg ergeara asdf wq(/)/"$=/§()Q/§($23eafwdfasdk tdzjtdz jdtzj dtghsrghs fgh sfgh sfgh 435q 3warq34 qwe€'];
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
        yield 'sentence with house number' => ['Bitte liefern Sie an die Musterstraße 24 in Köln.'];
        yield 'sentence with ticket reference' => ['Meine Bestellnummer ist AB-1234 und die Adresse ist korrekt.'];
        yield 'sentence with keyboard fragment and reference' => ['Bitte asdf und 24qf prüfen.'];
        yield 'sentence discussing two keyboard sequences' => ['Bitte asdf, qwert und asd im Beispiel prüfen.'];
        yield 'sentence quoting the same keyboard sequence twice' => ['Bitte asdf, asdf und asd prüfen.'];
    }
}
