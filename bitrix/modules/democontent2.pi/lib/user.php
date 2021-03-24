<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 12:12
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\UserTable;
use Democontent2\Pi\Balance\Account;
use Democontent2\Pi\Hl;
use \Datetime;

use Democontent2\Pi\Iblock\City;
use Democontent2\Pi\Iblock\Menu;
use Democontent2\Pi\Iblock\Reviews;
use Democontent2\Pi\Payments\SafeCrow\Cards;
use Democontent2\Pi\Payments\SafeCrow\SafeCrow;
use Democontent2\Pi\Profile\Profile;
use Democontent2\Pi\Profile\Subscriptions;
//use Kreait\Firebase\Exception\ApiException;
//use Democontent2\Pi\Hl;
//use Democontent2\Pi\Company;

class User
{
    const TABLE_NAME = 'company';
    protected $apiKey = '';
    protected $id = 0;
    protected $ttl = 0;
    protected $name = '';
    protected $email = '';
    protected $phone = '';
    protected $limit = 50;
    protected $offset = 0;
    protected $total = 0;
    protected $cityId = 0;
    protected $verification = 0;
    protected $order = [
        'ID' => 'ASC'
    ];
    private $redirect = '';

    /**
     * User constructor.
     * @param $id
     * @param int $ttl
     */
    public function __construct($id, $ttl = 86400)
    {
        $this->id = intval($id);
        $this->ttl = intval($ttl);
    }

    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param array $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @param int $verification
     */
    public function setVerification($verification)
    {
        $this->verification = $verification;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = filter_var($email, FILTER_VALIDATE_EMAIL);
        }
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = Utils::validatePhone($phone);
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = intval($limit);
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = intval($offset);
    }

    /**
     * @param int $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = intval($cityId);
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    public function getIdByApiKey(): void
    {
        if (!strlen($this->apiKey)) {
            return;
        }

        try {
            $userTable = new UserTable();
            $get = $userTable::getList(
                [
                    'select' => ['ID'],
                    'filter' => [
                        '=UF_DSPI_API_KEY' => $this->apiKey
                    ],
                    'limit' => 1
                ]
            );
            while ($res = $get->fetch()) {
                $this->id = intval($res['ID']);
            }
        } catch (ObjectPropertyException $e) {
        } catch (ArgumentException $e) {
        } catch (SystemException $e) {
        }
    }

    public function setSafeCrowId($safeCrowId)
    {
        if ($this->id > 0) {
            try {
                $us = new \CUser();
                $update = $us->Update(
                    $this->id,
                    [
                        'UF_DSPI_SAFECROW_ID' => $safeCrowId
                    ]
                );

                if ($update) {
                    Logger::add(
                        $this->id,
                        'setSafeCrowId',
                        [
                            'userId' => $this->id,
                            'safeCrowId' => $safeCrowId
                        ]
                    );
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function updateCurrentRating($rating)
    {
        if ($this->id > 0) {
            try {
                $us = new \CUser();
                $update = $us->Update(
                    $this->id,
                    [
                        'UF_DSPI_RATING' => $rating
                    ]
                );

                if ($update) {
                    Application::getInstance()->getTaggedCache()->clearByTag('userRating_' . $this->id);
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function updateUserPers($data, $files)
    {
        if ($this->id) {


                $updateData = [
                    'NAME' => htmlspecialcharsbx(strip_tags(trim($data['name']))),
//                    'PERSONAL_PHONE' => Utils::validatePhone($data['phone'])
                ];
                if (isset($data['lastName']) && strlen(htmlspecialcharsbx(strip_tags(trim($data['lastName'])))) > 0) {
                    $updateData['LAST_NAME'] = htmlspecialcharsbx(strip_tags(trim($data['lastName'])));
                }

                global $USER;
                $id = $USER->GetID();

                $rsUser = \CUser::GetByID($id);
                $arUser = $rsUser->Fetch();
                $arFile = \CFile::GetFileArray($arUser['PERSONAL_PHOTO']);

                if(!empty($arFile['ID'])) {
                    if($files['photo']['error'] == 0){
                    function RDir($path)
                    {
                        // если путь существует и это папка
                        if (file_exists($path) AND is_dir($path)) {
                            // открываем папку
                            $dir = opendir($path);
                            while (false !== ($element = readdir($dir))) {
                                // удаляем только содержимое папки
                                if ($element != '.' AND $element != '..') {
                                    $tmp = $path . '/' . $element;
                                    chmod($tmp, 0777);
                                    // если элемент является папкой, то
                                    // удаляем его используя нашу функцию RDir
                                    if (is_dir($tmp)) {
                                        RDir($tmp);
                                        // если элемент является файлом, то удаляем файл
                                    } else {
                                        unlink($tmp);
                                    }
                                }
                            }
// закрываем папку
                            closedir($dir);
// удаляем саму папку
                            if (file_exists($path)) {
                                rmdir($path);
                            }
                        }
                    }

//                   pre($_SERVER['DOCUMENT_ROOT']."/resize_cache/".$arFile['SUBDIR']);
//                   die();
                    unlink($_SERVER['DOCUMENT_ROOT'] . $arFile['SRC']);
                    RDir($_SERVER['DOCUMENT_ROOT'] . "/upload/resize_cache/" . $arFile['SUBDIR']);

                    $name = $arFile['FILE_NAME'];
                    $subdir = $arFile['SUBDIR'];
                    move_uploaded_file($files['photo']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/upload/" . "$subdir/$name");
//                    move_uploaded_file($files['photo']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/upload/resize_cache/".$arFile['SUBDIR']."/100_100_0/".$name);
                }
                }else{




                if (isset($files['photo'])) {
                    switch ($files['photo']['type']) {
                        case 'image/jpeg':
                            if (!intval($files['photo']['error'])) {
                                $get = $this->get();
                                if (File::isFileExists($files['photo']['tmp_name'])) {
                                    $updateData['PERSONAL_PHOTO'] = \CFile::MakeFileArray($files['photo']['tmp_name']);
                                    $updateData['PERSONAL_PHOTO']['name'] = md5(microtime(true)) . '.jpg';
                                    if (intval($get['PERSONAL_PHOTO']) > 0) {
                                        $updateData['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
                                        $updateData['PERSONAL_PHOTO']['del'] = 'Y';
                                        $updateData['PERSONAL_PHOTO']['old_file'] = intval($get['PERSONAL_PHOTO']);
                                    }
                                }
                            }
                            break;
                        case 'image/png':
                            if (!intval($files['photo']['error'])) {
                                $get = $this->get();
                                if (File::isFileExists($files['photo']['tmp_name'])) {
                                    $updateData['PERSONAL_PHOTO'] = \CFile::MakeFileArray($files['photo']['tmp_name']);
                                    $updateData['PERSONAL_PHOTO']['name'] = md5(microtime(true)) . '.png';
                                    if (intval($get['PERSONAL_PHOTO']) > 0) {
                                        $updateData['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
                                        $updateData['PERSONAL_PHOTO']['del'] = 'Y';
                                        $updateData['PERSONAL_PHOTO']['old_file'] = intval($get['PERSONAL_PHOTO']);
                                    }
                                }
                            }
                            break;
                    }
                }
                }
                if (isset($data['city']) && intval($data['city'])) {
                    $city = new City();
                    $getCity = $city->getById(intval($data['city']));
                    if (count($getCity) > 0) {
                        $updateData['UF_DSPI_CITY'] = intval($getCity['id']);
                    }
                }

                $us = new \CUser();
                $update = $us->Update($this->id, $updateData);
                if ($update) {
                    $taggedCache = Application::getInstance()->getTaggedCache();
                    $taggedCache->clearByTag(md5('user_' . $this->id));
                    Logger::add($this->id, 'userUpdate', $updateData);

                    $get = $this->get();
                    if (intval($get['UF_DSPI_SAFECROW_ID'])) {
                        if (strlen(Option::get(DSPI, 'safeCrowApiKey')) > 0
                            && strlen(Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                            $safeCrow = new SafeCrow();
                            $safeCrow->setUserId(intval($this->id));
                            $safeCrow->setPhone($get['PERSONAL_PHONE']);
                            $safeCrow->setEmail($get['EMAIL']);
                            $safeCrow->setName($get['NAME'] . ((strlen($get['LAST_NAME']) > 0) ? ' ' . $get['LAST_NAME'] : ''));
                            $safeCrow->updateUser(intval($get['UF_DSPI_SAFECROW_ID']));
                        }
                    }
                }

                if ($us->LAST_ERROR && strlen($us->LAST_ERROR) > 0) {
                    Logger::add($this->id, 'userUpdateError', [$us->LAST_ERROR]);
                }


            if (isset($data['password']) && isset($data['confirmPassword'])) {
                if (strlen(trim($data['password'])) > 0 && strlen(trim($data['confirmPassword'])) > 0) {
                    if (trim($data['password']) == trim($data['confirmPassword'])) {
                        $updateData = [];
                        $updateData['PASSWORD'] = trim($data['password']);
                        $updateData['CONFIRM_PASSWORD'] = trim($data['password']);
                        $us = new \CUser();
                        $update = $us->Update($this->id, $updateData);
                        if ($update) {
                            Logger::add($this->id, 'userUpdatePassword', []);
                        }
                    }
                }
            }

        }
    }

    public function update($data, $files)
    {
        if ($this->id) {
            if (isset($data['name']) && strlen(htmlspecialcharsbx(strip_tags(trim($data['name'])))) > 0
                && isset($data['phone']) && Utils::validatePhone($data['phone'])) {
                $updateData = [
                    'NAME' => htmlspecialcharsbx(strip_tags(trim($data['name']))),
                    'PERSONAL_PHONE' => Utils::validatePhone($data['phone'])
                ];
                if (isset($data['lastName']) && strlen(htmlspecialcharsbx(strip_tags(trim($data['lastName'])))) > 0) {
                    $updateData['LAST_NAME'] = htmlspecialcharsbx(strip_tags(trim($data['lastName'])));
                }

                if (isset($files['photo'])) {
                    switch ($files['photo']['type']) {
                        case 'image/jpeg':
                            if (!intval($files['photo']['error'])) {
                                $get = $this->get();
                                if (File::isFileExists($files['photo']['tmp_name'])) {
                                    $updateData['PERSONAL_PHOTO'] = \CFile::MakeFileArray($files['photo']['tmp_name']);
                                    $updateData['PERSONAL_PHOTO']['name'] = md5(microtime(true)) . '.jpg';
                                    if (intval($get['PERSONAL_PHOTO']) > 0) {
                                        $updateData['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
                                        $updateData['PERSONAL_PHOTO']['del'] = 'Y';
                                        $updateData['PERSONAL_PHOTO']['old_file'] = intval($get['PERSONAL_PHOTO']);
                                    }
                                }
                            }
                            break;
                        case 'image/png':
                            if (!intval($files['photo']['error'])) {
                                $get = $this->get();
                                if (File::isFileExists($files['photo']['tmp_name'])) {
                                    $updateData['PERSONAL_PHOTO'] = \CFile::MakeFileArray($files['photo']['tmp_name']);
                                    $updateData['PERSONAL_PHOTO']['name'] = md5(microtime(true)) . '.png';
                                    if (intval($get['PERSONAL_PHOTO']) > 0) {
                                        $updateData['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
                                        $updateData['PERSONAL_PHOTO']['del'] = 'Y';
                                        $updateData['PERSONAL_PHOTO']['old_file'] = intval($get['PERSONAL_PHOTO']);
                                    }
                                }
                            }
                            break;
                    }
                }

                if (isset($data['city']) && intval($data['city'])) {
                    $city = new City();
                    $getCity = $city->getById(intval($data['city']));
                    if (count($getCity) > 0) {
                        $updateData['UF_DSPI_CITY'] = intval($getCity['id']);
                    }
                }

                $us = new \CUser();
                $update = $us->Update($this->id, $updateData);
                if ($update) {
                    $taggedCache = Application::getInstance()->getTaggedCache();
                    $taggedCache->clearByTag(md5('user_' . $this->id));
                    Logger::add($this->id, 'userUpdate', $updateData);

                    $get = $this->get();
                    if (intval($get['UF_DSPI_SAFECROW_ID'])) {
                        if (strlen(Option::get(DSPI, 'safeCrowApiKey')) > 0
                            && strlen(Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                            $safeCrow = new SafeCrow();
                            $safeCrow->setUserId(intval($this->id));
                            $safeCrow->setPhone($get['PERSONAL_PHONE']);
                            $safeCrow->setEmail($get['EMAIL']);
                            $safeCrow->setName($get['NAME'] . ((strlen($get['LAST_NAME']) > 0) ? ' ' . $get['LAST_NAME'] : ''));
                            $safeCrow->updateUser(intval($get['UF_DSPI_SAFECROW_ID']));
                        }
                    }
                }

                if ($us->LAST_ERROR && strlen($us->LAST_ERROR) > 0) {
                    Logger::add($this->id, 'userUpdateError', [$us->LAST_ERROR]);
                }
            }

            if (isset($data['password']) && isset($data['confirmPassword'])) {
                if (strlen(trim($data['password'])) > 0 && strlen(trim($data['confirmPassword'])) > 0) {
                    if (trim($data['password']) == trim($data['confirmPassword'])) {
                        $updateData = [];
                        $updateData['PASSWORD'] = trim($data['password']);
                        $updateData['CONFIRM_PASSWORD'] = trim($data['password']);
                        $us = new \CUser();
                        $update = $us->Update($this->id, $updateData);
                        if ($update) {
                            Logger::add($this->id, 'userUpdatePassword', []);
                        }
                    }
                }
            }
        }
    }

    public function attachDocuments($files, $protocol, $host)
    {
        $preparedFiles = [];
        $savedFiles = [];
        $savedFilesLinks = [];

        if (isset($files['__documents'])) {
            if (isset($files['__documents']['tmp_name'])) {
                foreach ($files['__documents']['tmp_name'] as $fileId => $filePath) {
                    if (!intval($files['__documents']['error'][$fileId])) {
                        if (File::isFileExists($filePath)) {
                            switch ($files['__documents']['type'][$fileId]) {
                                case 'image/jpeg':
                                    $preparedFiles[] = [
                                        'name' => md5(microtime(true)) . ToLower(randString(rand(5, 15))) . '.jpg',
                                        'path' => $filePath
                                    ];
                                    break;
                                case 'image/png':
                                    $preparedFiles[] = [
                                        'name' => md5(microtime(true)) . ToLower(randString(rand(5, 15))) . '.png',
                                        'path' => $filePath
                                    ];
                                    break;
                                case 'application/pdf':
                                    $preparedFiles[] = [
                                        'name' => md5(microtime(true)) . ToLower(randString(rand(5, 15))) . '.pdf',
                                        'path' => $filePath
                                    ];
                                    break;
                            }
                        }
                    }
                }
            }
        }

        if (count($preparedFiles) > 0) {
            foreach ($preparedFiles as $file) {
                $fileArray = \CFile::MakeFileArray($file['path']);
                $fileArray['name'] = $file['name'];
                $fileArray['description'] = $file['name'];

                $saveFileId = \CFile::SaveFile($fileArray, DSPI . '/' . md5(Sign::getInstance()->get()) . '/' . md5($this->id . Sign::getInstance()->get()));
                if (intval($saveFileId) > 0) {
                    $savedFiles[] = intval($saveFileId);
                }
            }
        }

        if (count($savedFiles)) {
            $i = 0;
            foreach ($savedFiles as $file) {
                $i++;
                $savedFilesLinks[] = '<a href="' . $protocol . $host . \CFile::GetPath($file) . '" target="_blank">' . Loc::getMessage('FILE_N') . $i . '</a>';
            }

            Event::send(
                [
                    'EVENT_NAME' => 'DSPI_VERIFY_EXECUTOR',
                    'LID' => Application::getInstance()->getContext()->getSite(),
                    'C_FIELDS' => [
                        'USER_ID' => $this->id,
                        'LINKS' => implode('<br>', $savedFilesLinks)
                    ]
                ]
            );
        }

        return;
    }

    public function toggleBusy(string $type)
    {
        $result = false;
        if ($this->id) {
            $allow = false;
            $value = 0;
            switch ($type) {
                case 'free':
                    $allow = true;
                    $value = 0;
                    break;
                case 'busy':
                    $allow = true;
                    $value = 1;
                    break;
            }

            if ($allow) {
                $result = true;
                $us = new \CUser();
                $update = $us->Update(
                    $this->id,
                    [
                        'UF_DSPI_BUSY' => $value
                    ]
                );

                if ($update) {
                    try {
                        $taggedCache = Application::getInstance()->getTaggedCache();
                        $taggedCache->clearByTag(md5('user_' . $this->id));
                        $taggedCache->clearByTag('executors');
                    } catch (SystemException $e) {
                    }
                }
            }
        }

        return $result;
    }
    public function getCompanyManagerListTasks($messId){
        $arIdes = [];
        foreach ($messId as $idTaas => $valTas){
            $arIdes[] = $valTas['UF_USER_ID'];
        }
    }
    public function getCompanyManagerModer($idListOrOne = false){


        //получаю текущую компанию у текущего пользователя
        $hl = new Hl('CompanyList');
        $obj = $hl->obj;
        global $USER;
        if(empty($idListOrOne[0])){
            $rsUser = \CUser::GetByID($USER->GetID());
            $arUser = $rsUser->Fetch();

        }else{
            $arUser['UF_ID_COMPANY'] = $idListOrOne;
            //сортирую список id  пользователей , только те что модерацию компании прошли
            $filter = array(
                "ID" => $idListOrOne
            );

            $data = \CUser::GetList(($by="ID"), ($order="ASC"),
                array(
                    'ID' => $idListOrOne,
                    'ACTIVE' => 'Y'
                ),
                array(
                    "SELECT" => array(
                        "UF_MODERATION_ACCESS"
                    )
                )
            );

            $arFilter = Array(
                Array(
                    Array(
                        "ID"=> $idListOrOne
                    )
                )
            );

            $res = \Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("ID","NAME","UF_MODERATION_ACCESS","UF_ID_COMPANY"),
                "filter"=>$arFilter,
            ));
            $userTrueModeration = [];
            while ($arRes = $res->fetch()) {

                $userTrueModeration[] =    $arRes['UF_ID_COMPANY'];


//                die();
            }



            if(!empty($userTrueModeration[0])){

                $arUser['UF_ID_COMPANY'] = $userTrueModeration;
                $arUser['UF_MODERATION_ACCESS'] = 'true';
            }

        }




//        echo "<pre>"; print_r($arUser); echo "</pre>";
        $dataOneCount['MODERATION_IS_ACTIVE'] =  "DISABLED";
//        if($arUser['UF_MODERATION_ACCESS'] == 'true'){

        $dataOneCount  = $hl->getListHigload(
            '100' ,
            array(
                'ID',
                'UF_COMPANY_TYPE',
                'UF_NUMBER_OGRN',
                'UF_KOD_KPP',
                'UF_KOD_OKPO',
                'UF_FIO_GENDIR',
                'UF_UR_ADDRESS',
                'UF_REAL_ADDRESS',
                'UF_PHONE',
                'UF_EMAIL',
                'UF_DESCRIPTION',
                'UF_NUMBER_INN',
                'UF_LOGO',
                'UF_FILE0',
                'UF_FILE1',
                'UF_FILE2',
                'UF_FILE3',
                'UF_COMPANY_NAME_MIN',
                'UF_BIG_NAME',
                'UF_STATUS_MODERATION'
            ) ,
            '' ,
            array( 'ID' => $arUser['UF_ID_COMPANY']) );



        $arFilesMess = array( "UF_LOGO" , "UF_FILE0" , "UF_FILE1" , "UF_FILE2" , "UF_FILE3");
        $arDetailMess = [];

        foreach ($arFilesMess as $id => $value ){
            $arDetailMess[$value] = \CFile::GetFileArray($dataOneCount[0][$value]);


        }
        $dataOneCount['MODERATION_FILES'] =  $arDetailMess;
        $dataOneCount['MODERATION_IS_ACTIVE'] =  "ACTIVE";



        return array_reverse($dataOneCount);
//        }
    }
    public function getCompanyManager($idListOrOne = false){

        //получаю текущую компанию у текущего пользователя
        $hl = new Hl('CompanyList');
        $obj = $hl->obj;
        global $USER;
        if(empty($idListOrOne[0])){
            $rsUser = \CUser::GetByID($USER->GetID());
            $arUser = $rsUser->Fetch();

        }else{
            $arUser['UF_ID_COMPANY'] = $idListOrOne;
            //сортирую список id  пользователей , только те что модерацию компании прошли
            $filter = array(
                "ID" => $idListOrOne
            );

            $data = \CUser::GetList(($by="ID"), ($order="ASC"),
                array(
                    'ID' => $idListOrOne,
                    'ACTIVE' => 'Y'
                ),
                array(
                    "SELECT" => array(
                        "UF_MODERATION_ACCESS"
                    )
                )
            );

            $arFilter = Array(
                Array(
                    Array(
                        "ID"=> $idListOrOne
                    )
                )
            );

            $res = \Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("ID","NAME","UF_MODERATION_ACCESS","UF_ID_COMPANY"),
                "filter"=>$arFilter,
            ));
            $userTrueModeration = [];
            while ($arRes = $res->fetch()) {

                $userTrueModeration[] =    $arRes['UF_ID_COMPANY'];
            }



            if(!empty($userTrueModeration[0])){
                $arUser['UF_ID_COMPANY'] = $userTrueModeration;
                $arUser['UF_MODERATION_ACCESS'] = 'true';
            }

        }


//        echo "<pre>"; print_r($arUser); echo "</pre>";
        $dataOneCount['MODERATION_IS_ACTIVE'] =  "DISABLED";
//        if($arUser['UF_MODERATION_ACCESS'] == 'true'){

            $dataOneCount  = $hl->getListHigload(
                '100' ,
                array(
                    'ID',
                    'UF_COMPANY_TYPE',
                    'UF_NUMBER_OGRN',
                    'UF_KOD_KPP',
                    'UF_KOD_OKPO',
                    'UF_FIO_GENDIR',
                    'UF_UR_ADDRESS',
                    'UF_REAL_ADDRESS',
                    'UF_PHONE',
                    'UF_EMAIL',
                    'UF_DESCRIPTION',
                    'UF_NUMBER_INN',
                    'UF_LOGO',
                    'UF_FILE0',
                    'UF_FILE1',
                    'UF_FILE2',
                    'UF_FILE3',
                    'UF_COMPANY_NAME_MIN',
                    'UF_BIG_NAME',
                    'UF_STATUS_MODERATION',
                    'UF_ADMIN_COMPANY_ID',
                    'UF_STATUS_FALSE_MODERATION',
                    'UF_STATUS_FALSE_MODERATION2',
                    'UF_STATUS_FALSE_MODERATION3',
                    'UF_SITE_LINK',
                    'UF_EXECUTOR',
                    'UF_HOT_NAMBER_COMPANY'
                ) ,
                '' ,
                array( 'ID' => $arUser['UF_ID_COMPANY']) );


            $arFilesMess = array( "UF_LOGO" , "UF_FILE0" , "UF_FILE1" , "UF_FILE2" , "UF_FILE3");
            $arDetailMess = [];

            foreach ($arFilesMess as $id => $value ){
                $arDetailMess[$value] = \CFile::GetFileArray($dataOneCount[0][$value]);


            }
            $dataOneCount['MODERATION_FILES'] =  $arDetailMess;
            $dataOneCount['MODERATION_IS_ACTIVE'] =  "ACTIVE";



            return $dataOneCount;
//        }
    }
    public function getListCompanies(){
        //получаю все  компании
        $hl = new Hl('CompanyList');
        $obj = $hl->obj;
        global $USER;

        $dataOneCount  = $hl->getListHigload(
            '100' ,
            array(
                'ID',
                'UF_COMPANY_TYPE',
                'UF_NUMBER_OGRN',
                'UF_KOD_KPP',
                'UF_KOD_OKPO',
                'UF_FIO_GENDIR',
                'UF_UR_ADDRESS',
                'UF_REAL_ADDRESS',
                'UF_PHONE',
                'UF_EMAIL',
                'UF_DESCRIPTION',
                'UF_NUMBER_INN',
                'UF_LOGO',
                'UF_FILE0',
                'UF_FILE1',
                'UF_FILE2',
                'UF_FILE3',
                'UF_COMPANY_NAME_MIN',
                'UF_BIG_NAME',
                'UF_STATUS_MODERATION',
                'UF_ADMIN_COMPANY_ID',
                'UF_DATA_CREATE',
                'UF_EXECUTOR'
            ) ,
            '' ,
            array('UF_STATUS_MODERATION' => 1)
        );

        $idMessUsers = [];
        foreach ($dataOneCount as $id => $val){
            $idMessUsers[] = $val['UF_ADMIN_COMPANY_ID'];
        }

        $idMessUsers = implode("||" , $idMessUsers);



        $filter = Array
        (
            "ID"                  => $idMessUsers,

        );
        $order = array('sort' => 'asc');
        $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
        $rsUsers = \CUser::GetList($order, $tmp , $filter);
        $usersMess = [];
        while($arUser = $rsUsers->Fetch()) {
            $usersMess[] = $arUser;
        }

        foreach ($dataOneCount  as $id => $itemComp) {
            $dataOneCount[$id]['USER'] = $usersMess[$id];
        }

        return $dataOneCount;
    }
    public function getListCompanyToTask($idListOrOne = false){
        //получаю текущую компанию у текущего пользователя
        $hl = new Hl('CompanyList');
        $obj = $hl->obj;
        global $USER;
        //забиваю список айдишников юзеров
        $arFilter = Array(
            Array(
                Array(
                    "ID"=> $idListOrOne
                )
            )
        );
        //достаю пользователей по списку )
        $res = \Bitrix\Main\UserTable::getList(Array(
            "select"=>Array("ID","NAME","UF_MODERATION_ACCESS","UF_ID_COMPANY"),
            "filter"=>$arFilter,
        ));
        $usersMess = [];
        $usersIdMess = [];
        while ($arRes = $res->fetch()) {
            $usersIdMess[] = $arRes['UF_ID_COMPANY'];
            $usersMess[$arRes['ID']] =    $arRes;
        }
        $dataOneCount  = $hl->getListHigload(
            '100' ,
            array(
                'ID', 'UF_COMPANY_TYPE', 'UF_NUMBER_OGRN', 'UF_KOD_KPP', 'UF_KOD_OKPO', 'UF_FIO_GENDIR', 'UF_UR_ADDRESS', 'UF_REAL_ADDRESS', 'UF_PHONE', 'UF_EMAIL', 'UF_DESCRIPTION',
                'UF_NUMBER_INN', 'UF_LOGO', 'UF_FILE0', 'UF_FILE1', 'UF_FILE2', 'UF_FILE3', 'UF_COMPANY_NAME_MIN', 'UF_BIG_NAME', 'UF_STATUS_MODERATION', 'UF_ADMIN_COMPANY_ID'
            ) ,
            '' ,
            array( 'ID' => $usersIdMess) );
        //собираю список компаний и наполняю массив с пользователями /   если у поьзователя была компания  - у него массив комании не пуст / иначе пустой массив ) далее в шаблоне есть проверка
        //если компания пустая  - взять имя не компании а менеджера и его картинку
        $newMessCompanies = [];
        //переделываю массив чтобы ключем был  - id  компании
        foreach ($dataOneCount as $idMessCompany => $valueAllCompany){
            $newMessCompanies[$valueAllCompany['ID']] = $valueAllCompany;
        }
        foreach ($usersMess as $idUser => $valueAllUser){
//            UF_ID_COMPANY
            //по ключу компании из массива пользователя ищу компанию и вбрасываю в этого же пользователя / если ключ компании пустой то создаю пустое значение
            // / если компания не промодерирована  - создаю пустойе значение
            if(!empty($valueAllUser['UF_ID_COMPANY'])){
                //если есть компния  /   я по ключу ее достаю
                $companyPreData  = $newMessCompanies[$valueAllUser['UF_ID_COMPANY']];
                //если компания промодерирована /  закидываю ее в юзера
                if($companyPreData['UF_STATUS_MODERATION'] == 2){
                    //дополнительнокидаю туда сформированные массивы файлов
                    $arFilesMess = array( "UF_LOGO" , "UF_FILE0" , "UF_FILE1" , "UF_FILE2" , "UF_FILE3");
                    $arDetailMess = [];
                    foreach ($arFilesMess as $id => $value ){

                        $arDetailMess[$value] = \CFile::GetFileArray($companyPreData[$value]);
                    }
                    $companyPreData['MODERATION_FILES'] =  $arDetailMess;
                    $companyPreData['MODERATION_IS_ACTIVE'] =  "ACTIVE";
                    //закидываю компанию вместе с файлами
                    $usersMess[$idUser]['COMPANY'] =  $companyPreData;
                }else{
                    //если компания не промодерированна также создаю пустой ключ
                    $usersMess[$idUser]['COMPANY']['ID'] = '';
                }
            }else{
                //иначе создаю пустой ключ /  чтобы потом в шаблоне вместо названия компании высвестить модератора
                $usersMess[$idUser]['COMPANY']['ID'] = '';
            }
        }
//        debmes($idListOrOne);echo "IdUsers";echo "<br>";
//        debmes($usersMess);echo "idCompany";
//        debmes($newMessCompanies);
        $messReturn = [];
        foreach ($idListOrOne as $idUsersInTaskMess  => $idUserTask){
            $messReturn[] = $usersMess[$idUserTask]['COMPANY'];
        }
        return  $messReturn;
    }

    public function changePassFunc(){
        $result = [];
        global $USER;
        $idCurrentUser = $USER->GetID();

        $rsUser = \CUser::GetByID($idCurrentUser);
        $arUser = $rsUser->Fetch();
        $md5PassOld = md5($_POST['OldPass']);

        if($arUser['PASSWORD'] !==  $md5PassOld){
            $result['message'] = 'Старый пароль введен не верно';
            $result['keyInfo'] = 'OldInputError';
            return $result;
        }
        elseif ($_POST['NewPass'] !== $_POST['NewPass_REPEAT']){
            $result['message'] = 'Новый пароль не совпадает';
            $result['keyInfo'] = 'NewPassNotEqual';
            return $result;
        }else{
            $md5PassNew = trim($_POST['NewPass']);
            $md5PassNewRepeat = trim($_POST['NewPass_REPEAT']);

            $fields = array(
                "PASSWORD"=> $md5PassNew,//пароль
                "CONFIRM_PASSWORD"=> $md5PassNewRepeat, //подтверждение пароля
                "CHECKWORD"=> $md5PassNewRepeat //подтверждение пароля
            );

            $user = new \CUser;
            $userResult = $user->Update( $arUser['ID']  , $fields , false);

            if($userResult === true){
                $result['message'] = 'Пароль успешно обновлен';
                $result['keyInfo'] = 'SuccessUpdatePass';
                return $result;
            }else{
                $result['message'] = $user->LAST_ERROR;
                $result['keyInfo'] = 'ERROR_UPDATE';

                return $result;
            }
        }


    }
    public function company_moderation(){
        if(!empty($_POST['idCompany'])){
            $hl = new Hl('CompanyList');
            $obj = $hl->obj;
            $rows = array(
                "UF_STATUS_MODERATION" => 1
            );
            $result = $obj::update($_POST['idCompany'] , $rows);

            $result = \Bitrix\Main\UserGroupTable::getList(array(
                'filter' => array('GROUP_ID'=>1,'USER.ACTIVE'=>'Y'),
                'select' => array('USER_ID','NAME'=>'USER.NAME','LAST_NAME'=>'USER.LAST_NAME'), // выбираем идентификатор п-ля, имя и фамилию
                'order' => array('USER.ID'=>'DESC'), // сортируем по идентификатору пользователя
            ));

            $arGroupUsers  = [];
            while ($arGroup = $result->fetch())
            {
                $arGroupUsers[] = $arGroup['USER_ID'];
            }
            global $USER;
            $curUs = $USER->GetID();

            $arrItem = [
                "CompanyId" => $_POST['idCompany'],
                "userId" => $USER->GetID()
            ];

            $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $arGroupUsers  , 'CompanyAdd' , $arrItem , $_POST['idCompany'] );


        }
    }

    public function falseModeration(){

        global $APPLICATION;
        $dir  = $APPLICATION->GetCurPage();
        $dir  = explode('/' ,$dir);

        if(!empty($dir[4])){
            //отклонить модерацию компании - функция для модератора маскГруппа
            $hl = new Hl('CompanyList');
            $obj = $hl->obj;
            $rows = array(
                "UF_STATUS_MODERATION" => 0
            );




            if(!empty($_POST['1_Work'])){

                $rows['UF_STATUS_FALSE_MODERATION'] = $_POST['1_Work'];

            }


            if(!empty($_POST['2_Work'])){

                $rows['UF_STATUS_FALSE_MODERATION2'] = $_POST['2_Work'];

            }


            if(!empty($_POST['3_Work'])){

                $rows['UF_STATUS_FALSE_MODERATION3'] = $_POST['3_Work'];

            }













            $arrItem = [
                "CompanyId" => $dir[4],
                "userId" =>  $_POST['uf_moderation_company']
            ];
            $arGroupUsers = [
                0 => $_POST['uf_moderation_company']
            ];
            $result = $obj::update($dir[4] , $rows);
            $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $arGroupUsers  , 'CompanyFalseModeration' , $arrItem , $dir[4] );
        }
    }
    public function TrueModeration($company){
        //одобрить модерацию компании - функция для модератора маскГруппа
        global $APPLICATION;
        $dir  = $APPLICATION->GetCurPage();
        $dir  = explode('/' ,$dir);

        if(!empty($dir[4])){
            $user = new \CUser;
            $hl = new Hl('CompanyList');
            $obj = $hl->obj;
            $rows = array(
                "UF_STATUS_MODERATION" => 2
            );
//            $company[0]['UF_ADMIN_COMPANY_ID']

            $fields = Array(
                "UF_MODERATION_ACCESS"              => "true",
            );
            if($company[0]['UF_EXECUTOR'] == "supplier"  || $company[0]['UF_EXECUTOR'] == "cu-supplier" ){
                $fields['UF_DSPI_EXECUTOR'] = 1;
            }
            $user->Update($company[0]['UF_ADMIN_COMPANY_ID'], $fields);

            $result = $obj::update($dir[4] , $rows);


            $arrItem = [
                "CompanyId" => $dir[4],
                "userId" =>  $_POST['uf_moderation_company']
            ];
            $arGroupUsers = [
                0 => $_POST['uf_moderation_company']
            ];
            $result = $obj::update($dir[4] , $rows);
            $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $arGroupUsers  , 'CompanyTrueModeration' , $arrItem , $dir[4] );


        }
    }
    public function updateCompanyRepeat(){

        $hl = new Hl('CompanyList');
        $obj = $hl->obj;
        if ($hl->obj !== null && !empty($_POST['idCompany']) ) {

            $rows = array(
                "UF_REAL_ADDRESS" =>  $_POST['FACT_ADDRESS'],
                "UF_PHONE" =>  $_POST['authPhone'],
                "UF_EMAIL" =>  $_POST['EMAIL'],
                "UF_DESCRIPTION" =>  $_POST['COMPANY_DESCRIPTION'],
                "UF_SITE_LINK" => $_POST['UF_SITE_LINK'],
                "UF_EXECUTOR" => $_POST['UF_EXECUTOR']
            );
            $result = $obj::update($_POST['idCompany'] , $rows);
        }
        $us = new \CUser();


        if( $_POST['UF_EXECUTOR'] == "supplier" || $_POST['UF_EXECUTOR'] == "cu-supplier" ){

            $update = $us->Update(
                $this->id,
                [
                    'UF_DSPI_EXECUTOR' => 1
                ]
            );



        }else{
            if( $_POST['UF_EXECUTOR'] == "customer" ){
                $update = $us->Update(
                    $this->id,
                    [
                        'UF_DSPI_EXECUTOR' => 0
                    ]
                );
            }
        }

//        $obj::update();
    }
    public function updateCompany(){
        $hl = new Hl('CompanyList');
        $obj = $hl->obj;
//        var_dump($hl);
//        //получаю в своем методе получения higloadblock с лимитом 1 , проверяю на существование
        $arParams = array("replace_space"=>"-","replace_other"=>"-");
        $trans = \Cutil::translit($_FILES['__fileLOGO']['name'][0],"ru",$arParams);
        $fileArray = array(
            "name" => $_FILES['__fileLOGO']['name'][0],
            "size" => $_FILES['__fileLOGO']['size'][0],
            "tmp_name" => $_FILES['__fileLOGO']['tmp_name'][0],
            "type" => $_FILES['__fileLOGO']['type'][0],

        );
        $fid = \CFile::SaveFile( $fileArray , false  , false , false , 'list');
        $arFile = \CFile::GetFileArray($fid);

        $pieces = explode(",", $_POST['idElements']);
        $new_arr = array_diff($pieces, array(''));
        $oUser = new \CUser;

        if ($hl->obj !== null && !empty($_POST['idCompany']) ) {

            $rows = array(
                "UF_COMPANY_TYPE" => $_POST['type_company'] ,
                "UF_NUMBER_INN" => (integer) $_POST['INN'],
                "UF_NUMBER_OGRN" =>  $_POST['OGRN'],
                "UF_KOD_KPP" =>  (integer) $_POST['KPP'],
                "UF_KOD_OKPO" =>  $_POST['OKPO'],
                "UF_FIO_GENDIR" =>  $_POST['GEN_DERECTOR'],
                "UF_UR_ADDRESS" =>  $_POST['UR_ADDRESS'],
                "UF_REAL_ADDRESS" =>  $_POST['FACT_ADDRESS'],
                "UF_PHONE" =>  $_POST['authPhone'],
                "UF_EMAIL" =>  $_POST['EMAIL'],
                "UF_LOGO" =>  $arFile['ID'],
                "UF_DESCRIPTION" =>  $_POST['COMPANY_DESCRIPTION'],
                "UF_COMPANY_NAME_MIN" =>  $_POST['UF_COMPANY_NAME_MIN'],
                "UF_BIG_NAME" =>  $_POST['UF_BIG_NAME'],
                "UF_STATUS_MODERATION" => 0,
                "UF_SITE_LINK" => $_POST['UF_SITE_LINK'],
                "UF_EXECUTOR" => $_POST['UF_EXECUTOR']
            );


            foreach ($new_arr as $id => $value){
                if(!empty($value)){
                    $name = 'UF_FILE'.$id;
                    $rows[$name] = $value;
                }
            }


            $result = $obj::update($_POST['idCompany'] , $rows);



        }
//        $obj::update();

    }
    public function setCompany (){
        $hl = new Hl('CompanyList');
        $obj = $hl->obj;


//        var_dump($hl);
//        //получаю в своем методе получения higloadblock с лимитом 1 , проверяю на существование
        $arParams = array("replace_space"=>"-","replace_other"=>"-");
        $trans = \Cutil::translit($_FILES['__fileLOGO']['name'][0],"ru",$arParams);
        $fileArray = array(
            "name" => $_FILES['__fileLOGO']['name'][0],
            "size" => $_FILES['__fileLOGO']['size'][0],
            "tmp_name" => $_FILES['__fileLOGO']['tmp_name'][0],
            "type" => $_FILES['__fileLOGO']['type'][0],

        );
        $fid = \CFile::SaveFile( $fileArray , false  , false , false , 'list');
        $arFile = \CFile::GetFileArray($fid);
        $pieces = explode(",", $_POST['idElements']);
        $new_arr = array_values(array_diff($pieces, array('')));
        $oUser = new \CUser;

        if ($hl->obj !== null) {
            global $USER;
            $idCurrentNewAdmin = $USER->GetID();
            $data = \Bitrix\Main\Type\DateTime::createFromPhp( new DateTime(date('Y-m-d H:i:s')) );
            $rows = array(
                "UF_ADMIN_COMPANY_ID" => (int) $idCurrentNewAdmin ,//айди модератора своей компании
                "UF_COMPANY_TYPE" => $_POST['type_company'] ,
                "UF_NUMBER_INN" => (integer) $_POST['INN'],
                "UF_NUMBER_OGRN" =>  $_POST['OGRN'],
                "UF_KOD_KPP" =>  $_POST['KPP'],
                "UF_KOD_OKPO" =>  $_POST['OKPO'],
                "UF_FIO_GENDIR" =>  $_POST['GEN_DERECTOR'],
                "UF_UR_ADDRESS" =>  $_POST['UR_ADDRESS'],
                "UF_REAL_ADDRESS" =>  $_POST['FACT_ADDRESS'],
                "UF_PHONE" =>  $_POST['authPhone'],
                "UF_EMAIL" =>  $_POST['EMAIL'],
                "UF_LOGO" =>  $arFile['ID'],
                "UF_DESCRIPTION" =>  $_POST['COMPANY_DESCRIPTION'],
                "UF_COMPANY_NAME_MIN" =>  $_POST['UF_COMPANY_NAME_MIN'],
                "UF_BIG_NAME" =>  $_POST['UF_BIG_NAME'],
                "UF_STATUS_MODERATION" => 0,
                "UF_DATA_CREATE" => $data,
                "UF_SITE_LINK" => $_POST['UF_SITE_LINK'],
                "UF_EXECUTOR" => $_POST['UF_EXECUTOR']
            );

            function getHotNum(){
                $randNum = rand(000000 , 999999);
                $hlHotNum = new Hl('CompanyListHotNumber');
                $objHotNum = $hlHotNum->obj;
                $dataOneCount  = $hlHotNum->getListHigload(
                    '103' ,
                    array(
                        'ID',
                        'UF_HOT_NAMBER',
                    ) ,
                    '' ,
                    array( 'UF_HOT_NAMBER' => (int)$randNum) );
                if(!empty($dataOneCount[0]['ID'])){
                    if($dataOneCount[0]['UF_HOT_NAMBER'] == $randNum){
                        getHotNum();
                    }
                }else{
                    return (int)$randNum;
                }
            }

            $num = getHotNum();

            $rows['UF_HOT_NAMBER_COMPANY'] = (int)$num;

            foreach ($new_arr as $id => $value){
                if(!empty($value)){
                    $name = 'UF_FILE'.$id;
                    $rows[$name] = $value;
                }
            }

            $result = $obj::add($rows);

            //ставлю у пользователя модерация - false , на странице модератора при пройденной модерации меняю на true  у пользователя и ставлю компании 2 - модераци пройденна

            $resID = $result->getId();
            $aFields = array(
                'UF_ID_COMPANY' => $resID,
                'UF_MODERATION_ACCESS' => 'false'//пока не прошла модерация от модератора , ставлю компанию , но отменяю овзможность модерирования
            );

            $oUser->Update($idCurrentNewAdmin, $aFields); //$iUserID (int) ID of USER
        }

    }
    public function getEmployeesDetailTask(){

    }
    public function getEmployeesDetail(){
        //getCompanyManager -> получаю id  текущей компании по текущему модератору
        global $USER;

        $currentCompany = $this->getCompanyManager();



        if(!empty($currentCompany[0]["ID"]) &&  !empty($_REQUEST['DetailNum'])){

            //провожу проверку на то  - что модератор ищет именно своего сотрудника
            // сверяю по айди компании наличие айди модератора и айди сотрудника
            $arFilter = Array(
                Array(

                    Array(
                        "UF_ID_COMPANY" => $currentCompany[0]["ID"],
                        "ID" => $_REQUEST['DetailNum']
                    )
                )
            );
            $res = \Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("id","name","last_name","email","personal_phone","UF_CONFIRMED"),
                "filter"=>$arFilter,
            ));
            $arr = [];
            while ($arRes = $res->fetch()) {
                $arr[] = $arRes;
            }

            if(!empty($arr[0]['ID'])){
                global $USER;
                $items = new \Democontent2\Pi\Iblock\Items();
                $items->setUserId(intval($arr[0]['ID']));
                $items->setTtl(0);

                $result['ITEMS'] =  $items->getByUser();
//                debmes($result['ITEMS']);

                //количество заявок всех  - модерация 0 1 2
                $result['COUNT'] = count( $result['ITEMS']);

                //количество заявок всех  - модерация 0

                $result['ITEMS_ACTIVE'] = count( $items->getByUser(true) );
                $elements = [];


                foreach ( $result['ITEMS']  as $id => $val ){
                    if($val['UF_MODERATION'] == 0){
                        $elements[] = $val;
                    }
                }
                $result['COUNT_ACTIVE'] = count(  $elements);

                $result['USER_DETAIL'] = $arr;

                $result['message'] = 'successGetData';


            }else{
                $result['message'] = 'errorDelete';
            }
            return $result;

        }

    }

    /**
     * @return string
     */
    public function UpdateUsEmployees(){

        if(!empty($_POST['UpdateUsEmployeesID'])){
            global $USER;
            $userData = \CUser::GetByID($_POST['UpdateUsEmployeesID']);
            $arUser = $userData->Fetch();

            $_POST['PHONE_RMPLOYEES'] = preg_replace('![^0-9]+!', '', $_POST['PHONE_RMPLOYEES']);

            if($arUser['EMAIL'] !== trim( $_POST['EMAIL_RMPLOYEES'])){
                $template = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/ajax/templateMessageAjax.php');
                $str = ' <h2
                    style="font-size: 36px; 
                    font-family: Helvetica, Arial, sans-serif; 
                    color: #333 !important;
                     margin: 0px;"> Модератор обновил ваш email на сайте - </h2>' .$_SERVER['SERVER_NAME'].'. Ваш текущий логин -'. $_POST['EMAIL_EMPLOYEES'] ;


                $message =  str_replace('#WORK_AREA#' , $str , $template);
                $status = custom_mail( $_POST['EMAIL_EMPLOYEES'] , 'Ссылка  для регистрации на сайте - '.$_SERVER['SERVER_NAME'], $message);
            }


            $GLOBALS['APPLICATION']->RestartBuffer();

            $user = new \CUser;
            $fields = Array(
                "NAME"              => trim($_POST['NAME_RMPLOYEES']),
                "LAST_NAME"         =>  trim($_POST['SURNAME_RMPLOYEES']),
                "EMAIL"             => trim( $_POST['EMAIL_RMPLOYEES']),
                "LOGIN"             =>  trim($_POST['PHONE_RMPLOYEES']),
            );

            $user->Update((int) $_POST['UpdateUsEmployeesID'] , $fields);



            $user->LAST_ERROR;
            debmes( $user->LAST_ERROR );
            die();
            $GLOBALS['APPLICATION']->RestartBuffer();
            $resMess = [];
            if($result == 1){
                $resMess['message'] = 'updateSuccess';
                echo json_encode($resMess);
            }else{
                $resMess['message'] = 'updateError';
                $resMess['errorrMess'] = $user->LAST_ERROR;
                echo json_encode($resMess);
            }

            die();

        }
    }
    public function DeleteNum(){
        $GLOBALS['APPLICATION']->RestartBuffer();


        if(!empty($_REQUEST['passConfirm'])){
            global $USER;
            $userData = \CUser::GetByID($USER->GetID());
            $passBase64 = md5($_REQUEST['passConfirm']);

            $arUser = $userData->Fetch();

            if($arUser['PASSWORD'] == $passBase64){




                $currentCompany = $this->getCompanyManager();

                $arFilter = Array(
                    Array(

                        Array(
                            "UF_ID_COMPANY" => $currentCompany[0]["ID"],
                            "ID" => $_REQUEST['DeleteNum']
                        )
                    )
                );
                $res = \Bitrix\Main\UserTable::getList(Array(
                    "select"=>Array("id","name","last_name","email","personal_phone","UF_CONFIRMED"),
                    "filter"=>$arFilter,
                ));
                $arr = [];
                while ($arRes = $res->fetch()) {
                    $arr[] = $arRes;
                }


                if(!empty($arr[0]['ID'])){
                    if(!empty($_REQUEST['DeleteNum'])){

                        if (\CUser::Delete($_REQUEST['DeleteNum'])) {
                            $GLOBALS['APPLICATION']->RestartBuffer();
                            $result['message'] = 'successDelete';
                            $result['idDelete'] = $arr[0]['ID'];
                            echo json_encode($result);
                            die();

                        }
                    }
                }else{
                    $GLOBALS['APPLICATION']->RestartBuffer();
                    $result['message'] = 'errorDelete';
                    $result['idUserDel'] = $_POST['DeleteNum'];
                    $result['notification'] = 'Данный пользователь с номером - '.$_POST['DeleteNum'].' не найден в данной компании';
                    echo  json_encode($result);
                    die();
                }
            }else{



                $result['message'] = 'errorPass';
                $result['notification'] = 'Не верный пароль';
                echo  json_encode($result);
                die();
            }
        }






    }
    public function getCompanyEmployeesOne($id){

        //getCompanyManager -> получаю id  текущей компании по текущему модератору
//        getCompanyManager


        //далее собираю всех сотрудников и возращаю их
        if(!empty($id)){

            $arFilter = Array(
                Array(

                    Array(
                        "UF_ID_COMPANY" => $id
                    )
                )
            );
            $res = \Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("id","name","last_name","email","personal_phone","UF_CONFIRMED"),
                "filter"=>$arFilter,
            ));
            $arr = [];
            while ($arRes = $res->fetch()) {
                $arr[] = $arRes;
            }
            $usersList = [];
            global $USER;
            $currentUserId = $USER->GetID();
            foreach ($arr as $id => $value){
                if($value['ID'] !== $currentUserId){
                    $usersList['ITEMS_USERS'][] = $value;
                }
            }

