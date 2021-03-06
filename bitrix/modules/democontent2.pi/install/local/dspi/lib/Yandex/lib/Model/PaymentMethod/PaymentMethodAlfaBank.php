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

namespace YandexCheckout\Model\PaymentMethod;

use YandexCheckout\Common\Exceptions\EmptyPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\PaymentMethodType;

/**
 * PaymentMethodAlfaBank
 * ??????, ??????????? ????? ??????, ??? ?????? ????? ????? ????.
 * @property string $type ??? ???????
 * @property string $login ??? ???????????? ? ?????-?????
 */
class PaymentMethodAlfaBank extends AbstractPaymentMethod
{
    /**
     * @var string ??? ???????????? ? ?????-?????
     */
    private $_login;

    public function __construct()
    {
        $this->_setType(PaymentMethodType::ALFABANK);
    }

    /**
     * @return string ??? ???????????? ? ?????-?????
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * @param string $value ??? ???????????? ? ?????-?????
     */
    public function setLogin($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty login value', 0, 'PaymentMethodAlfaBank.login');
        } elseif (TypeCast::canCastToString($value)) {
            $this->_login = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid login value type', 0, 'PaymentMethodAlfaBank.login', $value
            );
        }
    }
}
