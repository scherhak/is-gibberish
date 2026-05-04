<?php

declare(strict_types=1);

namespace IsGibberish\Support;

/**
 * Class InputProfile
 * @package IsGibberish\Support
 *
 * Stores precomputed input statistics so heuristics can reuse them efficiently.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
final readonly class InputProfile
{
    /**
     * Create a new immutable input profile.
     *
     * @param list<string> $characters
     */
    private function __construct(
        private string $original,
        private string $lowered,
        private array $characters,
        private int $letterCount,
        private int $digitCount,
        private int $whitespaceCount,
        private int $specialCount,
        private int $vowelCount,
    ) {
    }

    /**
     * Build an input profile with precomputed character statistics.
     */
    public static function from(string $input): self
    {
        $characters = Unicode::characters($input);
        $letterCount = 0;
        $digitCount = 0;
        $whitespaceCount = 0;
        $specialCount = 0;
        $vowelCount = 0;

        foreach ($characters as $character) {
            if (CharacterClassifier::isWhitespace($character)) {
                $whitespaceCount++;
                continue;
            }

            if (CharacterClassifier::isLetter($character)) {
                $letterCount++;

                if (CharacterClassifier::isVowel($character)) {
                    $vowelCount++;
                }

                continue;
            }

            if (CharacterClassifier::isDigit($character)) {
                $digitCount++;
                continue;
            }

            $specialCount++;
        }

        return new self(
            original: $input,
            lowered: Unicode::lower($input),
            characters: $characters,
            letterCount: $letterCount,
            digitCount: $digitCount,
            whitespaceCount: $whitespaceCount,
            specialCount: $specialCount,
            vowelCount: $vowelCount,
        );
    }

    /**
     * Get the original raw input string.
     */
    public function original(): string
    {
        return $this->original;
    }

    /**
     * Get the Unicode-aware character length of the input.
     */
    public function length(): int
    {
        return count($this->characters);
    }

    /**
     * Get the number of letter characters in the input.
     */
    public function letterCount(): int
    {
        return $this->letterCount;
    }

    /**
     * Get the number of digit characters in the input.
     */
    public function digitCount(): int
    {
        return $this->digitCount;
    }

    /**
     * Get the number of vowel characters in the input.
     */
    public function vowelCount(): int
    {
        return $this->vowelCount;
    }

    /**
     * Get the ratio of letters relative to the full input length.
     */
    public function letterRatio(): float
    {
        return $this->ratio($this->letterCount);
    }

    /**
     * Get the ratio of digits relative to the full input length.
     */
    public function digitRatio(): float
    {
        return $this->ratio($this->digitCount);
    }

    /**
     * Get the ratio of special characters relative to the full input length.
     */
    public function specialCharRatio(): float
    {
        return $this->ratio($this->specialCount);
    }

    /**
     * Get the ratio of unique characters relative to the full input length.
     */
    public function uniqueCharacterRatio(): float
    {
        if ($this->length() === 0) {
            return 0.0;
        }

        return count(array_unique($this->characters)) / $this->length();
    }

    /**
     * Get the ratio of the most frequent character relative to the full input length.
     */
    public function dominantCharacterRatio(): float
    {
        if ($this->length() === 0) {
            return 0.0;
        }

        $counts = array_count_values($this->characters);

        return max($counts) / $this->length();
    }

    /**
     * Get the normalized lowercase input without whitespace characters.
     */
    public function loweredWithoutWhitespace(): string
    {
        return (string) preg_replace('/\s+/u', '', $this->lowered);
    }

    /**
     * Get a lowercase string that contains only letter characters.
     */
    public function lettersOnlyLowered(): string
    {
        $letters = array_filter(
            Unicode::characters($this->lowered),
            static fn (string $character): bool => CharacterClassifier::isLetter($character),
        );

        return implode('', $letters);
    }

    /**
     * Calculate the ratio of a count relative to the full input length.
     */
    private function ratio(int $value): float
    {
        if ($this->length() === 0) {
            return 0.0;
        }

        return $value / $this->length();
    }
}
