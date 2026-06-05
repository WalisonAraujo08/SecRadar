<?php

namespace App\Services;

class CommissionService
{
    const LEVELS = [
        ['min' => 0,    'max' => 29,   'rate' => 10, 'name' => 'Bronze',   'icon' => '🥉'],
        ['min' => 30,   'max' => 99,   'rate' => 15, 'name' => 'Prata',    'icon' => '🥈'],
        ['min' => 100,  'max' => 999,  'rate' => 20, 'name' => 'Ouro',     'icon' => '🥇'],
        ['min' => 1000, 'max' => null, 'rate' => 25, 'name' => 'Diamante', 'icon' => '💎'],
    ];

    public static function getLevel(int $activeReferrals): array
    {
        foreach (array_reverse(self::LEVELS) as $level) {
            if ($activeReferrals >= $level['min']) {
                return $level;
            }
        }
        return self::LEVELS[0];
    }

    public static function getRate(int $activeReferrals): float
    {
        return self::getLevel($activeReferrals)['rate'];
    }

    public static function getNextLevel(int $activeReferrals): ?array
    {
        foreach (self::LEVELS as $level) {
            if ($activeReferrals < $level['min']) {
                return $level;
            }
        }
        return null;
    }

    public static function calcCommission(float $planAmount, int $activeReferrals): float
    {
        return round($planAmount * (self::getRate($activeReferrals) / 100), 2);
    }
}
