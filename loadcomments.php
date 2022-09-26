<?php

session_start();
include("connect.php");
	$result = mysqli_query($mysql, "SELECT id, author, message, DATE_FORMAT(date, '%d.%m.%Y - %H:%i') as date FROM comments ORDER BY date DESC");
		if ($result) {
			$html = '';
			while ($array = mysqli_fetch_assoc($result)) {
				$html .= "<div class='comment' style='border: 1px solid gray; margin-top: 1%; border-radius: 5px; padding: 0.5%;'>Автор: <strong>".$array['author']."</strong><sup><small>".$array['date']."</small></sup><br>".$array['message']."<span style='float:right;' class='delbtnspan'><input class='delbtn' type=\"submit\" value=\"Удалить\" onclick=\"deleteComment($array[id]);\" /></span></div>";
                }
			if (!empty($html)) {
				echo $html;
			} else {
                    echo 'нет комментов';
				}
        }
?>