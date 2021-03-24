<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 16:23
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

class Router
{
    private $allowParams = [
        'cache_type',
        'cache_time',
        'namespace',
        'component',
        'template',
        'params'
    ];
    private $map = [];

    /**
     * Router constructor.
     * @param $data
     */
    function __construct($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (!in_array($k, $this->allowParams)) {
                    unset($data[$k]);
                }
            }

            $this->map = $data;
        }
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }
}