<?php
function debmes($message, $title = false, $color = "#008B8B")
{

    if ($GLOBALS["USER"]->IsAdmin())
    {
        $debug = debug_backtrace(false);
        echo $debug[0]['file'].PHP_EOL;

        echo '<table border="0" cellpadding="5" cellspacing="0" style="border:1px solid '.$color.';margin:2px;"><tr><td>';
        if (strlen($title)>0)
        {
            echo '<p style="color: '.$color.';font-size:11px;font-family:Verdana;">['.$title.']</p>';
        }

        if (is_array($message) || is_object($message))
        {
            echo '<pre style="color:'.$color.';font-size:11px;font-family:Verdana;">'; print_r($message); echo '</pre>';
        }
        else
        {
            echo '<p style="color:'.$color.';font-size:11px;font-family:Verdana;">'.$message.'</p>';
        }

        echo '</td></tr></table>';
    }
    else
        return "";
}
function pre($arr){
    $str = '<pre>'.print_r($arr).'</pre>';
    return $str;
}