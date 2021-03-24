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

use YandexCheckout\Common\AbstractObject;
use YandexCheckout\Common\Exceptions\EmptyPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;

/**
 * ����� ���������� �������.
 *
 * ���������� ������� �����, ���� �� ���������� ������ �������� � ������ ������ �������� ��� �������� ������ � �����
 * ������� ��������.
 *
 * @property string $accountId ������������� ��������
 * @property string $account_id ������������� ��������
 * @property string $gatewayId ������������� �����
 * @property string $gateway_id ������������� �����
 */
class Recipient extends AbstractObject implements RecipientInterface
{
    /**
     * @var string ������������� ��������
     */
    private $_accountId;

    /**
     * @var string ������������� �����. ������������ ��� ���������� ������� �������� � ������ ������ ��������.
     */
    private $_gatewayId;

    /**
     * ���������� ������������� ��������
     *
     * @return string ������������� ��������
     */
    public function getAccountId()
    {
        return $this->_accountId;
    }

    /**
     * ������������� ������������� ��������
     *
     * @param string $value ������������� ��������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �� ��������� ��������
     */
    public function setAccountId($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty accountId value in Recipient', 0, 'Recipient.accountId');
        } elseif (TypeCast::canCastToString($value)) {
            $this->_accountId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid accountId value type in Recipient', 0, 'Recipient.accountId', $value
            );
        }
    }

    /**
     * ���������� ������������� �����.
     *
     * ������������� ����� ������������ ��� ���������� ������� �������� � ������ ������ ��������.
     *
     * @return string ������������� �����
     */
    public function getGatewayId()
    {
        return $this->_gatewayId;
    }

    /**
     * ������������� ������������� �����
     *
     * @param string $value ������������� �����
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �� ��������� ��������
     */
    public function setGatewayId($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException(
                'Empty gatewayId value in Recipient', 0, 'Recipient.gatewayId'
            );
        } elseif (TypeCast::canCastToString($value)) {
            $this->_gatewayId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid gatewayId value type in Recipient', 0, 'Recipient.gatewayId', $value
            );
        }
    }
}
