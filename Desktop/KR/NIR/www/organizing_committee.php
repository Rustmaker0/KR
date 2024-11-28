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
$tblname = 'organizing_committee';
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
// Обработка добавления участника в Organizing_committee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nir_id'])) {
    $nir_id = intval($_POST['nir_id']);
    $passport_data = htmlspecialchars(stripslashes($_POST['passport_data']));
    $status_chairman = intval($_POST['status_chairman']); // Получаем статус председателя как TINYINT

    // Проверка, является ли участник студентом или сотрудником
    $query_check_employee = "SELECT status FROM employees WHERE passport_data = '$passport_data'";

    if ($employee = mysql_fetch_assoc(mysql_query($query_check_employee))) { // Участник - сотрудник
        $status = $employee['status'];
    } else {
         $info[] ='Участник не найден в базе данных.';
        exit();
    }

    // Проверка на наличие уже назначенного председателя для данной NIR

        $query_check_chairman = "SELECT COUNT(*) as chairman_count FROM organizing_committee WHERE id_nir = $nir_id AND status_chairman = 1";
        $result_check = mysql_query($query_check_chairman) or die("Query failed: " . mysql_error());
        $row_check = mysql_fetch_assoc($result_check);

        if ($row_check['chairman_count'] > 0 and $status_chairman == 1) {
             $info[] ='Ошибка: Для данного NIR уже назначен председатель. Выберите другого участника в качестве председателя.';

        }
		else{
			    // Проверка на уникальность паспортных данных в органиционном комитете
    $query_check_passport = "SELECT COUNT(*) as duplicate_count FROM organizing_committee WHERE passport_data = '$passport_data' AND id_nir = $nir_id";
    $result_passport_check = mysql_query($query_check_passport) or die("Query failed: " . mysql_error());
    $row_passport_check = mysql_fetch_assoc($result_passport_check);



    // Вставка новой записи в таблицу Organizing_committee
    $query_add_committee = "INSERT INTO organizing_committee (id_nir, passport_data, status_chairman) VALUES ($nir_id, '$passport_data', $status_chairman)";
    
    if (mysql_query($query_add_committee)) {
         $info[] = 'Участник успешно добавлен в организационный комитет!';
    } else {
         $info[] = 'Ошибка добавления: ' . mysql_error() . '>';
    }
		}
   }
// Обработка удаления участника
if (isset($_POST['participant_to_delete'])) {
    // Извлекаем паспортные данные и ID NIR из выбранного значения
    list($passport_data, $id_nir) = explode('|', htmlspecialchars(stripslashes($_POST['participant_to_delete'])));

    // Удаляем запись из таблицы Organizing_committee с условием по обоим параметрам
    $query_delete_participant = "DELETE FROM organizing_committee WHERE passport_data = '$passport_data' AND id_nir = $id_nir";
    
    if (mysql_query($query_delete_participant)) {
        $info[] = 'Участник успешно удалён!';
    } else {
        $info[] = 'Ошибка удаления: ' . mysql_error() . '';
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
<h1>Таблица Organizing_committee</h1>
	<h1>Добавление участника в Организационный комитет</h1>
<form method="post" action="">
    <label for="nir_id">Выберите NIR:</label>
    <select name="nir_id" id="nir_id" required>
        <?php
        // Получаем список NIR
        $query_nir = "SELECT id_nir, nir_name FROM nirs"; // Получаем список NIR
        $result_nir = mysql_query($query_nir) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result_nir)) {
            echo '<option value="' . $row['id_nir'] . '">' . htmlspecialchars($row['nir_name']) . ' (ID: ' . htmlspecialchars($row['id_nir']) . ')</option>';
        }
        ?>
    </select><br>

    <label for="passport_data">Выберите участника (Студент/Сотрудник):</label>
    <select name="passport_data" id="passport_data" required>
        <?php

        // Получаем список сотрудников
        $query_employees = "SELECT passport_data, status, CONCAT(empl_name, ' ', empl_secname, ' ', empl_surname) AS full_name FROM employees"; 
        $result_employees = mysql_query($query_employees) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result_employees)) {
            echo '<option value="' . htmlspecialchars($row['passport_data']) . '">'  . htmlspecialchars($row['status']) . ' - ' . htmlspecialchars($row['full_name']) . ' (' . htmlspecialchars($row['passport_data']) . ')</option>';
        }
        ?>
    </select><br>


    <label for="status_chairman">Является председателем:</label>
    <select name="status_chairman" id="status_chairman" required>
        <option value="0">Нет</option>
        <option value="1">Да</option>
    </select><br>

    <br/>
    <input type="submit" value="Добавить" />
