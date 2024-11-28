<?php
/* Страница приложения "База данных "Школа"" для редактирования
таблицы class */
//-----------------------------------------------------------
// Устанавливаем уровень оповещения ошибок
error_reporting(E_ALL); // Показывать все ошибки и предупреждения
session_start(); // Начинаем новую или открываем существующую сессию
/* Проверяем существование параметров сессии - логина и шифрованного пароля
Если параметров не существует, значит пересылаем на страницу входа adm.php */
if (!((isset($_SESSION['idsess'])) && (isset($_SESSION['hashpasswd']))))
{
header('Location: /adm.php'); // Пересылка на форму входа
exit();
}
else
{
$hello = "Добро пожаловать, ".$_SESSION['name']." ".$_SESSION['secname']."!";
$exitlink = "<a class=\"exitlink\" href=\"exitsess.php\">Выход с сайта</a>";
}
// Объявляем имя основной базы данных
$dbname = 'nir';
// Объявляем имя таблицы
$tblname = 'faculty';
// Объявляем массив info для сборки информации и дальнейшего вывода пользователю
$info = array();
// Строка для идентификации полей (добавляется в конце параметра name)
$choose_name = null;
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
// Соединяемся с сервером БД под сохраненными перемеными сессии
$link = mysql_connect ("localhost",$_SESSION['idsess'],$_SESSION['hashpasswd'])
or die("Could not connect : " . mysql_error());
// Выбираем БД
mysql_select_db ($dbname) or die("Could not select database");
// Получаем список заголовков столбцов таблицы
$fields = mysql_list_fields($dbname , $tblname, $link);
// Получаем количество столбцов таблиц
$columns = mysql_num_fields($fields);

// Добавление новой записи
if (isset($_POST['name_faculty_add'])) {
    $_POST['name_faculty_add'] = htmlspecialchars(stripslashes($_POST['name_faculty_add']));
    if (!empty($_POST['name_faculty_add'])) {
        $query_any_repeat = "SELECT * FROM " . $tblname . " WHERE name_faculty = '" . $_POST['name_faculty_add'] . "'";
        $result = mysql_query($query_any_repeat) or die("Query_any_repeat failed: " . mysql_error());
        if (mysql_numrows($result) != 0) {
            $info[] = 'Такая запись уже существует!';
        } else {
            $query_add = "INSERT INTO " . $tblname . " VALUES(NULL, '" . $_POST['name_faculty_add'] . "')";
            mysql_query($query_add) or die("Query_add failed: " . mysql_error());
            $info[] = 'Запись успешно добавлена!';
        }
    } else {
        $info[] = 'Не заполнены поля!';
    }
}

// Обработка удаления
if (isset($_POST['faculty_to_delete'])) {
    $faculty_id = intval($_POST['faculty_to_delete']);  // Преобразуем строку в число
    $query_del = "DELETE FROM ".$tblname." WHERE id_faculty = ".$faculty_id;
    mysql_query($query_del) or die("Query_del failed : " . mysql_error());
}

// Обработка изменения
if (isset($_POST['name_faculty_upd']) && isset($_POST['faculty_to_update'])) {
    $new_name = htmlspecialchars(stripslashes($_POST['name_faculty_upd']));
    $faculty_id = intval($_POST['faculty_to_update']);  // Получаем ID выбранного факультета
    $query_upd = "UPDATE ".$tblname." SET name_faculty = '".$new_name."' WHERE id_faculty = ".$faculty_id;
    mysql_query($query_upd) or die("Query_upd failed : " . mysql_error());
}


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
<title>База данных "НИР"</title>
<!--Укажем тип документа – text/html и кодировку – utf-8-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="description" content="База данных 'НИР'"/>
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
<h1>Таблица Faculty</h1>
<!-- Добавление элемента -->
	<h1>Добавление нового факультета</h1>
<form method="post" action="">
    <label for="name_faculty_add">Добавить факультет:</label>
    <input type="text" name="name_faculty_add" id="name_faculty_add" required>
    <input type="submit" value="Добавить" />
</form>
<br/>
<h1>Изменение факультета</h1>
<!-- Изменение элемента -->
<form method="post" action="">
    <label for="faculty_to_update">Изменить факультет:</label>
    <select name="faculty_to_update" required>
        <?php
        // Получаем список факультетов для выпадающего списка
        $query = "SELECT id_faculty, name_faculty FROM " . $tblname;
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_faculty'] . '">' . $row['name_faculty'] . '</option>';
        }
        ?>
    </select><br><br>
    <input type="text" name="name_faculty_upd" required placeholder="Новое имя факультета">
    <input type="submit" value="Изменить">
</form>
<br/>
<h1>Удаление факультета</h1>
<!-- Удаление элемента -->
<form method="post" action="">
    <label for="faculty_to_delete">Удалить факультет:</label>
    <select name="faculty_to_delete" required>
        <?php
        // Получаем список факультетов для выпадающего списка
        $query = "SELECT id_faculty, name_faculty FROM " . $tblname;
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_faculty'] . '">' . $row['name_faculty'] . '</option>';
        }
        ?>
    </select>
    <input type="submit" value="Удалить">
</form>
<br/>

<!-- Отрисовка таблицы -->
<?php
// Выполняем SQL-запросы для отображения содержимого таблицы
$query_content = "SELECT * FROM " . $tblname . " ORDER BY id_faculty";
$result_content = mysql_query($query_content) or die("Query_content failed: " . mysql_error());
print "<table class=\"tbl\" align=\"center\">\n";
print "\t\t\t<tr class=\"header\">\n";

// Выводим заголовки
for ($i = 0; $i < $columns; $i++) {
    print "\t\t\t\t<th>" . mysql_field_name($fields, $i) . "</th>\n";
}
print "\t\t\t</tr>\n";

// Выводим содержимое таблицы
while ($line_content = mysql_fetch_array($result_content, MYSQL_NUM)) {
    print "\t\t\t<tr>\n";
    foreach ($line_content as $col_value) {
        print "\t\t\t\t<td>" . $col_value . "</td>\n";
    }
    print "\t\t\t</tr>\n";
}
print "\t\t\t</table>\n";
?>
<p class="error">
<!-- Вывод информации об ошибках -->
<?php
// Функция implode перечисляет элементы массива через любой разделитель
echo implode('<br/>', $info) . "\n";
?>
</p>
<p class="centr">
<a href="/base.php">Вернуться к таблицам...</a>
</p>
<br/>
</div>
<div id="footer">
<p>Идея и реализация проекта &copy; istu.edu 2010
<br/>
<a href="advert.php">Информация для рекламодателей.</a>
<br/>
По любым вопросам: <a href="mailto:olelishna@gmail.com">olelishna@gmail.com</a>
</p>
</div>
</div>
</body>
</html>
<?php
//-----------------------------------------------------------
// Скрипт
//-----------------------------------------------------------
mysql_free_result($result_content);
// Закрываем соединение
mysql_close($link);
?>