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

use YandexCheckout\Model\Confirmation\ConfirmationRedirect;
use YandexCheckout\Model\Confirmation\ConfirmationExternal;
use YandexCheckout\Model\ConfirmationType;
use YandexCheckout\Model\Metadata;
use YandexCheckout\Model\MonetaryAmount;
use YandexCheckout\Model\Payment;
use YandexCheckout\Model\PaymentInterface;
use YandexCheckout\Model\PaymentMethod\AbstractPaymentMethod;
use YandexCheckout\Model\PaymentMethod\PaymentMethodFactory;
use YandexCheckout\Model\Recipient;

/**
 * ����� ������� ������ �� API �� ������� �������� ��������
 *
 * @package YandexCheckout\Request\Payments
 */
class PaymentsResponse
{
    /**
     * @var PaymentInterface[] ������ ��������
     */
    private $items;

    /**
     * @var string|null ����� ��������� ��������
     */
    private $nextPage;

    /**
     * �����������, ������������� �������� ������� �� ���������� �� API �������������� �������
     * @param array $options ������ ��������, ��������� �� API
     */
    public function __construct($options)
    {
        $this->items = array();
        foreach ($options['items'] as $paymentInfo) {
            $payment = new Payment();
            $payment->setId($paymentInfo['id']);
            $payment->setStatus($paymentInfo['status']);
            $payment->setAmount(new MonetaryAmount(
                $paymentInfo['amount']['value'],
                $paymentInfo['amount']['currency']
            ));
            $payment->setCreatedAt(strtotime($paymentInfo['created_at']));
            $payment->setPaymentMethod($this->factoryPaymentMethod($paymentInfo['payment_method']));
            $payment->setPaid($paymentInfo['paid']);

            if (!empty($paymentInfo['recipient'])) {
                $recipient = new Recipient();
                $recipient->setAccountId($paymentInfo['recipient']['account_id']);
                $recipient->setGatewayId($paymentInfo['recipient']['gateway_id']);
                $payment->setRecipient($recipient);
            }
            if (!empty($paymentInfo['captured_at'])) {
                $payment->setCapturedAt(strtotime($paymentInfo['captured_at']));
            }
            if (!empty($paymentInfo['confirmation'])) {
                if ($paymentInfo['confirmation']['type'] === ConfirmationType::REDIRECT) {
                    $confirmation = new ConfirmationRedirect();
                    $confirmation->setConfirmationUrl($paymentInfo['confirmation']['confirmation_url']);
                    $confirmation->setEnforce($paymentInfo['confirmation']['enforce']);
                    $confirmation->setReturnUrl($paymentInfo['confirmation']['return_url']);
                } else {
                    $confirmation = new ConfirmationExternal();
                }
                $payment->setConfirmation($confirmation);
            }
            if (!empty($paymentInfo['refunded_amount'])) {
                $payment->setRefundedAmount(new MonetaryAmount(
                    $paymentInfo['refunded_amount']['value'], $paymentInfo['refunded_amount']['currency']
                ));
            }
            if (!empty($paymentInfo['receipt_registration'])) {
                $payment->setReceiptRegistration($paymentInfo['receipt_registration']);
            }
            if (!empty($paymentInfo['metadata'])) {
                $metadata = new Metadata();
                foreach ($paymentInfo['metadata'] as $key => $value) {
                    $metadata->offsetSet($key, $value);
                }
                $payment->setMetadata($metadata);
            }
            $this->items[] = $payment;
        }
        if (!empty($options['next_page'])) {
            $this->nextPage = $options['next_page'];
        }
    }

    /**
     * ���������� ������ ��������
     * @return PaymentInterface[] ������ ��������
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * ���������� ����� ��������� ��������, ���� �� �����, ��� null
     * @return string|null ����� ��������� ��������
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * ��������� �������� �� � ������ ����� ��������� ��������
     * @return bool True ���� ����� ��������� �������� ����, false ���� ���
     */
    public function hasNextPage()
    {
        return $this->nextPage !== null;
    }

    /**
     * ��������� ����� ��� �������� �������� ������� ������
     * @param array $options ������ �������� ������ ������
     * @return AbstractPaymentMethod ������������ ������ ������
     */
    private function factoryPaymentMethod($options)
    {
        $factory = new PaymentMethodFactory();
        return $factory->factoryFromArray($options);
    }
}
