<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompanyLogoDimensionRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $image = $value;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $image = imagecreatefromstring(base64_decode($image));
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width < 100 || $height < 100) {
            $fail('Logo dimension must have width > 100px and height > 100px.');
        }
    }
}
