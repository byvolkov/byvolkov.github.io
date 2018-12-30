<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Страница благодарности</title>
<link href="css/style.css" type="text/css" rel="stylesheet"/>
</head>
<body>


<?php 
function show_form() 
{ 
?> 

<?
} 
function complete_mail() {  

    $leadData = $_POST['DATA'];
    // Получаем данные из форм и сохраняем в массив
    $postData = array(
        'Имя:' => $leadData['NAME'],
        'Телефон' => $leadData['PHONE_WORK'],
    );
        $strPostData = '';
        foreach ($postData as $key => $value)
            $strPostData .= ($strPostData == '' ? '' : ' ').$key.' '.($value)."<br>";
        	$str .= "<p><strong>Заявка:</strong> <br/> ".($strPostData)."</p>\r\n";
		require 'class.phpmailer.php'; //Дополнительный скрипт для отправки файла, можете не открывать, просто положите рядом с index.html и этим файлом.
		$mail = new PHPMailer(); 
        $mail->From = 'mail@test.ru';      // от кого 
        $mail->FromName = 'smartlanding.ru';   // от кого Имя
        $mail->AddAddress('roadttstars@gmail.com', 'Евгений'); // кому Ваша почта, Имя 
        $mail->IsHTML(true);        // формат письма HTML 
        $mail->Subject = "Новая заявка";  // тема письма 
        // если есть файл, то прикрепляем его к письму 
        if(isset($_FILES['upl'])) { 
                 if($_FILES['upl']['error'] == 0){ 
                    $mail->AddAttachment($_FILES['upl']['tmp_name'], $_FILES['upl']['name']); 
                 } 
        } 
        $mail->Body = $str; 
        // отправляем наше письмо 
        if (!$mail->Send()) die ('Mailer Error: '.$mail->ErrorInfo);     
} 

if (!empty($_POST['submit'])) complete_mail(); 
else show_form(); 
?> 

<?

define('CRM_HOST', 'novarich.bitrix24.ru'); // Домен срм системы
define('CRM_PORT', '443'); 
define('CRM_PATH', '/crm/configs/import/lead.php'); 
define('CRM_LOGIN', 'roadttstars@gmail.com');  // логин
define('CRM_PASSWORD', 'zyeZcJqbFp'); // пароль

/********************************************************************************************/

// POST processing
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $leadData = $_POST['DATA'];

    $metka = "Заявка Salomon Cross кроссовки"; // Название лида, обязательное условие
    // получаем данные из полей и задаем название лида
    $postData = array(
        'TITLE' => $metka, 
        'NAME' => $leadData['NAME'], 
        'PHONE_WORK' =>$leadData['PHONE_WORK'],
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
else
{
    $output = '';
}

?>
<!-- То, что будет показываться на странице благодарности -->
<div id="thanks"><h1>Спасибо, Ваша заявка принята.</h1><p class="Pthanks">Наш менеджер свяжется с Вами в течение 15 минут</p>
<p>Если ваша заявка поступила после 21:00, мы обязательно свяжемся с Вами<br/> на следующий день после 10:00.</p><a href="http://cleverlanding.ru">Вернуться на сайт</a></div>';
</body>
</html>