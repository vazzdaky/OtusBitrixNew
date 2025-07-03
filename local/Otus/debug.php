<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Проверка OtusLog");
?> 
      <?php
        // Открываем файл для записи в режиме добавления (чтобы не перезаписывать содержимое)
        $file = fopen('otusLog.log', 'a');

        // Проверяем, удалось ли открыть файл
        if ($file) {
            // Записываем текущую дату и время
            fwrite($file, date('Y-m-d H:i:s') . PHP_EOL);
            fclose($file);
        } else {
            echo 'Не удалось открыть файл для записи';
        }
        //Выводим на страницу данные из файла (для визуального контроля)
        $filename = 'otusLog.log';
        $content = file_get_contents($filename);
        if ($content !== false) {
            echo $content;
        } else {
            echo "Не удалось прочитать файл.";
        }
        ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
