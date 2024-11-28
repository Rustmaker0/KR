<?php
// Страница входа для администратора приложения "База данных "Школа""
//-----------------------------------------------------------
// Устанавливаем уровень оповещения ошибок
error_reporting(E_ALL); // Показывать все ошибки и предупреждения
session_start(); // Начинаем новую или открываем существующую сессию
/* Проверяем существование параметров сессии - логина и шифрованного пароля
Если вход уже был осуществлен, то пересылаем сразу на страницу редактирования */
if ((isset($_SESSION['idsess'])) && (isset($_SESSION['hashpasswd'])))
{
header('Location: /base.php'); // Пересылка на редактирование базы
exit();
}
//-----------------------------------------------------------
// Переменные
//-----------------------------------------------------------
// Проверяем, заполнены ли были поля Логин и Пароль
$login = !empty($_POST['login']) ? $_POST['login'] : null;
$passwd = !empty($_POST['passwd']) ? $_POST['passwd'] : null;
// Объявляем массив info для сборки информации и дальнейшего вывода пользователю
$info = array();
//-----------------------------------------------------------
// Скрипт
//-----------------------------------------------------------
$date = date("d.m.y"); // функция выдачи даты в формате "День, месяц, год"
$dn = date("l"); // функция выдачи даты в формате дня недели
if (!empty($_POST['ok'])) // Если кнопка Отправить была нажата
{
if(!$login)
$info[] = 'Нет имени пользователя.';
if(!$passwd)
$info[] = 'Не введен пароль.';
if (count($info) == 0) // Если замечаний нет и все поля заполнены
{
/* Осуществляем удаление HTML-тегов и обратных слешей, если они есть.
Это необходимо для защиты от SQL-инъекций и вредоносного кода. */
	$login = substr($login,0,50);
$login = htmlspecialchars(stripslashes($login));
$passwd = substr($passwd,0,50);
$passwd = htmlspecialchars(stripslashes($passwd));
/* Создаем содинение с базой данных MySQL userInfo с таблицей user_autentificate
Cделан доступ для любого пользователя (user@%), с ораничением прав (только SELECT) */
$link = mysql_connect ("", "admin", "698d51a19d8a121ce581499d7b701668") or die("Could not connect : " .
mysql_error());
$database_selected = mysql_select_db("NIR");
if (!$database_selected) {
    die("Could not select database: " . mysql_error());
}
/* Все отправляемые или принимаемые данные (если не задан SET NAMES) MySQL
принимает как cp1251. Скрипт отправляет данные в utf8, но БД принимает их,
как cp1251, из-за чего при просмотре они могут отображаться крякозябрами. */
mysql_query('SET NAMES utf8') or exit('SET NAMES Error');
$hash_val = md5($passwd); // шифрование введенного пароля для сравнения с

// Обращаемся к таблице user_autentificate для поиска совпадающей строки
// $query - текст запроса
$query = "select userid from user_autentificate where
username = '$login' and
password = '$hash_val'";
// Функция mysql_query выполняет указанный в параметре запрос
$result = mysql_query($query) or die("Query failed : " . mysql_error());
// Если совпадение не найдено, выводим соответствующую информацию
if (mysql_numrows($result) != 1) // mysql_numrows возвращает количество

{
$info[] = 'Доступ запрещен.';
}
else // Если проверка пройдена, получим данные пользователя из закрытой

{
$userid = mysql_result($result, 0, 0); // Находим идентификатор
/* Заносим логин и пароль пользователя в суперглобальный массив _SESSION
в качестве идентификатора сессии для доступа к базам mySQL*/
$_SESSION['idsess'] = $login;
$_SESSION['hashpasswd'] = $hash_val;
// По полученным данным заходим в базу с данными
$link2 = mysql_connect ("", "$login", "$hash_val") or die("Could not
connect : " . mysql_error());
mysql_select_db ("nir") or die("Could not select database");
mysql_query('SET NAMES utf8') or exit('SET NAMES Error');
// Осуществляем запрос к базе
$query = "select adminname, adminsecname from user_data where
fuserid = '$userid'";
$result = mysql_query($query) or die("Query failed : " .
mysql_error());
if (mysql_numrows($result) != 1)
{
$info[] = 'Ошибка в базе.';
}
else // Если данные найдены
{
while ($row = mysql_fetch_array($result, MYSQL_BOTH))
{
// Заносим имя и отчество пользователя в переменные сессии
$_SESSION['name'] = $row["adminname"];
$_SESSION['secname'] = $row["adminsecname"];
}
// Пересылаем пользователя на страницу редактирования
header('Location: /base.php');
exit();
}
// Освобождаем память от результата
mysql_free_result($result);
// Закрываем соединение
mysql_close($link2);
}
// Освобождаем память от результата
mysql_free_result($result);
// Закрываем соединение
mysql_close($link);
}
}
//-----------------------------------------------------------
// Отображение
//-----------------------------------------------------------
// Вывод BOM (для браузера)
echo chr(239).chr(187).chr(191);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--!DOCTYPE необходим, для того, чтобы браузер правильно понимал тип документа
В данном случае мы используем XHTML 1.0-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>База данных "Школа"</title>
<!--Укажем тип документа – text/html и кодировку – utf-8-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="description" content="База данных 'Школа'"/>
<meta name="keywords" content="База данных, школа, PHP, MySQL, web-программирование"/>
<link rel="stylesheet" type="text/css" href="my-style.css"/> 
</head>
<body>
<div id="container">
<div id="top">
<img src="pics/logo.png" border="0" alt="logo"/>
</div>
<div id="other">
<div id="daydata">
<?php
echo ("Сегодня ".$date." ".$dn."\n"); // вывод даты и дня недели
?>
</div>
</div>
<div id="menu">
<div><a href="index.php">Главная</a></div>
<div><a href="adm.php" style="border-bottom: 7px solid
#000066">Администрирование</a></div>
</div>
<div id="content">
<h1>Вход для администраторов</h1>
<p class="centr">
Введите следующие данные:
</p>
<!-- Начало формы ввода пользовательских данных -->
<form method="post" action="">
<table border="0" align="center">
<tr>
<td align="right">Логин:</td>
<!-- Текстовое поле ввода -->
<td><input type="text" size="30" name="login"/></td>
</tr>
<tr>
<td align="right">Пароль:</td>
<!-- Поле ввода для паролей -->
<td><input type="password" size="30" name="passwd"/></td>
</tr>
</table>
<br/>
<p class="centr">
<!-- Кнопка типа submit, по еѐ нажатию все данные
из всех полей, входящие в form, отправляются в
указанный в атрибуте action файл. Если атрибут пустой,
то данные отправляются в текущий файл-->
<input type="submit" value="Войти!" name="ok"/>
	<!-- Кнопка для очистки всех полей -->
<input type="reset" value="Очистить"/>
</p>
<!-- Конец формы ввода пользовательских данных -->
</form>
<p class="error">
<!-- Вывод информации об ошибках -->
<?php
// Функция implode перечисляет элементы массива через любой разделитель
echo implode('<br/>', $info)."\n";
?>
</p>
<br/>
</div>
<div id="footer">
<p>Идея и реализация проекта &copy; istu.edu 2010
<br/>
<a href="advert.php">Информация для рекламодателей.</a>
<br/>
По любым вопросам: <a href="mailto:your@e-mail.ru">your@e-mail.ru</a>
</p>
</div>
</div>
</body>
</html>