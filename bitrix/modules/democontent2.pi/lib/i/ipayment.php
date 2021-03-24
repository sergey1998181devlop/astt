<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\I;

interface IPayment
{
    public function getRedirect();

    public function make();

    public function verify();
}