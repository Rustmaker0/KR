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
$tblname = 'employees';
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
// Добавление нового сотрудника
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка наличия всех необходимых данных после отправки формы
    $empl_name = !empty($_POST['empl_name']) ? htmlspecialchars(stripslashes($_POST['empl_name'])) : '';
    $empl_secname = !empty($_POST['empl_secname']) ? htmlspecialchars(stripslashes($_POST['empl_secname'])) : '';
    $empl_surname = !empty($_POST['empl_surname']) ? htmlspecialchars(stripslashes($_POST['empl_surname'])) : '';
    $status = !empty($_POST['status']) ? htmlspecialchars(stripslashes($_POST['status'])) : '';
    $post = !empty($_POST['post']) ? htmlspecialchars(stripslashes($_POST['post'])) : '';
    $academic_degree = !empty($_POST['academic_degree']) ? htmlspecialchars(stripslashes($_POST['academic_degree'])) : NULL; // Здесь NULL будет на уровне PHP
    $passport_data = !empty($_POST['passport_data']) ? htmlspecialchars(stripslashes($_POST['passport_data'])) : '';
    $department_id = !empty($_POST['department_id']) ? intval($_POST['department_id']) : 0;

    // Проверка на пустые поля
    if (!empty($empl_name) && !empty($empl_secname) && !empty($empl_surname) && !empty($post) && !empty($passport_data)) {
        // Проверка на уникальность паспортных данных
        $query_check_employees = "SELECT * FROM employees WHERE passport_data = '$passport_data'";
        $result_check_employees = mysql_query($query_check_employees) or die("Query_check_employees failed: " . mysql_error());
		  $query_check_students = "SELECT * FROM students WHERE passport_data = '$passport_data'";
        $result_check_students = mysql_query($query_check_students) or die("Query_check_students failed: " . mysql_error());

        // Проверяем, существуют ли уже такие паспортные данные
        if (mysql_numrows($result_check_employees) > 0 || mysql_numrows($result_check_students) > 0) {
            $info[] = 'Ошибка: Паспортные данные уже существуют!';
        } else {
            // Вставка новой записи
            $query_add = "INSERT INTO employees (empl_name, empl_secname, empl_surname, status, post, academic_degree, id_dep, passport_data) 
                          VALUES ('$empl_name', '$empl_secname', '$empl_surname', '$status', '$post', '$academic_degree', $department_id, '$passport_data')";

            if (mysql_query($query_add)) {
                $info[] = 'Сотрудник успешно добавлен!';
            } else {
                $info[] = 'Ошибка добавления: ' . mysql_error();
            }
        }
    } else {

    }
}

// Обработка удаления сотрудника
if (isset($_POST['employee_to_delete'])) {
    $passport_data = mysql_real_escape_string($_POST['employee_to_delete']); // Экранируем ввод пользователя

    // Удаляем записи из participants, если они существуют
    $query_delete_participants = "DELETE FROM participants WHERE passport_data = '$passport_data'";
    mysql_query($query_delete_participants) or die("Query_delete_participants failed: " . mysql_error());

    // Удаляем записи из organizing_committee, если они существуют
    $query_delete_committee = "DELETE FROM organizing_committee WHERE passport_data = '$passport_data'";
    mysql_query($query_delete_committee) or die("Query_delete_committee failed: " . mysql_error());

    // Удаляем запись из employees
    $query_del = "DELETE FROM employees WHERE passport_data = '$passport_data'";
    mysql_query($query_del) or die("Query_del failed: " . mysql_error());

    // Уведомление об успешном удалении
    $info[] = 'Сотрудник успешно удалён!';
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
<h1>Таблица Employees</h1>
	
	<h1>Добавление нового сотрудника</h1>
<form method="post" action="">
    <label for="empl_name">Имя:</label>
    <input type="text" name="empl_name" id="empl_name" required><br>

    <label for="empl_secname">Отчество:</label>
    <input type="text" name="empl_secname" id="empl_secname" required><br>

    <label for="empl_surname">Фамилия:</label>
    <input type="text" name="empl_surname" id="empl_surname" required><br>

    <label for="status">Статус:</label>
    <select name="status" id="status" required>
        <option value="Преподаватель">Преподаватель</option>
        <option value="Аспирант">Аспирант</option>
    </select><br>

    <label for="post">Должность:</label>
    <input type="text" name="post" id="post" required><br>

<label for="academic_degree">Ученая степень:</label>
    <input type="text" name="academic_degree" id="academic_degree"><br>
	
    <label for="passport_data">Паспортные данные:</label>
    <input type="text" name="passport_data" id="passport_data" required><br>

    <label for="department_id">Выберите кафедру:</label>
    <select name="department_id" id="department_id" required>
        <?php
        // Получаем список кафедр
        $query = "SELECT id_dep, name_dep FROM department";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_dep'] . '">' . $row['name_dep'] . '</option>';
        }
        ?>
    </select>

    <br/><br/>
    <input type="submit" value="Добавить" />
