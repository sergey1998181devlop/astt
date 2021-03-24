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

namespace YandexCheckout\Request;

use YandexCheckout\Common\AbstractRequest;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\ConfirmationType;
use YandexCheckout\Model\CurrencyCode;

/**
 * ����� ������� ������ ��������� �������� ������
 *
 * @package YandexCheckout\Request
 *
 * @property string $accountId ������������� ��������
 * @property string $gatewayId ������������� �����
 * @property string $amount ����� ������
 * @property string $currency ��� ������
 * @property string $confirmationType �������� ������������� �������
 */
class PaymentOptionsRequest extends AbstractRequest implements PaymentOptionsRequestInterface
{
    /**
     * @var string ������������� ��������
     */
    private $_accountId;

    /**
     * @var string ������������� �����
     */
    private $_gatewayId;

    /**
     * @var string �����
     */
    private $_amount;

    /**
     * @var string ��� ������
     */
    private $_currency;

    /**
     * @var string �������� ������������� �������
     */
    private $_confirmationTypes;

    /**
     * ���������� ������������� �������� ��� �������� ��������� �������� �����
     * @return string ������������� ��������
     */
    public function getAccountId()
    {
        return $this->_accountId;
    }

    /**
     * ���������, ��� �� ���������� ������������� ��������
     * @return bool True ���� ������������� �������� ��� ����������, false ���� ���
     */
    public function hasAccountId()
    {
        return $this->_accountId !== null;
    }

    /**
     * ������������� ������������� ��������
     * @param string|null $value �������� �������������� ��������, null ���� ��������� ������� ��������
     */
    public function setAccountId($value)
    {
        if ($value === null || $value === '') {
            $this->_accountId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_accountId = (string)$value;
        } else {
            throw new \InvalidArgumentException('Invalid account_id value type "' . gettype($value) . '"');
        }
    }

    /**
     * ���������� ������������� �����
     * @return string ������������� �����
     */
    public function getGatewayId()
    {
        return $this->_gatewayId;
    }

    /**
     * ���������, ��� �� ���������� ������������� �����
     * @return bool True ���� ������������� ����� ��� ����������, false ���� ���
     */
    public function hasGatewayId()
    {
        return !empty($this->_gatewayId);
    }

    /**
     * ������������� ������������� �����
     * @param string|null $value �������� �������������� �����, null ���� ��������� ������� ��������
     */
    public function setGatewayId($value)
    {
        if ($value === null || $value === '') {
            $this->_gatewayId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_gatewayId = (string)$value;
        } else {
            throw new \InvalidArgumentException('Invalid gateway_id value type "' . gettype($value) . '"');
        }
    }

    /**
     * ���������� ����� ������
     * @return string ����� ������
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * ���������, ���� �� ����������� ����� ������
     * @return bool True ���� ����� ������ ���� �����������, false ���� ���
     */
    public function hasAmount()
    {
        return !empty($this->_amount);
    }

    /**
     * ������������� ����� �������
     * @param string|null $value ����� �������, null ���� ��������� ������� ��������
     */
    public function setAmount($value)
    {
        if ($value === null || $value === '') {
            $this->_amount = null;
        } else {
            if (!is_scalar($value)) {
                if (!is_object($value) || !method_exists($value, '__toString')) {
                    throw new InvalidPropertyValueTypeException(
                        'Invalid amount value type', 0, 'amount.value', $value
                    );
                }
                $value = (string)$value;
            }
            if (!is_numeric($value) || $value < 0.0) {
                throw new InvalidPropertyValueException(
                    'Invalid amount value "' . $value . '"', 0, 'amount.value', $value
                );
            } elseif ($value < 0.01) {
                $this->_amount = null;
            } else {
                $this->_amount = number_format($value, 2, '.', '');
            }
        }
    }

    /**
     * ���������� ��� ������, � ������� �������������� �������
     * @return string ��� ������
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * ��������� ��� �� ���������� ��� ������
     * @return bool True ���� ��� ������ ��� ����������, false ���� ���
     */
    public function hasCurrency()
    {
        return !empty($this->_currency);
    }

    /**
     * ������������� ��� ������ � ������� ��������� �������� �����
     * @param string $value ��� ������, null ���� ��������� ������� ��������
     */
    public function setCurrency($value)
    {
        if ($value === null || $value === '') {
            $this->_currency = null;
        } elseif (TypeCast::canCastToEnumString($value)) {
            $value = strtoupper($value);
            if (!CurrencyCode::valueExists($value)) {
                throw new \InvalidArgumentException('Invalid currency value: "' . $value . '"');
            }
            $this->_currency = $value;
        } else {
            throw new \InvalidArgumentException('Invalid currency value type: "' . gettype($value) . '"');
        }
    }

    /**
     * ���������� �������� ������������� �������, ��� �������� ������������� ������ �������� ������
     * @return string �������� ������������� �������
     */
    public function getConfirmationType()
    {
        return $this->_confirmationTypes;
    }

    /**
     * ��������� ��� �� ���������� �������� ������������� �������
     * @return bool True ���� �������� ������������� ������� ��� ����������, false ���� ���
     */
    public function hasConfirmationType()
    {
        return !empty($this->_confirmationTypes);
    }

    /**
     * ������������� �������� ������������� �������, ��� �������� ������������� ������ �������� ������
     * @param string $value �������� ������������� �������
     */
    public function setConfirmationType($value)
    {
        if ($value === null || $value === '') {
            $this->_confirmationTypes = null;
        } elseif (TypeCast::canCastToEnumString($value)) {
            if (!ConfirmationType::valueExists((string)$value)) {
                throw new \InvalidArgumentException('Invalid confirmation_type value: "' . $value . '"');
            }
            $this->_confirmationTypes = $value;
        } else {
            throw new \InvalidArgumentException('Invalid confirmation_type value type: "' . gettype($value) . '"');
        }
    }

    /**
     * ���������� ������� ������, ��������� ��� �� ������ �������� �����������
     * @return bool True ���� ������ �������, false ���� ���
     */
    public function validate()
    {
        if (empty($this->_accountId)) {
            $this->setValidationError('Account id not specified');
            return false;
        }
        return true;
    }

    /**
     * ���������� ������� ������� �������� �������� ������� �������� ������
     * @return PaymentOptionsRequestBuilder ������ �������� ������� �������� ������
     */
    public static function builder()
    {
        return new PaymentOptionsRequestBuilder();
    }
}
