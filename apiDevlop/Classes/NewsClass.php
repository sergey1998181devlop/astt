<?php
class NewsClass {

    function sort($section , $subSection) {

        $mysql = new SafeMySQL();
        $res = $mysql->query("SELECT * FROM `news`  WHERE section=?s AND subSection=?s", $section, $subSection);
        return $res;
    }

}