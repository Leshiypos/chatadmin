<?php 
global $wpdb;
global $pluginchat_db_version;                       // Создадим глобальную переменную для обозначения версии базы данных
global $table_name;
global $cur_user_id;
global $frome_user_id;
 

add_action('init', 'current_user');               //Присваиваем id пользователям
function current_user(){
    global $cur_user_id;
    global $frome_user_id;

    $current_user = wp_get_current_user();        /* Определяем глобальную переменную  $current_user */

    $cur_user_id = $current_user->ID;
    $frome_user_id = ($cur_user_id == 1) ? 2 : 1; //Временные данные пользователя. ТУТ БУДЕТ ВСТАВЛЯТЬСЯ ID ПОЛУЧАТЕЛЯ
}

$pluginchat_db_version = "1.0";
$table_name = $wpdb -> prefix."chat";          //Формируем название таблицы, совместно с префиксом
?>