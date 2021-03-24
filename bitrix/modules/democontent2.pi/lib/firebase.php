<?php
/**
 * Date: 13.09.2019
 * Time: 13:16
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

namespace Democontent2\Pi;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;
use MongoDB\Client;

try {
    if (File::isFileExists(Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/vendor/autoload.php'))) {
        include_once Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/vendor/autoload.php');
    }
} catch (InvalidPathException $e) {
}

class FireBase
{
    const COLLECTION_NAME = 'firebase_tokens';

    private $userId = 0;
    private $config = null;
    private $client = null;

    /**
     * FireBase constructor.
     * @param int $userId
     * @throws InvalidPathException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function __construct(int $userId)
    {
        $this->userId = intval($userId);
        if (strlen(Option::get(DSPI, 'firebase_config'))) {
            $this->config = ServiceAccount::fromJsonFile(
                Path::normalize(Application::getDocumentRoot() . '/' . Option::get(DSPI, 'firebase_config') . '.json')
            );

            $this->client = new Client(Utils::__mongoDbConnectString());
        }
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTokens($app = false)
    {
        $results = [];
        if (!is_null($this->client)) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_NAME);
            if ($app) {
                $find = $db->find(['type' => 'app']);
            } else {
                $find = $db->find([]);
            }

            foreach ($find as $item) {
                $results[$item['userId']][] = $item['token'];
            }
        }

        return $results;
    }

    private function deleteToken(int $userId, string $token)
    {
        if (!is_null($this->client)) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_NAME);
            $db->deleteMany([
                'userId' => $userId,
                'token' => $token
            ]);
        }
    }

    /**
     * @param array $data
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function webPush(array $data)
    {
        if (!is_null($this->client) && $this->userId > 0) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_NAME);
            $find = $db->find([
                'userId' => $this->userId,
                'type' => 'web'
            ]);

            foreach ($find as $item) {
                $firebase = (new Factory)
                    ->withServiceAccount($this->config)
                    ->withDatabaseUri(Option::get(DSPI, 'firebase_url'))
                    ->create();

                $messaging = $firebase->getMessaging();
                $message = CloudMessage::fromArray([
                    'token' => $item['token'],
                    'time_to_live' => 900,
                    'notification' => [
                        'title' => $data['title'],
                        'body' => $data['body'],
                    ],
                    'data' => [
                        'title' => $data['title'],
                        'body' => $data['body'],
                        'click_action' => isset($data['url']) ? $data['url'] : null,
                    ],
                    'webpush' => [
                        'notification' => [
                            'title' => $data['title'],
                            'body' => $data['body'],
                            'icon' => Option::get(DSPI, 'firebase_web_push_icon')
                        ],
                        'fcm_options' => [
                            'link' => isset($data['url']) ? $data['url'] : null
                        ]
                    ]
                ]);

                $messaging->send($message);
                unset($firebase, $messaging, $message);

                break;
            }
        }
    }

    /**
     * @param array $data
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function sendNewTasks(array $data)
    {
        if (!is_null($this->config)) {
            $tokens = $this->getTokens();
            if (count($tokens)) {
                $firebase = (new Factory)
                    ->withServiceAccount($this->config)
                    ->withDatabaseUri(Option::get(DSPI, 'firebase_url'))
                    ->create();

                $messaging = $firebase->getMessaging();

                foreach ($data as $taskId => $taskParams) {
                    foreach ($taskParams['USERS'] as $userId) {
                        if (!isset($tokens[$userId])) {
                            continue;
                        }

                        foreach ($tokens[$userId] as $userToken) {
                            try {
                                $this->pushTask(
                                    $messaging,
                                    $userToken,
                                    Loc::getMessage('FIREBASE_NEW_TASKS_TITLE'),
                                    $taskParams['UF_NAME'],
                                    '/public/tasks/' . $taskParams['UF_ITEM_ID'] . '/' . $taskParams['UF_IBLOCK_TYPE'] . '/' . $taskParams['UF_IBLOCK_CODE'] . '/' . $taskParams['UF_CODE']
                                );
                            } catch (\Exception $e) {
                                if ($e->getMessage() == 'The registration token is not a valid FCM registration token') {
                                    $this->deleteToken(intval($userId), $userToken);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $data
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function sendNewMessages(array $data)
    {
        if (!is_null($this->config)) {
            $tokens = $this->getTokens(true);
            if (count($tokens)) {
                $firebase = (new Factory)
                    ->withServiceAccount($this->config)
                    ->withDatabaseUri(Option::get(DSPI, 'firebase_url'))
                    ->create();

                $messaging = $firebase->getMessaging();

                foreach ($data as $userId => $rooms) {
                    if (!isset($tokens[$userId])) {
                        continue;
                    }

                    foreach ($tokens[$userId] as $userToken) {
                        if (count($rooms) > 1) {
                            try {
                                $this->pushMessage(
                                    $messaging,
                                    $userToken,
                                    Loc::getMessage('FIREBASE_NEW_MESSAGES_GROUP_TITLE'),
                                    Loc::getMessage('FIREBASE_NEW_MESSAGES_GROUP_FROM'),
                                    '/private/messages'
                                );
                            } catch (\Exception $e) {
                                if ($e->getMessage() == 'The registration token is not a valid FCM registration token') {
                                    $this->deleteToken(intval($userId), $userToken);
                                }
                            }
                        } else {
                            foreach ($rooms as $room) {
                                try {
                                    $this->pushMessage(
                                        $messaging,
                                        $userToken,
                                        Loc::getMessage('FIREBASE_NEW_MESSAGES_TITLE'),
                                        Loc::getMessage('FIREBASE_NEW_MESSAGES_FROM') . $room['userName'],
                                        '/private/messages/detail/' . $room['roomId'] . '/' . $room['taskId']
                                    );
                                } catch (\Exception $e) {
                                    if ($e->getMessage() == 'The registration token is not a valid FCM registration token') {
                                        $this->deleteToken(intval($userId), $userToken);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $token
     * @param bool $web
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ApiException
     */
    public function checkToken(string $token, bool $web = false)
    {
        if ($this->userId && !is_null($this->config) && !is_null($this->client)) {
            $exists = false;
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_NAME);
            $find = $db->find([
                'userId' => $this->userId,
                'token' => HTMLToTxt(strip_tags($token)),
                'type' => $web ? 'web' : 'app'
            ]);

            foreach ($find as $item) {
                $exists = true;
                break;
            }

            if (!$exists) {
                $insert = $db->insertOne([
                    'userId' => $this->userId,
                    'token' => HTMLToTxt(strip_tags($token)),
                    'type' => $web ? 'web' : 'app'
                ]);

                if ($insert->getInsertedId()) {
                    $db->createIndex([
                        'userId' => 1,
                        'token' => 1,
                        'type' => 1
                    ]);

                    $db->createIndex([
                        'userId' => 1,
                        'token' => 1
                    ]);

                    $db->createIndex([
                        'userId' => 1,
                        'type' => 1
                    ]);

                    $firebase = (new Factory)
                        ->withServiceAccount($this->config)
                        ->withDatabaseUri(Option::get(DSPI, 'firebase_url'))
                        ->create();

                    $database = $firebase->getDatabase();
                    $database
                        ->getReference('devices')
                        ->push([
                            'userId' => $this->userId,
                            'token' => $token
                        ]);

                    unset($firebase);
                }
            }
        }
    }

    /**
     * @param Messaging $messaging
     * @param string $token
     * @param string $title
     * @param string $body
     * @param string $path
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function pushTask(Messaging $messaging, string $token, string $title, string $body, string $path)
    {
        $ex = explode(':', $token);
        $isAndroid = count($ex) > 1;
        $d = new \DateTime();
        $data = [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => [
                'pushType' => 'newTask',
                'path' => $path
            ]
        ];

        if ($isAndroid) {
            $data['android'] = [
                'ttl' => '3600s',
                'priority' => 'HIGH',
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => 'ic_launcher',
                    'notification_priority' => 'PRIORITY_MAX',
                    'event_time' => $d->format(\DateTime::RFC3339_EXTENDED),
                    'default_vibrate_timings' => true,
                    'default_light_settings' => true,
                    'vibrate_timings' => ['2s', '10s'],
                    'visibility' => 'PRIVATE',
                    'light_settings' => [
                        'color' => [
                            'red' => 0.2,
                            'green' => 0.4,
                            'blue' => 1
                        ],
                        'light_on_duration' => '1s',
                        'light_off_duration' => '2s'
                    ]
                ]
            ];
        }

        $message = CloudMessage::fromArray($data);
        $messaging->send($message);
    }

    /**
     * @param Messaging $messaging
     * @param string $token
     * @param string $title
     * @param string $body
     * @param string $path
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function pushMessage(Messaging $messaging, string $token, string $title, string $body, string $path)
    {
        $ex = explode(':', $token);
        $isAndroid = count($ex) > 1;
        $d = new \DateTime();

        $data = [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => [
                'pushType' => 'newMessage',
                'path' => $path
            ]
        ];

        if ($isAndroid) {
            $data['android'] = [
                'ttl' => '3600s',
                'priority' => 'HIGH',
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => 'ic_launcher',
                    'notification_priority' => 'PRIORITY_MAX',
                    'event_time' => $d->format(\DateTime::RFC3339_EXTENDED),
                    'default_vibrate_timings' => true,
                    'default_light_settings' => true,
                    'vibrate_timings' => ['2s', '10s'],
                    'visibility' => 'PRIVATE',
                    'light_settings' => [
                        'color' => [
                            'red' => 0.2,
                            'green' => 0.4,
                            'blue' => 1
                        ],
                        'light_on_duration' => '1s',
                        'light_off_duration' => '2s'
                    ]
                ]
            ];
        }

        $message = CloudMessage::fromArray($data);
        $messaging->send($message);
    }
}
