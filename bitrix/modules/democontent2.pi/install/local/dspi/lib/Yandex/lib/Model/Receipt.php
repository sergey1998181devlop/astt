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
 * ����� ������ ��� ������������ ���� � ������-����� (��� ���������� 54-��)
 *
 * @property ReceiptItemInterface[] $items ������ ������� � ������
 * @property int $taxSystemCode ��� ������� ���������������. ����� 1-6.
 * @property int $tax_system_code ��� ������� ���������������. ����� 1-6.
 * @property string $phone ����� �������� ����������� � ������� ITU-T E.164 �� ������� ����� ������ ���.
 * @property string $email E-mail ����� ����������� �� ������� ����� ������ ���.
 */
class Receipt extends AbstractObject implements ReceiptInterface
{
    /**
     * @var ReceiptItem[] ������ ������� � ������
     */
    private $_items = array();

    /**
     * @var ReceiptItem[] ������ ������� � ������, ���������� ���������
     */
    private $_shippingItems = array();

    /**
     * @var int ��� ������� ���������������. ����� 1-6.
     */
    private $_taxSystemCode;

    /**
     * @var string ����� �������� ����������� � ������� ITU-T E.164 �� ������� ����� ������ ���.
     */
    private $_phone;

    /**
     * @var string E-mail ����� ����������� �� ������� ����� ������ ���.
     */
    private $_email;

