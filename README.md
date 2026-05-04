# is-gibberish

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892bf.svg)

A tiny, **zero-dependency** PHP package to detect if a string is just random keyboard smashing or "gibberish". Perfect for pre-filtering contact forms before sending emails.

## 🚀 Why use this?
Bots and frustrated users often fill form fields with nonsense like `asdfghjkl` or `kfkJHzgHjb6)?)7)`. This package helps you identify these strings using fast heuristic analysis instead of heavy machine learning.

## ✨ Features
- **Ultra Lightweight:** No dependencies, just pure PHP.
- **Fast:** Uses lightweight heuristics and pattern distribution for near-instant results.
- **Multilingual:** Correctly handles German Umlauts (ä, ö, ü) and accented letters conservatively.
- **Customizable:** Adjust sensitivity thresholds to fit your needs.
- **Explainable:** Optional analysis result with score and triggered reasons.

## 📦 Installation
```bash
composer require yourname/is-gibberish
```

```php
use IsGibberish\Detector;

$detector = new Detector();

$input = "kfkJHzgHjb6)?)7)??>>)!";

if ($detector->isGibberish($input)) {
    // Handle as invalid input
    echo "Please write a real message.";
}
```

## 🔎 Detailed Analysis

```php
$result = $detector->analyze("kfkJHzgHjb6)?)7)");

$result->isGibberish(); // true
$result->score();       // e.g. 58.0
$result->threshold();   // e.g. 45.0
$result->reasons();     // triggered heuristic reasons
$result->breakdown();   // heuristic scores
```

## 🧠 Current Heuristics

- Special character density
- Repeated characters and repeated short blocks
- Keyboard row patterns such as `asdfghjkl` and `qwertzuiop`
- Vowel to consonant imbalance
- Unnatural character-class distribution

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
