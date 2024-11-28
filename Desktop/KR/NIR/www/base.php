<?php
// Страница выбора таблицы для редактирования приложения "База данных "Школа""
//-----------------------------------------------------------
// Устанавливаем уровень оповещения ошибок
error_reporting(E_ALL); // Показывать все ошибки и предупреждения
session_start(); // Начинаем новую или открываем существующую сессию
/* Проверяем существование параметров сессии - логина и шифрованного пароля
Если параметров не существует, значит пересылаем на страницу входа adm.php */
if ((!isset($_SESSION['idsess'])) || (!isset($_SESSION['hashpasswd'])))
{
header('Location: /adm.php'); // Пересылка на форму входа
exit();
}
else
{
$hello = "Добро пожаловать, ".$_SESSION['name']." ".$_SESSION['secname']."!";
$exitlink = "<a class=\"exitlink\" href=\"exitsess.php\">Выход с сайта</a>";
}
//-----------------------------------------------------------
// Переменные
//-----------------------------------------------------------
// Массив для хранения списка таблиц
$list = array();
// Объявляем имя основной базы данных
$dbname = 'nir';
//-----------------------------------------------------------
// Скрипт
//-----------------------------------------------------------
$date = date("d.m.y"); // функция выдачи даты в формате "День, месяц, год"
$dn = date("l"); // функция выдачи даты в формате дня недели
// Соединяемся с сервером БД под сохраненными перемеными сессии
$link = mysql_connect ("localhost",$_SESSION['idsess'],$_SESSION['hashpasswd'])
or die("Could not connect : " . mysql_error());
/* Все отправляемые или принимаемые данные (если не задан SET NAMES) MySQL
принимает как cp1251. Скрипт отправляет данные в utf8, но БД принимает их,
как cp1251, из-за чего при просмотре они могут отображаться крякозябрами. */
mysql_query('SET NAMES utf8') or exit('SET NAMES Error');
// Функция mysql_list_tables($dbname) возвращает имена таблиц в базе
$list_of_tables = @mysql_list_tables($dbname)
or die("DB error, could not list tables : ". mysql_error());
while ($row = mysql_fetch_row($list_of_tables))
$list[] = $row[0];
mysql_free_result($list_of_tables);
// Закрываем соединение
mysql_close($link);
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
<div id="hello">
<?php
// Если вход осуществлен, то появляется приветствие и ссылка для разлогинивания
print $hello." -->".$exitlink."<--";
?>
</div>
</div>
<div id="menu">
<div><a href="index.php">Главная</a></div>
<div><a href="adm.php" style="border-bottom: 7px solid
#000066">Администрирование</a></div>
</div>
<div id="content">
<h1>Таблицы и отчеты</h1>
<p class="centr">
Выберите таблицу или отчет:
</p>
<table class="tbl" align="center">
<?php
foreach ($list as $col_value) {
    // Используем `continue`, чтобы пропустить определенные значения
    if ($col_value == 'user_autentificate' || $col_value == 'user_data') {
        continue;
    }

    print "\t\t<tr>\n";
    // Формируем ссылку и её название
    print "\t\t<td><a href=\"".$col_value.".php\">".$col_value."</a></td>\n";
    print "\t\t</tr>\n";
}
?>
<!--Выводим список отчетов-->
<tr>
<td><a href="rep1.php">Список всех НИР с указанием даты проведения на определенный год.</a></td>
</tr>
<tr>
<td><a href="rep2.php">Список олимпиад, проводимых определенным факультетом.</a></td>
</tr>
<tr>
<td><a href="rep3.php">Список НИР на указанную дату.</a></td>
</tr>
</table>
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