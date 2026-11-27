# is-gibberish

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892bf.svg)

A tiny, **zero-dependency** PHP package to detect if a string is just random keyboard smashing or "gibberish". Perfect for pre-filtering contact forms before sending emails.

## 🚀 Why use this?
Bots and frustrated users often fill form fields with nonsense like `asdfghjkl`, `sdfa sdfas df sadf asdfas asdf`, or `kfkJHzgHjb6)?)7)`. This package helps you identify these strings using fast heuristic analysis instead of heavy machine learning.

## ✨ Features
- **Ultra Lightweight:** No dependencies, just pure PHP.
- **Fast:** Uses lightweight heuristics and pattern distribution for near-instant results.
- **Multilingual:** Correctly handles German Umlauts (ä, ö, ü) and accented letters conservatively.
- **Customizable:** Adjust thresholds, heuristic weights and keyboard rows globally or per call.
- **Explainable:** Optional analysis result with score and triggered reasons.

## 📦 Installation
```bash
composer require yourname/is-gibberish
```

```php
use IsGibberish\Detector;

$detector = new Detector();

$input = "asdf 24qf waefasdf arg aerg ergeara asd";

if ($detector->isGibberish($input)) {
    // Handle as invalid input
    echo "Please enter a real message.";
}
```

The default configuration is tuned to detect the most common gibberish patterns in typical form submissions, including contact forms, support requests, and checkout-related text fields. For most applications, it should work well out of the box without additional tuning.

Per-call overrides are also supported when you need to tune the detector for a specific field, workflow, or validation policy:

```php
if ($detector->isGibberish($input, [
    'threshold' => 35.0,
])) {
    echo "Please enter a real message.";
}
```

You can also override heuristic weights for a single boolean check:

```php
if ($detector->isGibberish($input, [
    'weights' => [
        'token_pattern' => 30.0,
        'keyboard_pattern' => 70.0,
    ],
])) {
    echo "Please enter a real message.";
}
```

## 🔎 Detailed Analysis

```php
$result = $detector->analyze("asdf 24qf waefasdf arg aerg ergeara asd");

$result->isGibberish(); // true
$result->score();       // e.g. 58.0
$result->threshold();   // e.g. 45.0
$result->reasons();     // triggered heuristic reasons
$result->breakdown();   // heuristic scores
```

You can override configuration for a single analysis call:

```php
$result = $detector->analyze($input, [
    'threshold' => 35.0,
    'weights' => [
        'keyboard_pattern' => 70.0,
        'token_pattern' => 30.0,
    ],
    'keyboard_rows' => ['qwertyuiop', 'asdfghjkl', 'zxcvbnm'],
]);
```

If you prefer to configure the detector up front, you can still pass a `DetectorConfig` to the constructor:

```php
use IsGibberish\Config\DetectorConfig;
use IsGibberish\Detector;

$config = DetectorConfig::default()
    ->withThreshold(40.0)
    ->withMergedWeights([
        'keyboard_pattern' => 60.0,
    ]);

$detector = new Detector($config);
```

## ⚙️ Configuration Options

Both `isGibberish()` and `analyze()` accept an optional second argument:

```php
$result = $detector->analyze($input, [
    'threshold' => 35.0,
    'weights' => [
        'keyboard_pattern' => 70.0,
    ],
    'keyboard_rows' => ['qwertyuiop', 'asdfghjkl', 'zxcvbnm'],
]);
```

Supported options:

- `threshold` (`float`): Overrides the score threshold for this single call.
- `weights` (`array<string, float>`): Overrides selected heuristic weights for this single call. Missing keys keep their default values.
- `keyboard_rows` (`list<string>`): Replaces the keyboard row list for this single call.

Available heuristic weight keys:

- `special_character_density`
- `repetition`
- `keyboard_pattern`
- `vowel_consonant_balance`
- `character_distribution`
- `token_pattern`

Default configuration:

- `threshold`: `45.0`
- `weights.special_character_density`: `30.0`
- `weights.repetition`: `30.0`
- `weights.keyboard_pattern`: `50.0`
- `weights.vowel_consonant_balance`: `20.0`
- `weights.character_distribution`: `20.0`
- `weights.token_pattern`: `35.0`
- `keyboard_rows`: `['qwertyuiop', 'asdfghjkl', 'zxcvbnm', 'qwertzuiop', 'yxcvbnm']`

## 🧠 Current Heuristics

- Special character density
- Repeated characters and repeated short blocks
- Keyboard row patterns such as `asdfghjkl` and `qwertzuiop`
- Vowel to consonant imbalance
- Unnatural character-class distribution
- Suspicious multi-token fragment patterns, including repeated keyboard-row sequences across several tokens

## 🛠️ Development

Install development dependencies:

```bash
composer install
```

Run the test suite:

```bash
vendor/bin/phpunit
```

For contribution guidelines, development workflow and testing details, see [CONTRIBUTING.md](/Users/sascha/develop/is-gibberish/CONTRIBUTING.md:1).