    /**
     * ���������� ������ ������� � ������� ����
     *
     * @return ReceiptItemInterface[] ������ ������� � ������
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * ������������� ������ ������� � ����
     *
     * ���� �� ����� � ���� ��� ���� ����������� ��������, ��� ��������� � ��������� ���������� ���������� �������
     * �������. ��� ������������ �������� � ������� ������� ������ ���� ��������� ������, ������������ ���������
     * ReceiptItemInterface, � ��������� ������ ����� ��������� ���������� InvalidPropertyValueTypeException.
     *
     * @param ReceiptItemInterface[] $value ������ ������� � ������
     *
     * @throws EmptyPropertyValueException ������������� ���� �������� ������ ������ ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� �������� ��� ������� �� ������ � ��
     * ��������, ���� ���� ���� �� ���������� �������� �� ��������� ��������� ReceiptItemInterface
     */
    public function setItems($value)
    {
        if ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty items value in receipt', 0, 'receipt.items');
        }
        if (!is_array($value) && !($value instanceof \Traversable)) {
            throw new InvalidPropertyValueTypeException(
                'Invalid items value type in receipt', 0, 'receipt.items', $value
            );
        }
        $this->_items = array();
        $this->_shippingItems = array();
        foreach ($value as $key => $val) {
            if (is_object($val) && $val instanceof ReceiptItemInterface) {
                $this->addItem($val);
            } else {
                throw new InvalidPropertyValueTypeException(
                    'Invalid item value type in receipt', 0, 'receipt.items[' . $key . ']', $val
                );
            }
        }
    }

    /**
     * ��������� ����� � ���
     *
     * @param ReceiptItemInterface $value ������ ����������� � ��� �������
     */
    public function addItem(ReceiptItemInterface $value)
    {
        $this->_items[] = $value;
        if ($value->isShipping()) {
            $this->_shippingItems[] = $value;
        }
    }

    /**
     * ���������� ��� ������� ���������������
     *
     * @return int ��� ������� ���������������. ����� 1-6.
     */
    public function getTaxSystemCode()
    {
        return $this->_taxSystemCode;
    }

    /**
     * ������������� ��� ������� ���������������
     *
     * @param int $value ��� ������� ���������������. ����� 1-6
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� - �� �����
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� ������ ������ ��� ������ �����
     */
    public function setTaxSystemCode($value)
    {
        if ($value === null || $value === '') {
            $this->_taxSystemCode = null;
        } elseif (!is_numeric($value)) {
            throw new InvalidPropertyValueTypeException(
                'Invalid taxSystemCode value type', 0, 'receipt.taxSystemCode'
            );
        } else {
            $castedValue = (int)$value;
            if ($castedValue < 1 || $castedValue > 6) {
                throw new InvalidPropertyValueException(
                    'Invalid taxSystemCode value: ' . $value, 0, 'receipt.taxSystemCode'
                );
            }
            $this->_taxSystemCode = $castedValue;
        }
    }

    /**
     * ���������� ����� �������� ����������� � ������� ITU-T E.164 �� ������� ����� ������ ���
     *
     * @return string ����� �������� �����������
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * ���������������� ����� �������� ����������� � ������� ITU-T E.164 �� ������� ����� ������ ���
     *
     * @param string $value ����� �������� ����������� � ������� ITU-T E.164
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� �������� ���� �������� �� ������
     * @throws InvalidPropertyValueException ������������� ���� ������� �� ������������� ������� ITU-T E.164
     */
    public function setPhone($value)
    {
        if ($value === null || $value === '') {
            $this->_phone = null;
        } elseif (!TypeCast::canCastToString($value)) {
            throw new InvalidPropertyValueTypeException('Invalid phone value type', 0, 'receipt.phone');
        } elseif (!preg_match('/^[0-9]{4,15}$/', (string)$value)) {
            throw new InvalidPropertyValueException('Invalid phone value: "' . $value . '"', 0, 'receipt.phone');
        } else {
            $this->_phone = (string)$value;
        }
    }

    /**
     * ���������� ����� ����������� ����� �� ������� ����� ������ ���
     *
     * @return string E-mail ����� �����������
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * ������������� ����� ����������� ����� �� ������� ����� ������ ���
     *
     * @param string $value E-mail ����� �����������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � �������� �������� ���� �������� �� ������
     */
    public function setEmail($value)
    {
        if ($value === null || $value === '') {
            $this->_email = null;
        } elseif (!TypeCast::canCastToString($value)) {
            throw new InvalidPropertyValueTypeException('Invalid email value type', 0, 'receipt.email');
        } else {
            $this->_email = (string)$value;
        }
    }

    /**
     * ��������� ���� �� � ���� ���� �� ���� �������
     *
     * @return bool True ���� ��� �� ����, false ���� � ���� ��� �� ����� �������
     */
    public function notEmpty()
    {
        return !empty($this->_items);
    }

    /**
     * ���������� ��������� ������ ������ �� ������� ����
     * @param bool $withShipping �������� �� � ��������� ������ ��������� ��������
     * @return int ����� ��������� ������ � ������/��������
     */
    public function getAmountValue($withShipping = true)
    {
        $result = 0;
        foreach ($this->_items as $item) {
            if ($withShipping || !$item->isShipping()) {
                $result += $item->getAmount();
            }
        }
        return $result;
    }

    /**
     * ���������� ��������� �������� ������ �� ������� ����
     * @return int ��������� �������� �� ������� ���� � ������/��������
     */
    public function getShippingAmountValue()
    {
        $result = 0;
        foreach ($this->_items as $item) {
            if ($item->isShipping()) {
                $result += $item->getAmount();
            }
        }
        return $result;
    }

    /**
     * ��������� ��������� ������� � ���� � ����� ���� ������
     * @param AmountInterface $orderAmount ����� ��������� ������
     * @param bool $withShipping �������� �� ������ � ���� ��������
     */
    public function normalize(AmountInterface $orderAmount, $withShipping = false)
    {
        $amount = $orderAmount->getIntegerValue();
        if (!$withShipping) {
            if ($this->_shippingItems !== null) {
                if ($amount > $this->getShippingAmountValue()) {
                    $amount -= $this->getShippingAmountValue();
                } else {
                    $withShipping = true;
                }
            }
        }
        $realAmount = $this->getAmountValue($withShipping);
        if ($realAmount !== $amount) {
            $coefficient = (float)$amount / (float)$realAmount;
            $items = array();
            $realAmount = 0;
            foreach ($this->_items as $item) {
                if ($withShipping || !$item->isShipping()) {
                    $price = round($coefficient * $item->getPrice()->getIntegerValue());
                    if ($price < 1.0) {
                        if ($item->getPrice()->getIntegerValue() > 1) {
                            $item->getPrice()->setValue(0.01);
                        }
                        $amount -= $item->getAmount();
                    } else {
                        $items[] = $item;
                        $realAmount += $item->getAmount();
                    }
                }
            }
            uasort($items, function (ReceiptItemInterface $a, ReceiptItemInterface $b) {
                if ($a->getPrice()->getIntegerValue() > $b->getPrice()->getIntegerValue()) {
                    return -1;
                }
                if ($a->getPrice()->getIntegerValue() < $b->getPrice()->getIntegerValue()) {
                    return 1;
                }
                return 0;
            });

            $coefficient = (float)$amount / (float)$realAmount;
            $realAmount = 0;
            $aloneId = null;
            foreach ($items as $index => $item) {
                if ($withShipping || !$item->isShipping()) {
                    $item->applyDiscountCoefficient($coefficient);
                    $realAmount += $item->getAmount();
                    if ($aloneId === null && $item->getQuantity() === 1.0 && !$item->isShipping()) {
                        $aloneId = $index;
                    }
                }
            }
            if ($aloneId === null) {
                foreach ($this->_items as $index => $item) {
                    if (!$item->isShipping()) {
                        $aloneId = $index;
                        break;
                    }
                }
            }
            if ($aloneId === null) {
                $aloneId = 0;
            }
            $diff = $amount - $realAmount;
            if (abs($diff) >= 0.1) {
                if ($this->_items[$aloneId]->getQuantity() === 1.0) {
                    $this->_items[$aloneId]->increasePrice($diff / 100.0);
                } elseif ($this->_items[$aloneId]->getQuantity() > 1.0) {
                    $item = $this->_items[$aloneId]->fetchItem(1);
                    $item->increasePrice($diff / 100.0);
                    array_splice($this->_items, $aloneId + 1, 0, array($item));
                } else {
                    $item = $this->_items[$aloneId]->fetchItem($this->_items[$aloneId]->getQuantity() / 2);
                    $item->increasePrice($diff / 100.0);
                    array_splice($this->_items, $aloneId + 1, 0, array($item));
                }
            }
        }
    }

    public function fromArray($sourceArray)
    {
        if (!empty($sourceArray['items'])) {
            for ($i = 0; $i < count($sourceArray['items']); $i++) {
                if (is_array($sourceArray['items'][$i])) {
                    $item = new ReceiptItem();
                    $amount = new MonetaryAmount();
                    $amount->fromArray($sourceArray['items'][$i]['amount']);
                    $sourceArray['items'][$i]['price'] = $amount;
                    unset($sourceArray['items'][$i]['amount']);
                    $item->fromArray($sourceArray['items'][$i]);
                    $sourceArray['items'][$i] = $item;
                }
            }
        }
        parent::fromArray($sourceArray);
    }
}
