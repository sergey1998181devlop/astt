<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\I;

interface ISkrill
{
    public function make($data);

    public function verify();

    public function getRedirect();

    public function getTransactionId();

    public function getSum();
}