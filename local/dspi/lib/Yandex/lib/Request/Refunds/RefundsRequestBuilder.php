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

use YandexCheckout\Common\AbstractRequestBuilder;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;

/**
 * ����� ������� �������� �������� � API ������ ���������
 *
 * @package YandexCheckout\Request\Refunds
 */
class RefundsRequestBuilder extends AbstractRequestBuilder
{
    /**
     * @var RefundsRequest ������� ����������� ������� �������
     */
    protected $currentObject;

    /**
     * �������������� ����� ������� ����������� �������
     * @return RefundsRequest ������� ����������� �������
     */
    protected function initCurrentObject()
    {
        return new RefundsRequest();
    }

    /**
     * ������������� ������������� ��������
     * @param string $value ������������� ��������, ������� ������ � API
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ����� ����������� �������� �� ����� 36
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setRefundId($value)
    {
        $this->currentObject->setRefundId($value);
        return $this;
    }

    /**
     * ������������� ������������� ������� ��� null ���� ��������� ��� �������
     * @param string|null $value ������������� �������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ����� ���������� ������ �� ����� 36 ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setPaymentId($value)
    {
        $this->currentObject->setPaymentId($value);
        return $this;
    }

    /**
     * ������������� ������������� ��������
     * @param string $value ������������� �������� ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setAccountId($value)
    {
        $this->currentObject->setAccountId($value);
        return $this;
    }

    /**
     * ������������� ������������� �����
     * @param string|null $value ������������� ����� ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setGatewayId($value)
    {
        $this->currentObject->setGatewayId($value);
        return $this;
    }

    /**
     * ������������� ������ ���������� ���������
     * @param string $value ������ ���������� �������� ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� �� �������� �������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setStatus($value)
    {
        $this->currentObject->setStatus($value);
        return $this;
    }

    /**
     * ������������� ����� ��������� �������� �������
     * @param string $value ����� ��������� �������� ������� ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� � ����� ���� �������� �� ������
     */
    public function setNextPage($value)
    {
        $this->currentObject->setNextPage($value);
        return $this;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (�� �������) ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedGt($value)
    {
        $this->currentObject->setCreatedGt($value);
        return $this;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (������������) ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedGte($value)
    {
        $this->currentObject->setCreatedGte($value);
        return $this;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (�� �������) ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedLt($value)
    {
        $this->currentObject->setCreatedLt($value);
        return $this;
    }

    /**
     * ������������� ���� �������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ��������, �� (������������) ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setCreatedLte($value)
    {
        $this->currentObject->setCreatedLte($value);
        return $this;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ���������� ��������, �� (�� �������) ��� null ����� �������
     * ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedGt($value)
    {
        $this->currentObject->setAuthorizedGt($value);
        return $this;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ���������� ��������, �� (�� �������) ��� null ����� �������
     * ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedGte($value)
    {
        $this->currentObject->setAuthorizedGte($value);
        return $this;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ����������, �� (�� �������) ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedLt($value)
    {
        $this->currentObject->setAuthorizedLt($value);
        return $this;
    }

    /**
     * ������������� ���� ���������� �� ������� ���������� ��������
     * @param \DateTime|string|int|null $value ����� ����������, �� (������������) ��� null ����� ������� ��������
     * @return RefundsRequestBuilder ������� �������� ������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ���� �������� ���� � ���������� ������� (���� ��������
     * ������ ��� �����, ������� �� ������� ������������� � �������� ����)
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� ���� � �� ��� ����� (�������� ��
     * ������, �� ����� � �� �������� ���� \DateTime)
     */
    public function setAuthorizedLte($value)
    {
        $this->currentObject->setAuthorizedLte($value);
        return $this;
    }

    /**
     * �������� � ���������� ������ ������� ������ ��������� ��������
     * @param array|null $options ������ � ����������� �������
     * @return RefundsRequestInterface ������� ������� ������� � API ��� ��������� ������ ��������� ��������
     */
    public function build(array $options = null)
    {
        return parent::build($options);
    }
}