<?php
/**
 * Date: 13.08.2019
 * Time: 09:05
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

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../..");

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit(0);

$__time = time();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
try {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        if ($argv[1] == \Democontent2\Pi\Sign::getInstance()->get()) {
            $users = [];
            $push = [];
            $us = new \Democontent2\Pi\User(0);
            $chat = new \Democontent2\Pi\Chat();
            $response = new \Democontent2\Pi\Iblock\Response();
            $itemClass = new \Democontent2\Pi\Iblock\Item();
            $items = $chat->getNotifications();

            if (count($items)) {
                foreach ($items as $item) {
                    if ($item->sendtime > $__time) {
                        continue;
                    }

                    if (($__time - $item->sendtime) < 600) {
                        continue;
                    }

                    $chat->setRoomId($item->roomId);
                    $roomParams = $chat->getRoom(new \MongoDB\BSON\ObjectId($item->roomId));

                    if (count($roomParams)) {
                        $itemClass->setIBlockId($roomParams->iBlockId);
                        $itemClass->setItemId($roomParams->taskId);
                        $nameAndOwner = $itemClass->getTaskNameAndOwner();

                        if (count($nameAndOwner)) {
                            $response->setIBlockId($roomParams->iBlockId);
                            $response->setTaskId($roomParams->taskId);
                            $checkExecutor = $response->checkExecutor();
                            $executorId = $checkExecutor['userId'];
                            if ($executorId > 0) {
                                $us->setId(0);

                                if ($item->userHash == \Democontent2\Pi\Utils::getChatId($executorId)) {
                                    $us->setId($executorId);
                                } else {
                                    if ($item->userHash == \Democontent2\Pi\Utils::getChatId($nameAndOwner['userId'])) {
                                        $us->setId($nameAndOwner['userId']);
                                    }
                                }

                                if ($us->getId()) {
                                    if (!isset($users[$us->getId()])) {
                                        $userParams = $us->get();

                                        if (count($userParams)) {
                                            $users[$us->getId()]['email'] = $userParams['EMAIL'];
                                            $users[$us->getId()]['name'] = $userParams['NAME'];

                                            $push[$us->getId()][] = [
                                                'roomId' => (string)$item->roomId,
                                                'userName' => $userParams['NAME'],
                                                'taskId' => $roomParams->taskId
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $chat->sent($item->_id);
                }
            }

            if (count($users)) {
                if (count($push)) {
                    try {
                        $fireBase = new \Democontent2\Pi\FireBase(0);
                        $fireBase->sendNewMessages($push);
                        unset($fireBase);
                    } catch (\Bitrix\Main\ArgumentNullException $e) {
                    } catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
                    } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                    } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
                    }
                }

                foreach ($users as $userId => $userData) {
                    \Bitrix\Main\Mail\Event::send(
                        [
                            'EVENT_NAME' => 'DSPI_UNREAD_MESSAGES',
                            'LID' => \Bitrix\Main\Config\Option::get(DSPI, 'siteId'),
                            'C_FIELDS' => [
                                'NAME' => $userData['name'],
                                'EMAIL' => $userData['email']
                            ]
                        ]
                    );
                }
            }
        }
    }
} catch (\Exception $e) {
}

die();
