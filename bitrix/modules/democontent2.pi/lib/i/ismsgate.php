<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\I;

interface ISmsGate
{
    public function getError();

    public function getLog();

    public function setPhone($phone);

    public function setText($text);

    public function make();
}