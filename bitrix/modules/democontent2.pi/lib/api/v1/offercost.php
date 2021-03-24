<?php
/**
 * Date: 09.09.2019
 * Time: 11:04
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

namespace Democontent2\Pi\Api\V1;

use Bitrix\Main\HttpRequest;
use Democontent2\Pi\Iblock\Response;
use Democontent2\Pi\Utils;

class OfferCost extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * OfferCost constructor.
     */
    public function __construct()
    {
        parent::__construct(0);
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param HttpRequest $request
     */
    public function run(HttpRequest $request)
    {
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                if ($request->get('categoryType') && $request->get('categoryId') && $request->get('taskId')) {
                    $this->errorCode = 0;
                    $this->result = ['cost' => 0, 'offer' => null];

                    $prices = new \Democontent2\Pi\Iblock\Prices();
                    $profilePrices = new \Democontent2\Pi\Profile\Prices();
                    $profilePrices->setUserId($this->getId());
                    $response = new Response();
                    $response->setUserId($this->getId());
                    $response->setTaskId($request->get('taskId'));

                    $getPrices = $prices->get();
                    $getProfilePrices = $profilePrices->get();

                    if (count($getPrices) > 0) {
                        $getPrices = unserialize($getPrices['UF_DATA']);
                        if (isset($getPrices[$request->get('categoryType')]['item'][$request->get('categoryId')])) {
                            $this->result['cost'] = intval($getPrices[$request->get('categoryType')]['item'][$request->get('categoryId')]);
                        }
                    }

                    if (count($getProfilePrices) > 0) {
                        $getProfilePrices = unserialize($getProfilePrices['UF_DATA']);
                        if (isset($getProfilePrices[$request->get('categoryType')][0])) {
                            if (strtotime($getProfilePrices[$request->get('categoryType')][0]) > time()) {
                                $this->result['cost'] = 0;
                            } else {
                                if (isset($getProfilePrices[$request->get('categoryType')][$request->get('categoryId')])) {
                                    if (strtotime($getProfilePrices[$request->get('categoryType')][$request->get('categoryId')]) > time()) {
                                        $this->result['cost'] = 0;
                                    }
                                }
                            }
                        } else {
                            if (isset($getProfilePrices[$request->get('categoryType')][$request->get('categoryId')])) {
                                if (strtotime($getProfilePrices[$request->get('categoryType')][$request->get('categoryId')]) > time()) {
                                    $this->result['cost'] = 0;
                                }
                            }
                        }
                    }

                    $offer = $response->myOffer();
                    if (count($offer)) {
                        $this->result['offer'] = [
                            'id' => intval($offer['ID']),
                            'createdAt' => strtotime($offer['UF_CREATED_AT']),
                            'formattedCreatedAt' => Utils::formatDate($offer['UF_CREATED_AT']),
                            'isCandidate' => intval($offer['UF_CANDIDATE']),
                            'isExecutor' => intval($offer['UF_EXECUTOR']),
                            'isRejected' => intval($offer['UF_DENIED']),
                            'isRead' => intval($offer['UF_READ']),
                            'text' => $offer['UF_TEXT'],
                            'files' => null
                        ];

                        $files = unserialize($offer['UF_FILES']);
                        if (count($files) > 0) {
                            foreach ($files as $key => $file) {
                                $getFile = \CFile::GetFromCache($file);
                                $this->result['offer']['files'][] = [
                                    'id' => intval($file),
                                    'name' => Utils::shortString($getFile[$file]['FILE_NAME']),
                                    'src' => '/upload/' . $getFile[$file]['SUBDIR'] . '/' . $getFile[$file]['FILE_NAME'],
                                    'size' => Utils::formatBytes($getFile[$file]['FILE_SIZE'])
                                ];
                            }
                        }
                    }
                } else {
                    $this->errorMessage = 'Invalid params';
                }
            } else {
                $this->errorMessage = 'User not found';
            }
        } else {
            $this->errorMessage = 'Invalid key';
        }
    }
}
