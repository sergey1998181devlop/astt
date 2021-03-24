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

namespace YandexCheckout\Request\Payments;

use YandexCheckout\Common\AbstractRequest;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\Status;

/**
 * ����� ������� ������� � API ��� ��������� ������ �������� ��������
 *
 * @property string|null $paymentId ������������� �������
 * @property string|null $accountId ������������� ��������
 * @property string|null $gatewayId ������������� �����
 * @property \DateTime|null $createdGte ����� ��������, �� (������������)
 * @property \DateTime|null $createdGt ����� ��������, �� (�� �������)
 * @property \DateTime|null $createdLte ����� ��������, �� (������������)
 * @property \DateTime|null $createdLt ����� ��������, �� (�� �������)
 * @property \DateTime|null $authorizedGte ����� ���������� ��������, �� (������������)
 * @property \DateTime|null $authorizedGt ����� ���������� ��������, �� (�� �������)
 * @property \DateTime|null $authorizedLte ����� ����������, �� (������������)
 * @property \DateTime|null $authorizedLt ����� ����������, �� (�� �������)
 * @property string|null $status ������ �������
 * @property string|null $nextPage ����� ��� ��������� ��������� �������� �������
 */
class PaymentsRequest extends AbstractRequest implements PaymentsRequestInterface
{
    /**
     * @var string ������������� �������
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
     * @var string ������ �������
     */
    private $_status;

    /**
     * @var string ����� ��� ��������� ��������� �������� �������
     */
    private $_nextPage;

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
        return $this->_paymentId !== null;
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
                    'Invalid payment_id value length in PaymentsRequest (' . $length . ' != 36)',
                    0, 'PaymentsRequest.paymentId', $value
                );
            }
            $this->_paymentId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid payment_id value type in PaymentsRequest', 0, 'PaymentsRequest.paymentId', $value
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
        return $this->_accountId !== null;
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
                'Invalid accountId value type in PaymentsRequest', 0, 'PaymentsRequest.accountId', $value
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
        return $this->_gatewayId !== null;
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
                'Invalid gatewayId value type in PaymentsRequest', 0, 'PaymentsRequest.gatewayId', $value
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (������������)
     */
    public function getCreatedGte()
    {
        return $this->_createdGte;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedGte()
    {
        return $this->_createdGte !== null;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� �������
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
                    'Invalid created_gte value in PaymentsRequest', 0, 'PaymentRequest.createdGte'
                );
            }
            $this->_createdGte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_gte value type in PaymentsRequest', 0, 'PaymentRequest.createdGte'
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (�� �������)
     */
    public function getCreatedGt()
    {
        return $this->_createdGt;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedGt()
    {
        return $this->_createdGt !== null;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� �������
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
                    'Invalid created_gt value in PaymentsRequest', 0, 'PaymentRequest.createdGt'
                );
            }
            $this->_createdGt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_gt value type in PaymentsRequest', 0, 'PaymentRequest.createdGt'
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (������������)
     */
    public function getCreatedLte()
    {
        return $this->_createdLte;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedLte()
    {
        return $this->_createdLte !== null;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� �������
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
                    'Invalid created_lte value in PaymentsRequest', 0, 'PaymentRequest.createdLte'
                );
            }
            $this->_createdLte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_lte value type in PaymentsRequest', 0, 'PaymentRequest.createdLte'
            );
        }
    }

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (�� �������)
     */
    public function getCreatedLt()
    {
        return $this->_createdLt;
    }

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasCreatedLt()
    {
        return $this->_createdLt !== null;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� �������
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
                    'Invalid created_lt value in PaymentsRequest', 0, 'PaymentRequest.createdLt'
                );
            }
            $this->_createdLt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid created_lt value type in PaymentsRequest', 0, 'PaymentRequest.createdLt'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ���������� ��������, �� (������������)
     */
    public function getAuthorizedGte()
    {
        return $this->_authorizedGte;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedGte()
    {
        return $this->_authorizedGte !== null;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� �������
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
                    'Invalid authorized_gte value in PaymentsRequest', 0, 'PaymentRequest.authorizedGte'
                );
            }
            $this->_authorizedGte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_gte value type in PaymentsRequest', 0, 'PaymentRequest.authorizedGte'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ���������� ��������, �� (�� �������)
     */
    public function getAuthorizedGt()
    {
        return $this->_authorizedGt;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedGt()
    {
        return $this->_authorizedGt !== null;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� �������
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
                    'Invalid authorized_gt value in PaymentsRequest', 0, 'PaymentRequest.authorizedGt'
                );
            }
            $this->_authorizedGt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_gt value type in PaymentsRequest', 0, 'PaymentRequest.authorizedGt'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ����������, �� (������������)
     */
    public function getAuthorizedLte()
    {
        return $this->_authorizedLte;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedLte()
    {
        return $this->_authorizedLte !== null;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� �������
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
                    'Invalid authorized_lte value in PaymentsRequest', 0, 'PaymentRequest.authorizedLte'
                );
            }
            $this->_authorizedLte = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_lte value type in PaymentsRequest', 0, 'PaymentRequest.authorizedLte'
            );
        }
    }

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ������� ��� null ���� ��� �� ���� �����������
     * @return \DateTime|null ����� ����������, �� (�� �������)
     */
    public function getAuthorizedLt()
    {
        return $this->_authorizedLt;
    }

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    public function hasAuthorizedLt()
    {
        return $this->_authorizedLt !== null;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� �������
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
                    'Invalid authorized_lt value in PaymentsRequest', 0, 'PaymentRequest.authorizedLt'
                );
            }
            $this->_authorizedLt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid authorized_lt value type in PaymentsRequest', 0, 'PaymentRequest.authorizedLt'
            );
        }
    }

    /**
     * ���������� ������ ���������� �������� ��� null ���� �� �� ����� �� ��� ����������
     * @return string|null ������ ���������� ��������
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * ��������� ��� �� ���������� ������ ���������� ��������
     * @return bool True ���� ������ ��� ����������, false ���� ���
     */
    public function hasStatus()
    {
        return $this->_status !== null;
    }

    /**
     * ������������� ������ ���������� ��������
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
            if (!Status::valueExists((string)$value)) {
                throw new InvalidPropertyValueException(
                    'Invalid status value in PaymentsRequest', 0, 'PaymentsRequest.status', $value
                );
            } else {
                $this->_status = (string)$value;
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid status value in PaymentsRequest', 0, 'PaymentsRequest.status', $value
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
        return $this->_nextPage !== null;
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
     * ���������� ������� ������� �������� �������� ������ �������� ��������
     * @return PaymentsRequestBuilder ������ �������� �������� ������ ��������
     */
    public static function builder()
    {
        return new PaymentsRequestBuilder();
    }
}
