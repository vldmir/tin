<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== ТЕСТ СТРОГОГО РЕЖИМА ПАРСИНГА ===\n\n";

$testInputs = [
    '48 036 952 129',
    '48-036-952-129',
    '48.036.952.129',
];

foreach ($testInputs as $input) {
    echo "Вход: '$input'\n";
    
    // Проверим через from() (не строгий режим)
    try {
        $tin = TIN::from('DE', $input);
        $isValid = $tin->isValid();
        echo "  TIN::from() - Валидность: " . ($isValid ? 'ДА' : 'НЕТ') . "\n";
    } catch (TINException $e) {
        echo "  TIN::from() - ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    // Проверим через fromSlug() (не строгий режим)
    try {
        $tinSlug = TIN::fromSlug('DE' . $input);
        $isValidSlug = $tinSlug->isValid();
        echo "  TIN::fromSlug() - Валидность: " . ($isValidSlug ? 'ДА' : 'НЕТ') . "\n";
    } catch (TINException $e) {
        echo "  TIN::fromSlug() - ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    // Проверим через check() с разными режимами
    try {
        $tinCheck = TIN::from('DE', $input);
        
        // Не строгий режим (по умолчанию)
        $isValidNotStrict = $tinCheck->isValid(false);
        echo "  isValid(false) - Валидность: " . ($isValidNotStrict ? 'ДА' : 'НЕТ') . "\n";
        
        // Строгий режим
        $isValidStrict = $tinCheck->isValid(true);
        echo "  isValid(true) - Валидность: " . ($isValidStrict ? 'ДА' : 'НЕТ') . "\n";
        
    } catch (TINException $e) {
        echo "  check() - ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Проверка нормализации в разных режимах ===\n\n";

$testInput = '48 036 952 129';

// Симуляция разных режимов нормализации
$normalizedNotStrict = preg_replace('#[^[:alnum:]]#u', '', $testInput);
$normalizedStrict = $testInput; // В строгом режиме не нормализуется

echo "Вход: '$testInput'\n";
echo "Нормализация (не строгий): '$normalizedNotStrict'\n";
echo "Нормализация (строгий): '$normalizedStrict'\n";

echo "\n=== Анализ завершен ===\n"; 