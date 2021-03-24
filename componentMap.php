<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");?>
<?
use Democontent2\Pi\Hl;




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



pre($num);


?>
