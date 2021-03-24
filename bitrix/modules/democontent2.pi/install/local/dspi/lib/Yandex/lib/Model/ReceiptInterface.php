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
 * Interface ReceiptInterface
 * 
 * @package YandexCheckout\Model
 * 
 * @property-read ReceiptItemInterface[] $items ������ ������� � ������
 * @property-read int $taxSystemCode ��� ������� ���������������. ����� 1-6.
 * @property-read int $tax_system_code ��� ������� ���������������. ����� 1-6.
 * @property-read string $phone ����� �������� ����������� � ������� ITU-T E.164 �� ������� ����� ������ ���.
 * @property-read string $email E-mail ����� ����������� �� ������� ����� ������ ���.
 */
interface ReceiptInterface
{
    /**
     * ���������� ������ ������� � ������� ����
     *
     * @return ReceiptItemInterface[] ������ ������� � ������
     */
    function getItems();

    /**
     * ���������� ��� ������� ���������������
     *
     * @return int ��� ������� ���������������. ����� 1-6.
     */
    function getTaxSystemCode();

    /**
     * ���������� ����� �������� ����������� � ������� ITU-T E.164 �� ������� ����� ������ ���
     *
     * @return string ����� �������� �����������
     */
    function getPhone();

    /**
     * ���������� ����� ����������� ����� �� ������� ����� ������ ���
     *
     * @return string E-mail ����� �����������
     */
    function getEmail();

    /**
     * ��������� ���� �� � ���� ���� �� ���� �������
     * @return bool True ���� ��� �� ����, false ���� � ���� ��� �� ����� �������
     */
    function notEmpty();
}