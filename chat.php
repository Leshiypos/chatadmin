<?php 
/*
Plugin Name: ChatWP-dev
Description: Это тестовый плаген чата в WordPress
Author: WP_dev
Version: 1.0.0
Author URI: @wp_dev
*/

/*
* Подключение файлов
*/
require_once dirname(__FILE__)."/includes/config.php"; // Переменные и настройки
require_once dirname(__FILE__)."/includes/functions.php"; // Функции
require_once dirname(__FILE__)."/servises/ajax.php"; // Ajax запросы

// Запуск функции создания базы данных во время активации плагина
register_activation_hook( __FILE__, 'db_istall_chat'); 

//Подключение стилей и скриптов
add_action( 'admin_enqueue_scripts', 'chatadm_load_admin_styles');  // в бэкэнде
add_action( 'wp_enqueue_scripts', 'chatadm_load_frontend_styles'); //во фронте

add_action('wp_footer', 'display_chat_in_frontend'); // выводим окно чата во фронтэнд

function chatadm_load_admin_styles(){
    global $pluginchat_db_version; 
    wp_enqueue_style('chat-plugin-style-front', plugins_url( 'css/admin.css', __FILE__ ), array(), $pluginchat_db_version, "all" );
    wp_enqueue_script( 'charadm-script-main-adminpanel', plugins_url('js/frontend.js'), array('jquery'), $pluginchat_db_version, true );
}

function chatadm_load_frontend_styles(){
    global $pluginchat_db_version; 
    wp_enqueue_style('chat-plugin-style-front', plugins_url( 'css/chat-style.css', __FILE__ ), array(), $pluginchat_db_version, "all" );
    wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
	wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'charadm-script-main-frontend', plugins_url('chatadmin/js/frontend.js'), array('jquery'), $pluginchat_db_version, true );
}

/*
*   Регистрация меню в админку wordpress
*/
add_action('admin_menu', 'chatadmin_add_menu');
function chatadmin_add_menu(){
    add_menu_page( "Чат с адинистратором", 
    "ЧатАдмин", 
    "manage_options", 
    "chat_admin", 
    "chat_page_admin", 
    "dashicons-format-chat", 
    26 );
}

function chat_page_admin(){
    require_once dirname(__FILE__)."/adminpages/admin-chat.php";
}




















?>