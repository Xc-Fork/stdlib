<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str\Traits;

use Toolkit\Stdlib\Str;
use function array_shift;
use function explode;
use function function_exists;
use function is_scalar;
use function is_string;
use function lcfirst;
use function mb_convert_case;
use function mb_strtolower;
use function mb_strtoupper;
use function preg_replace;
use function preg_replace_callback;
use function str_replace;
use function strpos;
use function strtolower;
use function strtoupper;
use function trim;
use function ucfirst;
use function ucwords;
use const MB_CASE_TITLE;

/**
 * Trait StringCaseHelperTrait
 *
 * @package Toolkit\Stdlib\Str\Traits
 */
trait StringCaseHelperTrait
{
    ////////////////////////////////////////////////////////////////////////
    /// Case Convert
    ////////////////////////////////////////////////////////////////////////

    /**
     * Alias of the `strtolower()`
     *
     * @param string $str
     *
     * @return string
     */
    public static function lower($str): string
    {
        return static::strtolower($str);
    }

    /**
     * Alias of the `strtolower()`
     *
     * @param string $str
     *
     * @return string
     */
    public static function toLower($str): string
    {
        return static::strtolower($str);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function strtolower(string $str): string
    {
        if (!is_scalar($str)) {
            return '';
        }

        if (!is_string($str)) {
            return (string)$str;
        }

        return function_exists('mb_strtolower') ? mb_strtolower($str, 'utf-8') : strtolower($str);
    }

    /**
     * Alias of the `strtoupper()`
     *
     * @param string $str
     *
     * @return string
     */
    public static function upper($str): string
    {
        return static::strtoupper($str);
    }

    /**
     * Alias of the `strtoupper()`
     *
     * @param string $str
     *
     * @return string
     */
    public static function toUpper($str): string
    {
        return static::strtoupper($str);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function strtoupper($str): string
    {
        if (!is_scalar($str)) {
            return '';
        }

        if (!is_string($str)) {
            return (string)$str;
        }

        return function_exists('mb_strtoupper') ? mb_strtoupper($str, 'utf-8') : strtoupper($str);
    }

    /**
     * @param $str
     *
     * @return string
     */
    public static function ucfirst($str): string
    {
        if (!is_scalar($str)) {
            return '';
        }

        if (!is_string($str)) {
            return (string)$str;
        }

        return self::strtoupper(Str::substr($str, 0, 1)) . Str::substr($str, 1);
    }

    /**
     * @param $str
     *
     * @return string
     */
    public static function ucwords(string $str): string
    {
        return function_exists('mb_convert_case') ?
            mb_convert_case($str, MB_CASE_TITLE) :
            ucwords(self::strtolower($str));
    }

    /**
     * @param string $str
     * @param bool   $upperFirstChar
     *
     * @return mixed
     */
    public static function camel(string $str, bool $upperFirstChar = false): string
    {
        return self::toCamelCase($str, $upperFirstChar);
    }

    /**
     * @param string $str
     * @param bool   $upperFirstChar
     *
     * @return mixed
     */
    public static function toCamel(string $str, bool $upperFirstChar = false): string
    {
        return self::toCamelCase($str, $upperFirstChar);
    }

    /**
     * to camel
     *
     * @param string $name
     * @param bool   $upperFirst
     *
     * @return string
     */
    public static function camelCase(string $name, bool $upperFirst = false): string
    {
        $name = trim($name, '-_');

        // convert 'first-second' to 'firstSecond'
        if (strpos($name, '-')) {
            $name = ucwords(str_replace('-', ' ', $name));
            $name = str_replace(' ', '', lcfirst($name));
        }

        return $upperFirst ? ucfirst($name) : $name;
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -> firstName)
     *
     * @param string $str
     * @param bool   $upperFirst
     *
     * @return mixed
     */
    public static function toCamelCase(string $str, bool $upperFirst = false): string
    {
        $str = self::strtolower($str);

        if ($upperFirst) {
            $str = self::ucfirst($str);
        }

        return preg_replace_callback('/_+([a-z])/', static function ($c) {
            return strtoupper($c[1]);
        }, $str);
    }

    /**
     * @param string $str
     * @param string $sep
     *
     * @return string
     */
    public static function snake(string $str, string $sep = '_'): string
    {
        return self::toSnakeCase($str, $sep);
    }

    /**
     * @param string $str
     * @param string $sep
     *
     * @return string
     */
    public static function toSnake(string $str, string $sep = '_'): string
    {
        return self::toSnakeCase($str, $sep);
    }

    /**
     * Transform a CamelCase string to underscore_case string
     *
     * @param string $str
     * @param string $sep
     *
     * @return string
     */
    public static function toSnakeCase(string $str, string $sep = '_'): string
    {
        // 'CMSCategories' => 'cms_categories'
        // 'RangePrice' => 'range_price'
        return self::lower(trim(preg_replace('/([A-Z][a-z])/', $sep . '$1', $str), $sep));
    }

    /**
     * 驼峰式 <=> 下划线式
     *
     * @param string $str [description]
     * @param bool   $toCamelCase
     *                    true : 驼峰式 => 下划线式
     *                    false : 驼峰式 <= 下划线式
     *
     * @return string
     */
    public static function nameChange(string $str, bool $toCamelCase = true): string
    {
        $str = trim($str);

        // 默认 ：下划线式 =>驼峰式
        if ($toCamelCase) {
            if (strpos($str, '_') === false) {
                return $str;
            }

            $charList  = explode('_', strtolower($str));
            $newString = array_shift($charList);

            foreach ($charList as $val) {
                $newString .= ucfirst($val);
            }

            return $newString;
        }

        // 驼峰式 => 下划线式
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
    }

    /**
     * Convert \n and \r\n and \r to <br />
     *
     * @param string $str String to transform
     *
     * @return string New string
     */
    public static function nl2br(string $str): string
    {
        return str_replace(["\r\n", "\r", "\n"], '<br />', $str);
    }
}
