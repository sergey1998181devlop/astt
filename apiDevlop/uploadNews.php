<?php
$link = mysqli_connect("31.31.198.106", "u1167557_default", "_fQOQj6s" , "u1167557_default" );

$currentNews = 1;
if ($link == false){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else {
    $link->set_charset('utf8');
    $lenta = 'https://lenta.ru';
    $lentaApi = 'http://api.lenta.ru';
//массив всех рубрик
    $messSections = array(
        0 => array(
            'russia' => 'Россия',
            'link' => 'https://api.lenta.ru/rubrics/russia/',
            'SubSection' => array(
                0 => array('slug' => 'society', 'title' => 'Общество',),
                1 => array('slug' => 'politic', 'title' => 'Политика',),
                2 => array('slug' => 'accident', 'title' => 'Происшествия',),
                3 => array('slug' => 'regions', 'title' => 'Регионы',),
                4 => array('slug' => 'moscow', 'title' => 'Москва',),
                5 => array('slug' => 'nornik', 'title' => '69-я параллель',),
                6 => array('slug' => 'np', 'title' => 'Нацпроекты',),),),
        1 => array('world' => 'Мир', 'link' => 'https://api.lenta.ru/rubrics/world/', 'SubSection' => array(0 => array('slug' => 'politic', 'title' => 'Политика',), 1 => array('slug' => 'society', 'title' => 'Общество',), 2 => array('slug' => 'crime', 'title' => 'Преступность',), 3 => array('slug' => 'accident', 'title' => 'Происшествия',), 4 => array('slug' => 'conflict', 'title' => 'Конфликты',), 5 => array('slug' => 'stranovedenie', 'title' => 'Страноведение',), 6 => array('slug' => 'elections', 'title' => 'Выборы',),),), 2 => array('ussr' => 'Бывший СССР', 'link' => 'https://api.lenta.ru/rubrics/ussr/', 'SubSection' => array(0 => array('slug' => 'baltics', 'title' => 'Прибалтика',), 1 => array('slug' => 'ukraine', 'title' => 'Украина',), 2 => array('slug' => 'belarus', 'title' => 'Белоруссия',), 3 => array('slug' => 'moldova', 'title' => 'Молдавия',), 4 => array('slug' => 'kavkaz', 'title' => 'Закавказье',), 5 => array('slug' => 'kazakhstan', 'title' => 'Казахстан',), 6 => array('slug' => 'middle_asia', 'title' => 'Средняя Азия',),),), 3 => array('economics' => 'Экономика', 'link' => 'https://api.lenta.ru/rubrics/economics/', 'SubSection' => array(0 => array('slug' => 'economy', 'title' => 'Госэкономика',), 1 => array('slug' => 'companies', 'title' => 'Бизнес',), 2 => array('slug' => 'markets', 'title' => 'Рынки',), 3 => array('slug' => 'finance', 'title' => 'Деньги',), 4 => array('slug' => 'business_climate', 'title' => 'Деловой климат',), 5 => array('slug' => 'social', 'title' => 'Социальная сфера',),),), 4 => array('forces' => 'Силовые структуры', 'link' => 'https://api.lenta.ru/rubrics/forces/', 'SubSection' => array(0 => array('slug' => 'investigations', 'title' => 'Следствие и суд',), 1 => array('slug' => 'violation', 'title' => 'Криминал',), 2 => array('slug' => 'police', 'title' => 'Полиция и спецслужбы',), 3 => array('slug' => 'crimerussia', 'title' => 'Преступная Россия',),),), 5 => array('science' => 'Наука и техника', 'link' => 'https://api.lenta.ru/rubrics/science/', 'SubSection' => array(0 => array('slug' => 'science', 'title' => 'Наука',), 1 => array('slug' => 'natural', 'title' => 'Жизнь',), 2 => array('slug' => 'cosmos', 'title' => 'Космос',), 3 => array('slug' => 'mil', 'title' => 'Оружие',), 4 => array('slug' => 'history', 'title' => 'История',), 5 => array('slug' => 'digital', 'title' => 'Техника',), 6 => array('slug' => 'gadget', 'title' => 'Гаджеты',), 7 => array('slug' => 'games', 'title' => 'Игры',), 8 => array('slug' => 'soft', 'title' => 'Софт',),),), 6 => array('sport' => 'Спорт', 'link' => 'https://api.lenta.ru/rubrics/sport/', 'SubSection' => array(0 => array('slug' => 'football', 'title' => 'Футбол',), 1 => array('slug' => 'english', 'title' => 'Английский футбол',), 2 => array('slug' => 'boxing', 'title' => 'Бокс и ММА',), 3 => array('slug' => 'winter', 'title' => 'Зимние виды',), 4 => array('slug' => 'other', 'title' => 'Летние виды',), 5 => array('slug' => 'hockey', 'title' => 'Хоккей',), 6 => array('slug' => 'auto', 'title' => 'Авто',),),), 7 => array('culture' => 'Культура', 'link' => 'https://api.lenta.ru/rubrics/culture/', 'SubSection' => array(0 => array('slug' => 'kino', 'title' => 'Кино',), 1 => array('slug' => 'books', 'title' => 'Книги',), 2 => array('slug' => 'art', 'title' => 'Искусство',), 3 => array('slug' => 'music', 'title' => 'Музыка',), 4 => array('slug' => 'theatre', 'title' => 'Театр',), 5 => array('slug' => 'photographic', 'title' => 'Фотография',),),), 8 => array('media' => 'Интернет и СМИ', 'link' => 'https://api.lenta.ru/rubrics/media/', 'SubSection' => array(0 => array('slug' => 'internet', 'title' => 'Интернет',), 1 => array('slug' => 'hackers', 'title' => 'Киберпреступность',), 2 => array('slug' => 'viruses', 'title' => 'Вирусные ролики',), 3 => array('slug' => 'soc_network', 'title' => 'Coцсети',), 4 => array('slug' => 'memes', 'title' => 'Мемы',), 5 => array('slug' => 'netvert', 'title' => 'Реклама',), 6 => array('slug' => 'press', 'title' => 'Пресса',), 7 => array('slug' => 'tv', 'title' => 'ТВ и радио',),),), 9 => array('style' => 'Ценности', 'link' => 'https://api.lenta.ru/rubrics/style/', 'SubSection' => array(0 => array('slug' => 'look', 'title' => 'Стиль',), 1 => array('slug' => 'watch', 'title' => 'Часы',), 2 => array('slug' => 'exterior', 'title' => 'Внешний вид',), 3 => array('slug' => 'apparatus', 'title' => 'Инструменты',), 4 => array('slug' => 'movement', 'title' => 'Движение',), 5 => array('slug' => 'phenomenon', 'title' => 'Явления',), 6 => array('slug' => 'tastes', 'title' => 'Вкусы',),),), 10 => array('travel' => 'Путешествия', 'link' => 'https://api.lenta.ru/rubrics/travel/', 'SubSection' => array(0 => array('slug' => 'rus', 'title' => 'Россия',), 1 => array('slug' => 'world', 'title' => 'Мир',), 2 => array('slug' => 'events', 'title' => 'События',), 3 => array('slug' => 'accident', 'title' => 'Происшествия',), 4 => array('slug' => 'opinion', 'title' => 'Мнения',),),), 11 => array('life' => 'Из жизни', 'link' => 'https://api.lenta.ru/rubrics/life/', 'SubSection' => array(0 => array('slug' => 'people', 'title' => 'Люди',), 1 => array('slug' => 'animals', 'title' => 'Звери',), 2 => array('slug' => 'stuff', 'title' => 'Вещи',), 3 => array('slug' => 'food', 'title' => 'Еда',), 4 => array('slug' => 'events', 'title' => 'События',), 5 => array('slug' => 'accident', 'title' => 'Происшествия',), 6 => array('slug' => 'progress', 'title' => 'Достижения',),),), 12 => array('realty' => 'Дом', 'link' => 'https://api.lenta.ru/rubrics/realty/', 'SubSection' => array(0 => array('slug' => 'city', 'title' => 'Город',), 1 => array('slug' => 'village', 'title' => 'Дача',), 2 => array('slug' => 'flat', 'title' => 'Квартира',), 3 => array('slug' => 'office', 'title' => 'Офис',),),),);
    $newsOne = 'http://api.lenta.ru/news/2020/09/30/restrictions/';
    $news = '/rss/news';//новости далее  - /рубрика/подрубрика
    $supperNews = '/rss/top7'; //самые свежие и самые важные новости
    $news24 = '/rss/last24'; //главные новости за последние сутки
    $articles = '/rss/articles '; //все статьи
    $column = '/rss/columns'; //колонки
    $articles = '/rss/articles'; //все статьи

    require_once 'debugFunctions/functions.php';
    require_once 'phpQuery/phpQuery.php';
    header('Content-Type: text/html; charset=utf-8');
    $section  = '';
    $subSection = '';
    $title = '';
    $originLink = '';
    $minDescription = '';
    $publicDate = '';
    $urlImg = '';
    $description = '';
    $dateAdd = '';
    foreach ($messSections as $idSection => $newsSection){
        $rubric = array_key_first($newsSection);//название раздела
        $section = $rubric;
        foreach ($newsSection['SubSection'] as $idSubSection => $newsSubSection) {
            $subSection = $newsSubSection['slug'];
            $parsXMLnews = file_get_contents($lenta . $news . '/' . $rubric . '/' . $newsSubSection['slug']);
            //скармливаю парсеру страницу


            $sx = simplexml_load_string(
                $parsXMLnews
                , null
                , LIBXML_NOCDATA
            );
            foreach ($sx->channel->item as $idItem => $valueItem) {


            $title = $valueItem->title;
            $originLink = $valueItem->link;
            $minDescription = $valueItem->description;
            if (!isset($valueItem)) continue;
            $str = $valueItem->pubDate[0];
            $str = (array)$str;
            $str = $str[0];
            $dateTime = DateTime::createFromFormat('D, j M Y G:i:s O', $str);
            $publicDate = $dateTime->getTimestamp();
            $publicDate = date('Y-m-d H:i:s', $publicDate);
            $arrUmg = (array)$valueItem->enclosure;
            $urlImg = $arrUmg['@attributes']['url'];

            preg_match('/\bru\b\W*\b(.+)/iu', $originLink, $match);
            $urlToApi = substr($match[1], 0, -1);

            $dataDetailNews = file_get_contents($lenta . '/' . $urlToApi);
            $pq = phpQuery::newDocument($dataDetailNews);
            $block = $pq->find('.js-topic__text');
            $pq = phpQuery::newDocument($block);
            $block = $pq->find('p');
            $text = [];
            foreach ($block as $valueText) {

                $pqP = pq($valueText); //pq делает объект phpQuery

                $text[] = $pqP->html();

            }

            $text = implode('', $text);
            $description = $text;
            $description = addslashes($description);
            $originLink = addslashes($originLink);
            $queryVals = sprintf('"%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s"',
                $section, $subSection, $publicDate, $title, $originLink, $minDescription, $description, $urlImg);
            $query = "INSERT INTO `news` 
( `section`, `subSection`, `publicDate`, `title`, `originLink`, `minDescription`, `description`, `urlImg`) 
VALUES 
(" . $queryVals . ");";

            $strQuery = "SELECT id FROM `b_blog` WHERE originLink = '$originLink'";
                $res = $link->query($strQuery);
                $count = $res->num_rows;

                if( $count > 0 ) {

                    continue;
                } else {

                    $res = $link->query($query);

                    echo $currentNews . "\n";
                    $currentNews++;
                }




        }

        }
    }
    mysqli_close($link);

}

