<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi;

use Bitrix\Main\Config\Option;

class Sign
{
    private static $_instance = null;

    private function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * @return Sign|null
     */
    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function get()
    {
        return Option::get(DSPI, 'sign');
    }
}