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

use YandexCheckout\Common\AbstractRequest;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueException;
use YandexCheckout\Common\Exceptions\InvalidPropertyValueTypeException;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Model\AmountInterface;
use YandexCheckout\Model\PaymentData\AbstractPaymentData;
use YandexCheckout\Model\ConfirmationAttributes\AbstractConfirmationAttributes;
use YandexCheckout\Model\Metadata;
use YandexCheckout\Model\Receipt;
use YandexCheckout\Model\ReceiptInterface;
use YandexCheckout\Model\RecipientInterface;

/**
 * ����� ������� ������� � API �� ���������� ������ �������
 *
 * @package YandexCheckout\Request\Payments
 *
 * @property RecipientInterface $recipient ���������� �������, ���� �����
 * @property AmountInterface $amount ����� ������������ �������
 * @property ReceiptInterface $receipt ������ ����������� ���� 54-��
 * @property string $paymentToken ����������� ����� ��� ���������� ������, �������������� Yandex.Checkout JS widget
 * @property string $payment_token ����������� ����� ��� ���������� ������, �������������� Yandex.Checkout JS widget
 * @property string $paymentMethodId ������������� ������ � ����������� ��������� ������ ����������
 * @property string $payment_method_id ������������� ������ � ����������� ��������� ������ ����������
 * @property AbstractPaymentData $paymentMethodData ������ ������������ ��� �������� ������ ������
 * @property AbstractPaymentData $payment_method_data ������ ������������ ��� �������� ������ ������
 * @property AbstractConfirmationAttributes $confirmation ������ ������������� �������
 * @property bool $savePaymentMethod ��������� ��������� ������ ��� ������������ �������������. �������� true
 * ���������� �������� ������������� payment_method.
 * @property bool $save_payment_method ��������� ��������� ������ ��� ������������ �������������. �������� true
 * ���������� �������� ������������� payment_method.
 * @property bool $capture ������������� ������� ����������� ������
 * @property string $clientIp IPv4 ��� IPv6-����� ����������. ���� �� ������, ������������ IP-����� TCP-�����������.
 * @property string $client_ip IPv4 ��� IPv6-����� ����������. ���� �� ������, ������������ IP-����� TCP-�����������.
 * @property Metadata $metadata ���������� ����������� � �������
 */
class CreatePaymentRequest extends AbstractRequest implements CreatePaymentRequestInterface
{
    /**
     * @var RecipientInterface ���������� �������
     */
    private $_recipient;

    /**
     * @var AmountInterface ����� �������
     */
    private $_amount;

    /**
     * @var Receipt ������ ����������� ���� 54-��
     */
    private $_receipt;

    /**
     * @var string ����������� ����� ��� ���������� ������, �������������� Yandex.Checkout JS widget
     */
    private $_paymentToken;

    /**
     * @var string ������������� ������ � ����������� ��������� ������ ����������
     */
    private $_paymentMethodId;

    /**
     * @var AbstractPaymentData ������ ������������ ��� �������� ������ ������
     */
    private $_paymentMethodData;

    /**
     * @var AbstractConfirmationAttributes ������ ������������� �������
     */
    private $_confirmation;

    /**
     * @var bool ��������� ��������� ������ ��� ������������ �������������. �������� true ���������� �������� ������������� payment_method.
     */
    private $_savePaymentMethod;

    /**
     * @var bool ������������� ������� ����������� ������
     */
    private $_capture;

    /**
     * @var string IPv4 ��� IPv6-����� ����������. ���� �� ������, ������������ IP-����� TCP-�����������.
     */
    private $_clientIp;

    /**
     * @var Metadata ���������� ����������� � �������
     */
    private $_metadata;

    /**
     * ���������� ������ ���������� �������
     * @return RecipientInterface|null ������ � ����������� � ���������� ������� ��� null ���� ���������� �� �����
     */
    public function getRecipient()
    {
        return $this->_recipient;
    }

    /**
     * ��������� ������� ���������� ������� � �������
     * @return bool True ���� ���������� ������� �����, false ���� ���
     */
    public function hasRecipient()
    {
        return !empty($this->_recipient);
    }

