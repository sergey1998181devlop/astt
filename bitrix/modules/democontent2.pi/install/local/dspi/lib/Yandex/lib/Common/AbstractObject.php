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

if (!interface_exists('JsonSerializable')) {
    require_once dirname(__FILE__) . '/legacy_json_serializable.php';
}

/**
 * ������� ����� ������������ ��������
 *
 * @package YandexCheckout\Common
 */
abstract class AbstractObject implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array �������� ������������� �������������
     */
    private $unknownProperties = array();

    /**
     * ��������� ������� ��������
     * @param string $offset ��� ������������ ��������
     * @return bool True ���� �������� �������, false ���� ���
     */
    public function offsetExists($offset)
    {
        $method = 'get' . ucfirst($offset);
        if (method_exists($this, $method)) {
            return true;
        }
        $method = 'get' . self::matchPropertyName($offset);
        if (method_exists($this, $method)) {
            return true;
        }
        return array_key_exists($offset, $this->unknownProperties);
    }

    /**
     * ���������� �������� ��������
     * @param string $offset ��� ��������
     * @return mixed �������� ��������
     */
    public function offsetGet($offset)
    {
        $method = 'get' . ucfirst($offset);
        if (method_exists($this, $method)) {
            return $this->{$method} ();
        }
        $method = 'get' . self::matchPropertyName($offset);
        if (method_exists($this, $method)) {
            return $this->{$method} ();
        }
        return array_key_exists($offset, $this->unknownProperties) ? $this->unknownProperties[$offset] : null;
    }

    /**
     * ������������� �������� ��������
     * @param string $offset ��� ��������
     * @param mixed $value �������� ��������
     */
    public function offsetSet($offset, $value)
    {
        $method = 'set' . ucfirst($offset);
        if (method_exists($this, $method)) {
            $this->{$method} ($value);
        } else {
            $method = 'set' . self::matchPropertyName($offset);
            if (method_exists($this, $method)) {
                return $this->{$method} ($value);
            } else {
                $this->unknownProperties[$offset] = $value;
            }
        }
    }

    /**
     * ������� ��������
     * @param string $offset ��� ���������� ��������
     */
    public function offsetUnset($offset)
    {
        $method = 'set' . ucfirst($offset);
        if (method_exists($this, $method)) {
            $this->{$method} (null);
        } else {
            $method = 'set' . self::matchPropertyName($offset);
            if (method_exists($this, $method)) {
                $this->{$method} (null);
            } else {
                unset($this->unknownProperties[$offset]);
            }
        }
    }

    /**
     * ���������� �������� ��������
     * @param string $propertyName ��� ��������
     * @return mixed �������� ��������
     */
    public function __get($propertyName)
    {
        return $this->offsetGet($propertyName);
    }

    /**
     * ������������� �������� ��������
     * @param string $propertyName ��� ��������
     * @param mixed $value �������� ��������
     */
    public function __set($propertyName, $value)
    {
        $this->offsetSet($propertyName, $value);
    }

    /**
     * ��������� ������� ��������
     * @param string $propertyName ��� ������������ ��������
     * @return bool True ���� �������� �������, false ���� ���
     */
    public function __isset($propertyName)
    {
        return $this->offsetExists($propertyName);
    }

    /**
     * ������� ��������
     * @param string $propertyName ��� ���������� ��������
     */
    public function __unset($propertyName)
    {
        $this->offsetUnset($propertyName);
    }

    /**
     * ������������� �������� ������� �������� ������� �� �������
     * @param array|\Traversable $sourceArray ������������� ������ � �����������
     */
    public function fromArray($sourceArray)
    {
        foreach ($sourceArray as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * ���������� ������������� ������ �� ���������� �������� ������� ��� ��� ���������� JSON ������������
     * @return array ������������� ������ �� ���������� �������� �������
     */
    public function jsonSerialize()
    {
        $result = array();
        foreach (get_class_methods($this) as $method) {
            if (strncmp('get', $method, 3) === 0) {
                if ($method === 'getUnknownProperties') {
                    continue;
                }
                $property = strtolower(preg_replace('/[A-Z]/', '_\0', lcfirst(substr($method, 3))));
                $value = $this->serializeValueToJson($this->{$method} ());
                if ($value !== null) {
                    $result[$property] = $value;
                }
            }
        }
        if (!empty($this->unknownProperties)) {
            foreach ($this->unknownProperties as $property => $value) {
                if (!array_key_exists($property, $result)) {
                    $result[$property] = $this->serializeValueToJson($value);
                }
            }
        }
        return $result;
    }

    private function serializeValueToJson($value)
    {
        if ($value === null || is_scalar($value) || is_array($value)) {
            return $value;
        } elseif (is_object($value) && $value instanceof \JsonSerializable) {
            return $value->jsonSerialize();
        } elseif (is_object($value) && $value instanceof \DateTime) {
            return $value->format(DATE_ATOM);
        }
        return $value;
    }

    /**
     * ���������� ������ ������� ������� �� ����������, �� ���� ������ � �������
     * @return array ������������� ������ � �� ������������� � �������� ������� ����������
     */
    protected function getUnknownProperties()
    {
        return $this->unknownProperties;
    }

    /**
     * ����������� ��� �������� �� snake_case � camelCase
     * @param string $property ������������� ��������
     * @return string �������� � ����� �����
     */
    private static function matchPropertyName($property)
    {
        return preg_replace('/\_(\w)/', '\1', $property);
    }
}
