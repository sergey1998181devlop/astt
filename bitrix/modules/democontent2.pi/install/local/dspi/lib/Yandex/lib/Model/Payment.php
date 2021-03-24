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
use YandexCheckout\Model\PaymentMethod\AbstractPaymentMethod;

/**
 * Payment - ������ � �������
 *
 * @property string $id ������������� �������
 * @property string $status ������� ��������� �������
 * @property RecipientInterface $recipient  ���������� �������
 * @property AmountInterface $amount ����� ������
 * @property AbstractPaymentMethod $paymentMethod ������ ���������� �������
 * @property AbstractPaymentMethod $payment_method ������ ���������� �������
 * @property \DateTime $createdAt ����� �������� ������
 * @property \DateTime $created_at ����� �������� ������
 * @property \DateTime $capturedAt ����� ������������� ������� ���������
 * @property \DateTime $captured_at ����� ������������� ������� ���������
 * @property \DateTime $expiresAt �����, �� �������� ����� ��������� �������� ��� ����������� ������
 * @property \DateTime $expires_at �����, �� �������� ����� ��������� �������� ��� ����������� ������
 * @property Confirmation\AbstractConfirmation $confirmation ������ ������������� �������
 * @property AmountInterface $refundedAmount ����� ������������ ������� �������
 * @property AmountInterface $refunded_amount ����� ������������ ������� �������
 * @property bool $paid ������� ������ ������
 * @property string $receiptRegistration ��������� ����������� ����������� ����
 * @property string $receipt_registration ��������� ����������� ����������� ����
 * @property Metadata $metadata ���������� ������� ��������� ���������
 */
class Payment extends AbstractObject implements PaymentInterface
{
    /**
     * @var string ������������� �������
     */
    private $_id;

    /**
     * @var string ������� ��������� �������
     */
    private $_status;

    /**
     * @var RecipientInterface|null ���������� �������
     */
    private $_recipient;

    /**
     * @var AmountInterface
     */
    private $_amount;

    /**
     * @var AbstractPaymentMethod ������ ���������� �������
     */
    private $_paymentMethod;

    /**
     * @var \DateTime ����� �������� ������
     */
    private $_createdAt;

    /**
     * @var \DateTime ����� ������������� ������� ���������
     */
    private $_capturedAt;

    /**
     * @var Confirmation\AbstractConfirmation ������ ������������� �������
     */
    private $_confirmation;

    /**
     * @var AmountInterface ����� ������������ ������� �������
     */
    private $_refundedAmount;

    /**
     * @var bool ������� ������ ������
     */
    private $_paid;

    /**
     * @var string ��������� ����������� ����������� ����
     */
    private $_receiptRegistration;

    /**
     * @var Metadata ���������� ������� ��������� ���������
     */
    private $_metadata;

    /**
     * �����, �� �������� ����� ��������� �������� ��� ����������� ������. � ��������� ����� ������ � �������
     * `waiting_for_capture` ����� ������������� �������.
     *
     * @var \DateTime �����, �� �������� ����� ��������� �������� ��� ����������� ������
     * @since 1.0.2
     */
    private $_expiresAt;

    /**
     * ���������� ������������� �������
     * @return string ������������� �������
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * ������������� ������������� �������
     * @param string $value ������������� �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ����� ���������� ������ �� ����� 36
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setId($value)
    {
        if (TypeCast::canCastToString($value)) {
            $length = mb_strlen($value, 'utf-8');
            if ($length != 36) {
                throw new InvalidPropertyValueException('Invalid payment id value', 0, 'Payment.id', $value);
            }
            $this->_id = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid payment id value type', 0, 'Payment.id', $value);
        }
    }

    /**
     * ���������� ��������� �������
     * @return string ������� ��������� �������
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * ������������� ������ �������
     * @param string $value ������ �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� ������ �� �������� �������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setStatus($value)
    {
        if (TypeCast::canCastToEnumString($value)) {
            if (!PaymentStatus::valueExists((string)$value)) {
                throw new InvalidPropertyValueException('Invalid payment status value', 0, 'Payment.status', $value);
            }
            $this->_status = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid payment status value type', 0, 'Payment.status', $value
            );
        }
    }

    /**
     * ���������� ���������� �������
     * @return RecipientInterface|null ���������� ������� ��� null ���� ���������� �� �����
     */
    public function getRecipient()
    {
        return $this->_recipient;
    }

    /**
     * ������������� ���������� �������
     * @param RecipientInterface $value ������ � ����������� � ���������� �������
     */
    public function setRecipient(RecipientInterface $value)
    {
        $this->_recipient = $value;
    }

    /**
     * ���������� �����
     * @return AmountInterface ����� �������
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * ������������� ����� �������
     * @param AmountInterface $value ����� �������
     */
    public function setAmount(AmountInterface $value)
    {
        $this->_amount = $value;
    }

    /**
     * ���������� ������������ ������ ���������� �������
     * @return AbstractPaymentMethod ������ ���������� �������
     */
    public function getPaymentMethod()
    {
        return $this->_paymentMethod;
    }

    /**
     * @param AbstractPaymentMethod $value
     */
    public function setPaymentMethod(AbstractPaymentMethod $value)
    {
        $this->_paymentMethod = $value;
    }

    /**
     * ���������� ����� �������� ������
     * @return \DateTime ����� �������� ������
     */
    public function getCreatedAt()
    {
        return $this->_createdAt;
    }

