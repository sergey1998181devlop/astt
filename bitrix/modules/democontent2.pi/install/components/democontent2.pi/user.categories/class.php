<?php

/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
class UsersCategoriesClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAdmin()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $menu = new \Democontent2\Pi\Iblock\Menu();
            $prices = new \Democontent2\Pi\Iblock\Prices();

            $this->arResult['MENU'] = $menu->get();
            $this->arResult['PRICES'] = $prices->get();

            if ($request->isPost()) {
                if ($request->getPost('category')) {
                    $data = [];

                    foreach ($this->arResult['MENU'] as $k => $v) {
                        if (!isset($v['items']) || !count($v['items'])) {
                            continue;
                        }

                        $data[$k]['item'][0] = 0;
                        $data[$k]['package'][0] = 0;

                        foreach ($v['items'] as $item) {
                            $data[$k]['item'][$item['id']] = 0;
                            $data[$k]['package'][$item['id']] = 0;
                        }
                    }

                    foreach ($request->getPost('category') as $k => $v) {
                        if (!isset($data[$k])) {
                            continue;
                        }

                        if (!isset($v['item']) || !isset($v['package'])) {
                            continue;
                        }

                        if (count($v['item']) !== count($v['package'])) {
                            continue;
                        }

                        foreach ($v['item'] as $itemKey => $itemValue) {
                            if (isset($data[$k]['item'][$itemKey])) {
                                $data[$k]['item'][$itemKey] = intval($itemValue);
                            }

                            if (isset($v['package'][$itemKey])) {
                                if (isset($data[$k]['package'][$itemKey])) {
                                    $data[$k]['package'][$itemKey] = intval($v['package'][$itemKey]);
                                }
                            }
                        }
                    }

                    $prices->save($data);
                }

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            }

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}