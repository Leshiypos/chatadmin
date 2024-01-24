<?php 
global $wpdb;
global $pluginchat_db_version;                       // Создадим глобальную переменную для обозначения версии базы данных
global $table_name;

$pluginchat_db_version = "1.0";
$table_name = $wpdb -> prefix."chat";          //Формируем название таблицы, совместно с префиксом
?>