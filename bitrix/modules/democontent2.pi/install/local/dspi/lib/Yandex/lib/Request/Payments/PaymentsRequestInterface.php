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

/**
 * Interface PaymentsRequestInterface
 *
 * @package YandexCheckout\Request\Payments
 *
 * @property-read string|null $paymentId ������������� �������
 * @property-read string|null $accountId ������������� ��������
 * @property-read string|null $gatewayId ������������� �����
 * @property-read \DateTime|null $createdGte ����� ��������, �� (������������)
 * @property-read \DateTime|null $createdGt ����� ��������, �� (�� �������)
 * @property-read \DateTime|null $createdLte ����� ��������, �� (������������)
 * @property-read \DateTime|null $createdLt ����� ��������, �� (�� �������)
 * @property-read \DateTime|null $authorizedGte ����� ���������� ��������, �� (������������)
 * @property-read \DateTime|null $authorizedGt ����� ���������� ��������, �� (�� �������)
 * @property-read \DateTime|null $authorizedLte ����� ����������, �� (������������)
 * @property-read \DateTime|null $authorizedLt ����� ����������, �� (�� �������)
 * @property-read string|null $status ������ �������
 * @property-read string|null $nextPage ����� ��� ��������� ��������� �������� �������
 */
interface PaymentsRequestInterface
{
    /**
     * ���������� ������������� ������� ���� �� ����� ��� null
     * @return string|null ������������� �������
     */
    function getPaymentId();

    /**
     * ���������, ��� �� ����� ������������� �������
     * @return bool True ���� ������������� ��� �����, false ���� ���
     */
    function hasPaymentId();

    /**
     * ���������� ������������� ��������, ���� �� ��� �����
     * @return string|null ������������� ��������
     */
    function getAccountId();

    /**
     * ���������, ��� �� ���������� ������������� ��������
     * @return bool True ���� ������������� �������� ��� ����������, false ���� ���
     */
    function hasAccountId();

    /**
     * ���������� ������������� �����
     * @return string|null ������������� �����
     */
    function getGatewayId();

    /**
     * ��������� ��� �� ���������� ������������� �����
     * @return bool True ���� ������������� ����� ��� ����������, false ���� ���
     */
    function hasGatewayId();

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (������������)
     */
    function getCreatedGte();

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasCreatedGte();

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (�� �������)
     */
    function getCreatedGt();

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasCreatedGt();

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (������������)
     */
    function getCreatedLte();

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasCreatedLte();

    /**
     * ���������� ���� �������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ��������, �� (�� �������)
     */
    function getCreatedLt();

    /**
     * ��������� ���� �� ����������� ���� �������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasCreatedLt();

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ���������� ��������, �� (������������)
     */
    function getAuthorizedGte();

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasAuthorizedGte();

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ���������� ��������, �� (�� �������)
     */
    function getAuthorizedGt();

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasAuthorizedGt();

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ��� null ���� ���� �� ���� �����������
     * @return \DateTime|null ����� ����������, �� (������������)
     */
    function getAuthorizedLte();

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ���������� �������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasAuthorizedLte();

    /**
     * ���������� ���� ���������� �� ������� ����� ���������� ������� ������� ��� null ���� ��� �� ���� �����������
     * @return \DateTime|null ����� ����������, �� (�� �������)
     */
    function getAuthorizedLt();

    /**
     * ��������� ���� �� ����������� ���� ���������� �� ������� ����������
     * @return bool True ���� ���� ���� �����������, false ���� ���
     */
    function hasAuthorizedLt();

    /**
     * ���������� ������ ���������� �������� ��� null ���� �� �� ����� �� ��� ����������
     * @return string|null ������ ���������� ��������
     */
    function getStatus();

    /**
     * ��������� ��� �� ���������� ������ ���������� ��������
     * @return bool True ���� ������ ��� ����������, false ���� ���
     */
    function hasStatus();

    /**
     * ���������� ����� ��� ��������� ��������� �������� �������
     * @return string|null ����� ��� ��������� ��������� �������� �������
     */
    function getNextPage();

    /**
     * ��������� ��� �� ���������� ����� ��������� ��������
     * @return bool True ���� ����� ��� ����������, false ���� ���
     */
    function hasNextPage();
}
