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
/*
* Функция вывода окна чата на страницу во ФРОНТЭНД
*/

function display_chat_in_frontend(){
    global $wpdb;
    global $table_name;
    global $cur_user_id;
    global $frome_user_id; 
?>
<div class="wrap-wp-chatadmin" style="margin-left: 150px">
	<div class="message-windows-chat-admin"  id="chat">
<?php 
	$messages = $wpdb -> get_results(
	"SELECT ch.id,ch.message_date, ch.message, ch.status,ch.user_id, un.display_name 
	FROM $table_name ch
	INNER JOIN $wpdb->users un ON (ch.user_id=un.ID)
	WHERE (ch.user_id = '$cur_user_id' AND ch.fromeuser_id = '$frome_user_id') OR (ch.user_id = '$frome_user_id' AND ch.fromeuser_id = '$cur_user_id')
	ORDER BY ch.id",
	ARRAY_A);
	
	foreach ($messages as $mes){ ?>
	<div class="block-message <?php if ($mes['user_id'] == $cur_user_id ) {echo "sender";} else {echo "recipient";}?> ">
		<div class="name"> <?php echo $mes['display_name']; ?> </div>
		<div class="message" data-id="<?php echo $mes['id']; ?>"> <?php echo $mes['message']; ?> </div>

		<?php if (($mes['user_id'] != $cur_user_id) && ($mes['status'] == "new")){?>
		<div class="status-mes"> <?php echo $mes['status']; ?> </div>
	<?php } ?>
	</div>
<?php	
	}
?>

	</div>
	<form id="form-chat-admin" action="" method="POST">
		<input type="text" name="massage" required="required" autofocus id="message_win">
		<input type="submit" name='send_message' value="отправить" id="send_message" data-user-id="<?php echo $cur_user_id;?>">

	</form>
</div>
<?php 
echo 'Текущий пользователь '.$cur_user_id.'<br>';
echo 'Для кого '.$frome_user_id;
?>


<script type="text/javascript">                     //Прокрутка чата вниз
  var block = document.getElementById("chat");
  block.scrollTop = block.scrollHeight;
</script>
<?php 
};
?>