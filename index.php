<?php require 'connect.php'; ?>

<html>
<head>
<meta charset="utf-8">
<title>Комментарии</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery-3.6.1.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<link rel="stylesheet" href="css/styles.css">
</head>

<body>

<div class="ImageRow">
        <div class="ImageHolder">
			<img src="images/image1.jpg"></img>
        </div>
 </div>
 
 <div id="RowComment" class="CommentRow">
        <div class="CommentForm">
			<form id="CommentsForm" action="validator.php" method="POST">
				<input v-model="Login" type="text" id="Name" name="UserName" class="Inputs" placeholder="Имя" required maxlength="25">
					<textarea v-model="Message" id="CommentArea" name="Comment" class="Inputs" placeholder="Комментарий" required maxlength="255"></textarea>
					<span class="sss" style="margin-bottom:0;">Введите код с картинки:</span>
					<br/>
	
					<div class="captcha__image-reload" id="app-2">
						<button type="button" id="capcharefresh" class="captcha__refresh" v-on:click="refresher">&#x21bb;</button>
						<button type="button" id="testbtn" class="captcha__refresh">?</button>
						<img v-bind:src="ImageCaptcha" class="CaptchaImg" id="captchapic" alt="captcha" width="132"></img>
					</div>
							
					<input class="CaptchaInput" type="text" name="captcha" id="captcha" placeholder="Капча" maxlength="6" />
					<div id="resp" class="invalid-feedback"style="text-align:center; margin-bottom:2px; color:red;"></div>
				<button type="button" class="Inputs submitbtn" v-on:click="Send">Отправить</button>
			</form>
        </div>
		
		<div id="CommentBlock" >

		</div>
 </div>
</body>

<script>

var app3 = new Vue({
        el: '#RowComment',
        data: {
		  Login:'',
		  Message:'',
		  ImageCaptcha: 'captcha.php'
        },
		mounted: function(){
			$.ajax({
					url: "loadcomments.php",
                    type: "get",
                    success: function (data) 
					{
                    $('#CommentBlock').html(data);
					}
				});
		},
        // пишем метод
        methods: {
//--------------------------------------------------------Проверка капчи         
		Send: function () {
		const form = document.querySelector('#CommentsForm');
		try {
		fetch(form.action, {
		method: form.method,
		credentials: 'same-origin',
		body: new FormData(form)
		})
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        document.querySelectorAll('input.is-invalid').forEach((input) => {
          input.classList.remove('is-invalid');
          input.nextElementSibling.textContent = '';
        });
        if (!data.success) {
          document.getElementById('capcharefresh').click();
          data.errors.forEach(error => {
            console.log(error);
            const input = form.querySelector(`[name="${error[0]}"]`);
            if (input) {
              input.classList.add('is-invalid');
              input.nextElementSibling.textContent = error[1];
            }
          })
        }
//-----------------------Добавление комментария при успешном прохождении капчи
		else {
			
			$.ajax({
				type:"post",
				url: "sendcomments.php",
				data: {author: this.Login, message: this.Message},
				success: function(response){
					var messageResp = new Array('Успешно добавлено', 'Ошибка базы данных');
					var resultStat = messageResp[Number(response)];
					if(response == 0) {
						$.ajax({
                        url: "loadcomments.php",
                        type: "post",
                        data: 'comment=all',
                        success: function (data) {
                            $('#CommentBlock').html(data);
							}
						});
					}
				}	
			});		
			document.getElementById('capcharefresh').click();
			this.Login = '';
			this.Message = '';
        }
      });
  } catch (error) {
    console.error('Ошибка:', error);
  }
		},
//---------------------------------------------Обновление капчи
		refresher: function(){
			this.ImageCaptcha = 'captcha.php?r='+ new Date().getUTCMilliseconds();
		},
		
		deleter: function(id){	
		var gettrid = id; 
		$.ajax({
        type:"post",
        url: "delete.php",
        data: { del: gettrid },
        success: function(response){
            var messageResp = new Array('Успешно удалено', 'Ошибка базы данных');
            var resultStat = messageResp[Number(response)];
            if(response == 0) {
				$.ajax({
                        url: "loadcomments.php",
                        type: "post",
                        data: 'comment=all',
                        success: function (data) {
                            $('#CommentBlock').html(data);
                        }
                });
            }
        }
		});
			
		}
		

    }
	
})
//-----------------------Удаление комментария
function deleteComment(id) {
	var gettrid = id; 
    jQuery.ajax({
        type:"post",
        url: "delete.php",
        data: { del: gettrid },
        success: function(response){
            var messageResp = new Array('Успешно удалено', 'Ошибка базы данных');
            var resultStat = messageResp[Number(response)];
            if(response == 0) {
				$.ajax({
                        url: "loadcomments.php",
                        type: "post",
                        data: 'comment=all',
                        success: function (data) {
                            $('#CommentBlock').show();
                            $('#CommentBlock').html(data);
                        }
                    });
            }
        }
    });
}

//-----------------------Запрет ввода спец символов и цифр
$('body').on('input', '#Name', function(){
	this.value = this.value.replace(/[^a-zа-яё\s]/gi, '');
});


</script>


</html>