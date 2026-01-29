<?php
require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;

echo "=== Тест немецких TIN с пробелами ===\n\n";

// Валидный TIN в разных форматах
$valid_base = '26954371827';
$formats = [
    '26954371827',        // без пробелов
    '26 954 371 827',     // с пробелами
    '269 543 718 27',     // другая группировка
    '26954 371827',       // произвольная группировка
    ' 26954371827 ',      // пробелы в начале/конце
];

echo "ВАЛИДНЫЙ TIN '$valid_base' в разных форматах:\n";
foreach ($formats as $format) {
    $tinObj = TIN::from('DE', $format);
    $formatted = $tinObj->formatInput($format);
    $result = $tinObj->isValid() ? "✓ VALID" : "✗ INVALID";
    echo "Формат: '$format' -> Форматирован: '$formatted' -> $result\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Невалидный TIN в разных форматах
$invalid_base = '48036952129';
echo "НЕВАЛИДНЫЙ TIN '$invalid_base' в разных форматах:\n";
foreach ($formats as $format) {
    $formatted = str_replace($valid_base, $invalid_base, $format);
    $tinObj = TIN::from('DE', $formatted);
    $formattedOutput = $tinObj->formatInput($formatted);
    $result = $tinObj->isValid() ? "✓ VALID" : "✗ INVALID";
    echo "Формат: '$formatted' -> Форматирован: '$formattedOutput' -> $result\n";
}

echo "\n=== Тест завершен ===\n"; 