    /**
     * ���������� ����� ������
     * @return AmountInterface ����� ������
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * ������������� ����� �������
     * @param AmountInterface $value ����� �������
     */
    public function setAmount(AmountInterface $value)
    {
        $this->_amount = $value;
    }

    /**
     * ���������� ���, ���� �� ����
     * @return ReceiptInterface|null ������ ����������� ���� 54-�� ��� null ���� ���� ���
     */
    public function getReceipt()
    {
        return $this->_receipt;
    }

    /**
     * ������������� ���
     * @param ReceiptInterface $value ������ ����������� ���� 54-��
     */
    public function setReceipt(ReceiptInterface $value)
    {
        $this->_receipt = $value;
    }

    /**
     * ��������� ������� ���� � ����������� �������
     * @return bool True ���� ��� ����, false ���� ���
     */
    public function hasReceipt()
    {
        return $this->_receipt !== null;
    }

    /**
     * ������� ��� �� �������
     */
    public function removeReceipt()
    {
        $this->_receipt = null;
    }

    /**
     * ������������� ������ � ����������� � ���������� �������
     * @param RecipientInterface|null $value ������� ������� ���������� � ���������� ������� ��� null
     */
    public function setRecipient($value)
    {
        if ($value === null || $value === '') {
            $this->_recipient = null;
        } elseif (is_object($value) && $value instanceof RecipientInterface) {
            $this->_recipient = $value;
        } else {
            throw new \InvalidArgumentException('Invalid recipient value type');
        }
    }

    /**
     * ���������� ����������� ����� ��� ���������� ������
     * @return string ����������� ����� ��� ���������� ������, �������������� Yandex.Checkout JS widget
     */
    public function getPaymentToken()
    {
        return $this->_paymentToken;
    }

    /**
     * ��������� ������� ������������ ������ ��� ���������� ������
     * @return bool True ���� ����� ����������, false ���� ���
     */
    public function hasPaymentToken()
    {
        return !empty($this->_paymentToken);
    }

