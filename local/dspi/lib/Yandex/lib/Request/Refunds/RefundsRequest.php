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

namespace YandexCheckout\Request\Refunds;

use YandexCheckout\Common\AbstractRequest;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\RefundStatus;

/**
 * ����� ������� ������� � API ������ ��������� ��������
 *
 * @package YandexCheckout\Request\Refunds
 *
 * @property string $refundId ������������� ��������
 * @property string $paymentId ������������� �������
 * @property string $accountId ������������� ��������
 * @property string $gatewayId ������������� �����
 * @property \DateTime $createdGte ����� ��������, �� (������������)
 * @property \DateTime $createdGt ����� ��������, �� (�� �������)
 * @property \DateTime $createdLte ����� ��������, �� (������������)
 * @property \DateTime $createdLt ����� ��������, �� (�� �������)
 * @property \DateTime $authorizedGte ����� ���������� ��������, �� (������������)
 * @property \DateTime $authorizedGt ����� ���������� ��������, �� (�� �������)
 * @property \DateTime $authorizedLte ����� ����������, �� (������������)
 * @property \DateTime $authorizedLt ����� ����������, �� (�� �������)
 * @property string $status ������ ��������
 * @property string $nextPage ����� ��� ��������� ��������� �������� �������
 */
class RefundsRequest extends AbstractRequest implements RefundsRequestInterface
{
    /**
     * @var string ������������� ��������
     */
    private $_refundId;

    /**
     * @var string ������������� �����
     */
    private $_paymentId;

    /**
     * @var string ������������� ��������
     */
    private $_accountId;

    /**
     * @var string ������������� �����
     */
    private $_gatewayId;

    /**
     * @var \DateTime ����� ��������, �� (������������)
     */
    private $_createdGte;

    /**
     * @var \DateTime ����� ��������, �� (�� �������)
     */
    private $_createdGt;

    /**
     * @var \DateTime ����� ��������, �� (������������)
     */
    private $_createdLte;

    /**
     * @var \DateTime ����� ��������, �� (�� �������)
     */
    private $_createdLt;

    /**
     * @var \DateTime ����� ���������� ��������, �� (������������)
     */
    private $_authorizedGte;

    /**
     * @var \DateTime ����� ���������� ��������, �� (�� �������)
     */
    private $_authorizedGt;

    /**
     * @var \DateTime ����� ����������, �� (������������)
     */
    private $_authorizedLte;

    /**
     * @var \DateTime ����� ����������, �� (�� �������)
     */
    private $_authorizedLt;

    /**
     * @var string ������ ��������
     */
    private $_status;

    /**
     * @var string ����� ��� ��������� ��������� �������� �������
     */
    private $_nextPage;

    /**
     * ���������� ������������� ��������
     * @return string ������������� ��������
     */
    public function getRefundId()
    {
        return $this->_refundId;
    }

    /**
     * ��������� ��� �� ���������� ������������� ��������
     * @return bool True ���� ������������� �������� ��� ����������, false ���� �� ���
     */
    public function hasRefundId()
    {
        return $this->_refundId !== null;
    }

    /**
     * ������������� ������������� ��������
     * @param string $value ������������� ��������, ������� ������ � API
     *
     * @throws InvalidPropertyValueException ������������� ���� ����� ����������� �������� �� ����� 36
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setRefundId($value)
    {
        if ($value === null || $value === '') {
            $this->_refundId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $length = mb_strlen((string)$value, 'utf-8');
            if ($length != 36) {
                throw new InvalidPropertyValueException(
                    'Invalid refund id value', 0, 'RefundsRequest.refundId', $value
                );
            }
            $this->_refundId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid refund id value type', 0, 'RefundsRequest.refundId', $value
            );
        }
    }

    /**
     * ���������� ������������� ������� ���� �� ����� ��� null
     * @return string|null ������������� �������
     */
    public function getPaymentId()
    {
        return $this->_paymentId;
    }

    /**
     * ���������, ��� �� ����� ������������� �������
     * @return bool True ���� ������������� ��� �����, false ���� ���
     */
    public function hasPaymentId()
    {
        return !empty($this->_paymentId);
    }

