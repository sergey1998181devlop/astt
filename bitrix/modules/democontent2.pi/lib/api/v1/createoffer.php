<?php
/**
 * Date: 09.09.2019
 * Time: 16:11
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

use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Balance\Account;
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Iblock\Prices;
use Democontent2\Pi\Iblock\Response;

class CreateOffer extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * OfferConfirm constructor.
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
                $params = $request->getPostList()->toArray();
                if (!count($params)) {
                    try {
                        $params = Json::decode(file_get_contents('php://input'));
                    } catch (ArgumentException $e) {
                    }
                }

                $dict = new ParameterDictionary($params);

                if ($dict->get('taskId')) {
                    if ($dict->get('categoryId')) {
                        if ($dict->get('categoryType')) {
                            if ($dict->get('text')) {
                                $item = new Item();
                                $item->setItemId($dict->get('taskId'));
                                $item->setIBlockId($dict->get('categoryId'));
                                $ownerId = $item->getOwner();
                                if ($ownerId > 0 && $ownerId !== $this->getId()) {
                                    $cost = 0;
                                    $prices = new Prices();
                                    $profilePrices = new \Democontent2\Pi\Profile\Prices();
                                    $profilePrices->setUserId($this->getId());

                                    $response = new Response();
                                    $response->setIBlockId($dict->get('categoryId'));
                                    $response->setUserId($this->getId());
                                    $response->setTaskId($item->getItemId());
                                    $response->setText($dict->get('text'));
                                    $response->setFiles([]);

                                    $getPrices = $prices->get();
                                    $getProfilePrices = $profilePrices->get();

                                    if (count($getPrices) > 0) {
                                        $getPrices = unserialize($getPrices['UF_DATA']);
                                        if (isset($getPrices[$dict->get('categoryType')]['item'][$dict->get('categoryId')])) {
                                            $cost = intval($getPrices[$dict->get('categoryType')]['item'][$dict->get('categoryId')]);
                                        }
                                    }

                                    if (count($getProfilePrices) > 0) {
                                        $getProfilePrices = unserialize($getProfilePrices['UF_DATA']);
                                        if (isset($getProfilePrices[$dict->get('categoryType')][$dict->get('categoryId')])) {
                                            if (strtotime($getProfilePrices[$dict->get('categoryType')][$dict->get('categoryId')]) > time()) {
                                                $cost = 0;
                                            }
                                        }
                                    }

                                    if (!$cost) {
                                        if ($response->add($ownerId, $request)) {
                                            $this->errorCode = 0;
                                            $this->result['message'] = null;
                                        }
                                    } else {
                                        $account = new Account($this->getId());
                                        $account->create();
                                        $balance = $account->getAmount();
                                        if ($balance >= $cost) {
                                            if ($response->add($ownerId, $request)) {
                                                $this->errorCode = 0;
                                                $this->result['message'] = null;

                                                $account->setAmount($cost);
                                                $account->setDescription(
                                                    Loc::getMessage(
                                                        'CREATE_OFFER_DESCRIPTION',
                                                        ['#ID#' => $item->getItemId()]
                                                    )
                                                );
                                                $account->withdrawal();
                                            }
                                        } else {
                                            $this->errorCode = 0;
                                            $this->result['message'] = Loc::getMessage('CREATE_OFFER_INSUFFICIENT_FUNDS');
                                        }
                                    }
                                } else {
                                    $this->errorMessage = 'Unknown error';
                                }
                            } else {
                                $this->errorMessage = 'Invalid text';
                            }
                        } else {
                            $this->errorMessage = 'Invalid category type';
                        }
                    } else {
                        $this->errorMessage = 'Invalid category id';
                    }
                } else {
                    $this->errorMessage = 'Invalid task id';
                }
            } else {
                $this->errorMessage = 'User not found';
            }
        } else {
            $this->errorMessage = 'Invalid key';
        }
    }
}
