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

namespace YandexCheckout\Model\ConfirmationAttributes;

use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\ConfirmationType;

/**
 * @property bool $enforce ���������� ��������������� ������������� ������� �����������, ���������� 3-D Secure ���
 * ������ ����������� �������. �� ��������� ������������ ��������� ��������� �������.
 * @property string $returnUrl URL �� ������� �������� ���������� ����� ������������� ��� ������ �������
 * �� �������� ��������.
 * @property string $return_url URL �� ������� �������� ���������� ����� ������������� ��� ������ �������
 * �� �������� ��������.
 */
class ConfirmationAttributesRedirect extends AbstractConfirmationAttributes
{
    /**
     * @var bool ���������� ��������������� ������������� ������� �����������, ���������� 3-D Secure ��� ������
     * ����������� �������. �� ��������� ������������ ��������� ��������� �������.
     */
    private $_enforce;

    /**
     * @var string URL �� ������� �������� ���������� ����� ������������� ��� ������ ������� �� �������� ��������.
     */
    private $_returnUrl;

    public function __construct()
    {
        $this->_setType(ConfirmationType::REDIRECT);
    }

    /**
     * @return bool ���������� ��������������� ������������� ������� �����������, ���������� 3-D Secure ���
     * ������ ����������� �������. �� ��������� ������������ ��������� ��������� �������.
     */
    public function getEnforce()
    {
        return $this->_enforce;
    }

    /**
     * @param bool $value ���������� ��������������� ������������� ������� �����������, ���������� 3-D Secure
     * ��� ������ ����������� �������. �� ��������� ������������ ��������� ��������� �������.
     */
    public function setEnforce($value)
    {
        if ($value === null || $value === '') {
            $this->_enforce = null;
        } elseif (TypeCast::canCastToBoolean($value)) {
            $this->_enforce = (bool)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid enforce value type', 0, 'confirmationAttributesRedirect.enforce', $value
            );
        }
    }

    /**
     * @return string URL �� ������� �������� ���������� ����� ������������� ��� ������ ������� �� �������� ��������.
     */
    public function getReturnUrl()
    {
        return $this->_returnUrl;
    }

    /**
     * @param string $value URL �� ������� �������� ���������� ����� ������������� ��� ������ �������
     * �� �������� ��������.
     */
    public function setReturnUrl($value)
    {
        if ($value === null || $value === '') {
            $this->_returnUrl = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_returnUrl = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid returnUrl value type', 0, 'confirmationAttributesRedirect.returnUrl', $value
            );
        }
    }
}
