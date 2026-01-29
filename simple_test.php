<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;

echo "=== Проверка нормализации ===\n";

// Тест 1: без пробелов
$tin1 = TIN::from('DE', '26954371827');
echo "Без пробелов: " . ($tin1->isValid() ? 'ДА' : 'НЕТ') . "\n";

// Тест 2: с пробелами
$tin2 = TIN::from('DE', '269 543 718 27');
echo "С пробелами: " . ($tin2->isValid() ? 'ДА' : 'НЕТ') . "\n";

echo "=== Завершено ===\n"; 