    /**
     * ������������� ����������� ����� ��� ���������� ������, �������������� Yandex.Checkout JS widget
     * @param string $value ����������� ����� ��� ���������� ������
     *
     * @throws InvalidPropertyValueException ������������� ���� ���������� �������� ������ 200 ��������
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� �������
     */
    public function setPaymentToken($value)
    {
        if ($value === null || $value === '') {
            $this->_paymentToken = null;
        } elseif (TypeCast::canCastToString($value)) {
            $length = mb_strlen((string)$value, 'utf-8');
            if ($length > 10240) {
                throw new InvalidPropertyValueException(
                    'Invalid paymentToken value', 0, 'CreatePaymentRequest.paymentToken', $value
                );
            }
            $this->_paymentToken = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid paymentToken value type', 0, 'CreatePaymentRequest.paymentToken', $value
            );
        }
    }

    /**
     * ������������� ������������� ������ �������� ������ ����������
     * @return string ������������� ������ � ����������� ��������� ������ ����������
     */
    public function getPaymentMethodId()
    {
        return $this->_paymentMethodId;
    }

    /**
     * ��������� ������� �������������� ������ � �������� ������ ����������
     * @return bool True ���� ������������� �����, false ���� ���
     */
    public function hasPaymentMethodId()
    {
        return !empty($this->_paymentMethodId);
    }

    /**
     * ������������� ������������� ������ � ���������� ������ ����������
     * @param string $value ������������� ������ � ����������� ��������� ������ ����������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���������� �������� �� �������� ������� ��� null
     */
    public function setPaymentMethodId($value)
    {
        if ($value === null || $value === '') {
            $this->_paymentMethodId = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_paymentMethodId = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid paymentMethodId value type in CreatePaymentRequest',
                0,
                'CreatePaymentRequest.CreatePaymentRequest',
                $value
            );
        }
    }

    /**
     * ���������� ������ ��� �������� ������ ������
     * @return AbstractPaymentData ������ ������������ ��� �������� ������ ������
     */
    public function getPaymentMethodData()
    {
        return $this->_paymentMethodData;
    }

    /**
     * ��������� ���������� �� ������ � ������� ������
     * @return bool True ���� ������ ������ ������ ����������, false ���� ���
     */
    public function hasPaymentMethodData()
    {
        return !empty($this->_paymentMethodData);
    }

    /**
     * ������������� ������ � ����������� ��� �������� ������ ������
     * @param AbstractPaymentData|null $value ������ � �������� ������ ������ ��� null
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ��� ������� ������ ����������� ����
     */
    public function setPaymentMethodData($value)
    {
        if ($value === null || $value === '') {
            $this->_paymentMethodData = null;
        } elseif (is_object($value) && $value instanceof AbstractPaymentData) {
            $this->_paymentMethodData = $value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid paymentMethodData value type in CreatePaymentRequest',
                0,
                'CreatePaymentRequest.paymentMethodData',
                $value
            );
        }
    }

    /**
     * ���������� ������ ������������� �������
     * @return AbstractConfirmationAttributes ������ ������������� �������
     */
    public function getConfirmation()
    {
        return $this->_confirmation;
    }

    /**
     * ��������� ��� �� ���������� ������ ������������� �������
     * @return bool True ���� ������ ������������� ������� ��� ����������, false ���� ���
     */
    public function hasConfirmation()
    {
        return $this->_confirmation !== null;
    }

    /**
     * ������������� ������ ������������� �������
     * @param AbstractConfirmationAttributes|null $value ������ ������������� �������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� �������� ����
     * AbstractConfirmationAttributes ��� null
     */
    public function setConfirmation($value)
    {
        if ($value === null || $value === '') {
            $this->_confirmation = null;
        } elseif (is_object($value) && $value instanceof AbstractConfirmationAttributes) {
            $this->_confirmation = $value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid confirmation value type in CreatePaymentRequest',
                0,
                'CreatePaymentRequest.confirmation',
                $value
            );
        }
    }

    /**
     * ���������� ���� ���������� �������� ������
     * @return bool ���� ���������� �������� ������
     */
    public function getSavePaymentMethod()
    {
        return $this->_savePaymentMethod;
    }

    /**
     * ��������� ��� �� ���������� ���� ���������� �������� ������
     * @return bool True ���� ���� ��� ����������, false ���� ���
     */
    public function hasSavePaymentMethod()
    {
        return $this->_savePaymentMethod !== null;
    }

    /**
     * ������������� ���� ���������� �������� ������. �������� true ���������� �������� ������������� payment_method.
     * @param bool $value ��������� ��������� ������ ��� ������������ �������������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���������� �������� �� �������� � bool
     */
    public function setSavePaymentMethod($value)
    {
        if ($value === null || $value === '') {
            $this->_savePaymentMethod = null;
        } elseif (TypeCast::canCastToBoolean($value)) {
            $this->_savePaymentMethod = (bool)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid savePaymentMethod value type in CreatePaymentRequest',
                0,
                'CreatePaymentRequest.savePaymentMethod',
                $value
            );
        }
    }

    /**
     * ���������� ���� ��������������� �������� ����������� ������
     * @return bool True ���� ��������� ������������� ������� ����������� ������, false ���� ���
     */
    public function getCapture()
    {
        return $this->_capture;
    }

    /**
     * ��������� ��� �� ���������� ���� ��������������� ������� ����������� ������
     * @return bool True ���� ���� ��������������� �������� ������ ��� ����������, false ���� ���
     */
    public function hasCapture()
    {
        return $this->_capture !== null;
    }

    /**
     * ������������� ���� ��������������� �������� ����������� ������
     * @param bool $value ������������� ������� ����������� ������
     *
     * @throws InvalidPropertyValueTypeException ������������ ���� ���������� �������� �� �������� � bool
     */
    public function setCapture($value)
    {
        if ($value === null || $value === '') {
            $this->_capture = null;
        } elseif (TypeCast::canCastToBoolean($value)) {
            $this->_capture = (bool)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid capture value type in CreatePaymentRequest', 0, 'CreatePaymentRequest.capture', $value
            );
        }
    }

    /**
     * ���������� IPv4 ��� IPv6-����� ����������
     * @return string IPv4 ��� IPv6-����� ����������
     */
    public function getClientIp()
    {
        return $this->_clientIp;
    }

    /**
     * ��������� ��� �� ���������� IPv4 ��� IPv6-����� ����������
     * @return bool True ���� IP ����� ���������� ��� ����������, false ���� ���
     */
    public function hasClientIp()
    {
        return $this->_clientIp !== null;
    }

    /**
     * ������������� IP ����� ����������
     * @param string $value IPv4 ��� IPv6-����� ����������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� �������� �� �������� �������
     */
    public function setClientIp($value)
    {
        if ($value === null || $value === '') {
            $this->_clientIp = null;
        } elseif (TypeCast::canCastToString($value)) {
            $this->_clientIp = (string)$value;
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid clientIp value type in CreatePaymentRequest', 0, 'CreatePaymentRequest.clientIp', $value
            );
        }
    }

    /**
     * ���������� ������ ������ ������������� ���������
     * @return Metadata ����������, ����������� � �������
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * ��������� ���� �� ����������� ���������� ������
     * @return bool True ���� ���������� ���� �����������, false ���� ���
     */
    public function hasMetadata()
    {
        return !empty($this->_metadata) && $this->_metadata->count() > 0;
    }

    /**
     * ������������� ����������, ����������� � �������
     * @param Metadata|array|null $value ���������� �������, ��������������� ���������
     *
     * @throws InvalidPropertyValueTypeException ������������� ���� ���������� ������ �� ������� ���������������� ���
     * ���������� �������
     */
    public function setMetadata($value)
    {
        if ($value === null || (is_array($value) && empty($value))) {
            $this->_metadata = null;
        } elseif (is_object($value) && $value instanceof Metadata) {
            $this->_metadata = $value;
        } elseif (is_array($value)) {
            $this->_metadata = new Metadata();
            foreach ($value as $key => $val) {
                $this->_metadata->offsetSet($key, (string)$val);
            }
        } else {
            throw new InvalidPropertyValueTypeException(
                'Invalid metadata value type in CreatePaymentRequest', 0, 'CreatePaymentRequest.metadata', $value
            );
        }
    }

    /**
     * ��������� �� ���������� ������� ������
     * @return bool True ���� ������ ������� �������, false ���� ���
     */
    public function validate()
    {
        $amount = $this->_amount;
        if ($amount === null) {
            $this->setValidationError('Payment amount not specified');
            return false;
        }
        if ($amount->getValue() <= 0.0) {
            $this->setValidationError('Invalid payment amount value: ' . $amount->getValue());
            return false;
        }
        if ($this->_receipt !== null && $this->_receipt->notEmpty()) {
            $email = $this->_receipt->getEmail();
            $phone = $this->_receipt->getPhone();
            if (empty($email) && empty($phone)) {
                $this->setValidationError('Both email and phone values are empty in receipt');
                return false;
            }
            if ($this->_receipt->getTaxSystemCode() === null) {
                foreach ($this->_receipt->getItems() as $item) {
                    if ($item->getVatCode() === null) {
                        $this->setValidationError('Item vat_id and receipt tax_system_id not specified');
                        return false;
                    }
                }
            }
        }
        if ($this->hasPaymentToken()) {
            if ($this->hasPaymentMethodId()) {
                $this->setValidationError('Both paymentToken and paymentMethodID values are specified');
                return false;
            }
            if ($this->hasPaymentMethodData()) {
                $this->setValidationError('Both paymentToken and paymentData values are specified');
                return false;
            }
        } elseif ($this->hasPaymentMethodId()) {
            if ($this->hasPaymentMethodData()) {
                $this->setValidationError('Both paymentMethodID and paymentData values are specified');
                return false;
            }
        }
        return true;
    }

    /**
     * ���������� ������ �������� �������� �������� �������
     * @return CreatePaymentRequestBuilder ������� ������� �������� �������
     */
    public static function builder()
    {
        return new CreatePaymentRequestBuilder();
    }
}