    /**
     * ������������� ������������� ������� ��� null ���� ��������� ��� �������
     * @param string|null $value ������������� �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ����� ���������� ������ �� ����� 36 ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setPaymentId($value)
    {
        if ($value === null || $value === '') {
            $this->_paymentId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $length = mb_strlen((string)$value, 'utf-8');
            if ($length != 36) {
                throw new InvalidPropertyValueException(
                    'Invalid payment id value in RefundsRequest', 0, 'RefundsRequest.paymentId', $value
                );
            }
            $this->_paymentId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid payment id value type in RefundsRequest', 0, 'RefundsRequest.paymentId', $value
            );
        }
    }

    /**
     * ���������� ������������� ��������, ���� �� ��� �����
     * @return string|null ������������� ��������
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
        return !empty($this->_accountId);
    }

    /**
     * ������������� ������������� ��������
     * @param string $value ������������� �������� ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setAccountId($value)
    {
        if ($value === null || $value === '') {
            $this->_accountId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_accountId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid accountId value type in RefundsRequest', 0, 'RefundsRequest.accountId', $value
            );
        }
    }

    /**
     * ���������� ������������� �����
     * @return string|null ������������� �����
     */
    public function getGatewayId()
    {
        return $this->_gatewayId;
    }

    /**
     * ��������� ��� �� ���������� ������������� �����
     * @return bool True ���� ������������� ����� ��� ����������, false ���� ���
     */
    public function hasGatewayId()
    {
        return !empty($this->_gatewayId);
    }

