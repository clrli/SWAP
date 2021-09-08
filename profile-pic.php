<?php
include('./classes/DB.php');
include('./classes/Login.php');
$user_id = "";

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
} 
if (isset($_POST['postpic']) && isset($_FILES['file'])) {
    include "db_conn.php";
    $file = $_FILES['file'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) {
                $img_ex = pathinfo($fileName, PATHINFO_EXTENSION); 
                $img_ex_lc = strtolower($img_ex);
                if (in_array($img_ex_lc, $allowed)) {
                    $fileNameNew = uniqid('IMG-', true).".".$fileActualExt;
                    $fileDestination = 'profilepic/'.$fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);

                    DB::query('UPDATE usertable SET profilepic=:profilepic WHERE id=:user_id', array(':profilepic'=>$fileNameNew, ':user_id'=>$user_id));
                } else {
                    echo 'You cannot upload files of this type';
                    header("Location: ../profile.php?error=$em");
                }
            } else {
                echo 'Your file is too big';
                header("Location: ../profile.php?error=$em");
            }
        } else {
            echo 'There was an error uploading your file';
        }
    } else {
        echo 'You cannot upload files of this type';
    }
} 

?>


<form action='/profile-pic.php' method='POST' enctype='multipart/form-data'>
        <input type='file' name='file'><br>
        <button class='button__post' type='submit' name='postpic'>Upload</button>
</form>