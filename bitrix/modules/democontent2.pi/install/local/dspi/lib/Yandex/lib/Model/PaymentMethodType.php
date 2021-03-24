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

namespace YandexCheckout\Model;

use YandexCheckout\Common\AbstractEnum;

/**
 * PaymentMethodType - ��� ��������� ������� ��� ���������� �������
 * |���|��������|
 * --- | ---
 * |yandex_money|������ �� �������� ������.������|
 * |bank_card|������ � ������������ ���������� �����|
 * |sberbank|������ ��������������|
 * |cash|������ ���������|
 * |mobile_balance|������ � ������� ���������� ��������|
 * |apple_pay|������ ApplePay|
 * |android_pay|������ AndroidPay|
 * |qiwi|������ �� �������� Qiwi|
 * 
 */
class PaymentMethodType extends AbstractEnum
{
    const YANDEX_MONEY = 'yandex_money';
    const BANK_CARD = 'bank_card';
    const SBERBANK = 'sberbank';
    const CASH = 'cash';
    const MOBILE_BALANCE = 'mobile_balance';
    const APPLE_PAY = 'apple_pay';
    const ANDROID_PAY = 'android_pay';
    const QIWI = 'qiwi';
    const WEBMONEY = 'webmoney';
    const ALFABANK = 'alfabank';

    protected static $validValues = array(
        self::YANDEX_MONEY => true,
        self::BANK_CARD => true,
        self::SBERBANK => true,
        self::CASH => true,
        self::MOBILE_BALANCE => false,
        self::APPLE_PAY => false,
        self::ANDROID_PAY => false,
        self::QIWI => true,
        self::ALFABANK => true,
        self::WEBMONEY => true,
    );
}
