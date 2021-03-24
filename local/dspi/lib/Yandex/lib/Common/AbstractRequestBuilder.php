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

namespace YandexCheckout\Common;

use YandexCheckout\Common\Exceptions\InvalidPropertyException;
use YandexCheckout\Common\Exceptions\InvalidRequestException;

/**
 * ������� ����� ������� ��������
 *
 * @package YandexCheckout\Common
 */
abstract class AbstractRequestBuilder
{
    /**
     * @var AbstractRequest ������� ����������� �������
     */
    protected $currentObject;

    /**
     * �����������, �������������� ������ ������, ������� � ������� ����� ��������
     */
    public function __construct()
    {
        $this->currentObject = $this->initCurrentObject();
    }

    /**
     * �������������� ������ ������
     * @return AbstractRequest ������� ������� ������� ����� ��������
     */
    abstract protected function initCurrentObject();

    /**
     * ������ ������, ���������� ��� � ����������, ���� ��� ������ ���������
     * @param array $options ������ ������� �������, ���� ����� �� ���������� ����� �������
     * @return AbstractRequest ������� ���������� �������
     *
     * @throws InvalidRequestException ������������� ���� ��� ��������� ������� ��������� ������
     * @throws InvalidPropertyException ������������� ���� �� ������� ���������� ���� �� ����������, ���������� �
     * ������� ��������
     */
    public function build(array $options = null)
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
        try {
            $this->currentObject->clearValidationError();
            if (!$this->currentObject->validate()) {
                throw new InvalidRequestException($this->currentObject);
            }
        } catch (InvalidRequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new InvalidRequestException($this->currentObject, 0, $e);
        }
        $result = $this->currentObject;
        $this->currentObject = $this->initCurrentObject();
        return $result;
    }

    /**
     * ������������� �������� ������� �� �������
     * @param array|\Traversable $options ������ ������� �������
     * @return AbstractRequestBuilder ������� �������� ������� ��������
     *
     * @throws \InvalidArgumentException ������������� ���� �������� �� ������ � �� ����������� ������
     * @throws InvalidPropertyException ������������� ���� �� ������� ���������� ���� �� ����������, ����������
     * � ������� ��������
     */
    public function setOptions($options)
    {
        if (empty($options)) {
            return $this;
        }
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new \InvalidArgumentException('Invalid options value in setOptions method');
        }
        foreach ($options as $property => $value) {
            $method = 'set' . ucfirst($property);
            if (method_exists($this, $method)) {
                $this->{$method} ($value);
            } else {
                $tmp = preg_replace('/\_(\w)/', '\1', $property);
                $method = 'set' . ucfirst($tmp);
                if (method_exists($this, $method)) {
                    $this->{$method} ($value);
                }
            }
        }
        return $this;
    }
}
