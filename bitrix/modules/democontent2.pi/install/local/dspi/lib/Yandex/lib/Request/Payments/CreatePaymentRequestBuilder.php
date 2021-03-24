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

use YandexCheckout\Common\AbstractRequestBuilder;
use YandexCheckout\Common\Exceptions\EmptyPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Common\Exceptions\InvalidRequestException;
use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\ConfirmationAttributes\AbstractConfirmationAttributes;
use YandexCheckout\Model\ConfirmationAttributes\ConfirmationAttributesFactory;
use YandexCheckout\Model\Metadata;
use YandexCheckout\Model\MonetaryAmount;
use YandexCheckout\Model\PaymentData\AbstractPaymentData;
use YandexCheckout\Model\PaymentData\PaymentDataFactory;
use YandexCheckout\Model\Receipt;
use YandexCheckout\Model\ReceiptInterface;
use YandexCheckout\Model\ReceiptItem;
use YandexCheckout\Model\ReceiptItemInterface;
use YandexCheckout\Model\Recipient;
use YandexCheckout\Model\RecipientInterface;

/**
 * ����� ������� �������� ������� � API �� �������� �������
 *
 * @package YandexCheckout\Request\Payments
 */
class CreatePaymentRequestBuilder extends AbstractRequestBuilder
{
    /**
     * @var CreatePaymentRequest ���������� ������ �������
     */
    protected $currentObject;

    /**
     * @var Recipient ���������� �������
     */
    private $recipient;

    /**
     * @var Receipt ������ � ����������� � ����
     */
    private $receipt;

    /**
     * @var MonetaryAmount ����� ������
     */
    private $amount;

    /**
     * @var PaymentDataFactory ������� ������� ���������� ��������
     */
    private $paymentDataFactory;

    /**
     * @var ConfirmationAttributesFactory ������� �������� ������� ������������� ��������
     */
    private $confirmationFactory;

    /**
     * �������������� ������ �������, ������� � ���������� ����� ���������� ��������
     * @return CreatePaymentRequest ������� ����������� ������� ������� � API
     */
    protected function initCurrentObject()
    {
        $request = new CreatePaymentRequest();

        $this->recipient = new Recipient();
        $this->receipt = new Receipt();
        $this->amount = new MonetaryAmount();

        return $request;
    }

    /**
     * ������������� ������������� �������� ���������� �������
     * @param string $value ������������� ��������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �� ��������� ��������
     */
    public function setAccountId($value)
    {
        $this->recipient->setAccountId($value);
        return $this;
    }

    /**
     * ������������� ������������� �����
     * @param string $value ������������� �����
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �� ��������� ��������
     */
    public function setGatewayId($value)
    {
        $this->recipient->setGatewayId($value);
        return $this;
    }

    /**
     * ������������� ���������� ������� �� ������� ��� �������������� �������
     * @param RecipientInterface|array $value ���������� �������
     * @throws InvalidPropertyValueTypeException ������������� ���� ������� �������� �� ��������� ����
     */
    public function setRecipient($value)
    {
        if (is_array($value)) {
            $this->recipient->fromArray($value);
        } elseif ($value instanceof RecipientInterface) {
            $this->recipient->setAccountId($value->getAccountId());
            $this->recipient->setGatewayId($value->getGatewayId());
        } else {
            throw new InvalidPropertyValueTypeException('Invalid recipient value', 0, 'recipient', $value);
        }
    }

    /**
     * ������������� ����� ������
     * @param AmountInterface|string|array $value ����� ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws EmptyPropertyValueException ������������� ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueException ������������� ���� ��� ������� ���� ��� ������������� ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���� �������� �� ��������� ��������
     */
    public function setAmount($value)
    {
        if ($value instanceof AmountInterface) {
            $this->amount->setValue($value->getValue());
            $this->amount->setCurrency($value->getCurrency());
        } elseif ($value === null || $value === '') {
            throw new EmptyPropertyValueException('Empty payment amount value', 0, 'CreatePaymentRequest.amount');
        } elseif (is_array($value)) {
            $this->amount->fromArray($value);
        } elseif (!is_numeric($value)) {
            throw new InvalidPropertyValueTypeException(
                'Invalid payment amount value type', 0, 'CreatePaymentRequest.amount', $value
            );
        } elseif ($value > 0) {
            $this->amount->setValue($value);
        } else {
            throw new InvalidPropertyValueException(
                'Invalid payment amount value', 0, 'CreatePaymentRequest.amount', $value
            );
        }
        return $this;
    }

