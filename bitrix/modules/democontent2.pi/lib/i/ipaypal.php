<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 27.03.2019
 * Time: 16:12
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\I;

interface IPayPal
{
    public function make($data);

    public function verify();

    public function getRedirect();

    public function getTransactionId();

    public function getSum();
}