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
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;

/**
 * ����� ������� � ����������� � �������� �������
 *
 * @property string $id ������������� �������� �������
 * @property string $paymentId ������������� �������
 * @property string $payment_id ������������� �������
 * @property string $status ������ ��������
 * @property \DateTime $createdAt ����� �������� ��������
 * @property \DateTime $created_at ����� �������� ��������
 * @property AmountInterface $amount ����� ��������
 * @property string $receiptRegistration ������ ����������� ����
 * @property string $receipt_registration ������ ����������� ����
 * @property string $comment �����������, ��������� ��� �������� ������� ����������
 */
class Refund extends AbstractObject implements RefundInterface
{
    /**
     * @var string ������������� �������� �������
     */
    private $_id;

    /**
     * @var string ������������� �������
     */
    private $_paymentId;

    /**
     * @var string ������ ��������
     */
    private $_status;

    /**
     * @var \DateTime ����� �������� ��������
     */
    private $_createdAt;

    /**
     * @var MonetaryAmount ����� ��������
     */
    private $_amount;

    /**
     * @var string ������ ����������� ����
     */
    private $_receiptRegistration;

    /**
     * @var string �����������, ��������� ��� �������� ������� ����������
     */
    private $_comment;

    /**
     * ���������� ������������� �������� �������
     * @return string ������������� ��������
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * ������������� ������������� ��������
     * @param string $value ������������� ��������
     *
     * @throws EmptyPropertyValueException ������������� ���� ��� ������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ���� �������� ���������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� �������� �� �������� �������
     */
    public function setId($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty refund id', 0, 'Refund.id');
        } elseif (TypeCast::canCastToString($value)) {
            $castedValue = (string)$value;
            $length = mb_strlen($castedValue, 'utf-8');
            if ($length === 36) {
                $this->_id = $castedValue;
            } else {
                throw new InvalidPropertyValueException('Invalid refund id value', 0, 'Refund.id', $value);
            }
        } else {
            throw new InvalidPropertyValueTypeException('Invalid refund id value type', 0, 'Refund.id', $value);
        }
    }

    /**
     * ���������� ������������� �������
     * @return string ������������� �������
     */
    public function getPaymentId()
    {
        return $this->_paymentId;
    }

    /**
     * ������������� ������������� �������
     * @param string $value ������������� �������
     *
     * @throws EmptyPropertyValueException ������������� ���� ��� ������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ���� �������� ���������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� �������� �� �������� �������
     */
    public function setPaymentId($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty refund paymentId', 0, 'Refund.paymentId');
        } elseif (TypeCast::canCastToString($value)) {
            $castedValue = (string)$value;
            $length = mb_strlen($castedValue, 'utf-8');
            if ($length === 36) {
                $this->_paymentId = $castedValue;
            } else {
                throw new InvalidPropertyValueException(
                    'Invalid refund paymentId value', 0, 'Refund.paymentId', $value
                );
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid refund paymentId value type', 0, 'Refund.paymentId', $value
            );
        }
    }

    /**
     * ���������� ������ �������� ��������
     * @return string ������ ��������
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * ������������� ������ �������� �������
     * @param string $value ������ �������� �������
     *
     * @throws EmptyPropertyValueException ������������� ���� ��� ������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ���� �������� ���������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� �������� �� �������� �������
     */
    public function setStatus($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty refund status', 0, 'Refund.status');
        } elseif (TypeCast::canCastToEnumString($value)) {
            $castedValue = (string)$value;
            if (RefundStatus::valueExists($castedValue)) {
                $this->_status = $castedValue;
            } else {
                throw new InvalidPropertyValueException(
                    'Invalid refund status value', 0, 'Refund.status', $value
                );
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid refund status value type', 0, 'Refund.status', $value
            );
        }
    }

    /**
     * ���������� ���� �������� ��������
     * @return \DateTime ����� �������� ��������
     */
    public function getCreatedAt()
    {
        return $this->_createdAt;
    }

    /**
     * ������������� ����� �������� ��������
     * @param \DateTime $value ����� �������� ��������
     *
     * @throws EmptyPropertyValueException ������������� ���� ��� �������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ���������� ������ ��� ����� �� ������� ����������������
     * ��� ���� � �����
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �������� ����������� ����
     */
    public function setCreatedAt($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty refund created_at value', 0, 'Refund.createdAt');
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException('Invalid created_at value', 0, 'Refund.createdAt', $value);
            }
            $this->_createdAt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid created_at value', 0, 'Refund.createdAt', $value);
        }
    }

    /**
     * ���������� ����� ��������
     * @return AmountInterface ����� ��������
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * ������������� ����� ��������
     * @param AmountInterface $value ����� ��������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� ����� ������ ��� ����� ����
     */
    public function setAmount(AmountInterface $value)
    {
        if ($value->getIntegerValue() <= 0) {
            throw new InvalidPropertyValueException('Invalid refund amount', 0, 'Refund.amount', $value->getValue());
        }
        $this->_amount = $value;
    }

    /**
     * ���������� ������ ����������� ����
     * @return string ������ ����������� ����
     */
    public function getReceiptRegistration()
    {
        return $this->_receiptRegistration;
    }

    /**
     * ������������� ������ ����������� ����
     * @param string $value ������ ����������� ����
     *
     * @throws EmptyPropertyValueException ������������� ���� ��� ������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ���� �������� ���������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� �������� �� �������� �������
     */
    public function setReceiptRegistration($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty refund receiptRegistration', 0, 'Refund.receiptRegistration');
        } elseif (TypeCast::canCastToEnumString($value)) {
            $castedValue = (string)$value;
            if (ReceiptRegistrationStatus::valueExists($castedValue)) {
                $this->_receiptRegistration = $castedValue;
            } else {
                throw new InvalidPropertyValueException(
                    'Invalid refund receiptRegistration value', 0, 'Refund.receiptRegistration', $value
                );
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid refund receiptRegistration value type', 0, 'Refund.receiptRegistration', $value
            );
        }
    }

    /**
     * ���������� ����������� � ��������
     * @return string �����������, ��������� ��� �������� ������� ����������
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * ������������� ����������� � ��������
     * @param string $value �����������, ��������� ��� �������� ������� ����������
     *
     * @throws EmptyPropertyValueException ������������� ���� ��� ������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ���� �������� ���������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� �������� �� �������� �������
     */
    public function setComment($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty refund comment', 0, 'Refund.comment');
        } elseif (TypeCast::canCastToEnumString($value)) {
            $length = mb_strlen((string)$value, 'utf-8');
            if ($length > 250) {
                throw new InvalidPropertyValueException('Empty refund comment', 0, 'Refund.comment', $value);
            }
            $this->_comment = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException('Empty refund comment', 0, 'Refund.comment', $value);
        }
    }
}
