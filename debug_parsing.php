<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== Отладка парсинга slug ===\n\n";

// Создаем TIN objects разными способами
$inputs = [
    'DE26954371827',
    'DE269 543 718 27',
];

foreach ($inputs as $slug) {
    echo "Slug: '$slug'\n";
    
    // Проверим, как парсится slug
    $country = substr($slug, 0, 2);
    $tin = substr($slug, 2);
    
    echo "Страна: '$country'\n";
    echo "TIN: '$tin'\n";
    
    // Проверим нормализацию
    $normalized = preg_replace('#[^[:alnum:]]#u', '', $tin);
    echo "Нормализованный: '$normalized'\n";
    
    try {
        $tinObj = TIN::fromSlug($slug);
        echo "TIN создан успешно\n";
        echo "Валидность: " . ($tinObj->isValid() ? 'ДА' : 'НЕТ') . "\n";
        
        // Проверим через from() метод
        $tinObj2 = TIN::from($country, $tin);
        echo "TIN через from(): " . ($tinObj2->isValid() ? 'ДА' : 'НЕТ') . "\n";
        
    } catch (TINException $e) {
        echo "ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Отладка завершена ===\n"; 