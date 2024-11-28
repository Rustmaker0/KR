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
$tblname = 'department';
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name_dep_add'])) {
    // Обработка входящих данных
    $name_dep = htmlspecialchars(stripslashes($_POST['name_dep_add']));
    $dep_head_name = htmlspecialchars(stripslashes($_POST['dep_head_name']));
    $dep_head_secname = htmlspecialchars(stripslashes($_POST['dep_head_secname']));
    $dep_head_surname = htmlspecialchars(stripslashes($_POST['dep_head_surname']));
    $faculty_id = intval($_POST['faculty_id']);

    // Проверка на пустые поля
    if (!empty($name_dep) && !empty($dep_head_name) && !empty($dep_head_secname) && !empty($dep_head_surname)) {
        // Проверка на уникальность имени департамента
        $query_any_repeat = "SELECT * FROM department WHERE name_dep = '$name_dep'";
        $result = mysql_query($query_any_repeat) or die("Query_any_repeat failed: " . mysql_error());
        
        if (mysql_numrows($result) != 0) {
            $info[] = 'Кафедра с таким именем уже существует!';
        } else {
            // Вставка новой записи
            $query_add = "INSERT INTO department (name_dep, dep_head_name, dep_head_secname, dep_head_surname, id_faculty) 
                          VALUES ('$name_dep', '$dep_head_name', '$dep_head_secname', '$dep_head_surname', $faculty_id)";

            if (mysql_query($query_add)) {
                $info[] = 'Кафедра успешно добавлена!';
            } else {
                $info[] = 'Ошибка добавления: ' . mysql_error();
            }
        }
    } else {
        $info[] = 'Пожалуйста, заполните все поля.';
    }
}

// Обработка изменения записи в департаменте
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_department'])) {
    // Сбор данных из формы
    $department_id = intval($_POST['department_id']);
    $name_dep = htmlspecialchars(stripslashes($_POST['name_dep_upd']));
    $dep_head_name = htmlspecialchars(stripslashes($_POST['dep_head_name']));
    $dep_head_secname = htmlspecialchars(stripslashes($_POST['dep_head_secname']));
    $dep_head_surname = htmlspecialchars(stripslashes($_POST['dep_head_surname']));
    $faculty_id = intval($_POST['faculty_id']);

    // Проверка на пустые поля
    if (!empty($name_dep) && !empty($dep_head_name) && !empty($dep_head_secname) && !empty($dep_head_surname)) {
        // Обновление записи
        $query_update = "UPDATE department SET name_dep = '$name_dep', dep_head_name = '$dep_head_name', dep_head_secname = '$dep_head_secname', dep_head_surname = '$dep_head_surname', id_faculty = $faculty_id WHERE id_dep = $department_id";

        if (mysql_query($query_update)) {
            $info[] = 'Кафедра успешно изменена!';
        } else {
            $info[] = 'Ошибка изменения: ' . mysql_error();
        }
    } else {
        $info[] = 'Пожалуйста, заполните все поля.';
    }
}
// Обработка удаления департамента
if (isset($_POST['department_to_delete'])) {
    $department_id = intval($_POST['department_to_delete']);  // Преобразуем строку в число

    // Проверяем, существует ли запись в таблице employees
    $query_check = "SELECT COUNT(*) as count FROM employees WHERE id_dep = $department_id";
    $result_check = mysql_query($query_check) or die("Query_check failed: " . mysql_error());
    $check_data = mysql_fetch_assoc($result_check);

    // Если есть связанные записи, не удаляем
    if ($check_data['count'] > 0) {
        $info[] = 'Не удалось удалить запись! Кафедра связана с другими записями.';
    } else {
        // Если записей нет, удаляем департамент
        $query_del = "DELETE FROM department WHERE id_dep = $department_id";
        mysql_query($query_del) or die("Query_del failed: " . mysql_error());
        $info[] = 'Запись успешно удалена!';
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
<meta name="keywords" content="База данных, НИР, PHP, MySQL, web-программирование"/>
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
<h1>Таблица Department</h1>
	
<h1>Добавление новой кафедры</h1>
<form method="post" action="">
    <label for="name_dep_add">Название кафедры:</label>
    <input type="text" name="name_dep_add" id="name_dep_add" required><br>

    <label for="dep_head_name">Имя заведующего:</label>
    <input type="text" name="dep_head_name" id="dep_head_name" required><br>

    <label for="dep_head_secname">Отчество заведующего:</label>
    <input type="text" name="dep_head_secname" id="dep_head_secname" required><br>

    <label for="dep_head_surname">Фамилия заведующего:</label>
    <input type="text" name="dep_head_surname" id="dep_head_surname" required><br>

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
	
<h1>Изменение кафедры</h1>
<form method="post" action="">
    <label for="department_id">Выберите кафедру:</label>
    <select name="department_id" id="department_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php
        // Получаем список департаментов
        $query = "SELECT id_dep, name_dep FROM department";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_dep'] . '">' . $row['name_dep'] . '</option>';
        }
        ?>
    </select>
</form>
<br/>

<?php
if (isset($_POST['department_id']) && !empty($_POST['department_id'])) {
    $department_id = intval($_POST['department_id']);
    
    // Получаем данные выбранного департамента
    $query_get_dep = "SELECT * FROM department WHERE id_dep = $department_id";
    $result = mysql_query($query_get_dep) or die("Query failed: " . mysql_error());
    $department = mysql_fetch_assoc($result);
    
    if ($department) {
        // Отображение данных для изменения
        $name_dep = $department['name_dep'];
        $dep_head_name = $department['dep_head_name'];
        $dep_head_secname = $department['dep_head_secname'];
        $dep_head_surname = $department['dep_head_surname'];
        $faculty_id = $department['id_faculty'];
?>
        <form method="post" action="">
            <input type="hidden" name="department_id" value="<?php echo $department_id; ?>" />
            <label for="name_dep_upd">Название кафедры:</label>
            <input type="text" name="name_dep_upd" id="name_dep_upd" value="<?php echo $name_dep; ?>" required><br>

            <label for="dep_head_name">Имя заведующего:</label>
            <input type="text" name="dep_head_name" id="dep_head_name" value="<?php echo $dep_head_name; ?>" required><br>

            <label for="dep_head_secname">Отчество заведующего:</label>
            <input type="text" name="dep_head_secname" id="dep_head_secname" value="<?php echo $dep_head_secname; ?>" required><br>

            <label for="dep_head_surname">Фамилия заведующего:</label>
            <input type="text" name="dep_head_surname" id="dep_head_surname" value="<?php echo $dep_head_surname; ?>" required><br>

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

            <input type="submit" name="update_department" value="Изменить">
        </form>
<?php
    } else {
        echo '<p>Департамент не найден.</p>';
    }
}
?>
	<h1>Удаление кафедры</h1>
<form method="post" action="">
    <label for="department_to_delete">Удалить кафедру:</label>
    <select name="department_to_delete" required>
        <?php
        // Получаем список департаментов
        $query = "SELECT d.id_dep, d.name_dep, f.name_faculty FROM department d JOIN faculty f ON d.id_faculty = f.id_faculty";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_dep'] . '">' . $row['name_dep'] . '</option>';
        }
        ?>
    </select>
    <input type="submit" value="Удалить">
</form>
<br/>
<?php
// Выполняем SQL-запросы для отображения содержимого таблицы
$query_content = "SELECT * FROM " . $tblname . " ORDER BY id_dep";
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