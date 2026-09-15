<?php
/**
 * @author denis.chernonozhkin
 * @Date 15.09.2026 16:42
 */

namespace App\Support;

class Pluralizer
{
    /**
     * Склоняет существительное в зависимости от числа
     *
     * @param int $count
     * @param string $one
     * @param string $few
     * @param string $many
     * @return string
     */
    public static function make(int $count, string $one, string $few, string $many): string
    {
        $n = abs($count) % 100;
        $n1 = $n % 10;

        if ($n > 10 && $n < 20) {
            return $many;
        }

        if ($n1 > 1 && $n1 < 5) {
            return $few;
        }

        if ($n1 === 1) {
            return $one;
        }

        return $many;
    }
}