</form>
<br/>
<h1>Изменение сотрудника</h1>
<form method="post" action="">
    <label for="employee_id">Выберите сотрудника:</label>
    <select name="employee_id" id="employee_id" onchange="this.form.submit()">
        <option value="">----</option>
        <?php
        $query = "SELECT id_empl, empl_name, empl_secname, empl_surname, passport_data FROM employees";
        $result = mysql_query($query) or die("Query failed: " . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo '<option value="' . $row['id_empl'] . '">' . $row['empl_name'] . ' ' . $row['empl_secname'] . ' ' . $row['empl_surname']. ' (' . htmlspecialchars($row['passport_data']) . ')'. '</option>';
        }
        ?>
    </select>
</form>
<br/>

<?php
if (isset($_POST['employee_id']) && !empty($_POST['employee_id'])) {
    $employee_id = intval($_POST['employee_id']);
    $query_get_empl = "SELECT * FROM employees WHERE id_empl = $employee_id";
    $result = mysql_query($query_get_empl) or die("Query failed: " . mysql_error());
    $employee = mysql_fetch_assoc($result);

    if ($employee) {
        $empl_name = $employee['empl_name'];
        $empl_secname = $employee['empl_secname'];
        $empl_surname = $employee['empl_surname'];
        $status = $employee['status'];
        $post = $employee['post'];
        $academic_degree = $employee['academic_degree'];
        $passport_data = $employee['passport_data'];
        $department_id = $employee['id_dep'];
?>
        <form method="post" action="">
            <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />
            <label for="empl_name_upd">Имя:</label>
            <input type="text" name="empl_name_upd" id="empl_name_upd" value="<?php echo htmlspecialchars($empl_name); ?>" required><br>

            <label for="empl_secname_upd">Отчество:</label>
            <input type="text" name="empl_secname_upd" id="empl_secname_upd" value="<?php echo htmlspecialchars($empl_secname); ?>" required><br>

            <label for="empl_surname_upd">Фамилия:</label>
            <input type="text" name="empl_surname_upd" id="empl_surname_upd" value="<?php echo htmlspecialchars($empl_surname); ?>" required><br>

            <label for="status">Статус:</label>
            <select name="status" id="status" required>
                <option value="Преподаватель" <?php echo ($status == 'Преподаватель') ? 'selected' : ''; ?>>Преподаватель</option>
                <option value="Аспирант" <?php echo ($status == 'Аспирант') ? 'selected' : ''; ?>>Аспирант</option>
            </select><br>

            <label for="post">Должность:</label>
            <input type="text" name="post" id="post" value="<?php echo htmlspecialchars($post); ?>" required><br>

            <label for="academic_degree">Ученая степень:</label>
            <input type="text" name="academic_degree" id="academic_degree" value="<?php echo htmlspecialchars($academic_degree); ?>"><br>

            <label for="passport_data">Паспортные данные:</label>
			<input type="text" name="passport_data" id="passport_data" value="<?php echo htmlspecialchars($passport_data); ?>" required><br>

            <label for="department_id">Выберите кафедру:</label>
            <select name="department_id" id="department_id" required>
                <?php
                // Получаем список кафедр
                $query = "SELECT id_dep, name_dep FROM department";
                $result = mysql_query($query) or die("Query failed: " . mysql_error());
                while ($row = mysql_fetch_assoc($result)) {
                    $selected = ($row['id_dep'] == $department_id) ? 'selected' : '';
                    echo '<option value="' . $row['id_dep'] . '" ' . $selected . '>' . htmlspecialchars($row['name_dep']) . '</option>';
                }
                ?>
            </select>
            <br/><br/>
            <input type="submit" name="update_employee" value="Изменить">
        </form>
<?php
    } else {
        $info[] ='Сотрудник не найден.';
    }
}// Обработка обновления данных сотрудника
if (isset($_POST['update_employee'])) {
    $employee_id = intval($_POST['employee_id']);
    $empl_name = mysql_real_escape_string($_POST['empl_name_upd']);
    $empl_secname = mysql_real_escape_string($_POST['empl_secname_upd']);
    $empl_surname = mysql_real_escape_string($_POST['empl_surname_upd']);
    $status = mysql_real_escape_string($_POST['status']);
    $post = mysql_real_escape_string($_POST['post']);
    $academic_degree = !empty($_POST['academic_degree']) ? "'" . mysql_real_escape_string($_POST['academic_degree']) . "'" : "NULL"; // Если поле пустое, ставим NULL
    $passport_data = mysql_real_escape_string($_POST['passport_data']);
    $department_id = intval($_POST['department_id']);

    // Обновляем данные сотрудника
    $query_update = "UPDATE employees SET 
        empl_name = '$empl_name', 
        empl_secname = '$empl_secname', 
        empl_surname = '$empl_surname', 
        status = '$status', 
        post = '$post', 
        academic_degree = $academic_degree, 
        passport_data = '$passport_data', 
        id_dep = $department_id 
        WHERE id_empl = $employee_id";

    mysql_query($query_update) or die("Query_update failed: " . mysql_error());
    $info[] ='Данные сотрудника успешно обновлены.';
}
?>
	
<h1>Удаление сотрудника</h1>
<form method="post" action="">
    <label for="employee_to_delete">Выберите сотрудника для удаления:</label>
    <select name="employee_to_delete" required>
        <?php
        // Получаем список сотрудников
        $query = "SELECT passport_data, CONCAT(empl_name, ' ', empl_secname, ' ', empl_surname) AS full_name FROM employees";
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
$query_content = "SELECT * FROM " . $tblname . " ORDER BY id_empl";
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