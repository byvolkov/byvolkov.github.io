<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Страница благодарности</title>
<link type="text/css" rel="stylesheet" href="success_files/style000.css"/>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>

<?php
/*
    Всё, что нам потребуется для интеграфии с CRM системой bitrix24
    ВАЖНО: В файле с сайтом PP прописать следующую строчку в форме
---------------------------------------------------------------------
    <input name="metka" type="hidden" class="metka" value="НАЗВАНИЕ_ТОВАРА"/>
---------------------------------------------------------------------
    Причем хочу заметить, что value = "метка для формы" мы можем писать всё, что угодно. К примеру value = " cross-ecco-cool". Для того, чтобы в дальнейшем понимать, откуда приходят лиды, чтобы на этом уже формировать аудитории различных типов в Exel.(Экспорт в Exel очень простой, быстрый и понятный, нажатием всего лишь одной кнопки.)
    Также, необходимо поменять submit, прописав следующую строчку
---------------------------------------------------------------------
    <input type="button" class="" value="ОТПРАВИТЬ" onclick="submitTwice(this.form)">
---------------------------------------------------------------------
    Теперь после формы прописываем один невидимый iframe следующим образом.
---------------------------------------------------------------------
    <div style="visibility:hidden"> 
        <iframe name="ifr1" width="20" height="20"></iframe>
    </div>
---------------------------------------------------------------------
    И прописываем сам скрипт.
    f.action = 'http://cc.salesup-crm.com/PostOrder.aspx' - этот момент зависит от того, какая пп и какая срм у них.
    Эту информацию можно видеть в самой форме сайта, который вам предоставляет пп.
---------------------------------------------------------------------
    <script type="text/javascript"> 
        function submitTwice(f){ 
        f.action = 'rest.php';   
        f.submit();
        f.action = 'http://cc.salesup-crm.com/PostOrder.aspx';
        f.target = 'ifr1';
        f.submit();
        }
    </script>
---------------------------------------------------------------------
        После чего проверям все ли работает, отпарвив один лид. Смотрим в пп нашей сетки и в crm bitrix24. Видим наши лиды и всю информации о них, которую легко можно экспортировать в Exel
---------------------------------------------------------------------
*/
define('CRM_HOST', 'novarich.bitrix24.ru'); // Домен срм системы
define('CRM_PORT', '443'); 
define('CRM_PATH', '/crm/configs/import/lead.php'); 
define('CRM_LOGIN', 'roadttstars@gmail.com');  // логин
define('CRM_PASSWORD', 'zyeZcJqbFp'); // пароль

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
/* Можно убедиться, что следующий за командой код не выполнится из-за
перенаправления.*/
?>
<style>
    body{
        font-family: 'Open Sans', sans-serif;
    }
    .success{
        font-family: 'Open Sans', sans-serif;
        font-size: 15px; 
    }
    .back-to-main{
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background: rgb(30,87,153);
        background: -moz-linear-gradient(left, rgba(30,87,153,1) 0%, rgba(41,137,216,1) 50%, rgba(32,124,202,1) 100%, rgba(125,185,232,1) 100%, rgba(32,124,202,1) 101%);
        background: -webkit-linear-gradient(left, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 100%,rgba(125,185,232,1) 100%,rgba(32,124,202,1) 101%);
        background: linear-gradient(to right, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 100%,rgba(125,185,232,1) 100%,rgba(32,124,202,1) 101%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#207cca',GradientType=1 );
        border-radius: 10px;
        text-decoration: none;
        text-transform: uppercase;
        transition: all ease 2s;
        color: #fff;
    }
    .back-to-main:hover{
        background: rgb(30,87,153);
        background: -moz-linear-gradient(top, rgba(30,87,153,1) 0%, rgba(41,137,216,1) 50%, rgba(32,124,202,1) 100%, rgba(125,185,232,1) 100%, rgba(32,124,202,1) 101%);
        background: -webkit-linear-gradient(top, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 100%,rgba(125,185,232,1) 100%,rgba(32,124,202,1) 101%);
        background: linear-gradient(to bottom, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 100%,rgba(125,185,232,1) 100%,rgba(32,124,202,1) 101%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#207cca',GradientType=0 );
        color: #ccc;
    }
    .block_success{
        text-align: center;
    }
    </style>
    <div class="wrap_block_success">
        <div class="block_success">
            <h2 class="congrats" style="font-family: 'Open Sans', sans-serif; ">Поздравляем! Ваш заказ принят!</h2>
            <p>&nbsp;</p>
            <p class="success">В ближайшее время с вами свяжется оператор для подтверждения заказа. Пожалуйста, включите ваш контактный телефон.</p>
            <a class="back-to-main" href="index.html">Вернутся на сайт</a>
        </div>
    </div>
</body>
</html>