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
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\PaymentMethodType;

/**
 * PaymentMethodBankCard
 * ������, ����������� ����� ������ ���������� ������
 * @property string $type ��� �������
 * @property string $last4 ��������� 4 ����� ������ �����
 * @property string $expiryYear ���� ��������, ���
 * @property string $expiry_year ���� ��������, ���
 * @property string $expiryMonth ���� ��������, �����
 * @property string $expiry_month ���� ��������, �����
 * @property string $cardType ��� ���������� �����
 * @property string $card_type ��� ���������� �����
 */
class PaymentMethodBankCard extends AbstractPaymentMethod
{
    /**
     * @var string ��������� 4 ����� ������ �����
     */
    private $_last4;

    /**
     * @var string ���� ��������, ���
     */
    private $_expiryYear;

    /**
     * @var string ���� ��������, �����
     */
    private $_expiryMonth;

    /**
     * @var string ��� ���������� �����
     */
    private $_cardType;

    public function __construct()
    {
        $this->_setType(PaymentMethodType::BANK_CARD);
    }

    /**
     * @return string ��������� 4 ����� ������ �����
     */
    public function getLast4()
    {
        return $this->_last4;
    }

    /**
     * @param string $value ��������� 4 ����� ������ �����
     */
    public function setLast4($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty card last4 value', 0, 'PaymentMethodBankCard.last4');
        } elseif (TypeCast::canCastToString($value)) {
            if (preg_match('/^[0-9]{4}$/', (string)$value)) {
                $this->_last4 = (string)$value;
            } else {
                throw new InvalidPropertyValueException(
                    'Invalid card last4 value', 0, 'PaymentMethodBankCard.last4', $value
                );
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid card last4 value type', 0, 'PaymentMethodBankCard.last4', $value
            );
        }
    }

    /**
     * @return string ���� ��������, ���
     */
    public function getExpiryYear()
    {
        return $this->_expiryYear;
    }

    /**
     * @param string $value ���� ��������, ���
     */
    public function setExpiryYear($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException(
                'Empty card expiry year value', 0, 'PaymentMethodBankCard.expiryYear'
            );
        } elseif (is_numeric($value)) {
            if (!preg_match('/^\d\d\d\d$/', $value) || $value < 2000 || $value > 2200) {
                throw new InvalidPropertyValueException(
                    'Invalid card expiry year value', 0, 'PaymentMethodBankCard.expiryYear', $value
                );
            }
            $this->_expiryYear = (string)$value;
        } else {
            throw new InvalidPropertyValueException(
                'Invalid card expiry year value', 0, 'PaymentMethodBankCard.expiryYear', $value
            );
        }
    }

    /**
     * @return string ���� ��������, �����
     */
    public function getExpiryMonth()
    {
        return $this->_expiryMonth;
    }

    /**
     * @param string $value ���� ��������, �����
     */
    public function setExpiryMonth($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException(
                'Empty card expiry month value', 0, 'PaymentMethodBankCard.expiryMonth'
            );
        } elseif (is_numeric($value)) {
            if (!preg_match('/^\d\d$/', $value)) {
                throw new InvalidPropertyValueException(
                    'Invalid card expiry month value', 0, 'PaymentMethodBankCard.expiryMonth', $value
                );
            }
            if (is_string($value) && $value[0] == '0') {
                $month = (int)($value[1]);
            } else {
                $month = (int)$value;
            }
            if ($month < 1 || $month > 12) {
                throw new InvalidPropertyValueException(
                    'Invalid card expiry month value', 0, 'PaymentMethodBankCard.expiryMonth', $value
                );
            } else {
                $this->_expiryMonth = (string)$value;
            }
        } else {
            throw new InvalidPropertyValueException(
                'Invalid card expiry month value', 0, 'PaymentMethodBankCard.expiryMonth', $value
            );
        }
    }

    /**
     * @return string ��� ���������� �����
     */
    public function getCardType()
    {
        return $this->_cardType;
    }

    /**
     * @param string $value ��� ���������� �����
     */
    public function setCardType($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty cardType value', 0, 'PaymentMethodBankCard.cardType');
        } elseif (TypeCast::canCastToString($value)) {
            $castedValue = (string)$value;
            if (PaymentMethodCardType::valueExists($castedValue)) {
                $this->_cardType = $castedValue;
            } else {
                throw new InvalidPropertyValueException(
                    'Invalid cardType value', 0, 'PaymentMethodBankCard.cardType', $value
                );
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid cardType value type', 0, 'PaymentMethodBankCard.cardType', $value
            );
        }
    }
}
