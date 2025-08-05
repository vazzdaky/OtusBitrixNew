<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Заголовок страницы и добавляем файл стилей на страницу
$APPLICATION->SetTitle('Врачи поликлиники');
$APPLICATION->SetAdditionalCSS('/doctors/style.css');

// Классы которые используем в работе с инфоблоками
use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Models\Lists\ProcsPropertyValuesTable as ProcsTable;

// Объявляем массивы для хранения данных
$doctors = [];
$doctor = [];
$procs = [];

// Присваивам значения переменным
$path = trim($_GET['path'], '/');
$action = '';
$doctor_name = '';

// Проверяем переменную и присваиваем значения
if (!empty($path)) {
    $path_parts = explode('/', $path);
    if (sizeof($path_parts) < 3) {
        if (sizeof($path_parts) == 2 && $path_parts[0] == 'edit') {
            $action = 'edit';
            $doctor_name = $path_parts[1];
        } elseif (
            sizeof($path_parts) == 1 && in_array($path_parts[0], ['new', 'newproc'])
        ) {
            $action = $path_parts[0];
        } else $doctor_name = $path_parts[0];
    }
}
// Запрос к таблицам DoctorsTable и ProcsTable вывод данных врача
if (!empty($doctor_name)) {
    $doctor = DoctorsTable::query()
        ->setSelect([
            '*',
            'NAME' => 'ELEMENT.NAME',
            'PROC_ID',
            'ID' => 'ELEMENT.ID'
        ])
        ->where("NAME", $doctor_name)
        ->fetch();

    if (is_array($doctor)) {
        if ($doctor['PROC_ID']) {
            $procs = ProcsTable::query()
                ->setSelect(['NAME' => 'ELEMENT.NAME'])
                ->where("ELEMENT.ID", "in", $doctor['PROC_ID'])
                ->fetchAll();
        }
    } else {
        header("Location: /doctors");
        exit();
    }
}
// Если переменные пустые(не выбран врач), выводим всех врачей из таблицы
if (empty($doctor_name) && empty($action)) {
    $doctors = DoctorsTable::query()
        ->setSelect(['*', "NAME" => "ELEMENT.NAME", "ID" => "ELEMENT.ID"])
        ->fetchAll();
}
// Добавляем процедуру
if ($action == 'newproc') {
    if (isset($_POST['proc-submit'])) {
        unset($_POST['proc-submit']);
        if (ProcsTable::add($_POST)) {
            header("Location: /doctors");
            exit();
        } else echo "Произошла ошибка";
    }
}
// Добавляем врача
if ($action == 'new' || $action == 'edit') {
    if (isset($_POST['doctor-submit'])) {
        unset($_POST['doctor-submit']);
        if ($action == 'edit' && !empty($_POST['ID'])) {
            $ID = $_POST['ID'];
            unset($_POST['ID']);
            $_POST['IBLOCK_ELEMENT_ID'] = $ID;

            $procs = $_POST['PROC_ID'];
            unset($_POST['PROC_ID']);
            CIBlockElement::SetPropertyValues($ID, DoctorsTable::IBLOCK_ID, $procs, "PROC_ID");

            if (DoctorsTable::update($_POST['ID'], $_POST)) {
                header("Location: /doctors");
                exit();
            } else echo "Произошла ошибка";
        }
        if ($action == 'new' && DoctorsTable::add($_POST)) {
            header("Location: /doctors");
            exit();
        } else echo "Произошла ошибка";
    }

    $proc_options = ProcsTable::query()->setSelect(["ID" => "ELEMENT.ID", "NAME" => "ELEMENT.NAME"])->fetchAll();
    if (!empty($doctor_name)) {
        $data = $doctors;
    }
}
?>
<section class="doctors">
    <h1><a href="/doctors">Врачи поликлинники</a></h1>

    <?php if (empty($action)) : ?>
        <div class="add-buttons">
            <?php if (empty($doctor_name)) : ?>
                <a href="/doctors/new"><button>Добавить нового врача</button></в>
                    <a href="/doctors/newproc"><button>Добавить новую процедуру</button></a>
                <?php else : ?>
                    <a href="/doctors/edit/<?= $doctor_name ?>"></a>
                <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="cards-list">
        <?php foreach ($doctors as $doc) { ?>
            <a class="card" href="/doctors/<?= $doc["NAME"] ?>">
                <div class="fio">
                    <?= $doc['LAST_NAME'] ?>
                    <?= $doc['FIRST_NAME'] ?>
                    <?= $doc['MIDDLE_NAME'] ?>
                </div>
            </a>
        <?php } ?>
    </div>

    <?php if (is_array($doctor) && sizeof($doctor) > 0 && $action != 'edit') : ?>
        <div class="doctor-page">
            <h2><?= $doctor['LAST_NAME'] . " " . $doctor['FIRST_NAME'] . " " . $doctor['MIDDLE_NAME'] ?></h2>
            <h3>Процедуры:</h3>
            <ul>
                <?php foreach ($procs as $proc) : ?>
                    <li><?= $proc['NAME'] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($action == 'new' || $action == 'edit'): ?>
        <form method="POST">
            <h2 style="text-align:center;">Данные нового врача</h2>
            <div class="doctor-add-form">

                <?php if (isset($data['ID'])): ?>
                    <input type="hidden" name="ID" value="<?= $data['ID'] ?>" />
                <?php endif; ?>

                <input type="text" name="NAME" placeholder="Фамилия врача латиницей" value="<?= $data['NAME'] ?? '' ?>" />
                <input type="text" name="LAST_NAME" placeholder="Фамилия нового врача" value="<?= $data['LAST_NAME'] ?? '' ?>" />
                <input type="text" name="FIRST_NAME" placeholder="Имя нового вpaча" value="<?= $data['FIRST_NAME'] ?? '' ?>" />
                <input type="text" name="MIDDLE_NAME" placeholder="Отчество нового врача" value="<?= $data['MIDDLE_NAME'] ?? '' ?>" />

                <select multiple name="PROC_ID[]">
                    <option value="" selected disabled>Выберите процедуры</option>
                    <?php foreach ($proc_options as $proc): ?>
                        <option value="<?= $proc['ID'] ?>"
                            <?php if (isset($data['PROC_ID']) && in_array($proc['ID'], $data['PROC_ID'])): ?>selected<?php endif; ?>>
                            <?= $proc['NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" name="doctor-submit" value="Coxpaить" />
            </div>
        </form>
    <?php endif; ?>

    <?php if ($action == 'newproc'): ?>
        <form method="POST">
            <h2 style="text-align:center;">Добавить новую процедуру</h2>
            <div class="doctor-add-form">
                <input type="text" name="NAME" placeholder="Название процедуры" />
                <input type="submit" name="proc-submit" value="Coxpaнить" />
            </div>
        </form>
    <?php endif; ?>
</section>