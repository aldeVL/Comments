<?php require 'connect.php'; ?>

<html>
<head>
<meta charset="utf-8">
<title>Комментарии</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery-3.6.1.js"></script>
<link rel="stylesheet" href="css/styles.css">
</head>

<body>

<div class="ImageRow">
        <div class="ImageHolder">
			<img src="images/image1.jpg"></img>
        </div>
 </div>
 
 <div class="CommentRow">
        <div class="CommentForm">
			<form id="CommentsForm" action="validator.php" method="POST">
				<input type="text" id="Name" name="UserName" class="Inputs" placeholder="Имя" required maxlength="25">
					<textarea id="CommentArea" name="Comment" class="Inputs" placeholder="Комментарий" required maxlength="255"></textarea>
					<span class="sss" style="margin-bottom:0;">Введите код с картинки:</span>
					<br/>
					<div class="captcha__image-reload">
						<button type="button" id="capcharefresh" class="captcha__refresh">&#x21bb;</button>
						<img class="CaptchaImg" src = "captcha.php" alt="captcha" width="132"></img>
					</div>
					<input class="CaptchaInput" type="text" name="captcha" id="captcha" placeholder="Капча" maxlength="6" />
					<div id="resp" class="invalid-feedback"style="text-align:center; margin-bottom:2px; color:red;"></div>
				<button type="submit" class="Inputs submitbtn">Отправить</button>
			</form>
        </div>
		
		<div id="CommentBlock" class="CommentList">
			<?php	
			$result = $mysql->query("SELECT id, author, message, DATE_FORMAT(date, '%d.%m.%Y - %H:%i') as date FROM comments ORDER BY date DESC");
            if(mysqli_num_rows($result) != 0) {
			$comment = $result->fetch_assoc();
            do{echo "<div class='comment' style='border: 1px solid gray; margin-top: 1%; border-radius: 5px; padding: 0.5%;'>Автор: <strong>".$comment['author']."</strong><sup><small>".$comment['date']."</small></sup><br>".$comment['message']."<span style='display: inline-block; float: right;' class='delbtnspan'><input style='' class='delbtn' type=\"submit\" value=\"Удалить\" onclick=\"deleteComment($comment[id]);\" /></span></div>";
          }while($comment = $result->fetch_assoc());
			}
			else {echo 'нет комментов';} 
          ?>
		</div>
 </div>
</body>
<script>
//-----------------------Обновление капчи
const refreshCaptcha = (target) => {
  const captchaImage = target.closest('.captcha__image-reload').querySelector('.CaptchaImg');
  captchaImage.src = 'captcha.php?r=' + new Date().getUTCMilliseconds();
}
const captchaBtn = document.querySelector('.captcha__refresh');
captchaBtn.addEventListener('click', (e) => refreshCaptcha(e.target));

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

//-----------------------Проверка капчи
const form = document.querySelector('#CommentsForm');
form.addEventListener('submit', (e) => {
  e.preventDefault();
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
          refreshCaptcha(form.querySelector('.captcha__refresh'));
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
			var author = document.getElementById("Name").value; 
			var message = document.getElementById("CommentArea").value;
			jQuery.ajax({
				type:"post",
				url: "sendcomments.php",
				data: {author: author, message: message},
				success: function(response){
					var messageResp = new Array('Успешно добавлено', 'Ошибка базы данных');
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
			document.getElementById('capcharefresh').click();
			form.reset();
        }
      });
  } catch (error) {
    console.error('Ошибка:', error);
  }
});
//-----------------------Запрет ввода спец символов и цифр
$('body').on('input', '#Name', function(){
	this.value = this.value.replace(/[^a-zа-яё\s]/gi, '');
});

</script>


</html>