    /**
     * ������������� ������ � ������� ����� ������������
     * @param string $value ��� ������ ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws EmptyPropertyValueException ������������ ���� ���� �������� ������ ��������
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� �������� ����������� ����
     * @throws InvalidPropertyValueException ������������ ���� ��� ������� ���������������� ��� ������
     */
    public function setCurrency($value)
    {
        $this->amount->setCurrency($value);
        foreach ($this->receipt->getItems() as $item) {
            $item->getPrice()->setCurrency($value);
        }
        return $this;
    }

    /**
     * ������������� ���
     * @param ReceiptInterface|array $value ������� ���� ��� ������������� ������ � ������� ����
     */
    public function setReceipt($value)
    {
        if (is_array($value)) {
            $this->receipt->fromArray($value);
        } elseif ($value instanceof ReceiptInterface) {
            $this->receipt = clone $value;
        } else {
            throw new InvalidPropertyValueTypeException('Invalid receipt value type', 0, 'receipt', $value);
        }
    }

    /**
     * ������������� ������ ������� � ������ ��� �������� ����
     * @param array $value ������ ������� � ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     * 
     * @throws InvalidPropertyValueException ������������ ���� ���� �� ���� �� ������� ����� �������� ���������
     */
    public function setReceiptItems($value)
    {
        $this->receipt->setItems(array());
        $index = 0;
        foreach ($value as $item) {
            if ($item instanceof ReceiptItemInterface) {
                $this->receipt->addItem($item);
            } else {
                if (empty($item['title']) && empty($item['description'])) {
                    throw new InvalidPropertyValueException(
                        'Item#' . $index . ' title or description not specified',
                        0,
                        'CreatePaymentRequest.items[' . $index . '].title',
                        json_encode($item)
                    );
                }
                if (empty($item['price'])) {
                    throw new InvalidPropertyValueException(
                        'Item#' . $index . ' price not specified',
                        0,
                        'CreatePaymentRequest.items[' . $index . '].price',
                        json_encode($item)
                    );
                }
                $this->addReceiptItem(
                    empty($item['title']) ? $item['description'] : $item['title'],
                    $item['price'],
                    empty($item['quantity']) ? 1.0 : $item['quantity'],
                    empty($item['vatCode']) ? null : $item['vatCode']
                );
            }
            $index++;
        }
        return $this;
    }

    /**
     * ��������� � ��� �����
     * @param string $title �������� ��� �������� ������
     * @param string $price ���� ������ � ������, �������� � ������
     * @param float $quantity ���������� ����������� ������
     * @param int|null $vatCode ������ ���, ��� null ���� ������������ ������ ��� ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     */
    public function addReceiptItem($title, $price, $quantity = 1.0, $vatCode = null)
    {
        $item = new ReceiptItem();
        $item->setDescription($title);
        $item->setQuantity($quantity);
        $item->setVatCode($vatCode);
        $item->setPrice(new MonetaryAmount($price, $this->amount->getCurrency()));
        $this->receipt->addItem($item);
        return $this;
    }

    /**
     * ��������� � ��� �������� ������
     * @param string $title �������� �������� � ����
     * @param string $price ��������� ��������
     * @param int|null $vatCode ������ ���, ��� null ���� ������������ ������ ��� ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     */
    public function addReceiptShipping($title, $price, $vatCode = null)
    {
        $item = new ReceiptItem();
        $item->setDescription($title);
        $item->setQuantity(1);
        $item->setVatCode($vatCode);
        $item->setIsShipping(true);
        $item->setPrice(new MonetaryAmount($price, $this->amount->getCurrency()));
        $this->receipt->addItem($item);
        return $this;
    }

    /**
     * ������������� ����� ����������� ����� ���������� ����
     * @param string $value Email ���������� ����
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� �������� ����������� ����
     */
    public function setReceiptEmail($value)
    {
        $this->receipt->setEmail($value);
        return $this;
    }

    /**
     * ������������� ������� ���������� ����
     * @param string $value ������� ���������� ����
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueException ������������ ���� ��� ������� �� �������, � ���-�� ������
     * @throws InvalidPropertyValueTypeException ������������ ���� ���� �������� �������� ����������� ����
     */
    public function setReceiptPhone($value)
    {
        $this->receipt->setPhone($value);
        return $this;
    }

    /**
     * ������������� ��� ������� ���������������.
     * @param int $value ��� ������� ���������������. ����� 1-6.
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� - �� �����
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� ������ ������ ��� ������ �����
     */
    public function setTaxSystemCode($value)
    {
        $this->receipt->setTaxSystemCode($value);
        return $this;
    }

    /**
     * ������������� ����������� ����� ��� ���������� ������
     * @param string $value ����������� ����� ��� ���������� ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� ������ 200 ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� �������
     */
    public function setPaymentToken($value)
    {
        $this->currentObject->setPaymentToken($value);
        return $this;
    }

