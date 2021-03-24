<?php
/**
 * Date: 19.12.2019
 * Time: 11:07
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */

namespace Democontent2\Pi\I;

interface IClickUZ
{
    public function make($data);

    public function verify();

    public function getRedirect();

    public function getTransactionId();

    public function getSum();
}
