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
 * ���������� � �������� ������� � ������, ������� ����������� ����
 *
 * @property string $description ������������ ������
 * @property int $quantity ����������
 * @property-read int $amount ��������� ��������� ����������� ������ � ��������/������
 * @property AmountInterface $price ���� ������
 * @property int $vatCode ������ ���, ����� 1-6
 * @property int $vat_code ������ ���, ����� 1-6
 * @property-write bool $isShipping ���� ��������
 */
class ReceiptItem extends AbstractObject implements ReceiptItemInterface
{
    /**
     * @var string ������������ ������
     */
    private $_description;

    /**
     * @var int ����������
     */
    private $_quantity;

    /**
     * @var MonetaryAmount ���� ������
     */
    private $_amount;

    /**
     * @var int ������ ���, ����� 1-6
     */
    private $_vatCode;

    /**
     * @var bool True ���� ������� ����� ��������, false ���� ���
     */
    private $_shipping = false;

    /**
     * ���������� ������������ ������
     * @return string ������������ ������
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * ������������� ������������ ������
     * @param string $value ������������ ������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� ��������� ���� �������� �� ������
     */
    public function setDescription($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException(
                'Empty description value in ReceiptItem', 0, 'ReceiptItem.description'
            );
        } elseif (TypeCast::canCastToString($value)) {
            $castedValue = (string)$value;
            if ($castedValue === '') {
                throw new EmptyPropertyValueException(
                    'Empty description value in ReceiptItem', 0, 'ReceiptItem.description'
                );
            }
            $this->_description = $castedValue;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Empty description value in ReceiptItem', 0, 'ReceiptItem.description', $value
            );
        }
    }

    /**
     * ���������� ���������� ������
     * @return float ���������� ���������� ������
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * ������������� ���������� ����������� ������
     * @param int $value ����������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� � �������� ��������� ��� ������� ����
     * ��� ������������� �����
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� ��������� ���� �������� �� �����
     */
    public function setQuantity($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty quantity value in ReceiptItem', 0, 'ReceiptItem.quantity');
        } elseif (!is_numeric($value)) {
            throw new InvalidPropertyValueTypeException(
                'Invalid quantity value type in ReceiptItem', 0, 'ReceiptItem.quantity', $value
            );
        } elseif ($value <= 0.0) {
            throw new InvalidPropertyValueException(
                'Invalid quantity value in ReceiptItem', 0, 'ReceiptItem.quantity', $value
            );
        } else {
            $this->_quantity = (float)$value;
        }
    }

    /**
     * ���������� ����� ��������� ����������� ������ � ��������/������
     * @return int ����� ��������� ����������� ������
     */
    public function getAmount()
    {
        return (int)round($this->_amount->getIntegerValue() * $this->_quantity);
    }

    /**
     * ���������� ���� ������
     * @return AmountInterface ���� ������
     */
    public function getPrice()
    {
        return $this->_amount;
    }

    /**
     * ������������� ���� ������
     * @param AmountInterface $value ���� ������
     */
    public function setPrice(AmountInterface $value)
    {
        $this->_amount = $value;
    }

    /**
     * ���������� ������ ���
     * @return int|null ������ ���, ����� 1-6, ��� null ���� ������ �� ������
     */
    public function getVatCode()
    {
        return $this->_vatCode;
    }

    /**
     * ������������� ������ ���
     * @param int $value ������ ���, ����� 1-6
     *
     * @throws InvalidPropertyValueException ������������� ���� � �������� ��������� ���� �������� ����� ������ ������
     * ��� ������ �����
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� ��������� ���� �������� �� �����
     */
    public function setVatCode($value)
    {
        if ($value === null || $value === '') {
            $this->_vatCode = null;
        } elseif (!is_numeric($value)) {
            throw new InvalidPropertyValueTypeException(
                'Invalid vatId value type in ReceiptItem', 0, 'ReceiptItem.vatId', $value
            );
        } elseif ($value < 1 || $value > 6) {
            throw new InvalidPropertyValueException(
                'Invalid vatId value in ReceiptItem', 0, 'ReceiptItem.vatId', $value
            );
        } else {
            $this->_vatCode = (int)$value;
        }
    }

    /**
     * ������������� ���� �������� ��� �������� ������� ������ � ����
     * @param bool $value True ���� ����� �������� ���������, false ���� ���
     *
     * @throws InvalidPropertyValueException ������������ ���� �������� �������� ����������� ����
     */
    public function setIsShipping($value)
    {
        if ($value === null || $value === '') {
            $this->_shipping = false;
        } elseif (TypeCast::canCastToBoolean($value)) {
            $this->_shipping = $value ? true : false;
        } else {
            throw new InvalidPropertyValueException(
                'Invalid isShipping value in ReceiptItem', 0, 'ReceiptItem.isShipping', $value
            );
        }
    }

    /**
     * ���������, �������� �� ������� ������� ���� ��������
     * @return bool True ���� ��������, false ���� ������� �����
     */
    public function isShipping()
    {
        return $this->_shipping;
    }

    /**
     * ��������� ��� ������ ������
     * @param float $coefficient ��������� ������
     */
    public function applyDiscountCoefficient($coefficient)
    {
        $this->_amount->multiply($coefficient);
    }

    /**
     * ����������� ���� ������ �� ��������� ��������
     * @param float $value ����� �� ������� ���� ������ �����������
     */
    public function increasePrice($value)
    {
        $this->_amount->increase($value);
    }

    /**
     * ��������� ���������� ����������� ������ �� ���������, ���������� ������ ������� � ���� � ����������� �����������
     * @param float $count ���������� �� ������� ��������� ������� � ����
     * @return ReceiptItem ����� ������� ������� � ����
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� � �������� ��������� ��� ������� ����
     * ��� ������������� �����, ��� ����� ������ �������� ���������� ����������� ������
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� ��������� ���� �������� �� �����
     */
    public function fetchItem($count)
    {
        if ($count === null || $count === '') {
            throw new EmptyPropertyValueException(
                'Empty quantity value in ReceiptItem in fetchItem method', 0, 'ReceiptItem.quantity'
            );
        } elseif (!is_numeric($count)) {
            throw new InvalidPropertyValueTypeException(
                'Invalid quantity value type in ReceiptItem in fetchItem method', 0, 'ReceiptItem.quantity', $count
            );
        } elseif ($count <= 0.0 || $count >= $this->_quantity) {
            throw new InvalidPropertyValueException(
                'Invalid quantity value in ReceiptItem in fetchItem method', 0, 'ReceiptItem.quantity', $count
            );
        }
        $result = new ReceiptItem();
        $result->_description = $this->_description;
        $result->_quantity = $count;
        $result->_vatCode = $this->_vatCode;
        $result->_amount = new MonetaryAmount(
            $this->_amount->getValue(),
            $this->_amount->getCurrency()
        );
        $this->_quantity -= $count;
        return $result;
    }
}
