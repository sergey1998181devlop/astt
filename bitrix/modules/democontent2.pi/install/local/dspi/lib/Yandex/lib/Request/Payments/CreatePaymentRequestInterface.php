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

use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\ConfirmationAttributes\AbstractConfirmationAttributes;
use YandexCheckout\Model\Metadata;
use YandexCheckout\Model\PaymentData\AbstractPaymentData;
use YandexCheckout\Model\ReceiptInterface;
use YandexCheckout\Model\RecipientInterface;

/**
 * Interface CreatePaymentRequestInterface
 *
 * @package YandexCheckout\Request\Payments
 *
 * @property-read RecipientInterface|null $recipient ���������� �������, ���� �����
 * @property-read AmountInterface $amount ����� ������������ �������
 * @property-read ReceiptInterface $receipt ������ ����������� ���� 54-��
 * @property-read string $paymentToken ����������� ����� ��� ���������� ������, ��������������
 * Yandex.Checkout JS widget
 * @property-read string $payment_token ����������� ����� ��� ���������� ������, ��������������
 * Yandex.Checkout JS widget
 * @property-read string $paymentMethodId ������������� ������ � ����������� ��������� ������ ����������
 * @property-read string $payment_method_id ������������� ������ � ����������� ��������� ������ ����������
 * @property-read AbstractPaymentData $paymentMethodData ������ ������������ ��� �������� ������ ������
 * @property-read AbstractPaymentData $payment_method_data ������ ������������ ��� �������� ������ ������
 * @property-read AbstractConfirmationAttributes $confirmation ������ ������������� �������
 * @property-read bool $savePaymentMethod ��������� ��������� ������ ��� ������������ �������������
 * @property-read bool $save_payment_method ��������� ��������� ������ ��� ������������ �������������
 * @property-read bool $capture ������������� ������� ����������� ������
 * @property-read string $clientIp IPv4 ��� IPv6-����� ����������. ���� �� ������, ������������ IP-�����
 * TCP-�����������.
 * @property-read string $client_ip IPv4 ��� IPv6-����� ����������. ���� �� ������, ������������ IP-�����
 * TCP-�����������.
 * @property-read Metadata $metadata ���������� ����������� � �������
 */
interface CreatePaymentRequestInterface
{
    /**
     * ���������� ������ ���������� �������
     * @return RecipientInterface|null ������ � ����������� � ���������� ������� ��� null ���� ���������� �� �����
     */
    function getRecipient();

    /**
     * ��������� ������� ���������� ������� � �������
     * @return bool True ���� ���������� ������� �����, false ���� ���
     */
    function hasRecipient();

    /**
     * ���������� ����� ������
     * @return AmountInterface ����� ������
     */
    function getAmount();

    /**
     * ���������� ���, ���� �� ����
     * @return ReceiptInterface|null ������ ����������� ���� 54-�� ��� null ���� ���� ���
     */
    function getReceipt();

    /**
     * ��������� ������� ���� � ����������� �������
     * @return bool True ���� ��� ����, false ���� ���
     */
    function hasReceipt();

    /**
     * ���������� ����������� ����� ��� ���������� ������
     * @return string ����������� ����� ��� ���������� ������, �������������� Yandex.Checkout JS widget
     */
    function getPaymentToken();

    /**
     * ��������� ������� ������������ ������ ��� ���������� ������
     * @return bool True ���� ����� ����������, false ���� ���
     */
    function hasPaymentToken();

    /**
     * ������������� ������������� ������ �������� ������ ����������
     * @return string ������������� ������ � ����������� ��������� ������ ����������
     */
    function getPaymentMethodId();

    /**
     * ��������� ������� �������������� ������ � �������� ������ ����������
     * @return bool True ���� ������������� �����, false ���� ���
     */
    function hasPaymentMethodId();

    /**
     * ���������� ������ ��� �������� ������ ������
     * @return AbstractPaymentData ������ ������������ ��� �������� ������ ������
     */
    function getPaymentMethodData();

    /**
     * ��������� ���������� �� ������ � ������� ������
     * @return bool True ���� ������ ������ ������ ����������, false ���� ���
     */
    function hasPaymentMethodData();

    /**
     * ���������� ������ ������������� �������
     * @return AbstractConfirmationAttributes ������ ������������� �������
     */
    function getConfirmation();

    /**
     * ��������� ��� �� ���������� ������ ������������� �������
     * @return bool True ���� ������ ������������� ������� ��� ����������, false ���� ���
     */
    function hasConfirmation();

    /**
     * ���������� ���� ���������� �������� ������
     * @return bool ���� ���������� �������� ������
     */
    function getSavePaymentMethod();

    /**
     * ��������� ��� �� ���������� ���� ���������� �������� ������
     * @return bool True ���� ���� ��� ����������, false ���� ���
     */
    function hasSavePaymentMethod();

    /**
     * ���������� ���� ��������������� �������� ����������� ������
     * @return bool True ���� ��������� ������������� ������� ����������� ������, false ���� ���
     */
    function getCapture();

    /**
     * ��������� ��� �� ���������� ���� ��������������� ������� ����������� ������
     * @return bool True ���� ���� ��������������� �������� ������ ��� ����������, false ���� ���
     */
    function hasCapture();

    /**
     * ���������� IPv4 ��� IPv6-����� ����������
     * @return string IPv4 ��� IPv6-����� ����������
     */
    function getClientIp();

    /**
     * ��������� ��� �� ���������� IPv4 ��� IPv6-����� ����������
     * @return bool True ���� IP ����� ���������� ��� ����������, false ���� ���
     */
    function hasClientIp();

    /**
     * ���������� ������ ������ ������������� ���������
     * @return Metadata ���������� ����������� � �������
     */
    function getMetadata();

    /**
     * ��������� ���� �� ����������� ���������� ������
     * @return bool True ���� ���������� ���� �����������, false ���� ���
     */
    function hasMetadata();
}