//            global $USER;
            $items = new \Democontent2\Pi\Iblock\Items();
//            $items->setUserId(intval($arr[0]['ID']));
//ПЕРЕПИСАТь
//            $result['ITEMS'] =  $items->getByUser();
            foreach ($usersList['ITEMS_USERS'] as $id => $value){
                $items->setUserId($value['ID']);
                $items->setTtl(0);
                $itemsEl['ITEMS'] =  $items->getByUser();
                $result['COUNT_ELEMENTS'] = count($itemsEl['ITEMS']);
                $result['COUNT_ELEMENTS_ACTIVE'] = count($items->getByUserModeration(true));


                $usersList['ITEMS_USERS'][$id]['COUNTS'] = $result;



            }






            return  $usersList;


        }else{
            return "У вас нету промодерированной компании.Создать на странице ".$_SERVER['DOCUMENT_ROOT'].'/user/company/';
        }
    }
    public function getCompanyEmployees(){
        //getCompanyManager -> получаю id  текущей компании по текущему модератору
//        getCompanyManager
        global $USER;
        $idManager[0]  = $USER->GetID();
        $currentCompany = $this->getCompanyManager($idManager);

        //далее собираю всех сотрудников и возращаю их
        if(!empty($currentCompany[0]["ID"])){

            $arFilter = Array(
                Array(

                    Array(
                        "UF_ID_COMPANY" => $currentCompany[0]["ID"]
                    )
                )
            );
            $res = \Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("id","name","last_name","email","personal_phone","UF_CONFIRMED"),
                "filter"=>$arFilter,
            ));
            $arr = [];
            while ($arRes = $res->fetch()) {
                $arr[] = $arRes;
            }
            $usersList = [];
            global $USER;
            $currentUserId = $USER->GetID();
            foreach ($arr as $id => $value){
                if($value['ID'] !== $currentUserId){
                    $usersList['ITEMS_USERS'][] = $value;
                }
            }

