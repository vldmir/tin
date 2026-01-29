<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== АНАЛИЗ АЛГОРИТМА IdNr ===\n\n";

$testNumber = '48036952129';
echo "Анализируемый номер: $testNumber\n\n";

// Анализ структуры IdNr
$digits = str_split($testNumber);
$digitCount = array_count_values($digits);

echo "📊 РАСПРЕДЕЛЕНИЕ ЦИФР:\n";
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

// Проверим валидность
try {
    $tin = TIN::from('DE', $testNumber);
    $isValid = $tin->isValid();
    echo "\n🎯 РЕЗУЛЬТАТ ВАЛИДАЦИИ:\n";
    echo "Валидность: " . ($isValid ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
    
    $type = $tin->identifyTinType();
    if ($type) {
        echo "Тип: {$type['code']} - {$type['name']}\n";
    } else {
        echo "Тип: НЕ ОПРЕДЕЛЕН\n";
    }
    
} catch (TINException $e) {
    echo "ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\n🔍 СРАВНЕНИЕ С РАБОТАЮЩИМИ ПРИМЕРАМИ:\n\n";

$workingExamples = [
    '26954371827',
    '86095742719',
];

foreach ($workingExamples as $example) {
    echo "Работающий пример: $example\n";
    
    $digits = str_split($example);
    $digitCount = array_count_values($digits);
    
    $twiceCount = 0;
    $zeroCount = 0;
    
    for ($i = 0; $i <= 9; $i++) {
        $count = $digitCount[$i] ?? 0;
        if ($count === 2) $twiceCount++;
        if ($count === 0) $zeroCount++;
    }
    
    echo "  Дважды: $twiceCount, Отсутствующие: $zeroCount\n";
    
    try {
        $tin = TIN::from('DE', $example);
        $isValid = $tin->isValid();
        echo "  Валидность: " . ($isValid ? 'ДА' : 'НЕТ') . "\n";
    } catch (TINException $e) {
        echo "  ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Анализ завершен ===\n"; 