</form>
<br/>
	
	<h1>Изменение статуса председателя участника</h1>
<form method="post" action="">
    <label for="participant_id">Выберите участника для изменения:</label>
    <select name="participant_id" id="participant_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php
        // Получаем список всех участников
        $query = "SELECT passport_data, id_nir, status_chairman FROM organizing_committee"; 
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . htmlspecialchars($row['passport_data']) . '|' . htmlspecialchars($row['id_nir']) . '">' 
                . htmlspecialchars($row['passport_data']) . ' (NIR ID: ' . htmlspecialchars($row['id_nir']) 
                . ', Статус председателя: ' . ($row['status_chairman'] ? 'Да' : 'Нет') . ')</option>';
        }
        ?>
    </select>
</form>
<br/>

<?php
if (isset($_POST['participant_id']) && !empty($_POST['participant_id'])) {
    list($passport_data, $id_nir) = explode('|', htmlspecialchars(stripslashes($_POST['participant_id'])));
    
    // Получаем текущие данные участника
    $query_get_participant = "SELECT * FROM organizing_committee WHERE passport_data = '$passport_data' AND id_nir = $id_nir";
    $result = mysql_query($query_get_participant) or die("Query failed: " . mysql_error());
    $participant = mysql_fetch_assoc($result); 
    
    if ($participant) {
        $status_chairman = $participant['status_chairman'];
?>
        <form method="post" action="">
            <input type="hidden" name="passport_data" value="<?php echo $passport_data; ?>" />
            <input type="hidden" name="id_nir" value="<?php echo $id_nir; ?>" />
            
            <label for="status_chairman">Является председателем:</label>
            <select name="status_chairman" id="status_chairman" required>
                <option value="0" <?php if ($status_chairman == 0) echo "selected"; ?>>Нет</option>
                <option value="1" <?php if ($status_chairman == 1) echo "selected"; ?>>Да</option>
            </select><br>

            <br/>
            <input type="submit" name="update_participant" value="Изменить">
        </form>
<?php
    } else {
        echo '<p>Участник не найден.</p>';
    }
}

// Обработка изменения записи участника
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_participant'])) {
    $passport_data = htmlspecialchars(stripslashes($_POST['passport_data']));
    $id_nir = intval($_POST['id_nir']);
    $status_chairman = intval($_POST['status_chairman']);
    
    // Обновление записи в таблице Organizing_committee
    $query_update_participant = "UPDATE organizing_committee SET status_chairman = $status_chairman WHERE passport_data = '$passport_data' AND id_nir = $id_nir";

    if (mysql_query($query_update_participant)) {
        $info[] = 'Статус председателя успешно изменён!';
    } else {
        $info[] = 'Ошибка изменения: ' . mysql_error() . '';
    }
}
?>
	
	<h1>Удаление участника</h1>
<form method="post" action="">
    <label for="participant_to_delete">Выберите участника для удаления:</label>
    <select name="participant_to_delete" id="participant_to_delete" required>
        <?php
        // Получаем список участников из Organizing_committee
        $query = "SELECT passport_data, id_nir, status_chairman FROM organizing_committee"; 
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . htmlspecialchars($row['passport_data']) . '|' . htmlspecialchars($row['id_nir']) . '">' 
            . htmlspecialchars($row['passport_data']) . ' (NIR ID: ' . htmlspecialchars($row['id_nir']) 
            . ', Статус председателя: ' . ($row['status_chairman'] ? 'Да' : 'Нет') . ')</option>';
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