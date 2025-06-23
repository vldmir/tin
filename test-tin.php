#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use loophp\Tin\TIN;
use loophp\Tin\Exception\TINException;

echo "=== TIN Library Test Script ===\n\n";

// Test cases for different countries
$testCases = [
    ['country' => 'BE', 'tin' => '71102512345', 'description' => 'Belgian TIN'],
    ['country' => 'ES', 'tin' => '12345678Z', 'description' => 'Spanish DNI'],
    ['country' => 'DE', 'tin' => '12345678901', 'description' => 'German TIN'],
    ['country' => 'FR', 'tin' => '1234567890123', 'description' => 'French TIN'],
    ['country' => 'UK', 'tin' => 'AB123456C', 'description' => 'UK TIN'],
];

foreach ($testCases as $test) {
    echo "Testing {$test['description']} ({$test['country']}): {$test['tin']}\n";
    echo str_repeat('-', 50) . "\n";
    
    try {
        $tin = TIN::fromSlug($test['country'] . $test['tin']);
        
        // Basic validation
        $isValid = $tin->isValid();
        echo "Valid: " . ($isValid ? 'YES' : 'NO') . "\n";
        
        // Get input mask
        $mask = $tin->getInputMask();
        echo "Input Mask: $mask\n";
        
        // Get placeholder
        $placeholder = $tin->getPlaceholder();
        echo "Placeholder: $placeholder\n";
        
        // Format input
        $formatted = $tin->formatInput($test['tin']);
        echo "Formatted: $formatted\n";
        
        // Identify TIN type (if applicable)
        $tinType = $tin->identifyTinType();
        if ($tinType) {
            echo "TIN Type: {$tinType['code']} - {$tinType['name']}\n";
            if (isset($tinType['description'])) {
                echo "Description: {$tinType['description']}\n";
            }
        }
        
        // Check validation (will throw exception if invalid)
        $tin->check();
        echo "Validation: PASSED\n";
        
    } catch (TINException $e) {
        echo "Validation Error: " . $e->getMessage() . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test getting mask info without creating TIN instance
echo "=== Static Method Tests ===\n";
echo str_repeat('-', 50) . "\n";

$countries = ['BE', 'ES', 'DE', 'FR', 'IT'];
foreach ($countries as $country) {
    $maskInfo = TIN::getMaskForCountry($country);
    echo "$country: Mask = {$maskInfo['mask']}, Placeholder = {$maskInfo['placeholder']}\n";
}

echo "\n";

// Test TIN types for countries
echo "=== TIN Types by Country ===\n";
echo str_repeat('-', 50) . "\n";

$countriesWithTypes = ['ES', 'DE', 'UK'];
foreach ($countriesWithTypes as $country) {
    $types = TIN::getTinTypesForCountry($country);
    echo "$country TIN Types:\n";
    foreach ($types as $type => $info) {
        echo "  - $type: {$info['description']}\n";
    }
}

echo "\nTest completed!\n";