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

/**
 * Interface RefundInterface
 *
 * @package YandexCheckout\Model
 *
 * @property-read string $id ������������� �������� �������
 * @property-read string $paymentId ������������� �������
 * @property-read string $payment_id ������������� �������
 * @property-read string $status ������ ��������
 * @property-read \DateTime $createdAt ����� �������� ��������
 * @property-read \DateTime $create_at ����� �������� ��������
 * @property-read AmountInterface $amount ����� ��������
 * @property-read string $receiptRegistration ������ ����������� ����
 * @property-read string $receipt_registration ������ ����������� ����
 * @property-read string $comment �����������, ��������� ��� �������� ������� ����������
 */
interface RefundInterface
{
    /**
     * ���������� ������������� �������� �������
     * @return string ������������� ��������
     */
    function getId();

    /**
     * ���������� ������������� �������
     * @return string ������������� �������
     */
    function getPaymentId();

    /**
     * ���������� ������ �������� ��������
     * @return string ������ ��������
     */
    function getStatus();

    /**
     * ���������� ���� �������� ��������
     * @return \DateTime ����� �������� ��������
     */
    function getCreatedAt();

    /**
     * ���������� ����� ��������
     * @return AmountInterface ����� ��������
     */
    function getAmount();

    /**
     * ���������� ������ ����������� ����
     * @return string ������ ����������� ����
     */
    function getReceiptRegistration();

    /**
     * ���������� ����������� � ��������
     * @return string �����������, ��������� ��� �������� ������� ����������
     */
    function getComment();
}