    /**
     * ������������� ����� �������� ������
     * @param \DateTime|string|int $value ����� �������� ������
     *
     * @throws EmptyPropertyValueException ������������� ���� � ����� ���� �������� ������ ����
     * @throws InvalidPropertyValueException ������������ ���� �������� ������, ������� �� ������� �������� � ����
     * @throws InvalidPropertyValueTypeException ������������� ���� ��� ������� ��������, ������� ����������
     * ���������������� ��� ���� ��� �����
     */
    public function setCreatedAt($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty created_at value', 0, 'payment.createdAt');
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException('Invalid created_at value', 0, 'payment.createdAt', $value);
            }
            $this->_createdAt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid created_at value', 0, 'payment.createdAt', $value);
        }
    }

    /**
     * ���������� ����� ������������� ������� ��������� ��� null ���� ���� ����� �� ������
     * @return \DateTime|null ����� ������������� ������� ���������
     */
    public function getCapturedAt()
    {
        return $this->_capturedAt;
    }

    /**
     * ������������� ����� ������������� ������� ���������
     * @param \DateTime|string|int|null $value ����� ������������� ������� ���������
     *
     * @throws InvalidPropertyValueException ������������ ���� �������� ������, ������� �� ������� �������� � ����
     * @throws InvalidPropertyValueTypeException ������������� ���� ��� ������� ��������, ������� ����������
     * ���������������� ��� ���� ��� �����
     */
    public function setCapturedAt($value)
    {
        if ($value === null || $value === '') {
            $this->_capturedAt = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException('Invalid captured_at value', 0, 'payment.capturedAt', $value);
            }
            $this->_capturedAt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid captured_at value', 0, 'payment.capturedAt', $value);
        }
    }

    /**
     * ���������� ������ ������������� �������
     * @return Confirmation\AbstractConfirmation ������ ������������� �������
     */
    public function getConfirmation()
    {
        return $this->_confirmation;
    }

    /**
     * ������������� ������ ������������� �������
     * @param Confirmation\AbstractConfirmation $value ������ ������������� �������
     */
    public function setConfirmation(Confirmation\AbstractConfirmation $value)
    {
        $this->_confirmation = $value;
    }

    /**
     * ���������� ����� ������������ �������
     * @return AmountInterface ����� ������������ ������� �������
     */
    public function getRefundedAmount()
    {
        return $this->_refundedAmount;
    }

    /**
     * ������������� ����� ������������ �������
     * @param AmountInterface $value ����� ������������ ������� �������
     */
    public function setRefundedAmount(AmountInterface $value)
    {
        $this->_refundedAmount = $value;
    }

    /**
     * ��������� ��� �� ��� ������� �����
     * @return bool ������� ������ ������, true ���� ����� �������, false ���� ���
     */
    public function getPaid()
    {
        return $this->_paid;
    }

    /**
     * ������������� ���� ������ ������
     * @param bool $value ������� ������ ������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���������� �������� ����
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� � ������ ��������
     */
    public function setPaid($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty payment paid flag value', 0, 'Payment.paid');
        } elseif (TypeCast::canCastToBoolean($value)) {
            $this->_paid = (bool)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid payment paid flag value type', 0, 'Payment.paid', $value
            );
        }
    }

    /**
     * ���������� ��������� ����������� ����������� ����
     * @return string ��������� ����������� ����������� ����
     */
    public function getReceiptRegistration()
    {
        return $this->_receiptRegistration;
    }

    /**
     * ������������� ��������� ����������� ����������� ����
     * @param string $value ��������� ����������� ����������� ����
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� ��������� ����������� �� ����������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� ������
     */
    public function setReceiptRegistration($value)
    {
        if ($value === null || $value === '') {
            $this->_receiptRegistration = null;
        } elseif (TypeCast::canCastToEnumString($value)) {
            if (ReceiptRegistrationStatus::valueExists($value)) {
                $this->_receiptRegistration = (string)$value;
            } else {
                throw new InvalidPropertyValueException(
                    'Invalid receipt_registration value', 0, 'payment.receiptRegistration', $value
                );
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid receipt_registration value type', 0, 'payment.receiptRegistration', $value
            );
        }
    }

    /**
     * ���������� ���������� ������� ������������� ���������
     * @return Metadata ���������� ������� ��������� ���������
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * ������������� ���������� �������
     * @param Metadata $value ���������� ������� ��������� ���������
     */
    public function setMetadata(Metadata $value)
    {
        $this->_metadata = $value;
    }

    /**
     * ���������� ����� �� �������� ����� ��������� �������� ��� ����������� ������ ��� null ���� ��� �� ������
     * @return \DateTime|null �����, �� �������� ����� ��������� �������� ��� ����������� ������
     *
     * @since 1.0.2
     */
    public function getExpiresAt()
    {
        return $this->_expiresAt;
    }

    /**
     * ������������� ����� �� �������� ����� ��������� �������� ��� ����������� ������
     * @param \DateTime|string|int|null $value �����, �� �������� ����� ��������� �������� ��� ����������� ������
     *
     * @throws InvalidPropertyValueException ������������� ���� �������� ������, ������� �� ������� �������� � ����
     * @throws InvalidPropertyValueTypeException ������������� ���� ��� ������� ��������, ������� ����������
     * ���������������� ��� ���� ��� �����
     *
     * @since 1.0.2
     */
    public function setExpiresAt($value)
    {
        if ($value === null || $value === '') {
            $this->_expiresAt = null;
        } elseif (TypeCast::canCastToDateTime($value)) {
            $dateTime = TypeCast::castToDateTime($value);
            if ($dateTime === null) {
                throw new InvalidPropertyValueException('Invalid expires_at value', 0, 'payment.expires_at', $value);
            }
            $this->_expiresAt = $dateTime;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid expires_at value', 0, 'payment.expires_at', $value);
        }
    }
}
