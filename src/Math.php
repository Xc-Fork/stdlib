<?php declare(strict_types=1);

namespace Toolkit\Stdlib;

use function abs;
use function ceil;
use function floor;
use function round;
use const PHP_ROUND_HALF_UP;

/**
 * Class Math
 *
 * @package Toolkit\Stdlib
 */
class Math
{
    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function floor($value): int
    {
        return (int)floor((float)$value);
    }

    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function ceil($value): int
    {
        return (int)ceil((float)$value);
    }

    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function abs($value): int
    {
        return (int)abs($value);
    }

    /**
     * @param int|float $value
     * @param int $precision
     * @param int $mode
     *
     * @return float
     */
    public static function round($value, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        return (float)round((float)$value, $precision, $mode);
    }

    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function roundInt($value): int
    {
        return (int)round((float)$value);
    }
}