//            global $USER;
            $items = new \Democontent2\Pi\Iblock\Items();
//            $items->setUserId(intval($arr[0]['ID']));
//ПЕРЕПИСАТь
//            $result['ITEMS'] =  $items->getByUser();
            foreach ($usersList['ITEMS_USERS'] as $id => $value){
                $items->setUserId($value['ID']);
                $items->setTtl(0);
                $itemsEl['ITEMS'] =  $items->getByUser();
                $result['COUNT_ELEMENTS'] = count($itemsEl['ITEMS']);
                $result['COUNT_ELEMENTS_ACTIVE'] = count($items->getByUserModeration(true));


                $usersList['ITEMS_USERS'][$id]['COUNTS'] = $result;



            }






            return  $usersList;


        }else{
            return "У вас нету промодерированной компании.Создать на странице ".$_SERVER['DOCUMENT_ROOT'].'/user/company/';
        }
    }
    public function addUserCompany(){
        global $USER;
        $id = $USER->GetID();

        $rsUser = \CUser::GetByID($id);
        $arUser = $rsUser->Fetch();
        $arFile = \CFile::GetFileArray($arUser['PERSONAL_PHOTO']);
        $messFile = array(
            "name" => $arFile['FILE_NAME'],
            "size" => $arFile['FILE_SIZE'],
            "tmp_name" => $_SERVER['DOCUMENT_ROOT'].$arFile['SRC'],
            "type" => $arFile['CONTENT_TYPE'],
        );



        $ar = array(0 => $id);
        $currentCompany = $this->getCompanyManager($ar);

        if($currentCompany[0]['UF_STATUS_MODERATION'] == 2){
            if(!empty($currentCompany[0]["ID"])){
                $user = new \CUser;
                $_POST['phone'] = preg_replace('![^0-9]+!', '', $_POST['phone']);
                $arFields = array(
                    'NAME'             => $_POST['NAME_NEW_EMPLOYEES'],
                    'LAST_NAME'        => $_POST['SURNAME_EMPLOYEES'],
                    'LID'              => 'ru',
                    'ACTIVE'           => 'Y',
                    'LOGIN'          => $_POST['EMAIL_EMPLOYEES'],
                    'EMAIL'           =>  $_POST['EMAIL_EMPLOYEES'],
                    'PERSONAL_PHONE'           => $_POST['PHONE_EMPLOYEES'],
                    'GROUP_ID'         => array(5),
                    "UF_ID_COMPANY"       => $currentCompany[0]["ID"],
                    "UF_CONFIRMED"       => 'false',
//                    "PERSONAL_PHOTO" => $arFile['ID']
                );



                $pass = randString(7);
                $arFields['PASSWORD'] = $pass;
                $arFields['CONFIRM_PASSWORD'] = $pass;

                $ID = $user->Add($arFields);

                $sql = "UPDATE   `sitemanager`.`b_user` SET PERSONAL_PHOTO=".$arFile['ID']."  WHERE ID=".$ID;
                global $DB;
                $res = $DB->Query($sql, false);

//            $status = custom_mail( $_POST['email'] , ', $message);
                $res = \CUser::GetByID((int)$ID);

                $checkword = '';
                $name = '';
                $last_name = '';
                $login = '';
                if($ar_res = $res->GetNext()){
                    $checkword = $ar_res['CHECKWORD'];
                    $name = $ar_res['NAME'];
                    $last_name = $ar_res['LAST_NAME'];
                    $login = $ar_res['LOGIN'];

                }



//
//
//
//            Event::send(array(
//                "EVENT_NAME" => "USER_PASS_REQUEST",
//                "LID" => "s1",
//                "C_FIELDS" => array(
//                    "CHECKWORD" => $checkword,
//                    "NAME" => $name,
//                    "LAST_NAME" => $last_name,
//                    "USER_LOGIN" => urlencode($login),
//                ),
//            ));




//            Модератор добавил вас на сайте - '.$_SERVER['SERVER_NAME'].'.перейдите по ссылке для создания пароля , текущий логин -'. $_POST['EMAIL_EMPLOYEES']
                $template = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/ajax/templateMessageAjax.php');
                $link = "<a href=http://".$_SERVER['HTTP_HOST']."/createPass/index.php?change_password=yes&lang=ru&USER_CHECKWORD=".$checkword."&USER_LOGIN=".urlencode($login).">ссылке</a>";
                $str = ' <h2
                    style="font-size: 36px; 
                    font-family: Helvetica, Arial, sans-serif; 
                    color: #333 !important;
                     margin: 0px;">Здравствуйте '.$name.'</h2>' .
                    '<br> Модератор добавил вас на сайте -  '.$_SERVER['SERVER_NAME'].'.Перейдите по '.$link.' для создания пароля ' ;


                $message =  str_replace('#WORK_AREA#' , $str , $template);
                $status = custom_mail( $_POST['EMAIL_EMPLOYEES'] , 'Ссылка  для регистрации на сайте - '.$_SERVER['SERVER_NAME'], $message);
                $GLOBALS['APPLICATION']->RestartBuffer();
//если добавлен пользователь и сообщение отправилось
//                if ((int)$ID > 0 && $status == 1) {
// если пользоватлеь добавлен
                if ((int)$ID > 0) {
                    $dataNewForm = '<div class="task-preview task-preview-employees-fir">
<div class="tbl tbl-fixed">
        <div class="tbc">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-xxs-6">
                    <div class="form-group ">
                        <p   class="name_employees">'.$_POST['NAME_NEW_EMPLOYEES'].'</p>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-xxs-6">
                    <div class="form-group ">
                        <p   class="surname_employees">'.$_POST['SURNAME_EMPLOYEES'].'</p>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-xxs-6">
                    <div class="form-group ">
                        <p   class="phone_employees">'.$_POST['PHONE_EMPLOYEES'].'</p>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-xxs-6">
                    <div class="form-group ">
                        <p   class="email_employees">'.$_POST['EMAIL_EMPLOYEES'].'</p>
                    </div>
                </div>
            </div>
            <div class="row rowBtn">
                <a href="/user/employees/update/?DetailNum='.$ID.'" >
                    <div class=" btn btn-green  btnEmployeesNewFor ">
                        Изменить сотрудника
                    </div>
                </a>
            </div>
        </div>
        <div class="tbc tbc-info ourBlockEmplo">
            <div class="price-box info-cardEmployees">
                   <span > Сводка</span>
            </div>
            <div class="list-info">
                <ul>
                    <li>
                        <p>
                            Кол-во заявок: <b>0</b>
                        </p>
                    </li>
                    <li>
                        <p>
                            Активные заявки: <b>0</b>
                        </p>
                    </li>
                    <li>
                        <p>Статус</p>
                            <div div class="alert alert-danger" role="alert">
                                <strong>Не подтвержден</strong>
                            </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>';


                    $status = [
                        'message' => "REGOK",
                        'IDUSER'  => $ID,
                        'data' => $dataNewForm
                    ];

                    echo json_encode($status);
                    die();
                } else {

                    $status = [
                        'message' => "FLASEREG",
                        'message_send' => $user->LAST_ERROR
                    ];
                    echo json_encode($status);
                    die();
                }
            }
        }else{
            $status = [
                'message' => "NOTISSETCOMPANYMODERATION",
                'message_send' => "Ваша компания не подтвержденна"
            ];
            echo json_encode($status);
        }

    }

    public function setExecutor($skipPayment = false)
    {
        if ($this->id) {
            $this->ttl = 0;
            $userParams = $this->get();
            if (!intval($userParams['UF_DSPI_EXECUTOR'])) {
                $registrationFee = intval(Option::get(DSPI, 'registration_fee'));

                if ($registrationFee > 0 && !$skipPayment) {
                    $account = new Account($this->id);
                    $balance = $account->getAmount();

                    $order = new Order($this->id);
                    $order->setType('executor');
                    $order->setSum($registrationFee);
                    $order->setDescription(Loc::getMessage('REGISTER_FEE_PAYMENT'));

                    if ($balance > 0 && $balance >= $registrationFee) {
                        if ($order->make(true)) {
                            $order->setPayed(true);

                            $account->setAmount($order->getSum());
                            $account->setDescription(Loc::getMessage('REGISTER_FEE_PAYMENT'));
                            $account->withdrawal();
                        }
                    } else {
                        $order->make();
                        if ($order->getRedirect()) {
                            $this->redirect = $order->getRedirect();
                        }
                    }
                } else {
                    $taggedCache = Application::getInstance()->getTaggedCache();
                    $us = new \CUser();
                    $update = $us->Update(
                        $this->id,
                        [
                            'UF_DSPI_EXECUTOR' => 1
                        ]
                    );

                    if ($update) {
                        try {
                            $fireBase = new FireBase($this->id);
                            $fireBase->webPush([
                                'title' => Loc::getMessage('SERVICE_APPLIED'),
                                'body' => Loc::getMessage('SERVICE_EXECUTOR_APPLIED'),
                            ]);
                            unset($fireBase);

                            $profile = new Profile();
                            $profile->setUserId($this->id);

                            $subscriptions = new Subscriptions();
                            $subscriptions->setUserId($this->id);

                            $menu = new Menu();
                            $menuItems = $menu->get();

                            $data = [
                                'description' => '',
                                'specializations' => [],
                                'subSpecializations' => []
                            ];
                            $specializations = [];
                            $subSpecializations = [];

                            foreach ($menuItems as $k => $v) {
                                if (isset($v['items'])) {
                                    $subscriptions->setIBlockType($k);

                                    foreach ($v['items'] as $item) {
                                        $specializations[$k] = $menuItems[$k]['name'];
                                        $subSpecializations[$k][$item['id']] = $menuItems[$k]['items'][$item['id']]['name'];

                                        $subscriptions->setIBlockId($item['id']);
                                        $subscriptions->add();
                                    }
                                }
                            }

                            $data['specializations'] = $specializations;
                            $data['subSpecializations'] = $subSpecializations;

                            $profile->setTtl(0);
                            $profile->setData($data);
                            $profile->update();
                        } catch (\Exception $e) {

                        }
                    }

                    $taggedCache->clearByTag(md5('user_' . $this->id));
                    $taggedCache->clearByTag('executors');
                }
            }
        }
    }

    protected function apiKey()
    {
        $result = '';

        if ($this->id > 1) {
            try {
                $userTable = new UserTable();
                $get = $userTable::getList(
                    [
                        'select' => [
                            'UF_DSPI_API_KEY'
                        ],
                        'filter' => [
                            '=ID' => $this->id
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = $res['UF_DSPI_API_KEY'];
                    header('X-PI-KEY: ' . $res['UF_DSPI_API_KEY']);
                }
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            }
        }

        return $result;
    }

    public function get()
    {
        $result = [];

        if ($this->id) {
            try {
                $cache = Application::getInstance()->getCache();
                $cache_time = $this->ttl;
                $cache_id = md5('user_' . $this->id);
                $cache_path = '/' . DSPI . '/users';

                $taggedCache = Application::getInstance()->getTaggedCache();

                if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                    $res = $cache->getVars();

                    if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                        $result = $res[$cache_id];
                    }
                } else {
                    $taggedCache->startTagCache($cache_path);
                    $taggedCache->registerTag($cache_id);

                    $userData = UserTable::getList(
                        [
                            'select' => [
                                'ID',
                                'NAME',
                                'LAST_NAME',
                                'LOGIN',
                                'EMAIL',
                                'PERSONAL_PHONE',
                                'PERSONAL_PHOTO',
                                'DATE_REGISTER',
                                'UF_DSPI_MOD_OFF',
                                'UF_DSPI_API_KEY',
                                'UF_DSPI_LIMIT',
                                'UF_DSPI_EXECUTOR',
                                'UF_DSPI_DOCUMENTS',
                                'UF_DSPI_SAFECROW_ID',
                                'UF_DSPI_CITY',
                                'UF_DSPI_RATING',
                                'UF_DSPI_BUSY',
                                'UF_MODERATION_ACCESS',
                                'UF_CONFIRMED'
                            ],
                            'filter' => [
                                '=ID' => $this->id
                            ]
                        ]
                    )->fetch();

                    if (intval($userData['ID'])) {
                        $result = $userData;

                        if (intval($result['PERSONAL_PHOTO'])) {
                            $result['AVATAR'] = \CFile::ResizeImageGet(
                                intval($result['PERSONAL_PHOTO']),
                                [
                                    'width' => 100,
                                    'height' => 100
                                ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                true
                            );
                        }

                        $card = new Cards();
                        $card->setUserId($userData['ID']);
                        $result['CARD'] = (count($card->getUserCard())) ? 1 : 0;
                    }

                    if ($cache_time > 0) {
                        $cache->startDataCache($cache_time, $cache_id, $cache_path);
                        if (!count($result)) {
                            $cache->abortDataCache();
                            $taggedCache->abortTagCache();
                        }
                        $cache->endDataCache([$cache_id => $result]);
                        $taggedCache->endTagCache();
                    }
                }

                if (count($result) > 0) {
                    if (!intval($result['UF_DSPI_SAFECROW_ID'])) {
                        if (strlen(Option::get(DSPI, 'safeCrowApiKey')) > 0
                            && strlen(Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                            $safeCrow = new SafeCrow();
                            $safeCrow->setUserId(intval($result['ID']));
                            $safeCrow->setPhone($result['PERSONAL_PHONE']);
                            $safeCrow->setEmail($result['EMAIL']);
                            $safeCrow->setName($result['NAME'] . ((strlen($result['LAST_NAME']) > 0) ? ' ' . $result['LAST_NAME'] : ''));

                            $safeCrowId = $safeCrow->addUser();

                            if ($safeCrowId > 0) {
                                $result['UF_DSPI_SAFECROW_ID'] = $safeCrowId;

                                $taggedCache = Application::getInstance()->getTaggedCache();
                                $taggedCache->clearByTag(md5('user_' . $this->id));
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                preg_match_all('/UF_DSPI_([A-Z_]+)/m', $e->getMessage(), $matches, PREG_SET_ORDER, 0);
                if (count($matches) > 0) {
                    if (isset($matches[0][0])) {
                        switch ($matches[0][0]) {
                            case 'UF_DSPI_DOCUMENTS':
                            case 'UF_DSPI_EXECUTOR':
                            case 'UF_DSPI_MOD_OFF':
                            case 'UF_DSPI_BUSY':
                                $userType = new \CUserTypeEntity();
                                $userType->Add(
                                    [
                                        "ENTITY_ID" => "USER",
                                        "FIELD_NAME" => $matches[0][0],
                                        "USER_TYPE_ID" => "boolean",
                                        "XML_ID" => "",
                                        "MULTIPLE" => "N",
                                        "MANDATORY" => "N",
                                        "SHOW_FILTER" => "N",
                                        "SHOW_IN_LIST" => "Y",
                                        "EDIT_IN_LIST" => "Y",
                                        "DEFAULT_VALUE" => 0,
                                        "IS_SEARCHABLE" => "N",
                                        'EDIT_FORM_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_COLUMN_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_FILTER_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ]
                                    ]
                                );
                                break;
                            case 'UF_DSPI_CITY':
                            case 'UF_DSPI_SAFECROW_ID':
                            case 'UF_DSPI_LIMIT':
                                $userType = new \CUserTypeEntity();
                                $userType->Add(
                                    [
                                        "ENTITY_ID" => "USER",
                                        "FIELD_NAME" => $matches[0][0],
                                        "USER_TYPE_ID" => "integer",
                                        "XML_ID" => "",
                                        "MULTIPLE" => "N",
                                        "MANDATORY" => "N",
                                        "SHOW_FILTER" => "N",
                                        "SHOW_IN_LIST" => "Y",
                                        "EDIT_IN_LIST" => "Y",
                                        "DEFAULT_VALUE" => "0",
                                        "IS_SEARCHABLE" => "N",
                                        'EDIT_FORM_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_COLUMN_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_FILTER_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ]
                                    ]
                                );
                                break;
                            case 'UF_DSPI_API_KEY':
                                $userType = new \CUserTypeEntity();
                                $userType->Add(
                                    [
                                        "ENTITY_ID" => "USER",
                                        "FIELD_NAME" => $matches[0][0],
                                        "USER_TYPE_ID" => "string",
                                        "XML_ID" => "",
                                        "MULTIPLE" => "N",
                                        "MANDATORY" => "N",
                                        "SHOW_FILTER" => "N",
                                        "SHOW_IN_LIST" => "Y",
                                        "EDIT_IN_LIST" => "Y",
                                        "DEFAULT_VALUE" => "",
                                        "IS_SEARCHABLE" => "N",
                                        'EDIT_FORM_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_COLUMN_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_FILTER_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ]
                                    ]
                                );
                                break;
                            case 'UF_DSPI_RATING':
                                $userType = new \CUserTypeEntity();
                                $userType->Add(
                                    [
                                        "ENTITY_ID" => "USER",
                                        "FIELD_NAME" => $matches[0][0],
                                        "USER_TYPE_ID" => "double",
                                        "XML_ID" => "",
                                        "MULTIPLE" => "N",
                                        "MANDATORY" => "N",
                                        "SHOW_FILTER" => "N",
                                        "SHOW_IN_LIST" => "Y",
                                        "EDIT_IN_LIST" => "Y",
                                        "DEFAULT_VALUE" => 0,
                                        "IS_SEARCHABLE" => "N",
                                        'EDIT_FORM_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_COLUMN_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'LIST_FILTER_LABEL' => [
                                            'ru' => Loc::getMessage($matches[0][0])
                                        ],
                                        'SETTINGS' => [
                                            'DEFAULT_VALUE' => 0,
                                            'PRECISION' => 2,
                                            'MIN_VALUE' => 0,
                                            'MAX_VALUE' => 100,
                                        ]
                                    ]
                                );
                                break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function restorePassword()
    {
        $result = false;

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) && Utils::validatePhone($this->phone)) {
            $userTable = new UserTable();
            $get = $userTable::getList(
                [
                    'select' => [
                        'ID',
                        'NAME',
                        'EMAIL'
                    ],
                    'filter' => [
                        '=EMAIL' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                        '=PERSONAL_PHONE' => Utils::validatePhone($this->phone)
                    ],
                    'limit' => 1
                ]
            );
            while ($res = $get->fetch()) {
                $result = true;
                $password = randString(10, '123456789');
                $us = new \CUser();
                $update = $us->Update(
                    intval($res['ID']),
                    [
                        'PASSWORD' => $password,
                        'CONFIRM_PASSWORD' => $password
                    ]
                );
                if ($update) {
                    $sms = new Sms();
                    $sms->setPhone($this->phone);

                    if (strlen(Option::get(DSPI, 'restoreSmsText')) > 0) {
                        $sms->setText(
                            str_replace('#PASSWORD#', $password, Option::get(DSPI, 'restoreSmsText'))
                        );
                    } else {
                        $sms->setText(
                            Loc::getMessage(
                                'USER_SEND_NEW_PASSWORD',
                                ['#PASSWORD#' => $password]
                            )
                        );
                    }

                    $sms->make();

                    try {
                        Event::send(
                            [
                                'EVENT_NAME' => 'DSPI_RESTORE_PASSWORD',
                                'LID' => Application::getInstance()->getContext()->getSite(),
                                'C_FIELDS' => [
                                    'NAME' => $res['NAME'],
                                    'EMAIL' => $res['EMAIL'],
                                    'PASSWORD' => $password
                                ]
                            ]
                        );
                    } catch (SystemException $e) {
                    }

                    $event = new EventManager(
                        'restorePassword',
                        [
                            'userId' => intval($res['ID'])
                        ]
                    );
                    $event->execute();

                    Logger::add(intval($res['ID']), 'restorePassword');
                }
            }
        }

        return $result;
    }

    public function register()
    {
        $result = 0;
        $phoneRequired = intval(Option::get(DSPI, 'register_phone_required')) > 0;

        if ($phoneRequired) {
            if (!Utils::validatePhone($this->phone)) {
                return $result;
            }
        }

        if (strlen(trim(htmlspecialcharsbx(strip_tags($this->name)))) > 0 && strlen($this->email) > 0) {
            if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $filter = [
                    '>ID' => 0,
                    'LOGIC' => 'AND',
                    [
                        'LOGIC' => 'OR',
                        [
                            '=EMAIL' => filter_var($this->email, FILTER_VALIDATE_EMAIL)
                        ]
                    ]
                ];

                if ($phoneRequired) {
                    $filter = [
                        '>ID' => 0,
                        'LOGIC' => 'AND',
                        [
                            'LOGIC' => 'OR',
                            [
                                '=EMAIL' => filter_var($this->email, FILTER_VALIDATE_EMAIL)
                            ],
                            [
                                '=PERSONAL_PHONE' => Utils::validatePhone($this->phone)
                            ]
                        ]
                    ];
                }

                $userTable = new UserTable();
                try {
                    $id = 0;
                    $get = $userTable::getList(
                        [
                            'select' => ['ID'],
                            'filter' => $filter,
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $id = intval($res['ID']);
                    }

                    if (!$id) {
                        $enabledEmailConfirmation = false;
                        $enabledUserRegistration = true;
                        $enabledCaptcha = false;
                        $registerGroup = intval(Option::get('main', 'new_user_registration_def_group'));

                        if (Option::get('main', 'new_user_registration_email_confirmation') == 'Y') {
                            $enabledEmailConfirmation = true;
                            Option::set('main', 'new_user_registration_email_confirmation', 'N');
                        }

                        if (Option::get('main', 'new_user_registration') == 'N') {
                            $enabledUserRegistration = false;
                            Option::set('main', 'new_user_registration', 'Y');
                        }

                        if (Option::get('main', 'captcha_registration') == 'Y') {
                            $enabledCaptcha = true;
                            Option::set('main', 'captcha_registration', 'N');
                        }

                        $password = randString(8, '123456789');
                        $us = new \CUser();
                        $add = $us->Add(
                            [
                                'NAME' => trim(htmlspecialcharsbx(strip_tags($this->name))),
                                'LOGIN' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                'EMAIL' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                'PERSONAL_PHONE' => $phoneRequired ? Utils::validatePhone($this->phone) : '',
                                'PASSWORD' => $password,
                                'CONFIRM_PASSWORD' => $password,
                                'ACTIVE' => 'Y',
                                'GROUP_ID' => [$registerGroup],
                                'UF_DSPI_API_KEY' => md5(microtime(true) . randString(15))
                            ]
                        );
                        if ($add) {
                            $error = false;
                            $result = intval($add);
                            if (strlen(Option::get(DSPI, 'defaultSmsGate')) > 0 && $phoneRequired) {
                                $sms = new Sms();
                                $sms->setPhone(Utils::validatePhone($this->phone));

                                if (strlen(Option::get(DSPI, 'registerSmsText')) > 0) {
                                    $sms->setText(
                                        str_replace(
                                            '#LOGIN#',
                                            filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                            str_replace(
                                                '#PASSWORD#',
                                                $password,
                                                Option::get(DSPI, 'registerSmsText')
                                            )
                                        )
                                    );
                                } else {
                                    $sms->setText(
                                        Loc::getMessage(
                                            'USER_SEND_PASSWORD',
                                            [
                                                '#LOGIN#' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                                '#PASSWORD#' => $password
                                            ]
                                        )
                                    );
                                }

                                $sms->make();

                                if (strlen($sms->getError()) > 0 && $sms->getError() !== 'no') {
                                    //���� �������� �������� ��� � ��� ����������� � �������
                                    //$error = true;
                                }
                            }

                            if (!$error) {
                                Event::send(
                                    [
                                        'EVENT_NAME' => 'DSPI_NEW_USER',
                                        'LID' => Application::getInstance()->getContext()->getSite(),
                                        'C_FIELDS' => [
                                            'NAME' => trim(htmlspecialcharsbx(strip_tags($this->name))),
                                            'LOGIN' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                            'PHONE' => $phoneRequired ? Utils::validatePhone($this->phone) : '',
                                            'PASSWORD' => $password
                                        ]
                                    ]
                                );

                                Logger::add(
                                    $add,
                                    'registerSuccess',
                                    [
                                        'id' => $add,
                                        'name' => trim(htmlspecialcharsbx(strip_tags($this->name))),
                                        'email' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                        'phone' => $phoneRequired ? Utils::validatePhone($this->phone) : ''
                                    ]
                                );

                                $event = new EventManager(
                                    'registerSuccess',
                                    [
                                        'id' => $add,
                                        'name' => trim(htmlspecialcharsbx(strip_tags($this->name))),
                                        'email' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                        'phone' => $phoneRequired ? Utils::validatePhone($this->phone) : ''
                                    ]
                                );
                                $event->execute();
                            } else {
//                                \CUser::Delete($add);
//                                $result = 0;
                            }
                        } else {
                            Logger::add(
                                $add,
                                'registerFailed',
                                [
                                    'name' => trim(htmlspecialcharsbx(strip_tags($this->name))),
                                    'email' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                    'phone' => $phoneRequired ? Utils::validatePhone($this->phone) : '',
                                    'error' => $us->LAST_ERROR
                                ]
                            );

                            $event = new EventManager(
                                'registerFailed',
                                [
                                    'name' => trim(htmlspecialcharsbx(strip_tags($this->name))),
                                    'email' => filter_var($this->email, FILTER_VALIDATE_EMAIL),
                                    'phone' => $phoneRequired ? Utils::validatePhone($this->phone) : '',
                                    'error' => $us->LAST_ERROR
                                ]
                            );
                            $event->execute();
                        }

                        if ($enabledEmailConfirmation) {
                            Option::set('main', 'new_user_registration_email_confirmation', 'Y');
                        }

                        if (!$enabledUserRegistration) {
                            Option::set('main', 'new_user_registration', 'N');
                        }

                        if ($enabledCaptcha) {
                            Option::set('main', 'captcha_registration', 'Y');
                        }
                    }
                } catch (ArgumentException $e) {
                } catch (SystemException $e) {
                }
            }
        }

        return $result;
    }
    public function getExecutorsFilter($iBlockType = '', $iBlockId = 0 , $arUser = false){
        global $USER;

        $this->limit = 5;
        $limit = 5;
        unset( $this->order['USER_ID']);
        $this->order['USER.DATE_REGISTER'] = "DESC";
        $filter = [];
        if($_POST['ajaxItem'] == "Y"){
//            $GLOBALS['APPLICATION']->RestartBuffer();
            pre($_POST);
            $filter['UF_IBLOCK_ID'] = $_POST['subCodigories'];
        }
        $profile = new Profile();
        $cards = new Cards();
        $reviews = new Reviews();
        $userTable = new UserTable();
        $npageSize = 1;
        $offset = 1;
        $filter = [
            '=USER_ACTIVE' => 'Y',
            '=USER_EXECUTOR' => 1
        ];

        if ($USER->IsAuthorized()){
            $idUserCurrent = $USER->GetID();
            $rsUser = \CUser::GetByID($idUserCurrent);
            $arUser = $rsUser->Fetch();

        }else{
            $arUser['UF_DSPI_CITY'] = 1;
        }
        $filter["=USER.UF_DSPI_CITY"] = $arUser['UF_DSPI_CITY'];
        if($_POST['ajaxItem'] == "Y"){
            if(!empty($_POST['sortOne'])){
                if($_POST['sortOne'] == "po-date-height"){
//                    "DATE_REGISTER" =>
//                    debmes($this->order);
                    //от нового к старому
                    unset( $this->order['USER_ID']);
                    $this->order['USER.DATE_REGISTER'] = "DESC";
                }
                if($_POST['sortOne'] == "po-date-down"){
                    //от старого к новому
                    unset( $this->order['USER_ID']);
                    $this->order['USER.DATE_REGISTER'] = "ASC";
                }
            }
            if(!empty($_POST['sortTwe'])){
                if($_POST['sortOne'] == "po-rate-down"){

                }
                if($_POST['sortOne'] == "po-rate-height"){

                }
            }
            if(!empty($_POST['offset'])){
                $this->offset = $_POST['offset'];
            }
            if(!empty($_POST['npagesize'])){
                $npageSize = $_POST['npagesize'];
            }
            if(!empty($_POST['city']) && $_POST['city'] !== "emptyCity" && $_POST['city'] !== "notcity"){
                $filter["=USER.UF_DSPI_CITY"] = $_POST['city'];
            }else{
                unset( $filter["=USER.UF_DSPI_CITY"]);
            }
            if(!empty($_POST['subCodigories'][0])){
                $subscriptionsTable = new Hl('Democontentpisubscriptions');
                $obj = $subscriptionsTable->obj;
                if ($subscriptionsTable->obj !== null) {
                    $get = $obj::getList(
                        [
                            'select' => [
                                "ID","UF_USER_ID","UF_IBLOCK_TYPE","UF_IBLOCK_ID"
                            ],
                            'filter' => [
                                "UF_IBLOCK_ID" => $_POST['subCodigories']
                            ]
                        ]
                    );
                    $messIdUsers = [];
                    while ($res = $get->fetch()) {
                        $messIdUsers[] = $res['UF_USER_ID'];
                    }

                    $filter["USER_ID"] = $messIdUsers;


                }
            }

            if(!empty($_POST['currentlimit'])){
                $limit = $_POST['currentlimit'];
            }else{
                $limit = 5;
            }
        }
        $arParams["NAV_PARAMS"] =  array("nPageSize" => $npageSize );
        $arParams["SELECT"] =  array("UF_DSPI_EXECUTOR" , "UF_DSPI_CITY" , "UF_DSPI_DOCUMENTS"  );




        $subscriptionsTable = new Hl('Democontentpisubscriptions');
        $obj = $subscriptionsTable->obj;
        $get = $obj::getList(
            [
                'select' => [
                    'UF_USER_ID',
                    'USER_ID' => 'USER.ID',
                    'USER_ACTIVE' => 'USER.ACTIVE',
                    'USER_EXECUTOR' => 'USER.UF_DSPI_EXECUTOR',
                    'USER_CITY' => 'USER.UF_DSPI_CITY',
                    'USER_DOCUMENTS' => 'USER.UF_DSPI_DOCUMENTS',
                    'USER_RATING' => 'USER.UF_DSPI_RATING',
                ],
                'runtime' => [
                    'USER' => [
                        'data_type' => $userTable::getEntity(),
                        'reference' => [
                            '=this.UF_USER_ID' => 'ref.ID'
                        ],
                        'join_type' => 'inner'
                    ]
                ],
                'filter' => $filter,
                'offset' => $this->offset,// смещение для limit
                'limit' => $limit,// количество записей
                'order' => $this->order,// параметры сортировки
                'group' => ['UF_USER_ID']// явное указание полей, по которым нужно группировать результат
            ]
        );



        $i = 0;
        $arIdes = [];
        global $USER;
        $us = new \Democontent2\Pi\User(intval($USER->GetID()));
        while ($res = $get->fetch()) {

            $this->id = intval($res['USER_ID']);
            $arIdes[] = $res['USER_ID'];
            $reviews->setUserId(intval($res['USER_ID']));
            $cards->setUserId(intval($res['USER_ID']));
            $profile->setUserId(intval($res['USER_ID']));


            $res = $this->get();
            $arIds = [ 0 => $res['ID']];
            $res['COMPANY'] = $us->getCompanyManager($arIds);
            $res['CURRENT_RATING'] = $reviews->rating();
            $res['PROFILE'] = $profile->get();
            $res['CARD'] = (count($cards->getUserCard()) > 0) ? 1 : 0;

            $result[] = $res;
        }
        return $result;

        if($_POST['ajaxItem'] == "Y"){
            die();
        }
    }

    public function getExecutors($iBlockType = '', $iBlockId = 0)
    {

        if($_POST['ajaxItem'] == "Y"){
//            $GLOBALS['APPLICATION']->RestartBuffer();
            pre($_POST);
        }
        $result = [];



            try {
                $profile = new Profile();
                $cards = new Cards();
                $reviews = new Reviews();
                $userTable = new UserTable();
                $subscriptionsTable = new Hl('Democontentpisubscriptions');

                if ($subscriptionsTable->obj !== null) {
                    $filter = [
                        '=USER_ACTIVE' => 'Y',
                        '=USER_EXECUTOR' => 1
                    ];

                    if (strlen($iBlockType) > 0) {
                        $filter['=UF_IBLOCK_TYPE'] = $iBlockType;
                    }

                    if (intval($iBlockId) > 0) {
                        $filter['=UF_IBLOCK_ID'] = intval($iBlockId);
                    }

                    if ($this->cityId > 0) {
                        $filter['=USER_CITY'] = $this->cityId;
                    }

                    if ($this->verification > 0) {
                        $filter['=USER_DOCUMENTS'] = 1;
                    }

                    if($_POST['ajaxItem'] == "Y"){
                        if(!empty($_POST['subCodigories'][0])){

                            $subscriptionsTable = new Hl('Democontentpisubscriptions');
                            $obj = $subscriptionsTable->obj;

//                            'runtime' => [
//                                'USER' => [
//                                    'data_type' => $userTable::getEntity(),
//                                    'reference' => [
//                                        '=this.UF_USER_ID' => 'ref.ID'
//                                    ],
//                                    'join_type' => 'inner'
//                                ]
//                            ],
                            $filterSubscribes = [];
                            $get = $obj::getList(
                                [
                                    'select ' => [
                                        'UF_USER_ID',
                                    ],
                                    'filter' => $filter,
                                    'group' => ['UF_USER_ID']
                                ]
                            );
                            while ($resulatAjaxCOtigories = $get->fetch()) {
                                pre($resulatAjaxCOtigories);
                            }
                        }


                    }
                    $obj = $subscriptionsTable->obj;

                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_USER_ID',
                                'USER_ID' => 'USER.ID',
                                'USER_ACTIVE' => 'USER.ACTIVE',
                                'USER_EXECUTOR' => 'USER.UF_DSPI_EXECUTOR',
                                'USER_CITY' => 'USER.UF_DSPI_CITY',
                                'USER_DOCUMENTS' => 'USER.UF_DSPI_DOCUMENTS',
                            ],
                            'runtime' => [
                                'USER' => [
                                    'data_type' => $userTable::getEntity(),
                                    'reference' => [
                                        '=this.UF_USER_ID' => 'ref.ID'
                                    ],
                                    'join_type' => 'inner'
                                ]
                            ],
                            'filter' => $filter,
                            'group' => ['UF_USER_ID']
                        ]
                    );

                    $this->total = $get->getSelectedRowsCount();

                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_USER_ID',
                                'USER_ID' => 'USER.ID',
                                'USER_ACTIVE' => 'USER.ACTIVE',
                                'USER_EXECUTOR' => 'USER.UF_DSPI_EXECUTOR',
                                'USER_CITY' => 'USER.UF_DSPI_CITY',
                                'USER_DOCUMENTS' => 'USER.UF_DSPI_DOCUMENTS',
                                'USER_RATING' => 'USER.UF_DSPI_RATING',
                            ],
                            'runtime' => [
                                'USER' => [
                                    'data_type' => $userTable::getEntity(),
                                    'reference' => [
                                        '=this.UF_USER_ID' => 'ref.ID'
                                    ],
                                    'join_type' => 'inner'
                                ]
                            ],
                            'filter' => $filter,
                            'offset' => $this->offset,
                            'limit' => $this->limit,
                            'order' => $this->order,
                            'group' => ['UF_USER_ID']
                        ]
                    );

                    $i = 0;
                    $arIdes = [];
                    global $USER;
                    $us = new \Democontent2\Pi\User(intval($USER->GetID()));
                    while ($res = $get->fetch()) {
                        debmes($res);
                        $this->id = intval($res['USER_ID']);


                        $arIdes[] = $res['USER_ID'];
                        $reviews->setUserId(intval($res['USER_ID']));
                        $cards->setUserId(intval($res['USER_ID']));
                        $profile->setUserId(intval($res['USER_ID']));


                        $res = $this->get();
                        $arIds = [ 0 => $res['ID']];
                        $res['COMPANY'] = $us->getCompanyManager($arIds);
                        $res['CURRENT_RATING'] = $reviews->rating();
                        $res['PROFILE'] = $profile->get();
                        $res['CARD'] = (count($cards->getUserCard()) > 0) ? 1 : 0;

                        $result[] = $res;
                    }



                }
            } catch (\Exception $e) {
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache(
                    [
                        $cache_id => [
                            'result' => $result,
                            'total' => $this->total
                        ]
                    ]
                );
                $taggedCache->endTagCache();
            }


        return $result;
    }

    /**
     * @param array $item
     * @throws SystemException
     */
    public function sendAnOffer(array $item)
    {
        if ($this->id > 0) {
            $userParams = $this->get();
            if (count($userParams) > 0) {
                $request = Application::getInstance()->getContext()->getRequest();

                Event::send(
                    [
                        'EVENT_NAME' => 'DSPI_NEW_OFFER',
                        'LID' => Application::getInstance()->getContext()->getSite(),
                        'C_FIELDS' => [
                            'EMAIL' => $userParams['EMAIL'],
                            'ITEM_NAME' => $item['UF_NAME'],
                            'PRICE' => (intval($item['UF_PRICE']) > 0) ? Utils::price($item['UF_PRICE']) : Loc::getMessage('BUDGET_BY_AGREEMENT'),
                            'SAFETY' => (intval($item['UF_SAFE']) > 0) ? Loc::getMessage('YES') : Loc::getMessage('NO'),
                            'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://') . Path::normalize($request->getHttpHost() . SITE_DIR
                                    . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE']) . '/'
                        ]
                    ]
                );
            }
        }
    }

    /**
     * @param HttpRequest $request
     * @return bool
     */
    public function checkKey(HttpRequest $request)
    {
        if ($request->getHeaders()->get('x-pi-key') && strlen($request->getHeaders()->get('x-pi-key')) == 32) {
            $this->setApiKey($request->getHeaders()->get('x-pi-key'));
            $this->getIdByApiKey();
            return true;
        } else {
            return false;
        }
    }
}