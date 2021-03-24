<?php

/**
 * The MIT License
 *
 * Copyright (c) 2017 NBCO Yandex.Money LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace YandexCheckout\Helpers;

/**
 * ����� ������� ��� ��������� ��������� ��������, ������������ � ������
 *
 * @package YandexCheckout\Helpers
 */
class Random
{
    /**
     * ���������� ��������� ����� �����. �� ��������� ���������� ����� �� ���� �� PHP_INT_MAX.
     * @param int|null $min ���������� ��������� ��������
     * @param int|null $max ����������� ��������� ��������
     * @param bool $useBest ������������ �� ������� random_int ���� ��� ��������
     * @return int ��������� ����� �����
     */
    public static function int($min = null, $max = null, $useBest = true)
    {
        if ($min === null) {
            $min = 0;
        }
        if ($max === null) {
            $max = PHP_INT_MAX;
        }
        if (function_exists('random_int') && $useBest) {
            return random_int($min, $max);
        } else {
            return mt_rand($min, $max);
        }
    }

    /**
     * ���������� ��������� ����� � ��������� ������. �� ��������� ���������� �������� � ���������� �� ���� �� ������.
     * @param float|null $min ���������� ��������� ��������
     * @param float|null $max ����������� ��������� ��������
     * @param bool $useBest ������������ �� ������� random_int ���� ��� ��������
     * @return float ��������� ����� � ��������� ������
     */
    public static function float($min = null, $max = null, $useBest = true)
    {
        $random = self::int(null, null, $useBest) / PHP_INT_MAX;
        if ($min === null) {
            $min = 0.0;
        }
        if ($max === null) {
            return $random + $min;
        }
        return ($random * ($max - $min)) + $min;
    }

    /**
     * ���������� ������ �� ��������� ��������
     * @param int $length ����� ������������ ������, ��� ����������� �����, ���� ������� ������� $maxLength
     * @param int|null $maxLength ���� �������� �� ����� null, ���������� ����� ������ �� $length �� $maxLength
     * @param string|array|null $characters ������ ��� ������ ������������ � ������ ��������
     * @param bool $useBest ������������ �� ������� random_int ���� ��� ��������
     * @return string ������, ��������� �� ��������� ��������
     */
    public static function str($length, $maxLength = null, $characters = null, $useBest = true)
    {
        $result = '';
        if ($maxLength !== null) {
             if (is_string($maxLength)) {
                 $characters = $maxLength;
             } else {
                 $length = self::int($length, $maxLength, $useBest);
             }
        }
        if ($characters === null) {
            for ($i = 0; $i < $length; $i++) {
                $chr = chr(self::int(32, 125, $useBest));
                $result .= $chr;
            }
        } else {
            for ($i = 0; $i < $length; $i++) {
                $chr = $characters[self::int(0, strlen($characters) - 1, $useBest)];
                $result .= $chr;
            }
        }
        return $result;
    }

    /**
     * ���������� ������, ��������� �� �������� '0123456789abcdef'
     * @param int $length ����� ������������ ������
     * @param bool $useBest ������������ �� ������� random_int ���� ��� ��������
     * @return string ������, ��������� �� ��������� ��������
     */
    public static function hex($length, $useBest = true)
    {
        return self::str($length, '0123456789abcdef', $useBest);
    }

    /**
     * ���������� ��������� ������������������ ����
     * @param int $length ����� ������������ ������
     * @param bool $useBest ������������ �� ������� random_int ���� ��� ��������
     * @return string ������, ��������� �� ��������� ��������
     */
    public static function bytes($length, $useBest = true)
    {
        if (function_exists('random_bytes') && $useBest) {
            $result = random_bytes($length);
        } else {
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $chr = chr(self::int(0, 255));
                $result .= $chr;
            }
        }
        return $result;
    }

    /**
     * ���������� ��������� �������� �� ����������� �������
     * @param array $values ������ �������� ������
     * @param bool $useBest ������������ �� ������� random_int ���� ��� ��������
     * @return mixed ��������� �������� �� ����������� �������
     */
    public static function value(array $values, $useBest = true)
    {
        return $values[self::int(0, count($values) - 1, $useBest)];
    }

    /**
     * ���������� ��������� ������� ��������
     * @return bool ���� true ���� false, ���� �� ����
     */
    public static function bool()
    {
        return self::int(0, 1) === 1;
    }
}
