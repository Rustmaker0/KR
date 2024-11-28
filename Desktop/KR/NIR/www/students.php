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
$tblname = 'students';
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

// Добавление нового студента
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка наличия всех необходимых данных после отправки формы
    $stud_name = !empty($_POST['stud_name']) ? htmlspecialchars(stripslashes($_POST['stud_name'])) : '';
    $stud_secname = !empty($_POST['stud_secname']) ? htmlspecialchars(stripslashes($_POST['stud_secname'])) : '';
    $stud_surname = !empty($_POST['stud_surname']) ? htmlspecialchars(stripslashes($_POST['stud_surname'])) : '';
    $group = !empty($_POST['group']) ? htmlspecialchars(stripslashes($_POST['group'])) : '';
    $id_spec = !empty($_POST['id_spec']) ? intval($_POST['id_spec']) : 0;
    $passport_data = !empty($_POST['passport_data']) ? htmlspecialchars(stripslashes($_POST['passport_data'])) : '';

    // Проверка на пустые поля
    if (!empty($stud_name) && !empty($stud_secname) && !empty($stud_surname) && !empty($group) && !empty($passport_data)) {
        // Проверка на уникальность паспортных данных
        $query_check_students = "SELECT * FROM students WHERE passport_data = '$passport_data'";
        $result_check_students = mysql_query($query_check_students) or die("Query_check_students failed: " . mysql_error());

        if (mysql_numrows($result_check_students) > 0) {
            $info[] = 'Ошибка: Паспортные данные уже существуют!';
        } else {
            // Вставка новой записи
            $query_add = "INSERT INTO students (stud_name, stud_secname, stud_surname, `group`, id_spec, passport_data) 
                          VALUES ('$stud_name', '$stud_secname', '$stud_surname', '$group', $id_spec, '$passport_data')";

            if (mysql_query($query_add)) {
                $info[] = 'Студент успешно добавлен!';
            } else {
                $info[] = 'Ошибка добавления: ' . mysql_error();
            }
        }
    } else {

    }


}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    $student_id = intval($_POST['student_id']);
    $stud_name = htmlspecialchars(stripslashes($_POST['stud_name']));
    $stud_secname = htmlspecialchars(stripslashes($_POST['stud_secname']));
    $stud_surname = htmlspecialchars(stripslashes($_POST['stud_surname']));
    $group = htmlspecialchars(stripslashes($_POST['group']));
    $id_spec = intval($_POST['id_spec']);
    $passport_data = htmlspecialchars(stripslashes($_POST['passport_data']));

    if (!empty($stud_name) && !empty($stud_secname) && !empty($stud_surname) && !empty($group) && !empty($passport_data)) {
        // Проверка на уникальность паспортных данных
        $query_check_employees = "SELECT * FROM employees WHERE passport_data = '$passport_data' AND id_stud != $student_id";
        $result_check_employees = mysql_query($query_check_employees) or die("Query_check_employees failed: " . mysql_error());
        
        $query_check_students = "SELECT * FROM students WHERE passport_data = '$passport_data' AND id_stud != $student_id";
        $result_check_students = mysql_query($query_check_students) or die("Query_check_students failed: " . mysql_error());

        if (mysql_numrows($result_check_employees) > 0) {
            $info[] = 'Ошибка: Паспортные данные уже существуют в таблице сотрудников!';
        } elseif (mysql_numrows($result_check_students) > 0) {
            $info[] = 'Ошибка: Паспортные данные уже существуют в таблице студентов!';
        } else {
            // Вставка новой записи
            $query_update = "UPDATE students SET stud_name = '$stud_name', stud_secname = '$stud_secname', stud_surname = '$stud_surname', `group` = '$group', id_spec = $id_spec, passport_data = '$passport_data' WHERE id_stud = $student_id";

            if (mysql_query($query_update)) {
                $info[] = 'Студент успешно изменен!';
            } else {
                $info[] = 'Ошибка изменения: ' . mysql_error();
            }
        }
    } else {
        $info[] = 'Пожалуйста, заполните все поля.';
    }
}

