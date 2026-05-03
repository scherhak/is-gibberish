# is-gibberish

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892bf.svg)

A tiny, **zero-dependency** PHP package to detect if a string is just random keyboard smashing or "gibberish". Perfect for pre-filtering contact forms before sending emails.

## 🚀 Why use this?
Bots and frustrated users often fill form fields with nonsense like `asdfghjkl` or `kfkJHzgHjb6)?)7)`. This package helps you identify these strings using fast heuristic analysis instead of heavy machine learning.

## ✨ Features
- **Ultra Lightweight:** No dependencies, just pure PHP.
- **Fast:** Uses entropy and pattern distribution for near-instant results.
- **Multilingual:** Correcty handles German Umlauts (ä, ö, ü) and special characters.
- **Customizable:** Adjust sensitivity thresholds to fit your needs.

## 📦 Installation
```bash
composer require yourname/is-gibberish
```

```php
use YourName\IsGibberish\Detector;

$detector = new Detector();

$input = "kfkJHzgHjb6)?)7)??>>)!";

if ($detector->isGibberish($input)) {
    // Handle as invalid input
    echo "Please write a real message.";
}
```