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
$tblname = 'participants';
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

// Обработка добавления участника
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nir_id'])) {
    $nir_id = intval($_POST['nir_id']);
    $passport_data = htmlspecialchars(stripslashes($_POST['passport_data']));

    // Проверка, является ли участник студентом или сотрудником
    // Предположим, что если passport_data находится в таблице students, это студент
    $query_check_student = "SELECT * FROM students WHERE passport_data = '$passport_data'";
    $query_check_employee = "SELECT status FROM employees WHERE passport_data = '$passport_data'";

    if (mysql_num_rows(mysql_query($query_check_student)) > 0) { // Участник - студент
        $status = 'Студент';
    } else if ($employee = mysql_fetch_assoc(mysql_query($query_check_employee))) { // Участник - сотрудник
        $status = $employee['status'];
    } else {
        $info[] = 'Участник не найден в базе данных.';
        exit();
    }

    // Вставка новой записи в таблицу participants
    $query_add_participant = "INSERT INTO participants (id_nir, passport_data, status) VALUES ($nir_id, '$passport_data', '$status')";
    
    if (mysql_query($query_add_participant)) {
        $info[] = 'Участник успешно добавлен!';
    } else {
        $info[] ='Ошибка добавления: ' . mysql_error() . '';
    }
}

// Обработка удаления участника
if (isset($_POST['participant_to_delete'])) {
    // Извлекаем паспортные данные и ID NIR из выбранного значения
    list($passport_data, $nir_id) = explode('|', htmlspecialchars(stripslashes($_POST['participant_to_delete'])));

    // Удаляем запись из таблицы participants с условием по обоим параметрам
    $query_delete_participant = "DELETE FROM participants WHERE passport_data = '$passport_data' AND id_nir = $nir_id";
    
    if (mysql_query($query_delete_participant)) {
        $info[] = 'Участник успешно удалён!';
    } else {
        $info[] = 'Ошибка удаления: ' . mysql_error();
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
<h1>Таблица Participants</h1>
<h1>Добавление участника в NIR</h1>
<form method="post" action="">
    <label for="nir_id">Выберите NIR:</label>
    <select name="nir_id" id="nir_id" required>
        <?php
        // Получаем список NIR
        $query_nir = "SELECT id_nir, nir_name FROM nirs"; // Получаем список NIR
        $result_nir = mysql_query($query_nir) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result_nir)) {
            echo '<option value="' . $row['id_nir'] . '">' . htmlspecialchars($row['nir_name']). ' (ID: ' . htmlspecialchars($row['id_nir']) . ') </option>';
        }
        ?>
    </select><br>

    <label for="passport_data">Выберите участника (Студент/Сотрудник):</label>
    <select name="passport_data" id="passport_data" required>
        <?php
        // Получаем список студентов
        $query_students = "SELECT passport_data, CONCAT(stud_name, ' ', stud_secname, ' ', stud_surname) AS full_name FROM students";
        $result_students = mysql_query($query_students) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result_students)) {
            echo '<option value="' . htmlspecialchars($row['passport_data']) . '">' .'Студент - '. htmlspecialchars($row['full_name']) . ' (' . htmlspecialchars($row['passport_data']) . ')</option>';
        }

        // Получаем список сотрудников
        $query_employees = "SELECT passport_data, status, CONCAT(empl_name, ' ', empl_secname, ' ', empl_surname) AS full_name FROM employees"; 
        $result_employees = mysql_query($query_employees) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result_employees)) {
            echo '<option value="' . htmlspecialchars($row['passport_data']) . '">'  . htmlspecialchars($row['status']). ' - ' . htmlspecialchars($row['full_name']). ' (' . htmlspecialchars($row['passport_data']) . ')</option>';
        }
        ?>
    </select><br>

    <br/>
    <input type="submit" value="Добавить" />
</form>
<br/>
	
<h1>Изменение записи участника</h1>
<form method="post" action="">
    <label for="participant_id">Выберите участника для изменения:</label>
    <select name="participant_id" id="participant_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php
        // Получаем список всех участников
        $query = "SELECT id_nir, passport_data, status FROM participants"; // Запрос для получения всех участников
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['passport_data'] . '">' . htmlspecialchars($row['passport_data']) . ' (NIR ID: ' . htmlspecialchars($row['id_nir']) . ', Статус: ' . htmlspecialchars($row['status']) . ')</option>';
        }
        ?>
    </select>
</form>
<br/>

<?php
	
if (isset($_POST['participant_id']) && !empty($_POST['participant_id'])) {
    $passport_data = htmlspecialchars(stripslashes($_POST['participant_id']));
    $query_get_participant = "SELECT * FROM participants WHERE passport_data = '$passport_data'";
    $result = mysql_query($query_get_participant) or die("Query failed: " . mysql_error());
    $participant = mysql_fetch_assoc($result); 
    
    if ($participant) {
        $id_nir = $participant['id_nir'];
        $status = $participant['status'];
?>
        <form method="post" action="">
            <input type="hidden" name="passport_data" value="<?php echo $passport_data; ?>" />
            <label for="id_nir">ID NIR:</label>
            <input type="text" name="id_nir" id="id_nir" value="<?php echo $id_nir; ?>" readonly><br>

            <label for="status">Статус:</label>
            <input type="text" name="status" id="status" value="<?php echo htmlspecialchars($status); ?>" required><br>

            <br/>
            <input type="submit" name="update_participant" value="Изменить">
        </form>
<?php
    } else {
        $info[] = 'Участник не найден.';
    }
}

// Обработка изменения записи участника
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_participant'])) {
    $passport_data = htmlspecialchars(stripslashes($_POST['passport_data']));
    $status = htmlspecialchars(stripslashes($_POST['status']));
    
    // Обновление записи в таблице participants
    $query_update_participant = "UPDATE participants SET status = '$status' WHERE passport_data = '$passport_data'";

    if (mysql_query($query_update_participant)) {
       $info[] = 'Запись участника успешно изменена!';
    } else {
        $info[] = 'Ошибка изменения: ' . mysql_error() . '';
    }
}
?>
	
	<h1>Удаление участника</h1>
<form method="post" action="">
    <label for="participant_to_delete">Выберите участника для удаления:</label>
    <select name="participant_to_delete" required>
        <?php
        // Получаем список участников
        $query = "SELECT passport_data, id_nir, status FROM participants"; // Запрос на получение всех участников
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . htmlspecialchars($row['passport_data']) . '|' . htmlspecialchars($row['id_nir']) . '">' . htmlspecialchars($row['passport_data']) . ' (NIR ID: ' . htmlspecialchars($row['id_nir']) . ', Статус: ' . htmlspecialchars($row['status']) . ')</option>';
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