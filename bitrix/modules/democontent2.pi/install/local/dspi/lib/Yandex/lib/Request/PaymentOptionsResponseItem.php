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

use YandexCheckout\Common\AbstractObject;
use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\ConfirmationType;
use YandexCheckout\Model\MonetaryAmount;
use YandexCheckout\Model\PaymentMethodType;

/**
 * ����� �������� ������, ������������ API ��� ������� ��������� �������� ������
 *
 * @package YandexCheckout\Request
 *
 * @property-read string $paymentMethodType ��� ��������� ������� ��� ���������� �������
 * @property-read string[] $confirmationTypes ������ ��������� ��������� ������������� �������
 * @property-read AmountInterface $charge ����� �������
 * @property-read AmountInterface $fee ����� ��������
 * @property-read bool $extraFee ������� ����������� �������������� �������� �� ������� ��������
 */
class PaymentOptionsResponseItem extends AbstractObject
{
    /**
     * @var string ��� ��������� ������� ��� ���������� �������
     */
    private $_paymentMethodType;

    /**
     * @var string[] ������ ��������� ��������� ������������� �������
     */
    private $_confirmationTypes;

    /**
     * @var AmountInterface ����� �������
     */
    private $_charge;

    /**
     * @var AmountInterface ����� �������������� �������� ��� ���������� ������� � ������� �������� ������� ������
     */
    private $_fee;

    /**
     * @var bool ������� ����������� �������������� �������� �� ������� ��������
     */
    private $_extraFee;

    public function __construct($options)
    {
        $this->_paymentMethodType = $options['payment_method_type'];
        $this->_confirmationTypes = array();
        foreach ($options['confirmation_types'] as $opt) {
            $this->_confirmationTypes[] = $opt;
        }

        $this->_charge = new MonetaryAmount($options['charge']['value'], $options['charge']['currency']);
        $this->_fee = new MonetaryAmount();
        if (!empty($options['fee'])) {
            $this->_fee->setValue($options['fee']['value']);
            $this->_fee->setCurrency($options['fee']['currency']);
        } else {
            $this->_fee->setCurrency($options['charge']['currency']);
        }

        $this->_extraFee = false;
        if (!empty($options['extra_fee'])) {
            $this->_extraFee = (bool)$options['extra_fee'];
        }
    }

    /**
     * ���������� ��� ��������� ������� ��� ���������� �������
     * @return string ��� ��������� ������� ��� ���������� �������
     * @see PaymentMethodType
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethodType;
    }

    /**
     * ���������� ������ ��������� ��������� ������������� �������
     * @return string[] ������ ��������� ��������� ������������� �������
     * @see ConfirmationType
     */
    public function getConfirmationTypes()
    {
        return $this->_confirmationTypes;
    }

    /**
     * ���������� ����� �������
     * @return AmountInterface ����� �������
     */
    public function getCharge()
    {
        return $this->_charge;
    }

    /**
     * ���������� ����� �������������� �������� ��� ���������� ������� � ������� �������� ������� ������
     * @return AmountInterface ����� ��������
     */
    public function getFee()
    {
        return $this->_fee;
    }

    /**
     * ���������� ������� ����������� �������������� �������� �� ������� ��������
     * @return bool True ���� �������� �� ������� ������� �������, false ���� ���
     */
    public function getExtraFee()
    {
        return $this->_extraFee;
    }
}
