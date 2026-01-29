<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== ТЕСТ НОВОГО НОМЕРА: 48 036 952 129 ===\n\n";

$testNumber = '48 036 952 129';
echo "Тестируемый номер: $testNumber\n\n";

try {
    $tin = TIN::from('DE', $testNumber);
    
    echo "✅ РЕЗУЛЬТАТЫ ВАЛИДАЦИИ:\n";
    echo "Валидность: " . ($tin->isValid() ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
    
    $type = $tin->identifyTinType();
    if ($type) {
        echo "Тип: {$type['code']} - {$type['name']}\n";
        echo "Описание: {$type['description']}\n";
    } else {
        echo "Тип: НЕ ОПРЕДЕЛЕН\n";
    }
    
    echo "\n📋 ДЕТАЛИ ФОРМАТИРОВАНИЯ:\n";
    echo "Маска: {$tin->getInputMask()}\n";
    echo "Плейсхолдер: {$tin->getPlaceholder()}\n";
    echo "Отформатированный: {$tin->formatInput($testNumber)}\n";
    
    echo "\n🔍 АНАЛИЗ СТРУКТУРЫ:\n";
    $normalized = preg_replace('#[^[:alnum:]]#u', '', $testNumber);
    echo "Нормализованный: $normalized\n";
    echo "Длина: " . strlen($normalized) . " (требуется: 11)\n";
    echo "Первая цифра: {$normalized[0]} (не должна быть 0)\n";
    echo "Последняя цифра: {$normalized[10]} (контрольная цифра)\n";
    
    echo "\n📊 АНАЛИЗ РАСПРЕДЕЛЕНИЯ ЦИФР:\n";
    $digits = str_split($normalized);
    $digitCount = array_count_values($digits);
    
    echo "Распределение цифр:\n";
    $twiceCount = 0;
    $zeroCount = 0;
    $onceCount = 0;
    
    for ($i = 0; $i <= 9; $i++) {
        $count = $digitCount[$i] ?? 0;
        if ($count > 0) {
            echo "  $i: $count раз";
            if ($count === 2) {
                echo " (дважды)";
                $twiceCount++;
            } elseif ($count === 1) {
                $onceCount++;
            }
            echo "\n";
        } else {
            echo "  $i: отсутствует\n";
            $zeroCount++;
        }
    }
    
    echo "\n✅ ПРОВЕРКА ПРАВИЛ IdNr:\n";
    echo "Цифры встречающиеся дважды: $twiceCount (должно быть 1) " . ($twiceCount === 1 ? "✅" : "❌") . "\n";
    echo "Отсутствующие цифры: $zeroCount (должно быть 1) " . ($zeroCount === 1 ? "✅" : "❌") . "\n";
    echo "Цифры встречающиеся один раз: $onceCount (должно быть 8) " . ($onceCount === 8 ? "✅" : "❌") . "\n";
    
    echo "\n🎯 ПРОВЕРКА РАЗЛИЧНЫХ ФОРМАТОВ:\n";
    $formats = [
        '48036952129',
        '48 036 952 129',
        '480 369 521 29',
        '48-036-952-129',
        '48.036.952.129',
    ];
    
    foreach ($formats as $format) {
        try {
            $testTin = TIN::from('DE', $format);
            $isValid = $testTin->isValid();
            echo "  '$format' → " . ($isValid ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
        } catch (TINException $e) {
            echo "  '$format' → ОШИБКА: " . $e->getMessage() . "\n";
        }
    }
    
} catch (TINException $e) {
    echo "❌ ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\n=== Тест завершен ===\n"; 