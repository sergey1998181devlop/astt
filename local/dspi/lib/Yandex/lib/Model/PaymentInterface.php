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

use YandexCheckout\Model\PaymentMethod\AbstractPaymentMethod;

/**
 * Interface PaymentInterface
 * 
 * @package YandexCheckout\Model
 *
 * @property-read string $id ������������� �������
 * @property-read string $status ������� ��������� �������
 * @property-read RecipientInterface $recipient ���������� �������
 * @property-read AmountInterface $amount ����� ������
 * @property-read AbstractPaymentMethod $paymentMethod ������ ���������� �������
 * @property-read AbstractPaymentMethod $payment_method ������ ���������� �������
 * @property-read \DateTime $createdAt ����� �������� ������
 * @property-read \DateTime $created_at ����� �������� ������
 * @property-read \DateTime $capturedAt ����� ������������� ������� ���������
 * @property-read \DateTime $captured_at ����� ������������� ������� ���������
 * @property-read Confirmation\AbstractConfirmation $confirmation ������ ������������� �������
 * @property-read AmountInterface $refundedAmount ����� ������������ ������� �������
 * @property-read AmountInterface $refunded_amount ����� ������������ ������� �������
 * @property-read bool $paid ������� ������ ������
 * @property-read string $receiptRegistration ��������� ����������� ����������� ����
 * @property-read string $receipt_registration ��������� ����������� ����������� ����
 * @property-read Metadata $metadata ���������� ������� ��������� ���������
 */
interface PaymentInterface
{
    /**
     * ���������� ������������� �������
     * @return string ������������� �������
     */
    function getId();

    /**
     * ���������� ��������� �������
     * @return string ������� ��������� �������
     */
    public function getStatus();

    /**
     * ���������� ���������� �������
     * @return RecipientInterface|null ���������� ������� ��� null ���� ���������� �� �����
     */
    public function getRecipient();

    /**
     * ���������� �����
     * @return AmountInterface ����� �������
     */
    public function getAmount();

    /**
     * ���������� ������������ ������ ���������� �������
     * @return AbstractPaymentMethod ������ ���������� �������
     */
    public function getPaymentMethod();

    /**
     * ���������� ����� �������� ������
     * @return \DateTime ����� �������� ������
     */
    public function getCreatedAt();

    /**
     * ���������� ����� ������������� ������� ��������� ��� null ���� ���� ����� �� ������
     * @return \DateTime|null ����� ������������� ������� ���������
     */
    public function getCapturedAt();

    /**
     * ���������� ������ ������������� �������
     * @return Confirmation\AbstractConfirmation ������ ������������� �������
     */
    public function getConfirmation();

    /**
     * ���������� ����� ������������ �������
     * @return AmountInterface ����� ������������ ������� �������
     */
    public function getRefundedAmount();

    /**
     * ��������� ��� �� ��� ������� �����
     * @return bool ������� ������ ������, true ���� ����� �������, false ���� ���
     */
    public function getPaid();

    /**
     * ���������� ��������� ����������� ����������� ����
     * @return string ��������� ����������� ����������� ����
     */
    public function getReceiptRegistration();

    /**
     * ���������� ���������� ������� ������������� ���������
     * @return Metadata ���������� ������� ��������� ���������
     */
    public function getMetadata();

    /**
     * ���������� ����� �� �������� ����� ��������� �������� ��� ����������� ������ ��� null ���� ��� �� ������
     * @return \DateTime|null �����, �� �������� ����� ��������� �������� ��� ����������� ������
     * @since 1.0.2
     */
    public function getExpiresAt();
}