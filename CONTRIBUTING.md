# Contributing

Thanks for your interest in contributing to `is-gibberish`.

This package is intentionally small and focused. Contributions should preserve that character: fast heuristics, zero runtime dependencies, conservative detection in V1, and behavior that stays understandable.

## Core Principles

- **Minimalism first:** Keep the package small and focused on obvious gibberish detection.
- **Zero-dependency runtime:** Do not introduce external runtime dependencies lightly.
- **Conservative detection:** Prefer letting borderline input pass over creating false positives.
- **Explainable heuristics:** Detection should stay modular, deterministic, and debuggable.
- **Performance matters:** Favor efficient string operations and simple scoring over heavy processing.
- **Extensibility:** New heuristics should fit into the modular scoring architecture.

## Before You Start

- Check existing issues, discussions, or roadmap notes before starting larger work.
- Discuss major feature ideas before investing significant implementation effort.
- Prefer small, focused pull requests over broad mixed changes.

## Development Setup

**Requirements:**

- PHP 8.2+
- Composer

**Install dependencies:**

```bash
composer install
```

## Running Tests

Run the full test suite:

```bash
vendor/bin/phpunit
```

Or:

```bash
composer test
```

Run a single test file:

```bash
vendor/bin/phpunit tests/DetectorTest.php
```

Run a filtered test method:

```bash
vendor/bin/phpunit --filter testItDetectsObviousGibberish
```

The project provides both [phpunit.xml](/Users/sascha/develop/is-gibberish/phpunit.xml:1) and [phpunit.xml.dist](/Users/sascha/develop/is-gibberish/phpunit.xml.dist:1) so CLI tools and IDEs like PHPStorm can use the local PHPUnit setup directly.

## Code Standards

- Follow `PSR-12`
- Use explicit type hints
- Keep classes small and focused
- Prefer immutable value objects where appropriate
- Add PHPDoc only where it adds real clarity
- Avoid clever but opaque logic in heuristics

## Heuristic Guidelines

When changing or adding detection rules:

- Optimize for obvious gibberish first
- Avoid aggressive rules that increase false positives
- Keep each heuristic isolated and testable
- Prefer score-based contributions over hard-coded global branching
- Document why a rule exists, especially if it targets a narrow pattern
- Add or update tests for every behavioral change

Examples of valid change directions:

- improving keyboard smash detection
- refining repetition scoring
- refining multi-token fragment detection
- improving Unicode handling for legitimate names and words
- adding configurable thresholds, per-call overrides, or presets

Examples that need extra scrutiny:

- introducing dictionaries or language models
- adding runtime dependencies
- tightening rules in a way that may reject valid multilingual input

## Pull Request Expectations

- Keep the change focused on one feature or fix
- Include tests for behavioral changes
- Update `README.md` if public behavior or API changes
- Update `README.md` when default thresholds, weights, keyboard rows, or heuristics change
- Explain what changed and how it was tested

Useful PR description points:

- What problem does this solve?
- What changed?
- How was it tested?
- Are there any edge cases or tradeoffs?

## Documentation Structure

- [README.md](/Users/sascha/develop/is-gibberish/README.md:1): package usage and public overview
- [CONTRIBUTING.md](/Users/sascha/develop/is-gibberish/CONTRIBUTING.md:1): development and contribution workflow

## Notes

- If you touch scoring or thresholds, verify both positive and negative examples.
- If you add or change configurability, cover both constructor-based config and per-call overrides where applicable.
- If you expand Unicode handling, keep the implementation portable and lightweight.
- If you add configurability, preserve the simple default experience.
