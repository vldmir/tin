<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== ОТСЛЕЖИВАНИЕ ПРОЦЕССА ВАЛИДАЦИИ ===\n\n";

$testInput = '48 036 952 129';
echo "Тестируемый вход: '$testInput'\n\n";

// Шаг 1: Проверим нормализацию напрямую
$normalized = preg_replace('#[^[:alnum:]]#u', '', $testInput);
echo "1. Нормализация: '$testInput' → '$normalized'\n";

// Шаг 2: Проверим через TIN::from()
try {
    $tin = TIN::from('DE', $testInput);
    echo "2. TIN::from() создан успешно\n";
    
    // Шаг 3: Проверим валидность
    $isValid = $tin->isValid();
    echo "3. Валидность: " . ($isValid ? 'ДА' : 'НЕТ') . "\n";
    
    // Шаг 4: Проверим тип
    $type = $tin->identifyTinType();
    if ($type) {
        echo "4. Тип: {$type['code']} - {$type['name']}\n";
    } else {
        echo "4. Тип: НЕ ОПРЕДЕЛЕН\n";
    }
    
} catch (TINException $e) {
    echo "ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\n=== Сравнение с работающим примером ===\n\n";

$workingInput = '48-036-952-129';
echo "Работающий вход: '$workingInput'\n";

$normalizedWorking = preg_replace('#[^[:alnum:]]#u', '', $workingInput);
echo "Нормализация: '$workingInput' → '$normalizedWorking'\n";

try {
    $tinWorking = TIN::from('DE', $workingInput);
    $isValidWorking = $tinWorking->isValid();
    echo "Валидность: " . ($isValidWorking ? 'ДА' : 'НЕТ') . "\n";
    
    $typeWorking = $tinWorking->identifyTinType();
    if ($typeWorking) {
        echo "Тип: {$typeWorking['code']} - {$typeWorking['name']}\n";
    }
    
} catch (TINException $e) {
    echo "ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\n=== Проверка через slug ===\n\n";

$slugWithSpaces = 'DE48 036 952 129';
$slugWithDashes = 'DE48-036-952-129';

echo "Slug с пробелами: '$slugWithSpaces'\n";
try {
    $tinSlug = TIN::fromSlug($slugWithSpaces);
    echo "Валидность: " . ($tinSlug->isValid() ? 'ДА' : 'НЕТ') . "\n";
} catch (TINException $e) {
    echo "ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\nSlug с дефисами: '$slugWithDashes'\n";
try {
    $tinSlug2 = TIN::fromSlug($slugWithDashes);
    echo "Валидность: " . ($tinSlug2->isValid() ? 'ДА' : 'НЕТ') . "\n";
} catch (TINException $e) {
    echo "ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\n=== Анализ завершен ===\n"; 