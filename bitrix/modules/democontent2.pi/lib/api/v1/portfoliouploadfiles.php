<?php
/**
 * Date: 06.08.2019
 * Time: 08:55
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
use Bitrix\Main\IO\File;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Profile\Portfolio\Category;
use Democontent2\Pi\Profile\Portfolio\Files;
use Democontent2\Pi\Utils;

class PortfolioUploadFiles extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * PortfolioUploadFiles constructor.
     */
    public function __construct()
    {
        parent::__construct(0);
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
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param HttpRequest $request
     * @throws \Bitrix\Main\IO\InvalidPathException
     * @throws \Bitrix\Main\SystemException
     */
    public function run(HttpRequest $request)
    {
        $params = $request->getPostList()->toArray();
        if (!count($params)) {
            try {
                $params = Json::decode(file_get_contents('php://input'));
            } catch (ArgumentException $e) {
            }
        }

        $dict = new ParameterDictionary($params);

        if ($dict->get('id') && intval($dict->get('id'))) {
            if ($dict->get('file')) {
                if ($this->checkKey($request)) {
                    $category = new Category($this->getId());
                    $checkCategory = $category->get($dict->get('id'));

                    if (count($checkCategory)) {
                        $this->errorCode = 0;

                        $__image = Utils::createTempImageFromBase64($dict->get('file'));
                        if (strlen($__image)) {
                            $file = new Files($this->getId(), intval($dict->get('id')));
                            $fileArray = \CFile::MakeFileArray($__image);
                            $fileId = \CFile::SaveFile($fileArray, DSPI . '/portfolio');
                            if (intval($fileId) > 0) {
                                if ($file->add($fileId)) {
                                    $image = \CFile::ResizeImageGet(
                                        $fileId,
                                        [
                                            'width' => 800,
                                            'height' => 800
                                        ],
                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                        true
                                    );

                                    $this->result = [
                                        'id' => intval($fileId),
                                        'description' => null,
                                        'path' => $image['src']
                                    ];
                                } else {
                                    $this->errorMessage = 'Failed to save file';
                                    \CFile::Delete($fileId);
                                }
                            } else {
                                $this->errorMessage = 'Failed to save file';
                            }

                            if (File::isFileExists($__image)) {
                                File::deleteFile($__image);
                            }
                        } else {
                            $this->errorMessage = 'Failed to save file';
                        }
                    } else {
                        $this->errorMessage = 'Category Not Found';
                    }

                    unset($category, $checkCategory);
                } else {
                    $this->errorMessage = 'Invalid Key';
                }
            } else {
                $this->errorMessage = 'Empty Images';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
