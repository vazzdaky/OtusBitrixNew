<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

// Импорт необходимых классов и моделей
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Query;
use Models\Lists\TrainingTable;

// Проверка наличия модуля iblock. Если модуль не найден, то выполнение скрипта прекращается.
if (!Loader::includeModule('iblock')) {
    return;
}

// Создаем новый объект Query для работы с таблицей TrainingTable и устанавливаем выборку полей из таблицы, включая связи
$q = new Query(TrainingTable::class);
$q->setSelect([
    'ID',
    'TITLE',
    'DESCRIPTION',
    'BEGINNING_COURSE',
    'DOCTOR_NAME' => 'DOCTOR_TRAINING.NAME',
    'PROC_NAME' => 'TRAINING_PROCEDUR.NAME',
    'AUTHOR_NAME' => 'AUTHORS.FIRST_NAME',
    'AUTHOR_LAST_NAME' => 'AUTHORS.LAST_NAME',
    'AUTHOR_SECOND_NAME' => 'AUTHORS.SECOND_NAME',
]);

// Выполнение запроса и инициализация массива, перебираем все данные из запроса, добавляем данные в массив
$result = $q->exec();
$training = [];

while ($arItem = $result->Fetch()) {
    $trainingId = $arItem['ID'];

    if (!isset($training[$trainingId])) {
        $training[$trainingId] = [
            'TITLE' => $arItem['TITLE'],
            'DESCRIPTION' => $arItem['DESCRIPTION'],
            'BEGINNING_COURSE' => $arItem['BEGINNING_COURSE'],
            'DOCTORS' => [],
            'PROC' => [],
            'AUTHORS' => [],

        ];
    }

    if ($arItem['DOCTOR_NAME'] && !in_array($arItem['DOCTOR_NAME'], $training[$trainingId]['DOCTORS'])) {
        $training[$trainingId]['DOCTORS'][] = $arItem['DOCTOR_NAME'];
    }

    if ($arItem['PROC_NAME'] && !in_array($arItem['PROC_NAME'], $training[$trainingId]['PROC'])) {
        $training[$trainingId]['PROC'][] = $arItem['PROC_NAME'];
    }

    if ($arItem['AUTHOR_NAME'] && !in_array(sprintf(
        '%s %s %s',
        $arItem['AUTHOR_LAST_NAME'],
        $arItem['AUTHOR_NAME'],
        $arItem['AUTHOR_SECOND_NAME'],
    ), $training[$trainingId]['AUTHORS'])) {
        $training[$trainingId]['AUTHORS'][] = sprintf(
            '%s %s %s',
            $arItem['AUTHOR_LAST_NAME'],
            $arItem['AUTHOR_NAME'],
            $arItem['AUTHOR_SECOND_NAME'],
        );
    }
}

foreach ($training as $id => $item) {
?>
    <div class="training">
        <h1><a href="/training">Тренинги врачей поликлинники</a></h1>
        <h2>Название тренинга: <?= htmlspecialchars($item['TITLE']) ?></h2>
        <h2>Описание: <?= htmlspecialchars($item['DESCRIPTION']) ?></h2>
        <p>Начало курса: <?= htmlspecialchars($item['BEGINNING_COURSE']) ?></p>
        <p>Доктора: <?php $doctorsString = implode(', ', $item['DOCTORS']);
                    echo htmlspecialchars($doctorsString); ?></p>
        <p>Процедуры: <?php $proceduresString = implode(', ', $item['PROC']);
                        echo htmlspecialchars($proceduresString); ?></p>
        <p>Авторы: <?php foreach ($item['AUTHORS'] as $author) : ?>
                <?= htmlspecialchars($author) ?>
            <?php endforeach; ?></p>
    </div>
<?php
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>