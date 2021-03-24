<?
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function custom_mail($to, $subject, $message, $additionalHeaders = '')
{
    require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
    $mail = new PHPMailer();

    /* Решение проблем для старых версий Битрикса, когда письма приходят с закодированным заголовком
      $subject = str_replace('=?UTF-8?B?', '', $subject);
      $subject = str_replace('?=', '', $subject);
      $subject = base64_decode($subject);
    //*/
    try {
        $mail->IsSMTP();
        $mail->SMTPAuth      = true;
        $mail->SMTPKeepAlive = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPDebug = 0;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.mail.ru';
        $mail->Port = 465; // 587
        $mail->Username = 'no-reply@astt.su';
        $mail->Password = 'egxPSF1kt3B%';
        $mail->CharSet =  'UTF-8'; // 'Windows-1251'

        $mail->SetFrom($mail->Username);
        $mail->AddAddress(trim($to));
        $mail->Subject = $subject;
        $mail->MsgHTML($message);

        $bRet = $mail->Send();

        $mail->ClearAddresses();
        $mail->ClearAttachments();

        return $bRet;

    } catch (Exception $e) {
        die('Message could not be sent. Mailer Error: '. $mail->ErrorInfo);
    }
}

function pre($arr){

    $str = '<pre>'.print_r($arr).'</pre>';
    return $str;
}
function get_time_ago( $time )
{
    $time_difference = time() - $time;

    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );
            return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}
function diff_time_string($start_date, $end_date = NULL, $words = NULL)
{
    //  Если конечная дата периода не указана, используется текущая дата и время
    if (!$end_date)
    {
        $end_date = time();
    }
    //  Unix-даты переводятся в текстовый формат
    if (is_numeric($start_date))
    {
        $start_date = date('Y-m-d H:i:s', $start_date);
    }
    if (is_numeric($end_date))
    {
        $end_date = date('Y-m-d H:i:s', $end_date);
    }

    //  Через аргумент можно передать массив слов на другом языке
    if (!$words)
    {
        $words = [
            'y' => ['год', 'года', 'лет'],
            'm' => ['месяц', 'месяца', 'месяцев'],
            'd' => ['день', 'дня', 'дней'],
            'h' => ['час', 'часа', 'часов'],
            'i' => ['минута', 'минуты', 'минут'],
            's' => ['секунда', 'секунды', 'секунд'],
        ];
    }

    //  Разница формируется в виде объекта класса DateInterval
    $interval = date_diff(date_create($start_date), date_create($end_date));
    if (is_object($interval))
    {
        $string = [];
        foreach ($words as $type => $variants)
        {
            //  Нулевые значения не добавляются, если только они не идут после ненулевых
            if ($interval->$type > 0 || count($string))
            {
                $number = $interval->$type;
                $word = $variants[2];
                if ($number < 5 || $number > 20)
                {
                    $number %= 10;
                    if ($number == 1)
                    {
                        $word = $variants[0];
                    }
                    elseif ($number >= 2 && $number <= 4)
                    {
                        $word = $variants[1];
                    }
                }
                $string[] = $interval->$type.' '.$word;
                break;
//                $string[] = $interval->$type.' '.$word;
            }

        }

        return implode(' ', $string);

    }

    return FALSE;
}
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

//pre($arResult['CATEGORIES']);