    /**
     * ������������� ������������� ������ � ���������� ������ ����������
     * @param string $value ������������� ������ � ����������� ��������� ������ ����������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���������� �������� �� �������� ������� ��� null
     */
    public function setPaymentMethodId($value)
    {
        $this->currentObject->setPaymentMethodId($value);
        return $this;
    }

    /**
     * ������������� ������ � ����������� ��� �������� ������ ������
     * @param AbstractPaymentData|string|array|null $value ������ � �������� ������ ������ ��� null
     * @param array $options ��������� ������� ������ � ���� �������������� �������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ��� ������� ������ ����������� ����
     */
    public function setPaymentMethodData($value, array $options = null)
    {
        if (is_string($value) && $value !== '') {
            if (empty($options)) {
                $value = $this->getPaymentDataFactory()->factory($value);
            } else {
                $value = $this->getPaymentDataFactory()->factoryFromArray($options, $value);
            }
        } elseif (is_array($value)) {
            $value = $this->getPaymentDataFactory()->factoryFromArray($value);
        }
        $this->currentObject->setPaymentMethodData($value);
        return $this;
    }

    /**
     * ������������� ������ ������������� �������
     * @param AbstractConfirmationAttributes|string|array|null $value ������ ������������� �������
     * @param array|null $options ��������� ������� ������������� ������� � ���� �������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� �������� ����
     * AbstractConfirmationAttributes ��� null
     */
    public function setConfirmation($value, array $options = null)
    {
        if (is_string($value) && $value !== '') {
            if (empty($options)) {
                $value = $this->getConfirmationFactory()->factory($value);
            } else {
                $value = $this->getConfirmationFactory()->factoryFromArray($options, $value);
            }
        } elseif (is_array($value)) {
            $value = $this->getConfirmationFactory()->factoryFromArray($value);
        }
        $this->currentObject->setConfirmation($value);
        return $this;
    }

    /**
     * ������������� ���� ���������� �������� ������. �������� true ���������� �������� ������������� payment_method.
     * @param bool $value ��������� ��������� ������ ��� ������������ �������������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���������� �������� �� �������� � bool
     */
    public function setSavePaymentMethod($value)
    {
        $this->currentObject->setSavePaymentMethod($value);
        return $this;
    }

    /**
     * ������������� ���� ��������������� �������� ����������� ������
     * @param bool $value ������������� ������� ����������� ������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���������� �������� �� �������� � bool

     */
    public function setCapture($value)
    {
        $this->currentObject->setCapture($value);
        return $this;
    }

    /**
     * ������������� IP ����� ����������
     * @param string $value IPv4 ��� IPv6-����� ����������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� �������
     */
    public function setClientIp($value)
    {
        $this->currentObject->setClientIp($value);
        return $this;
    }

    /**
     * ������������� ����������, ����������� � �������
     * @param Metadata|array|null $value ���������� �������, ��������������� ���������
     * @return CreatePaymentRequestBuilder ������� �������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� ������ �� ������� ���������������� ���
     * ���������� �������
     */
    public function setMetadata($value)
    {
        $this->currentObject->setMetadata($value);
        return $this;
    }

    /**
     * ������ � ���������� ������ ������� ��� �������� � API ������ �����
     * @param array|null $options ������ ���������� ��� ��������� � ������ �������
     * @return CreatePaymentRequestInterface ������� ������� �������
     *
     * @throws InvalidRequestException ������������� ���� ������� ������ ������� �� �������
     */
    public function build(array $options = null)
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
        $accountId = $this->recipient->getAccountId();
        $gatewayId = $this->recipient->getGatewayId();
        if (!empty($accountId) && !empty($gatewayId)) {
            $this->currentObject->setRecipient($this->recipient);
        }
        if ($this->receipt->notEmpty()) {
            $this->currentObject->setReceipt($this->receipt);
        }
        $this->currentObject->setAmount($this->amount);
        return parent::build();
    }

    /**
     * ���������� ������� ������� ���������� ��������
     * @return PaymentDataFactory ������� ������� ���������� ��������
     */
    protected function getPaymentDataFactory()
    {
        if ($this->paymentDataFactory === null) {
            $this->paymentDataFactory = new PaymentDataFactory();
        }
        return $this->paymentDataFactory;
    }

    /**
     * ���������� ������� ��� �������� ������� ������������� ��������
     * @return ConfirmationAttributesFactory ������� �������� ������� ������������� ��������
     */
    protected function getConfirmationFactory()
    {
        if ($this->confirmationFactory === null) {
            $this->confirmationFactory = new ConfirmationAttributesFactory();
        }
        return $this->confirmationFactory;
    }
}