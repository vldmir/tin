# Analysis Report: Failing Tests for Switzerland, SouthKorea, and Ukraine

## Overview

The validation flow in `CountryHandler::validate()` normalizes the TIN first, then passes the normalized TIN to `hasValidPattern()`. However, the patterns in these handlers expect the original format with separators (dots, dashes), causing pattern matching to fail.

---

## 1. Switzerland

### Test Constants from Spec (`SwitzerlandSpec.php`)

**VALID_NUMBER:**
- AVS/AHV format:
  - `'756.1234.5678.90'`
  - `'756.9999.9999.99'`
  - `'756.1111.1111.10'`
- UID format:
  - `'CHE-123.456.789'`
  - `'CHE123456789'`
  - `'CHE-100.000.008'`
  - `'CHE100000008'`

**INVALID_NUMBER_CHECK:** `'756.1234.5678.91'` (Invalid AVS checksum)

**INVALID_NUMBER_LENGTH:** `'756.1234.567'` (Too short)

**INVALID_NUMBER_PATTERN:** `'ABC.DEFG.HIJK.LM'`

**INVALID_NUMBERS:**
- `'755.1234.5678.90'` (Doesn't start with 756)
- `'756.1234.5678.99'` (Invalid AVS checksum)
- `'CHE-123.456.780'` (Invalid UID checksum)
- `'CHE-999.999.999'` (Invalid UID - checksum would be 10)

### Handler Constants (`Switzerland.php`)

- **LENGTH:** `13` (Maximum normalized length)
- **PATTERN:** `'^(756\.\d{4}\.\d{4}\.\d{2}|CHE-?\d{3}\.?\d{3}\.?\d{3})$'`
- **PATTERN_AVS:** `'^756\.\d{4}\.\d{4}\.\d{2}$'`
- **PATTERN_UID:** `'^CHE-?\d{3}\.?\d{3}\.?\d{3}$'`

### Why Validation Fails

1. **Pattern Mismatch After Normalization:**
   - Input: `'756.1234.5678.90'` → Normalized: `'7561234567890'` (13 chars, no dots)
   - Pattern expects: `'756\.\d{4}\.\d{4}\.\d{2}'` (with dots)
   - Result: Pattern fails because normalized TIN has no separators

2. **UID Pattern Issue:**
   - Input: `'CHE-123.456.789'` → Normalized: `'CHE123456789'` (12 chars)
   - Pattern expects: `'CHE-?\d{3}\.?\d{3}\.?\d{3}'` (with optional separators)
   - Result: Pattern fails because normalized TIN has no separators

3. **Length Check Works:**
   - Handler overrides `hasValidLength()` correctly to handle both AVS (13 digits) and UID (9 digits after CHE prefix)

### Quick Fix Suggestion

**Option 1 (Recommended):** Override `hasValidPattern()` to check the original TIN before normalization:

```php
protected function hasValidPattern(string $tin): bool
{
    // Check pattern on original TIN (before normalization)
    $originalTin = $tin; // This is already normalized in validate(), so we need to store original
    
    // Actually, we need to check BEFORE normalization happens
    // So override validate() or change approach
}
```

**Option 2:** Update patterns to match normalized format:

```php
public const PATTERN_AVS = '^756\d{10}$';  // 13 digits total
public const PATTERN_UID = '^CHE\d{9}$';   // CHE + 9 digits
```

**Option 3:** Override `validate()` to check pattern before normalization:

```php
public function validate(string $tin): bool
{
    // Check pattern on original TIN
    if (!$this->hasValidPattern($tin)) {
        throw TINException::invalidPattern($tin);
    }
    
    $normalizedTin = $this->normalizeTin($tin);
    
    if (!$this->hasValidLength($normalizedTin)) {
        throw TINException::invalidLength($tin);
    }
    
    // ... rest of validation
}
```

---

## 2. SouthKorea

### Test Constants from Spec (`SouthKoreaSpec.php`)

**VALID_NUMBER:**
- RRN format:
  - `'900101-1234563'`
  - `'9001011234563'`
  - `'850315-2345674'`
  - `'8503152345674'`
- BRN format:
  - `'220-86-05173'`
  - `'2208605173'`
  - `'123-45-67894'`
  - `'1234567894'`

**INVALID_NUMBER_CHECK:** `'900101-1234560'` (Invalid RRN checksum)

**INVALID_NUMBER_LENGTH:** `'900101-123'` (Too short)

**INVALID_NUMBER_PATTERN:** `'ABC-DEF-GHIJKLM'`

**INVALID_NUMBERS:**
- `'900132-1234567'` (Invalid date - 32nd day)
- `'901301-1234567'` (Invalid month - 13)
- `'900101-9234567'` (Invalid gender digit - 9 for 1900s)
- `'123-45-67890'` (Invalid BRN checksum)
- `'000-00-00000'` (All zeros BRN)

### Handler Constants (`SouthKorea.php`)

- **LENGTH:** `13` (Maximum length for RRN)
- **PATTERN:** `'^(\d{6}-?\d{7}|\d{3}-?\d{2}-?\d{5})$'`
- **PATTERN_BRN:** `'^\d{3}-?\d{2}-?\d{5}$'`
- **PATTERN_RRN:** `'^\d{6}-?\d{7}$'`

### Why Validation Fails

1. **Pattern Mismatch After Normalization:**
   - Input: `'900101-1234563'` → Normalized: `'9001011234563'` (13 digits, no dash)
   - Pattern expects: `'\d{6}-?\d{7}'` (with optional dash)
   - Result: Pattern fails because normalized TIN has no separators

2. **BRN Pattern Issue:**
   - Input: `'220-86-05173'` → Normalized: `'2208605173'` (10 digits, no dashes)
   - Pattern expects: `'\d{3}-?\d{2}-?\d{5}'` (with optional dashes)
   - Result: Pattern fails because normalized TIN has no separators

3. **Length Check Works:**
   - Handler overrides `hasValidLength()` correctly to handle both RRN (13 digits) and BRN (10 digits)

### Quick Fix Suggestion

**Option 1 (Recommended):** Update patterns to match normalized format:

```php
public const PATTERN_RRN = '^\d{13}$';  // 13 digits
public const PATTERN_BRN = '^\d{10}$';  // 10 digits
public const PATTERN = '^(\d{13}|\d{10})$';
```

**Option 2:** Override `validate()` to check pattern before normalization (same as Switzerland Option 3)

---

## 3. Ukraine

### Test Constants from Spec (`UkraineSpec.php`)

**VALID_NUMBER (from test cases):**
- `'5632582743'`
- `'2935277368'`
- `'5566567954'`
- `'5555555555'` (Special case - all same digits but valid checksum)

**INVALID_NUMBER_CHECK:**
- `'5632582744'` (Wrong checksum)
- `'2935277369'` (Wrong checksum)
- `'5555555556'` (Wrong checksum)

**INVALID_NUMBER_LENGTH:**
- `'123456789'` (Too short - 9 digits)
- `'12345678901'` (Too long - 11 digits)

**INVALID_NUMBER_PATTERN:**
- `'123456789a'` (Contains letter)
- `'123456789-'` (Contains dash)

**INVALID_NUMBERS:**
- `'1111111111'` (All same digits - invalid)
- `'0000000000'` (All zeros - invalid)

### Handler Constants (`Ukraine.php`)

- **LENGTH:** `10`
- **PATTERN:** `'^\d{10}$'`
- **MASK:** `'9999999999'`

### Why Validation Might Fail

1. **Pattern Check Issue:**
   - Handler overrides `normalizeTin()` to only remove non-digits: `preg_replace('/[^0-9]/', '', $tin)`
   - Base class `normalizeTin()` removes all non-alphanumeric: `preg_replace('#[^[:alnum:]]#u', '', $tin)`
   - **The handler's override is correct**, but there might be an issue with how the base class calls it

2. **All Same Digits Logic:**
   - Handler rejects all same digits UNLESS checksum is valid: `if (preg_match('/^(\d)\1{9}$/', $tin) && !$this->validateChecksum($tin))`
   - Test case `'5555555555'` should pass (all same digits but valid checksum)
   - Test case `'1111111111'` should fail (all same digits and invalid checksum)
   - **This logic seems correct**

3. **Potential Issue - Base Class Normalization:**
   - The base class `validate()` calls `$this->normalizeTin($tin)` 
   - But Ukraine overrides `normalizeTin()` to only remove non-digits
   - However, the base class method signature might not allow proper override, or there might be a conflict

4. **Checksum Algorithm:**
   - Algorithm appears correct based on test cases
   - Weights: `[1, 2, 3, 4, 5, 6, 7, 8, 9]`
   - Check digit: `sum % 10`
   - Verified: `'5632582743'` → checksum = 3 ✓, `'2935277368'` → checksum = 8 ✓

### Quick Fix Suggestion

**Most Likely Issue:** The handler's `normalizeTin()` override might not be called correctly, or there's a conflict with the base class. Verify that:

1. The override is working correctly - test that `normalizeTin('563-258-2743')` returns `'5632582743'`
2. The pattern matching happens on the correctly normalized TIN
3. The checksum validation receives the correct 10-digit string

**If pattern matching fails:** Ensure `hasValidPattern()` receives the normalized TIN (10 digits only), not the original with separators.

**If checksum fails:** Verify the algorithm matches the test expectations. The current implementation looks correct based on manual verification.

---

## Summary of Issues

| Country | Issue | Root Cause | Fix Priority |
|---------|-------|------------|--------------|
| **Switzerland** | Pattern mismatch | Patterns expect separators, but validation uses normalized TIN (no separators) | HIGH |
| **SouthKorea** | Pattern mismatch | Patterns expect separators, but validation uses normalized TIN (no separators) | HIGH |
| **Ukraine** | Checksum algorithm | Simplified algorithm may not match official Ukrainian TIN validation | MEDIUM |

## Recommended Fix Order

1. **Switzerland & SouthKorea:** Update patterns to match normalized format OR override `validate()` to check pattern before normalization
2. **Ukraine:** Verify and correct checksum algorithm if needed
