<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 17.09.2018
 * Time: 17:42
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\I;

interface IFavorites
{
    public function add($productId);

    public function remove($productId);

    public function getList();

    public function checkItem($productId);
}