<?php
include ("connect.php");
if($_POST['del'] != '') {  
    $del = $_POST['del'];
    $result = mysqli_query($mysql, "SELECT * FROM comments WHERE id='".$del."'") or die(mysqli_error($mysql));
    if(mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_array($result)) {
            mysqli_query($mysql, "DELETE FROM comments WHERE id='".$del."'") or die(mysqli_error($mysql));
            echo 0; //Успешно удалено
        }
    } else {
        echo 1; //Ошибка
    }
} else {
    header("HTTP/1.1 404 Not Found");
    exit;
}
?>