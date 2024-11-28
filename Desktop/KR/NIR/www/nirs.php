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
$tblname = 'nirs';
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
//------------------Обработка таблицы------------------------

// Добавление нового NIR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка наличия всех необходимых данных после отправки формы
    $nir_name = !empty($_POST['nir_name']) ? htmlspecialchars(stripslashes($_POST['nir_name'])) : '';
    $type = !empty($_POST['type']) ? htmlspecialchars(stripslashes($_POST['type'])) : '';
    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $place = !empty($_POST['place']) ? htmlspecialchars(stripslashes($_POST['place'])) : '';

    // Проверка на пустые поля
    if (!empty($nir_name) && !empty($type) && !empty($date) && !empty($place)) {
        // Вставка новой записи
        $query_add = "INSERT INTO nirs (nir_name, type, date, place) VALUES ('$nir_name', '$type', '$date', '$place')";

        if (mysql_query($query_add)) {
            $info[] = 'NIR успешно добавлен!';
        } else {
            $info[] = 'Ошибка добавления: ';
        }
    } else {

    }
}
// Изменение записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_nir'])) {
    $nir_id = intval($_POST['nir_id']);
    $nir_name = htmlspecialchars(stripslashes($_POST['nir_name_upd']));
    $type = htmlspecialchars(stripslashes($_POST['type']));
    $date = htmlspecialchars(stripslashes($_POST['date']));
    $place = htmlspecialchars(stripslashes($_POST['place']));

    // Проверяем, чтобы поле название NIR не было пустым
    if (!empty($nir_name) && !empty($type) && !empty($date) && !empty($place)) {
        $query_update = "UPDATE nirs SET nir_name = '$nir_name', type = '$type', date = '$date', place = '$place' 
                         WHERE id_nir = $nir_id";

        if (mysql_query($query_update)) {
            $info[] = 'Запись NIR успешно изменена!';
        } else {
            $info[] = 'Ошибка изменения: ' . mysql_error();
        }
    } else {
        $info[] = 'Пожалуйста, заполните все поля!';
    }

}

// Обработка удаления NIR
if (isset($_POST['nir_to_delete'])) {
    $nir_id = intval($_POST['nir_to_delete']); // Получаем id NIR для удаления

    // Удаляем записи из связанных таблиц
    $query_delete_participants = "DELETE FROM participants WHERE id_nir = $nir_id";
    mysql_query($query_delete_participants) or die("Query_delete_participants failed: " . mysql_error());

    $query_delete_committee = "DELETE FROM organizing_committee WHERE id_nir = $nir_id";
    mysql_query($query_delete_committee) or die("Query_delete_committee failed: " . mysql_error());

    // Удаляем запись из таблицы NIR
    $query_delete_nir = "DELETE FROM nirs WHERE id_nir = $nir_id";
    mysql_query($query_delete_nir) or die("Query_delete_nir failed: " . mysql_error());

    // Уведомление об успешном удалении
    $info[] = 'NIR успешно удалён!';
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
<h1>Таблица Nirs</h1>
<h1>Добавление нового NIR</h1>
<form method="post" action="">
    <label for="nir_name">Название NIR:</label>
    <input type="text" name="nir_name" id="nir_name" required><br>

    <label for="type">Тип:</label>
    <input type="text" name="type" id="type" required><br>

    <label for="date">Дата:</label>
    <input type="date" name="date" id="date" required><br>

    <label for="place">Место:</label>
    <input type="text" name="place" id="place" required><br>

    <br/>
    <input type="submit" value="Добавить" />
</form>
<br/>

	
<h1>Изменение записи NIRS</h1>
<form method="post" action="">
    <label for="nir_id">Выберите NIR:</label>
    <select name="nir_id" id="nir_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php
        // Получаем список всех NIR
        $query = "SELECT id_nir, nir_name FROM nirs";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_nir'] . '">' . htmlspecialchars($row['nir_name']) . ' (ID: ' . htmlspecialchars($row['id_nir']) . ') </option>';
        }
        ?>
    </select>
</form>
<br/>

<?php
if (isset($_POST['nir_id']) && !empty($_POST['nir_id'])) {
    $nir_id = intval($_POST['nir_id']);
    $query_get_nir = "SELECT * FROM nirs WHERE id_nir = $nir_id";
    $result = mysql_query($query_get_nir) or die("Query failed: " . mysql_error());
    $nir = mysql_fetch_assoc($result); 
    
    if ($nir) {
        $nir_name = $nir['nir_name'];
        $type = $nir['type'];
        $date = $nir['date'];
        $place = $nir['place'];
?>
        <form method="post" action="">
            <input type="hidden" name="nir_id" value="<?php echo $nir_id; ?>" />
            <label for="nir_name_upd">Название NIR:</label>
            <input type="text" name="nir_name_upd" id="nir_name_upd" value="<?php echo htmlspecialchars($nir_name); ?>" required><br>
            
            <label for="type">Тип:</label>
            <input type="text" name="type" id="type" value="<?php echo htmlspecialchars($type); ?>" required><br>

            <label for="date">Дата:</label>
            <input type="date" name="date" id="date" value="<?php echo $date; ?>" required><br>

            <label for="place">Место:</label>
            <input type="text" name="place" id="place" value="<?php echo htmlspecialchars($place); ?>" required><br>

            <br/>
            <input type="submit" name="update_nir" value="Изменить">
        </form>
<?php
    } else {
        echo '<p>NIR не найден.</p>';
    }
}
?>
	
	<h1>Удаление NIR</h1>
<form method="post" action="">
    <label for="nir_to_delete">Выберите NIR для удаления:</label>
    <select name="nir_to_delete" required>
        <?php
        // Получаем список NIR
        $query = "SELECT id_nir, nir_name FROM nirs"; // Получаем список NIR
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_nir'] . '">' . htmlspecialchars($row['nir_name']) . ' (ID: ' . htmlspecialchars($row['id_nir']) . ')</option>';
        }
        ?>
    </select>
    <input type="submit" value="Удалить">
</form>
<br/>
	
<?php
// Выполняем SQL-запросы для отображения содержимого таблицы
$query_content = "SELECT * FROM " . $tblname . " ORDER BY id_nir";
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