    /**
     * ������������� ������������� �����
     * @param string|null $value ������������� ����� ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setGatewayId($value)
    {
        if ($value === null || $value === '') {
            $this->_gatewayId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_gatewayId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid gatewayId value type in RefundsRequest', 0, 'RefundsRequest.gatewayId', $value
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (������������)
     */
    public function getCreatedGte()
    {
        return $this->_createdGte;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedGte()
    {
        return !empty($this->_createdGte);
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (������������) ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedGte($value)
    {
        if ($value === null || $value === '') {
            $this->_createdGte = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid created_gte value in RefundsRequest', 0, 'RefundsRequest.createdGte'
                );
            }
            $this->_createdGte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_gte value type in RefundsRequest', 0, 'RefundsRequest.createdGte'
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (�� �������)
     */
    public function getCreatedGt()
    {
        return $this->_createdGt;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedGt()
    {
        return !empty($this->_createdGt);
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (�� �������) ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedGt($value)
    {
        if ($value === null || $value === '') {
            $this->_createdGt = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid created_gt value in RefundsRequest', 0, 'RefundsRequest.createdGt'
                );
            }
            $this->_createdGt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_gt value type in RefundsRequest', 0, 'RefundsRequest.createdGt'
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (������������)
     */
    public function getCreatedLte()
    {
        return $this->_createdLte;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedLte()
    {
        return !empty($this->_createdLte);
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (������������) ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedLte($value)
    {
        if ($value === null || $value === '') {
            $this->_createdLte = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid created_lte value in RefundsRequest', 0, 'RefundsRequest.createdLte'
                );
            }
            $this->_createdLte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_lte value type in RefundsRequest', 0, 'RefundsRequest.createdLte'
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (�� �������)
     */
    public function getCreatedLt()
    {
        return $this->_createdLt;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedLt()
    {
        return !empty($this->_createdLt);
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (�� �������) ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedLt($value)
    {
        if ($value === null || $value === '') {
            $this->_createdLt = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid created_lt value in RefundsRequest', 0, 'RefundsRequest.createdLt'
                );
            }
            $this->_createdLt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_lt value type in RefundsRequest', 0, 'RefundsRequest.createdLt'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ���������� ��������, �� (������������)
     */
    public function getAuthorizedGte()
    {
        return $this->_authorizedGte;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedGte()
    {
        return !empty($this->_authorizedGte);
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ���������� ��������, �� (�� �������) ��� null ����� �������
     * ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedGte($value)
    {
        if ($value === null || $value === '') {
            $this->_authorizedGte = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid authorized_gte value in RefundsRequest', 0, 'RefundsRequest.authorizedGte'
                );
            }
            $this->_authorizedGte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_gte value type in RefundsRequest', 0, 'RefundsRequest.authorizedGte'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ���������� ��������, �� (�� �������)
     */
    public function getAuthorizedGt()
    {
        return $this->_authorizedGt;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedGt()
    {
        return !empty($this->_authorizedGt);
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ���������� ��������, �� (�� �������) ��� null ����� �������
     * ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedGt($value)
    {
        if ($value === null || $value === '') {
            $this->_authorizedGt = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid authorized_gt value in RefundsRequest', 0, 'RefundsRequest.authorizedGt'
                );
            }
            $this->_authorizedGt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_gt value type in RefundsRequest', 0, 'RefundsRequest.authorizedGt'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� �������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ����������, �� (������������)
     */
    public function getAuthorizedLte()
    {
        return $this->_authorizedLte;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedLte()
    {
        return !empty($this->_authorizedLte);
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ����������, �� (������������) ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedLte($value)
    {
        if ($value === null || $value === '') {
            $this->_authorizedLte = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid authorized_lte value in RefundsRequest', 0, 'RefundsRequest.authorizedLte'
                );
            }
            $this->_authorizedLte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_lte value type in RefundsRequest', 0, 'RefundsRequest.authorizedLte'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� �������� ��� null ���� ��� �� ���� �����������
     * @return \DateTime|null ����� ����������, �� (�� �������)
     */
    public function getAuthorizedLt()
    {
        return $this->_authorizedLt;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� ��������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedLt()
    {
        return !empty($this->_authorizedLt);
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ����������, �� (�� �������) ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedLt($value)
    {
        if ($value === null || $value === '') {
            $this->_authorizedLt = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException(
                    'Invalid authorized_lt value in RefundsRequest', 0, 'RefundsRequest.authorizedLt'
                );
            }
            $this->_authorizedLt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_lt value type in RefundsRequest', 0, 'RefundsRequest.authorizedLt'
            );
        }
    }

    /**
     * ���������� ������ ���������� ��������� ��� null ���� �� �� ����� �� ��� ����������
     * @return string|null ������ ���������� ���������
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * ��������� ��� �� ���������� ������ ���������� ���������
     * @return bool True ���� ������ ��� ����������, false ���� ���
     */
    public function hasStatus()
    {
        return !empty($this->_status);
    }

    /**
     * ������������� ������ ���������� ���������
     * @param string $value ������ ���������� �������� ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� �� �������� �������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setStatus($value)
    {
        if ($value === null || $value === '') {
            $this->_status = null;
        } elseif (TypeCast::canCastToEnumString($value)) {
            if (!RefundStatus::valueExists((string)$value)) {
                throw new InvalidPropertyValueException(
                    'Invalid status value in RefundsRequest', 0, 'RefundsRequest.status', $value
                );
            } else {
                $this->_status = (string)$value;
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid status value in RefundsRequest', 0, 'RefundsRequest.status', $value
            );
        }
    }

    /**
     * ���������� ����� ��� ��������� ��������� �������� �������
     * @return string|null ����� ��� ��������� ��������� �������� �������
     */
    public function getNextPage()
    {
        return $this->_nextPage;
    }

    /**
     * ��������� ��� �� ���������� ����� ��������� ��������
     * @return bool True ���� ����� ��� ����������, false ���� ���
     */
    public function hasNextPage()
    {
        return !empty($this->_nextPage);
    }

    /**
     * ������������� ����� ��������� �������� �������
     * @param string $value ����� ��������� �������� ������� ��� null ����� ������� ��������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setNextPage($value)
    {
        if ($value === null || $value === '') {
            $this->_nextPage = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_nextPage = (string) $value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid status value in PaymentsRequest', 0, 'PaymentsRequest.status', $value
            );
        }
    }

    /**
     * ��������� ���������� �������� ������� �������
     * @return bool True ���� ������ �������, false ���� ���
     */
    public function validate()
    {
        if (empty($this->_accountId)) {
            $this->setValidationError('Shop id not specified');
            return false;
        }
        return true;
    }

    /**
     * ���������� ������� ������� �������� �������� ������ ��������� ��������
     * @return RefundsRequestBuilder ������ �������� �������� ������ ���������
     */
    public static function builder()
    {
        return new RefundsRequestBuilder();
    }
}
