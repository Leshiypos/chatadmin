<?php
global $wpdb;
global $table_name;
$cur_user_id = get_current_user_id();
$frome_user_id = ($cur_user_id == 1) ? 2 : 1;  //Временные данные пользователя. ТУТ БУДЕТ ВСТАВЛЯТЬСЯ ID ПОЛУЧАТЕЛЯ

//ОТПРАВКА ДАННЫХ СООБЩЕНИЯ В БАЗУ ДАННЫХ
if (isset($_POST['massage'])){						
	$mess =  sanitize_text_field($_POST['massage']);
	$wpdb->insert($table_name, array(					//вставляем данные сообщения в базу данных
		'message_date' 	=> current_time('mysql'), 
		'message' 		=> $mess,
		'user_id'		=> $cur_user_id,
		'fromeuser_id' 	=> $frome_user_id
		 ));

	$wpdb->update($table_name, 
			['status'=>'read'], 
			['fromeuser_id' => $cur_user_id, 'status' => 'new']); //обновляем статус сообщения для получателя при отправке
		 
				//Запрет повторной отправки формы после обновления страницы
}
//КОНЕЦ ОТПРАВКА ДАННЫХ СООБЩЕНИЯ В БАЗУ ДАННЫХ
?>

<div style="margin-left: 150px">
	<div class="message-windows-chat-admin"  id="chat">
<?php 
	$messages = $wpdb -> get_results(
	"SELECT ch.message_date, ch.message, ch.status,ch.user_id, un.display_name 
	FROM $table_name ch
	INNER JOIN $wpdb->users un ON (ch.user_id=un.ID)
	WHERE (ch.user_id = '$cur_user_id' AND ch.fromeuser_id = '$frome_user_id') OR (ch.user_id = '$frome_user_id' AND ch.fromeuser_id = '$cur_user_id')",
	ARRAY_A);
	
	foreach ($messages as $mes){ ?>
	<div class="block-message <?php if ($mes['user_id'] == $cur_user_id ) {echo "sender";} else {echo "recipient";}?> ">
		<div class="name"> <?php echo $mes['display_name']; ?> </div>
		<div class="message"> <?php echo $mes['message']; ?> </div>

		<?php if (($mes['user_id'] != $cur_user_id) && ($mes['status'] == "new")){?>
		<div class="status-mes"> <?php echo $mes['status']; ?> </div>
	<?php } ?>
	</div>
<?php	
	}
?>

	</div>
	<form id="form-chat-admin" action="" method="POST">
		<input type="text" name="massage" required="required" autofocus>
		<input type="submit" value="отправить">

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