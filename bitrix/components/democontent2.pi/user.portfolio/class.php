<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.04.2019
 * Time: 18:13
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class PortfolioComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $portfolioCategory = new \Democontent2\Pi\Profile\Portfolio\Category($USER->GetID());

            $this->arResult['CATEGORIES'] = $portfolioCategory->getList();
            $this->arResult['CATEGORY'] = [];

            if ($request->isPost()) {
                $portfolioCategory->add($request->getPostList());

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            } else {
                if ($request->get('id') && intval($request->get('id'))) {
                    $this->arResult['CATEGORY'] = $portfolioCategory->get(intval($request->get('id')));

                    if (!count($this->arResult['CATEGORY'])) {
                        LocalRedirect(SITE_DIR . 'user/portfolio/', true);
                    }
                }
            }

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}