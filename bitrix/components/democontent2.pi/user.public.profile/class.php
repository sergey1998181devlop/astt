<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 18.01.2019
 * Time: 20:03
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserPublicProfileComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $items = new \Democontent2\Pi\Iblock\Items();
        $us = new \Democontent2\Pi\User($this->arParams['id']);
        $reviews = new \Democontent2\Pi\Iblock\Reviews();

        $items->setUserId($this->arParams['id']);
        $reviews->setUserId($this->arParams['id']);

        $this->arResult['USER'] = $us->get();

        $idUserCurrent = $USER->GetID();
        $rsUser = CUser::GetByID($idUserCurrent);
        $arUser = $rsUser->Fetch();

        $this->arResult['MODERATION_CURRENT_USER'] = $arUser['UF_MODERATION_ACCESS'];

        $arUserl = [0 =>  $this->arParams['id'] ];

        $this->arResult['COMPANY'] = $us->getCompanyManager($arUserl);

        if($this->arResult['COMPANY'][0]['ID']){
            $this->arResult['COMPANY_EMPLOYEES'] = $us->getCompanyEmployeesOne($this->arResult['COMPANY'][0]['ID']);

        }





        if (count($this->arResult['USER']) > 0) {
            $portfolioCategory = new \Democontent2\Pi\Profile\Portfolio\Category($this->arResult['USER']['ID'], 86400);
            $profile = new \Democontent2\Pi\Profile\Profile();
            $card = new \Democontent2\Pi\Payments\SafeCrow\Cards();
            $card->setUserId($this->arResult['USER']['ID']);
            $profile->setUserId($this->arResult['USER']['ID']);

            $this->arResult['PORTFOLIO_CATEGORIES'] = $portfolioCategory->getList();
            $this->arResult['PORTFOLIO_CATEGORY_IMAGES'] = [];
            $this->arResult['PORTFOLIO_CATEGORY_SELECTED'] = 0;
            $this->arResult['PROFILE'] = $profile->get();
            $this->arResult['CARD'] = $card->getUserCard();
            $this->arResult['CITY_NAME'] = '';
            $this->arResult['CURRENT_RATING'] = $reviews->rating();
            $this->arResult['TASKS_STAT'] = $items->tasksStat();
            $this->arResult['ITEMS'] = $items->getByUser(true);
            $this->arResult['EXECUTOR_ITEMS'] = $items->getWhereExecutor(true);
            $this->arResult['REVIEWS'] = $reviews->getListByUser();

            if ($request->get('id') && intval($request->get('id')) > 0) {
                if (count($this->arResult['PORTFOLIO_CATEGORIES'])) {
                    $allow = false;
                    foreach ($this->arResult['PORTFOLIO_CATEGORIES'] as $k => $category) {
                        if (intval($category['ID']) == intval($request->get('id'))) {
                            $allow = true;
                            $this->arResult['PORTFOLIO_CATEGORY_IMAGES'] = $portfolioCategory->get($category['ID']);
                            $this->arResult['PORTFOLIO_CATEGORY_SELECTED'] = $category['ID'];
                            break;
                        }
                    }

                    if (!$allow) {
                        foreach ($this->arResult['PORTFOLIO_CATEGORIES'] as $k => $category) {
                            if (!isset($this->arResult['PORTFOLIO_CATEGORY_IMAGES']['files'])
                                || !count($this->arResult['PORTFOLIO_CATEGORY_IMAGES']['files'])) {
                                $this->arResult['PORTFOLIO_CATEGORY_IMAGES'] = $portfolioCategory->get($category['ID']);

                                if (count($this->arResult['PORTFOLIO_CATEGORY_IMAGES']['files'])) {
                                    $this->arResult['PORTFOLIO_CATEGORY_SELECTED'] = $category['ID'];
                                } else {
                                    unset($this->arResult['PORTFOLIO_CATEGORIES'][$k]);
                                }
                            } else {
                                $tempImages = $portfolioCategory->get($category['ID']);
                                if (!count($tempImages['files'])) {
                                    unset($this->arResult['PORTFOLIO_CATEGORIES'][$k]);
                                }
                            }
                        }
                    }
                }
            } else {
                if (count($this->arResult['PORTFOLIO_CATEGORIES'])) {
                    foreach ($this->arResult['PORTFOLIO_CATEGORIES'] as $k => $category) {
                        if (!isset($this->arResult['PORTFOLIO_CATEGORY_IMAGES']['files'])
                            || !count($this->arResult['PORTFOLIO_CATEGORY_IMAGES']['files'])) {
                            $this->arResult['PORTFOLIO_CATEGORY_IMAGES'] = $portfolioCategory->get($category['ID']);

                            if (count($this->arResult['PORTFOLIO_CATEGORY_IMAGES']['files'])) {
                                $this->arResult['PORTFOLIO_CATEGORY_SELECTED'] = $category['ID'];
                            } else {
                                unset($this->arResult['PORTFOLIO_CATEGORIES'][$k]);
                            }
                        } else {
                            $tempImages = $portfolioCategory->get($category['ID']);

                            if (!count($tempImages['files'])) {
                                unset($this->arResult['PORTFOLIO_CATEGORIES'][$k]);
                            }
                        }
                    }
                }
            }

            if (intval($this->arResult['USER']['UF_DSPI_CITY'])) {
                $city = new \Democontent2\Pi\Iblock\City();
                $getCity = $city->getById(intval($this->arResult['USER']['UF_DSPI_CITY']));

                if (count($getCity)) {
                    $this->arResult['CITY_NAME'] = $getCity['name'];
                }
            }

            if ($USER->IsAuthorized() && $USER->GetId() !== $this->arParams['id']) {
                $items->setTtl(0);
                $items->setUserId($USER->GetId());
                $this->arResult['MY_ITEMS'] = $items->getByUser(true);

                if ($request->isPost()) {
                    if ($request->getPost('offer')) {
                        foreach ($this->arResult['MY_ITEMS'] as $item) {
                            if ($item['ID'] == $request->getPost('offer')) {
                                try {
                                    $us->sendAnOffer($item);
                                } catch (\Bitrix\Main\SystemException $e) {
                                }
                                break;
                            }
                        }
                    }

                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                }
            }

            $this->includeComponentTemplate();
        } else {
            CHTTP::SetStatus('404 Not Found');
        }
    }
}