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

use YandexCheckout\Common\AbstractRequestBuilder;
use YandexCheckout\Model\AmountInterface;

/**
 * ����� ������� �������� ��� ��������� ������ ��������� �������� ������
 *
 * @package YandexCheckout\Request
 */
class PaymentOptionsRequestBuilder extends AbstractRequestBuilder
{
    /**
     * @var PaymentOptionsRequest ������� ����������� �������
     */
    protected $currentObject;

    /**
     * �������������� ������ ������
     * @return PaymentOptionsRequest ������� ������� ������� ����� ��������
     */
    protected function initCurrentObject()
    {
        return new PaymentOptionsRequest();
    }

    /**
     * ������������� ������������� ��������
     * @param string|null $value �������� �������������� ��������, null ���� ��������� ������� ��������
     * @return PaymentOptionsRequestBuilder ������� �������� ������� ��������
     */
    public function setAccountId($value)
    {
        $this->currentObject->setAccountId($value);
        return $this;
    }

    /**
     * ������������� ������������� �����
     * @param string|null $value �������� �������������� �����, null ���� ��������� ������� ��������
     * @return PaymentOptionsRequestBuilder ������� �������� ������� ��������
     */
    public function setGatewayId($value)
    {
        $this->currentObject->setGatewayId($value);
        return $this;
    }

    /**
     * ������������� ����� �������
     * @param string|AmountInterface|null $value ����� �������, null ���� ��������� ������� ��������
     * @return PaymentOptionsRequestBuilder ������� �������� ������� ��������
     */
    public function setAmount($value)
    {
        if (empty($value)) {
            $this->currentObject->setAmount(null);
        } elseif ($value instanceof AmountInterface) {
            if ($value->getValue() > 0.0) {
                $this->currentObject->setAmount($value->getValue());
            }
            $this->currentObject->setCurrency($value->getCurrency());
        } else {
            $this->currentObject->setAmount($value);
        }
        return $this;
    }

    /**
     * ������������� ��� ������ � ������� ��������� �������� �����
     * @param string $value ��� ������, null ���� ��������� ������� ��������
     * @return PaymentOptionsRequestBuilder ������� �������� ������� ��������
     */
    public function setCurrency($value)
    {
        $this->currentObject->setCurrency($value);
        return $this;
    }

    /**
     * ������������� �������� ������������� �������, ��� �������� ������������� ������ �������� ������
     * @param string $value �������� ������������� �������
     * @return PaymentOptionsRequestBuilder ������� �������� ������� ��������
     */
    public function setConfirmationType($value)
    {
        $this->currentObject->setConfirmationType($value);
        return $this;
    }

    /**
     * �������� � ���������� ������� ������ ������� ��������� ������ ��������� �������� ������
     * @param array|null $options ������ ��� ��������������� ��������
     * @return PaymentOptionsRequestInterface ������� ������� �������
     */
    public function build(array $options = null)
    {
        return parent::build($options);
    }
}
