<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:28
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserCreateComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        $this->arResult['CITY_ID'] = 0;
        $this->arResult['ALLOW_CHECKLISTS'] = intval(\Bitrix\Main\Config\Option::get(DSPI, 'response_checklists'));

        try {
            $this->arResult['COURIER_IBLOCKS'] = \Bitrix\Main\Web\Json::decode(
                \Bitrix\Main\Config\Option::get(DSPI, 'courierIblocks')
            );
        } catch (\Bitrix\Main\ArgumentNullException $e) {
        } catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
        } catch (\Bitrix\Main\ArgumentException $e) {
            $this->arResult['COURIER_IBLOCKS'] = [];
        }

        if ($USER->IsAuthorized()) {
            $menu = new \Democontent2\Pi\Iblock\Menu();
            $city = new \Democontent2\Pi\Iblock\City();

            $this->arResult['CITIES'] = $city->getList();
            $this->arResult['CATEGORIES'] = $menu->get();

            if (intval($request->getCookie('current_city')) > 0) {
                if (isset($this->arResult['CITIES'][intval($request->getCookie('current_city'))])) {
                    $this->arResult['CITY_ID'] = intval($request->getCookie('current_city'));
                }
            }

            if ($request->isPost()) {
                $item = new \Democontent2\Pi\Iblock\Item(0);
                $item->setUserId(intval($USER->GetID()));
                $item->create($_POST, $_FILES);

                if (strlen($item->getPaymentRedirect()) > 0) {
                    LocalRedirect($item->getPaymentRedirect(), true);
                } else {
                    if ($item->getItemId() > 0 && $item->getIBlockId() > 0) {
                        LocalRedirect(SITE_DIR . 'user/items/' . $item->getIBlockId() . '-' . $item->getItemId() . '/', true);
                    }
                }

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            }

            $this->includeComponentTemplate();
        } else {
            if ($request->getCookie('nonameUser') && strlen($request->getCookie('nonameUser')) == 32) {
                $menu = new \Democontent2\Pi\Iblock\Menu();
                $city = new \Democontent2\Pi\Iblock\City();
                $this->arResult['CITIES'] = $city->getList();
                $this->arResult['CATEGORIES'] = $menu->get();

                if (intval($request->getCookie('current_city')) > 0) {
                    if (isset($this->arResult['CITIES'][intval($request->getCookie('current_city'))])) {
                        $this->arResult['CITY_ID'] = intval($request->getCookie('current_city'));
                    }
                }

                if ($request->isPost()) {
                    $item = new \Democontent2\Pi\Iblock\Item(0);
                    $item->setUserId(intval($USER->GetID()));
                    $item->addToTemp($_POST, $_FILES, $request->getCookie('nonameUser'));

                    LocalRedirect($APPLICATION->GetCurPage(false) . '?needAuth=1', true);
                }

                $this->setTemplateName('anonymous');
                $this->includeComponentTemplate();
            } else {
                LocalRedirect(SITE_DIR, true);
            }
        }
    }
}