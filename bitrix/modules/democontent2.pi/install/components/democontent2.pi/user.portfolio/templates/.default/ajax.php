<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.04.2019
 * Time: 08:54
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;
$result = [];

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && $USER->IsAuthorized()) {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        if ($request->getPost('type')) {
            switch ($request->getPost('type')) {
                case 'sort':
                    if ($request->getPost('categoryId') && intval($request->getPost('categoryId')) && $request->getPost('ids')) {
                        $files = new \Democontent2\Pi\Profile\Portfolio\Files($USER->GetID(), $request->getPost('categoryId'));
                        $files->sort($request->getPost('ids'));
                        $error = 0;
                    }
                    break;
                case 'changeDescription':
                    if ($request->getPost('categoryId') && intval($request->getPost('categoryId')) && $request->getPost('id')
                        && $request->getPost('description')) {
                        $files = new \Democontent2\Pi\Profile\Portfolio\Files($USER->GetID(), $request->getPost('categoryId'));
                        if ($files->changeDescription($request->getPost('id'), $request->getPost('description'))) {
                            $error = 0;
                        }
                    }
                    break;
                case 'removeFile':
                    if ($request->getPost('categoryId') && intval($request->getPost('categoryId')) && $request->getPost('id')) {
                        $files = new \Democontent2\Pi\Profile\Portfolio\Files($USER->GetID(), $request->getPost('categoryId'));
                        if ($files->remove($request->getPost('id'))) {
                            $error = 0;
                        }
                    }
                    break;
                case 'removeCategory':
                    $portfolioCategory = new \Democontent2\Pi\Profile\Portfolio\Category($USER->GetID());
                    if ($portfolioCategory->remove(intval($request->getPost('categoryId')))) {
                        $error = 0;
                    }
                    break;
                case 'file':
                    if ($request->getPost('categoryId') && intval($request->getPost('categoryId'))) {
                        if (isset($_FILES['__file'])) {
                            if (isset($_FILES['__file']['error']) && !intval($_FILES['__file']['error'])) {
                                if (isset($_FILES['__file']['tmp_name'])) {
                                    if (\Bitrix\Main\IO\File::isFileExists($_FILES['__file']['tmp_name'])) {
                                        $allow = false;
                                        $fileArray = CFile::MakeFileArray($_FILES['__file']['tmp_name']);
                                        switch ($_FILES['__file']['type']) {
                                            case 'image/jpeg':
                                                $allow = true;
                                                $fileArray['name'] = md5(microtime() . randString() . intval($request->getPost('categoryId'))) . '.jpg';
                                                break;
                                            case 'image/png':
                                                $allow = true;
                                                $fileArray['name'] = md5(microtime() . randString() . intval($request->getPost('categoryId'))) . '.png';
                                                break;
                                        }

                                        if ($allow) {
                                            $portfolioCategory = new \Democontent2\Pi\Profile\Portfolio\Category($USER->GetID());
                                            $category = $portfolioCategory->get(intval($request->getPost('categoryId')));

                                            if (count($category)) {
                                                $fileId = CFile::SaveFile(
                                                    $fileArray,
                                                    DSPI . '/portfolio'
                                                );

                                                if (intval($fileId) > 0) {
                                                    $files = new \Democontent2\Pi\Profile\Portfolio\Files($USER->GetID(), $request->get('categoryId'));
                                                    $addFile = $files->add($fileId);

                                                    if ($addFile > 0) {
                                                        $error = 0;
                                                        $result['id'] = $addFile;
                                                    } else {
                                                        CFile::Delete($fileId);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(
    [
        'error' => $error,
        'result' => $result
    ]
);