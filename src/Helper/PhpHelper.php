<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use RuntimeException;
use Toolkit\Stdlib\Obj\ObjectHelper;
use function array_sum;
use function explode;
use function fopen;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function memory_get_peak_usage;
use function memory_get_usage;
use function method_exists;
use function microtime;
use function number_format;
use function ob_get_clean;
use function ob_start;
use function preg_replace;
use function strpos;
use function strtoupper;
use function var_dump;
use function var_export;

/**
 * Class PhpHelper
 *
 * @package Toolkit\PhpKit
 */
class PhpHelper
{
    /**
     * @param $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        if (is_callable($value)) {
            return $value();
        }

        return $value;
    }

    /**
     * @param string $mode
     *
     * @return resource
     */
    public static function newMemoryStream(string $mode = 'rwb')
    {
        $handle = fopen('php://memory', $mode);

        if (!is_resource($handle)) {
            throw new RuntimeException('create temp memory stream fail');
        }

        return $handle;
    }

    /**
     * get $_SERVER value
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public static function serverParam(string $name, $default = '')
    {
        $name = strtoupper($name);

        return $_SERVER[$name] ?? $default;
    }

    /**
     * @param callable|mixed $cb
     * @param array          ...$args
     *
     * @return mixed
     */
    public static function call($cb, ...$args)
    {
        if (is_string($cb)) {
            // function
            if (strpos($cb, '::') === false) {
                return $cb(...$args);
            }

            // ClassName::method
            $cb = explode('::', $cb, 2);
        } elseif (is_object($cb) && method_exists($cb, '__invoke')) {
            return $cb(...$args);
        }

        if (is_array($cb)) {
            [$obj, $mhd] = $cb;

            return is_object($obj) ? $obj->$mhd(...$args) : $obj::$mhd(...$args);
        }

        return $cb(...$args);
    }

    /**
     * @param callable $cb
     * @param array    $args
     *
     * @return mixed
     */
    public static function callByArray(callable $cb, array $args)
    {
        return self::call($cb, ...$args);
    }

    /**
     * Set property values ​​for object
     * - 会先尝试用 setter 方法设置属性
     * - 再尝试直接设置属性
     *
     * @param mixed $object An object instance
     * @param array $options
     *
     * @return mixed
     */
    public static function initObject($object, array $options)
    {
        return ObjectHelper::init($object, $options);
    }

    /**
     * 获取资源消耗
     *
     * @param int       $startTime
     * @param int|float $startMem
     * @param array     $info
     * @param bool      $realUsage
     *
     * @return array
     */
    public static function runtime($startTime, $startMem, array $info = [], $realUsage = false): array
    {
        $info['startTime'] = $startTime;
        $info['endTime']   = microtime(true);
        $info['endMemory'] = memory_get_usage($realUsage);

        // 计算运行时间
        $info['runtime'] = number_format(($info['endTime'] - $startTime) * 1000, 3) . 'ms';

        if ($startMem) {
            $startMem = array_sum(explode(' ', $startMem));
            $endMem   = array_sum(explode(' ', $info['endMemory']));

            $info['memory'] = number_format(($endMem - $startMem) / 1024, 3) . 'kb';
        }

        $peakMem = memory_get_peak_usage(true) / 1024 / 1024;
        // record
        $info['peakMemory'] = number_format($peakMem, 3) . 'Mb';

        return $info;
    }

    /**
     * dump vars
     *
     * @param array ...$args
     *
     * @return string
     */
    public static function dumpVars(...$args): string
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return preg_replace("/=>\n\s+/", '=> ', $string);
    }

    /**
     * print vars
     *
     * @param array ...$args
     *
     * @return string
     */
    public static function printVars(...$args): string
    {
        $string = '';

        foreach ($args as $arg) {
            $string .= print_r($arg, 1) . PHP_EOL;
        }

        return preg_replace("/Array\n\s+\(/", 'Array (', $string);
    }

    /**
     * @param mixed $var
     *
     * @return string
     */
    public static function exportVar($var): string
    {
        $string = var_export($var, true);

        return preg_replace('/=>\s+\n\s+array \(/', '=> array (', $string);
    }
}
