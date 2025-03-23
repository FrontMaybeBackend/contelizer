<?php

namespace App\Service;

class PeselValidator
{
    /**
     * Waliduje numer PESEL zgodnie z oficjalnym algorytmem.
     */
    public function validate(string $pesel): bool
    {
        // Sprawdzenie czy PESEL składa się z 11 cyfr
        if (!preg_match('/^\d{11}$/', $pesel)) {
            return false;
        }

        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

        $controlSum = 0;
        for ($i = 0; $i < 11; $i++) {
            $controlSum += (int)$pesel[$i] * $weights[$i];
        }

        // Sprawdzenie czy suma kontrolna jest poprawna (ostatnia cyfra sumy powinna być 0)
        return $controlSum % 10 === 0;
    }

    /**
     * Waliduje datę urodzenia zawartą w numerze PESEL.
     */
    public function validateBirthDate(string $pesel): bool
    {
        if (!$this->validate($pesel)) {
            return false;
        }

        // Wyciągnięcie daty z PESEL
        $year = (int)substr($pesel, 0, 2);
        $month = (int)substr($pesel, 2, 2);
        $day = (int)substr($pesel, 4, 2);

        // Określenie wieku na podstawie miesiąca
        if ($month > 80) {
            $year += 1800;
            $month -= 80;
        } elseif ($month > 60) {
            $year += 2200;
            $month -= 60;
        } elseif ($month > 40) {
            $year += 2100;
            $month -= 40;
        } elseif ($month > 20) {
            $year += 2000;
            $month -= 20;
        } else {
            $year += 1900;
        }
        return checkdate($month, $day, $year);
    }
}
