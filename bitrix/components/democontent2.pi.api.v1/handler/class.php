<?php
/**
 * Date: 14.07.2019
 * Time: 19:14
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

use Democontent2\Pi\ErrorLog;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class HandlerComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $errorCode = 1;
        $errorMessage = 'Unknown Error';
        $result = [];
        $extra = [];
        $allow = true;

        if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'apiEnabled'))) {
            $protocolAllow = true;

            if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'apiOnlyHttps'))) {
                if (!$request->isHttps()) {
                    $protocolAllow = false;
                }
            }

            if ($protocolAllow) {
                if ($this->arParams['method']) {
                    if (isset(\Democontent2\Pi\Api\V1\Methods::LIST[$this->arParams['method']])) {
                        switch (\Democontent2\Pi\Api\V1\Methods::LIST[$this->arParams['method']]['method']) {
                            case 'GET':
                                if ($request->getRequestMethod() !== 'GET') {
                                    $allow = false;
                                }
                                break;
                            case 'POST':
                                if ($request->getRequestMethod() !== 'POST') {
                                    $allow = false;
                                }
                                break;
                            default:
                                if ($request->getRequestMethod() !== 'OPTIONS') {
                                    $allow = false;
                                }
                        }

                        if ($allow) {
                            try {
                                if (strlen(\Democontent2\Pi\Api\V1\Methods::LIST[$this->arParams['method']]['alias'])) {
                                    $class = '\Democontent2\Pi\Api\V1\\' . \Democontent2\Pi\Api\V1\Methods::LIST[$this->arParams['method']]['alias'];
                                } else {
                                    $class = '\Democontent2\Pi\Api\V1\\' . ucfirst($this->arParams['method']);
                                }

                                if (class_exists($class)) {
                                    $obj = new $class();
                                    $obj->run($request);
                                    $errorCode = $obj->getErrorCode();
                                    $errorMessage = $obj->getErrorMessage();
                                    $result = $obj->getResult();
                                    $extra = $obj->getExtra();
                                } else {
                                    $errorMessage = 'Method does not exists';
                                }
                            } catch (\Exception $e) {
                                ErrorLog::add(0, 'apiException', [$e->getMessage()]);
                            }
                        } else {
                            $errorMessage = 'Invalid Request';
                        }
                    } else {
                        $errorMessage = 'Method does not exists';
                    }
                } else {
                    $errorMessage = 'Invalid Request';
                }
            } else {
                $errorMessage = 'HTTPS only';
            }
        } else {
            $errorMessage = 'Service is Unavailable';
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, HEAD');
        header('Access-Control-Allow-Headers: X-PI-KEY, X-PI-EMAIL, X-PI-PASSWORD, Content-Type, X-Requested-With, Access-Control-Allow-Headers, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers');
        header('Content-Type: application/json');

        if ($errorCode) {
            CHTTP::SetStatus('400 Bad Request');
        }

        if (ToUpper($request->getRequestMethod()) == 'OPTIONS') {
            CHTTP::SetStatus('204');
        } else {
            echo \Bitrix\Main\Web\Json::encode(
                [
                    'errorCode' => $errorCode,
                    'errorMessage' => $errorMessage,
                    'result' => $result,
                    'extra' => $extra
                ]
            );
        }
    }
}
