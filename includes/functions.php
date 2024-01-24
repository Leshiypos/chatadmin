<?php 
/*
*   Функция создает базу данных, если ее нет
*/
function db_istall_chat(){
    global $wpdb; // получаем глабальную переменную
    global $pluginchat_db_version;
    global $table_name;

    $installed_ver = get_option( "pluginchat_db_version" );
    if ($wpdb -> get_var("SHOW TABLES LIKE '$table_name'") != $table_name){ //ПРоверяем существование таблицы

        $sql = "CREATE TABLE ".$table_name.                                  //Создание запроса
        "(
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            message_date DATETIME,
            message LONGTEXT NOT NULL,
            user_id BIGINT NOT NULL,
            fromeuser_id BIGINT NOT NULL,
            status CHAR(5) DEFAULT 'new' NOT NULL,
            UNIQUE KEY id (id)
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   //Подключение файла функции dbDelta
    dbDelta($sql);                                              // Отправка запроса в бузц данных
    
    $welcom_message = "Установка прошла успешно";

    $row_effected = $wpdb -> insert($table_name, array('message_date' => current_time('mysql'), 'message' => $welcom_message)); // вставка исходных данных
    
    add_option( "pluginchat_db_version", $pluginchat_db_version); // Добавим данные версии созданной базы данных в опции сайта
    }
    
    if ($installed_ver != $pluginchat_db_version){              //Проверяет, если версия базы данных изменилась, то вносит изменения
        $sql = "ALTER TABLE ".$table_name." ADD status CHAR(5) DEFAULT 'new' NOT NULL";
        $wpdb->query($sql);
        update_option( "pluginchat_db_version", $pluginchat_db_version ); // также обновляет версию базы данных
    }
} //КОНЕЦ функции db_istall_chat
?>