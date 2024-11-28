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
$tblname = 'specialization';
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
// Добавление новой записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name_spec_add'])) {
    // Обработка входящих данных
    $name_spec = htmlspecialchars(stripslashes($_POST['name_spec_add']));
    $faculty_id = intval($_POST['faculty_id']);

    // Проверка на пустые поля
    if (!empty($name_spec)) {
        // Проверка на уникальность названия специальности
        $query_any_repeat = "SELECT * FROM specialization WHERE name_spec = '$name_spec'";
        $result = mysql_query($query_any_repeat) or die("Query_any_repeat failed: " . mysql_error());
        
        if (mysql_numrows($result) != 0) {
            $info[] = 'Специальность с таким именем уже существует!';
        } else {
            // Вставка новой записи
            $query_add = "INSERT INTO specialization (name_spec, id_faculty) 
                          VALUES ('$name_spec', $faculty_id)";

            if (mysql_query($query_add)) {
                $info[] = 'Специальность успешно добавлена!';
            } else {
                $info[] = 'Ошибка добавления: ' . mysql_error();
            }
        }
    } else {
        $info[] = 'Пожалуйста, заполните все поля.';
    }
}
// Изменение записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_specialization'])) {
    $specialization_id = intval($_POST['specialization_id']);
    $name_spec = htmlspecialchars(stripslashes($_POST['name_spec_upd']));
    $faculty_id = intval($_POST['faculty_id']);

    // Проверяем, чтобы поле название специальности не было пустым
    if (!empty($name_spec)) {
        $query_update = "UPDATE specialization SET name_spec = '$name_spec', id_faculty = $faculty_id WHERE id_spec = $specialization_id";

        if (mysql_query($query_update)) {
            $info[] = 'Специальность успешно изменена!';
        } else {
            $info[] = 'Ошибка изменения: ' ;
        }
    } else {
		$info[] = 'Пожалуйста, заполните все поля!';
        
    }
}
// Обработка удаления специальности
if (isset($_POST['delete_specialization'])) {
    $specialization_id = intval($_POST['specialization_to_delete']);  // Преобразуем строку в число

    // Проверяем, существуют ли записи в таблице students
    $query_check = "SELECT COUNT(*) as count FROM students WHERE id_spec = $specialization_id";
    $result_check = mysql_query($query_check) or die("Query_check failed: " . mysql_error());
    $check_data = mysql_fetch_assoc($result_check);

    // Если есть связанные записи, не удаляем
    if ($check_data['count'] > 0) {
        $info[] = 'Не удалось удалить запись! Специальность связана с другими записями.';
    } else {
        // Если записей нет, удаляем специальность
        $query_del = "DELETE FROM specialization WHERE id_spec = $specialization_id";
        if (mysql_query($query_del)) {
            $info[] = 'Запись успешно удалена!';
        } else {
            $info[] = 'Ошибка удаления: ';
        }
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
<h1>Таблица Specialization</h1>
<h1>Добавление новой специальности</h1>
<form method="post" action="">
    <label for="name_spec_add">Название специальности:</label>
    <input type="text" name="name_spec_add" id="name_spec_add" required><br>

    <label for="faculty_id">Выберите факультет:</label>
    <select name="faculty_id" id="faculty_id" required>
        <?php
        // Получаем список факультетов
        $query = "SELECT id_faculty, name_faculty FROM faculty";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_faculty'] . '">' . $row['name_faculty'] . '</option>';
        }
        ?>
    </select>

    <br/><br/>
    <input type="submit" value="Добавить" />
</form>
<br/>
	<h1>Изменение специальности</h1>
<form method="post" action="">
    <label for="specialization_id">Выберите специальность:</label>
    <select name="specialization_id" id="specialization_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php
        $query = "SELECT id_spec, name_spec FROM specialization";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_spec'] . '">' . $row['name_spec'] . '</option>';
        }
        ?>
    </select>
</form>
<br/>

<?php
if (isset($_POST['specialization_id']) && !empty($_POST['specialization_id'])) {
    $specialization_id = intval($_POST['specialization_id']);
    $query_get_spec = "SELECT * FROM specialization WHERE id_spec = $specialization_id";
    $result = mysql_query($query_get_spec) or die("Query failed: " . mysql_error());
    $specialization = mysql_fetch_assoc($result); 
    
    if ($specialization) {
        $name_spec = $specialization['name_spec'];
        $faculty_id = $specialization['id_faculty'];
?>
        <form method="post" action="">
            <input type="hidden" name="specialization_id" value="<?php echo $specialization_id; ?>" />
            <label for="name_spec_upd">Название специальности:</label>
            <input type="text" name="name_spec_upd" id="name_spec_upd" value="<?php echo $name_spec; ?>" required><br>
            
            <label for="faculty_id">Выберите факультет:</label>
            <select name="faculty_id" id="faculty_id" required>
                <?php
                // Получаем список факультетов
                $query = "SELECT id_faculty, name_faculty FROM faculty";
                $result = mysql_query($query) or die("Query failed: " . mysql_error());
                while ($row = mysql_fetch_assoc($result)) {
                    $selected = ($row['id_faculty'] == $faculty_id) ? 'selected' : '';
                    echo '<option value="' . $row['id_faculty'] . '" ' . $selected . '>' . $row['name_faculty'] . '</option>';
                }
                ?>
            </select>
            <br/><br/>

            <input type="submit" name="update_specialization" value="Изменить">
        </form>
<?php
    } else {
        echo '<p>Специальность не найдена.</p>';
    }
}
?>
	<h1>Удаление специальности</h1>
<form method="post" action="">
    <label for="specialization_to_delete">Удалить специальность:</label>
    <select name="specialization_to_delete" required>
        <?php
        // Получаем список специальностей
        $query = "SELECT s.id_spec, s.name_spec, f.name_faculty FROM specialization s JOIN faculty f ON s.id_faculty = f.id_faculty";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_spec'] . '">' . $row['name_spec'] . '</option>';
        }
        ?>
    </select>
    <input type="submit" name="delete_specialization" value="Удалить">
</form>
<br/>
<?php
// Выполняем SQL-запросы для отображения содержимого таблицы
$query_content = "SELECT * FROM " . $tblname . " ORDER BY id_spec";
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