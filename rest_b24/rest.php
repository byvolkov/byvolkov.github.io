<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Страница благодарности</title>
<link href="css/style.css" type="text/css" rel="stylesheet"/>
</head>
<body>

<?php
define('CRM_HOST', 'b24-k6ubec.bitrix24.ru'); // Домен срм системы
define('CRM_PORT', '443'); 
define('CRM_PATH', '/crm/configs/import/lead.php'); 
define('CRM_LOGIN', 'evgeniivolkoff@gmail.com');  // логин
//define('CRM_PASSWORD', 'zyeZcJqbFp'); // пароль

/********************************************************************************************/

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    // получаем данные из полей и задаем название лида
    
    $postData = array(
        'TITLE' => $_POST['metka'], // сохраняем нашу метку и формируем заголовок лида
        'NAME' => $_POST['name'],   // сохраняем имя
        'PHONE_WORK' =>$_POST['phone'], // сохраняем телефон
    );

    // авторизация, проверка логина и пароля
    if (defined('CRM_AUTH'))
    {
        $postData['AUTH'] = CRM_AUTH;
    }
    else
    {
        $postData['LOGIN'] = CRM_LOGIN;
        $postData['PASSWORD'] = CRM_PASSWORD;
    }

    $fp = fsockopen("ssl://".CRM_HOST, CRM_PORT, $errno, $errstr, 30);
    if ($fp)
    {
        // формируем и шифруем строку с данными из формы
        $strPostData = '';
        foreach ($postData as $key => $value)
            $strPostData .= ($strPostData == '' ? '' : '&').$key.'='.urlencode($value);
            $str = "POST ".CRM_PATH." HTTP/1.0\r\n";
            $str .= "Host: ".CRM_HOST."\r\n";
            $str .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $str .= "Content-Length: ".strlen($strPostData)."\r\n";
            $str .= "Connection: close\r\n\r\n";

        $str .= $strPostData;

        // отправляем запрос в срм систему
        fwrite($fp, $str );
        $result = '';
        while (!feof($fp))
        {
            $result .= fgets($fp, 128);
        }
        fclose($fp);
        $response = explode("\r\n\r\n", $result);
        $output = '<pre>'.print_r($response[1], 1).'</pre>';
    }
    else
    {
        echo 'Connection Failed! '.$errstr.' ('.$errno.')';
    }
}
//else
// {
//     $output = 'Ошибка';
// }
?>

</body>
</html>