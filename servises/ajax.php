<?php 
//Определяем переменную для адреса к ajax обработчику
add_action( 'wp_enqueue_scripts', 'myajax_data', 99 );

function myajax_data(){
	wp_localize_script( 'charadm-script-main-frontend', 'myajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);
}

/*
* СКРИПТ JS Отправки сообщения
*/

//Подключаем скрипт для отправки сообщения
add_action( 'wp_footer', 'ajax_send_message_javascript', 99 );

function ajax_send_message_javascript(){ ?>
<script>
    $(document).ready(function(){
    
        $('#form-chat-admin').submit(function(evt){
            evt.preventDefault();
            var message = $('#message_win').val();
            var currentUserName = $('#send_message').attr('data-username');
            var lastMessId = $('#chat .block-message:last-child .message'). attr('data-id');
            console.log(currentUserName);
            var fomatData = {
                action : 'message_send',
                message : message,
                lastMessId : lastMessId
            }
            $.ajax({                                                    //Делаем запрос ajax
                url : myajax.url, // обработчик
                data : fomatData, // данные
                type : 'POST', // тип запроса
                success : function( data ){
                    data.forEach(function(val){
                        var classMess = (val.display_name == currentUserName) ? 'sender' : 'recipient'; 
                        console.log(val.display_name);
                        var status = val.status == 'new' ? '<div class="status-mes"> new </div>' : '' ;
                        var autput = '<div class="block-message  '+classMess+'">';

                        autput += '<div class="name">'+val.display_name+'</div>';
                        autput += '<div class="message" data-id="'+val.id+'">'+val.message+'</div>';
                        autput += '</div>';
                        autput += status;
                        $('#chat').append(autput);
                    });
                        $('#message_win').val('');

                        var block = document.getElementById("chat");    //прокручиваем область сообщений вниз каждый раз при добавлении нового 
                        block.scrollTop = block.scrollHeight;
                        }
                });
        })//Конец Click
    }) //Конец READY
</script>
<?php }
/*
* КОНЕЦ скрипта JS Отправки сообщения
*/



/*
* Отправка сообщения
*/
if( wp_doing_ajax() ){                                               //Подключаем AJAX только когда в этом есть смысл
    add_action( 'wp_ajax_message_send', 'ajax_send_message' );
    add_action( 'wp_ajax_nopriv_message_send', 'ajax_send_message' );
}

function ajax_send_message(){
    global $wpdb;
    global $table_name;
    global $cur_user_id;
    global $frome_user_id;

    $mess = sanitize_text_field($_POST['message']);
    $last_mess_id = $_POST['lastMessId'];

    $wpdb->insert($table_name, array(					            //вставляем данные сообщения в базу данных
		'message_date' 	=> current_time('mysql'), 
		'message' 		=> $mess,
		'user_id'		=> $cur_user_id,
		'fromeuser_id' 	=> $frome_user_id
		 ));

    $mess_id = $wpdb->insert_id;

	$wpdb->update($table_name, 
			['status'=>'read'], 
			['fromeuser_id' => $cur_user_id, 'status' => 'new']);   //обновляем статус сообщения для получателя при отправке
    
    $mess = $wpdb -> get_results(
        "SELECT ch.id,ch.message_date, ch.message, ch.status,ch.user_id, un.display_name
        FROM $table_name ch
        INNER JOIN $wpdb->users un ON (ch.user_id=un.ID)
        WHERE ((ch.user_id = '$cur_user_id' AND ch.fromeuser_id = '$frome_user_id') OR (ch.user_id = '$frome_user_id' AND ch.fromeuser_id = '$cur_user_id')) AND ch.id>$last_mess_id",
        ARRAY_A);

        wp_send_json($mess);
 }
/*
* КОНЕЦ отправки сообщения
*/

?>