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
use YandexCheckout\Common\Exceptions\EmptyPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\MonetaryAmount;
use YandexCheckout\Model\ReceiptInterface;

/**
 * ����� ������� ������� ��� �������� ��������
 *
 * @property string $paymentId ���� ������� ��� �������� �������� �������
 * @property AmountInterface $amount ����� ��������
 * @property string $comment ����������� � �������� ��������, ��������� ��� �������� ������� ����������.
 * @property ReceiptInterface|null $receipt ������� ���� ��� null
 */
class CreateRefundRequest extends AbstractRequest implements CreateRefundRequestInterface
{
    /**
     * @var string ���� ������� ��� �������� �������� �������
     */
    private $_paymentId;

    /**
     * @var MonetaryAmount ����� ��������
     */
    private $_amount;

    /**
     * @var string ����������� � �������� ��������, ��������� ��� �������� ������� ����������.
     */
    private $_comment;

    /**
     * @var ReceiptInterface|null ��� ��� ������ ���������� � ��������
     */
    private $_receipt;

    /**
     * ���������� ���� ������� ��� �������� �������� ������� �������
     * @return string ���� ������� ��� �������� �������� �������
     */
    public function getPaymentId()
    {
        return $this->_paymentId;
    }

    /**
     * ������������� ���� ������� ��� �������� �������� �������
     * @param string $value ���� �������
     *
     * @throws EmptyPropertyValueException ������������� ���� �������� ������ �������� ���� �������
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� �������� �������, �� �� ��������
     * �������� ��������� ���� �������
     * @throws InvalidPropertyValueTypeException ������������� ���� �������� �������� �� ��������� ����
     */
    public function setPaymentId($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException(
                'Empty payment id value in CreateRefundRequest', 0, 'CreateRefundRequest.paymentId'
            );
        } elseif (TypeCast::canCastToString($value)) {
            $length = mb_strlen($value, 'utf-8');
            if ($length != 36) {
                throw new InvalidPropertyValueException(
                    'Invalid payment id value in CreateRefundRequest', 0, 'CreateRefundRequest.paymentId', $value
                );
            }
            $this->_paymentId = (string)$value;
        } else {
            throw new InvalidPropertyValueException(
                'Invalid payment id value type in CreateRefundRequest', 0, 'CreateRefundRequest.paymentId', $value
            );
        }
    }

    /**
     * ���������� ����� ������������ �������
     * @return AmountInterface ����� ��������
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * ������������� ����� ������������ �������
     * @param AmountInterface $value ����� ��������
     */
    public function setAmount(AmountInterface $value)
    {
        $this->_amount = $value;
    }

    /**
     * ���������� ����������� � �������� ��� null, ���� ����������� �� �����
     * @return string ����������� � �������� ��������, ��������� ��� �������� ������� ����������.
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * ��������� ����� �� ����������� � ������������ ��������
     * @return bool True ���� ����������� ����������, false ���� ���
     */
    public function hasComment()
    {
        return $this->_comment !== null;
    }

    /**
     * ������������� ����������� � ��������
     * @param string $value ����������� � �������� ��������, ��������� ��� �������� ������� ����������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� ������ ������ 250 ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �� ������
     */
    public function setComment($value)
    {
        if ($value === null || $value === '') {
            $this->_comment = null;
        } elseif (TypeCast::canCastToString($value)) {
            $length = mb_strlen($value, 'utf-8');
            if ($length > 250) {
                throw new InvalidPropertyValueException(
                    'Invalid commend value in CreateRefundRequest', 0, 'CreateRefundRequest.comment', $value
                );
            }
            $this->_comment = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid commend value type in CreateRefundRequest', 0, 'CreateRefundRequest.comment', $value
            );
        }
    }

    /**
     * ���������� ������� ���� ��� null ���� ��� �� �����
     * @return ReceiptInterface|null ������� ���� ��� null
     */
    public function getReceipt()
    {
        return $this->_receipt;
    }

    /**
     * ��������� ����� �� ���
     * @return bool True ���� ��� ����, false ���� ���
     */
    public function hasReceipt()
    {
        return $this->_receipt !== null && $this->_receipt->notEmpty();
    }

    /**
     * ������������� ��� ��� ������
     * @param ReceiptInterface|null $value ������� ���� ��� null ��� �������� ���������� � ����
     * @throws InvalidPropertyValueTypeException ������������� ���� ������� �� ������� ������ ���� � �� null
     */
    public function setReceipt($value)
    {
        if ($value === null || $value instanceof ReceiptInterface) {
            $this->_receipt = $value;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid receipt in Refund', 0, 'Refund.receipt', $value);
        }
    }

    /**
     * ���������� ������� ������ �������
     * @return bool True ���� ������� ������ ������� �������, false ���� ���
     */
    public function validate()
    {
        if (empty($this->_paymentId)) {
            $this->setValidationError('Payment id not specified');
            return false;
        }
        if (empty($this->_amount)) {
            $this->setValidationError('Amount not specified');
            return false;
        }
        if ($this->_amount->getValue() <= 0.0) {
            $this->setValidationError('Invalid amount value: ' . $this->_amount->getValue());
            return false;
        }
        if ($this->_receipt !== null && $this->_receipt->notEmpty()) {
            $email = $this->_receipt->getEmail();
            $phone = $this->_receipt->getPhone();
            if (empty($email) && empty($phone)) {
                $this->setValidationError('Both email and phone values are empty in receipt');
                return false;
            }
            if ($this->_receipt->getTaxSystemCode() === null) {
                foreach ($this->_receipt->getItems() as $item) {
                    if ($item->getVatCode() === null) {
                        $this->setValidationError('Item vat_id and receipt tax_system_id not specified');
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * ���������� ������ �������� �������� ����
     * @return CreateRefundRequestBuilder ������� ������� �������
     */
    public static function builder()
    {
        return new CreateRefundRequestBuilder();
    }
}
