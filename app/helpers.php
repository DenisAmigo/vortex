<?php

use App\Support\Pluralizer;

if (!function_exists('plural')) {
    /**
     * Склоняет существительное в зависимости от числа.
     */
    function plural(int $count, string $one, string $few, string $many): string
    {
        return Pluralizer::make($count, $one, $few, $many);
    }
}