// Обработка удаления студента
if (isset($_POST['student_to_delete'])) {
    $passport_data = mysql_real_escape_string($_POST['student_to_delete']); // Экранируем ввод пользователя

    // Удаляем записи из coursework или других связанных таблиц, если они существуют (например, зачетные работы)
    $query_delete_participants = "DELETE FROM participants WHERE passport_data = '$passport_data'";
    mysql_query($query_delete_participants) or die("Query_delete_courses failed: " . mysql_error());

	    $query_delete_committee = "DELETE FROM organizing_committee WHERE passport_data = '$passport_data'";
    mysql_query($query_delete_committee) or die("Query_delete_courses failed: " . mysql_error());
	
    // Удаляем запись из таблицы студентов
    $query_del = "DELETE FROM students WHERE passport_data = '$passport_data'";
    mysql_query($query_del) or die("Query_del failed: " . mysql_error());

    // Уведомление об успешном удалении
    $info[] = 'Студент успешно удалён!';
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
<h1>Таблица Students</h1>

<h1>Добавление нового студента</h1>
<form method="post" action="">
    
    <label for="stud_name">Имя:</label>
    <input type="text" name="stud_name" id="stud_name" required><br>

    <label for="stud_secname">Отчество:</label>
    <input type="text" name="stud_secname" id="stud_secname" required><br>

    <label for="stud_surname">Фамилия:</label>
    <input type="text" name="stud_surname" id="stud_surname" required><br>

    <label for="group">Группа:</label>
    <input type="text" name="group" id="group" required><br>

    <label for="id_spec">Выберите специальность:</label>
    <select name="id_spec" id="id_spec" required>
        <?php
        // Получаем список специальностей
        $query = "SELECT id_spec, name_spec FROM specialization";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_spec'] . '">' . htmlspecialchars($row['name_spec']) . '</option>';
        }
        ?>
    </select><br>

    <label for="passport_data">Паспортные данные:</label>
    <input type="text" name="passport_data" id="passport_data" required><br>

    <br/>
    <input type="submit" value="Добавить" />
</form>
<br/>

	
	<h1>Изменение студента</h1>
<form method="post" action="">
    <label for="student_id">Выберите студента:</label>
    <select name="student_id" id="student_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php

        $query = "SELECT id_stud, stud_name, stud_secname, stud_surname, passport_data FROM students"; // Получаем список студентов
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_stud'] . '">' . htmlspecialchars($row['stud_name']) . ' ' . htmlspecialchars($row['stud_secname']) . ' ' . htmlspecialchars($row['stud_surname']) .  ' (' . htmlspecialchars($row['passport_data']) . ')'.'</option>';
        }
        ?>
    </select>
</form>
<br/>
	
<?php
if (isset($_POST['student_id']) && !empty($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    $query_get_student = "SELECT * FROM students WHERE id_stud = $student_id";
    $result = mysql_query($query_get_student) or die("Query failed: " . mysql_error());
    $student = mysql_fetch_assoc($result); 
    if ($student) {
        $stud_name = $student['stud_name'];
        $stud_secname = $student['stud_secname'];
        $stud_surname = $student['stud_surname'];
        $group = $student['group'];
        $id_spec = $student['id_spec'];
        $passport_data = $student['passport_data'];
?>
        <form method="post" action="">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>" />
            <label for="stud_name">Имя:</label>
            <input type="text" name="stud_name" id="stud_name" value="<?php echo htmlspecialchars($stud_name); ?>" required><br>

            <label for="stud_secname">Отчество:</label>
            <input type="text" name="stud_secname" id="stud_secname" value="<?php echo htmlspecialchars($stud_secname); ?>" required><br>

            <label for="stud_surname">Фамилия:</label>
            <input type="text" name="stud_surname" id="stud_surname" value="<?php echo htmlspecialchars($stud_surname); ?>" required><br>

            <label for="group">Группа:</label>
            <input type="text" name="group" id="group" value="<?php echo htmlspecialchars($group); ?>" required><br>

            <label for="id_spec">Выберите специальность:</label>
            <select name="id_spec" id="id_spec" required>
                <?php
                // Получаем список специальностей
                $query = "SELECT id_spec, name_spec FROM specialization"; // Предположим, у вас есть таблица specification
                $result = mysql_query($query) or die("Query failed: " . mysql_error());
                while ($row = mysql_fetch_assoc($result)) {
                    $selected = ($row['id_spec'] == $id_spec) ? 'selected' : '';
                    echo '<option value="' . $row['id_spec'] . '" ' . $selected . '>' . htmlspecialchars($row['name_spec']) . '</option>';
                }
                ?>
            </select><br>

            <label for="passport_data">Паспортные данные:</label>
			<input type="text" name="passport_data" id="passport_data" value="<?php echo htmlspecialchars($passport_data); ?>" required><br>

            <br/><br/>
            <input type="submit" name="update_student" value="Изменить">
        </form>
<?php
    } else {
        echo '<p>Студент не найден.</p>';
    }
}
?>

<h1>Удаление студента</h1>
<form method="post" action="">
    <label for="student_to_delete">Выберите студента для удаления:</label>
    <select name="student_to_delete" required>
        <?php
        // Получаем список студентов
        $query = "SELECT passport_data, CONCAT(stud_name, ' ', stud_secname, ' ', stud_surname) AS full_name FROM students";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['passport_data'] . '">' . htmlspecialchars($row['full_name']) . ' (' . htmlspecialchars($row['passport_data']) . ')</option>';
        }
        ?>
    </select>
    <input type="submit" value="Удалить">
</form>
<br/>
<?php
// Выполняем SQL-запросы для отображения содержимого таблицы
$query_content = "SELECT * FROM " . $tblname . " ORDER BY id_stud";
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