<?php

namespace App\Services;

class NutriCalculationService
{
    public function calculateBMI(float $weightKg, float $heightCm): array
    {
        if ($heightCm <= 0) {
            return ['bmi' => 0, 'category' => 'Invalid'];
        }

        $heightM = $heightCm / 100;
        $bmi = $weightKg / ($heightM * $heightM);
        
        return [
            'bmi' => round($bmi, 2),
            'category' => $this->getBMICategory($bmi)
        ];
    }

    private function getBMICategory(float $bmi): string
    {
        if ($bmi < 16.0) return 'Severely Wasted';
        if ($bmi < 18.5) return 'Wasted';
        if ($bmi < 25.0) return 'Normal';
        if ($bmi < 30.0) return 'Overweight';
        return 'Obese';
    }
}
