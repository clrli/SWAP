<link rel="stylesheet" href="styles.css">
<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include "db_conn.php";
$username = "";
$verified = False;
$isFollowing = False;
$follower_id = Login::isLoggedIn();
$first = "";
$user = "";
$caption = "";
$iscaption = False;

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
} else {
    die('Not logged in');
}
if (isset($_GET['username'])) {
    if (DB::query('SELECT username FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        $user_id = DB::query('SELECT id FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
        $first = DB::query('SELECT first FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['first'];

        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id'=>$user_id, ':follower_id'=>$follower_id))) {
            $isFollowing = True;    
        }
        if (isset($_POST['deletepost'])) {
            if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$follower_id))) {
                DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$follower_id));   
                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
            }
        }
        if(isset($_POST['post'])) {
            Post::createPost($_POST['postbody'], Login::isLoggedIn(), $user_id);
        }
        if (Login::isLoggedIn() != False) {
            if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
                Post::likePost($_GET['postid'], $follower_id);
            }
        }
        $posts = Post::displayPosts($user_id, $username, $follower_id, $_GET['postid']);
    } 
}

if (isset($_POST['postpic']) && isset($_FILES['file'])) {
    $caption = $_POST['caption'];
    if ($follower_id == $user_id) {
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
                        $fileDestination = 'img/'.$fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        
                        DB::query('INSERT INTO imgtable VALUES (0, :user_id, :fileNameNew, :caption, :likes)', array(':user_id'=>$user_id, ':fileNameNew'=>$fileNameNew, ':caption'=>$caption, ':likes'=>'0'));
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
    } else {
        echo 'Not logged in';
    }
} 
?>

<a href="profile.php?username=<?=$username?>">&#8592;</a>

<?php
if ($follower_id == $user_id) {
echo "<form action='/create-post.php?username=".$username."' method='POST'>
        <textarea placeholder='Say something...' class='textarea__post' name='postbody' rows='6' cols='40'></textarea>
        <input class='button__post' type='submit' name='post' value='Post'>
</form> <br>
<form action='/create-post.php?username=".$username."' method='POST' enctype='multipart/form-data'>
        <input type='file' name='file'><br>
        <textarea placeholder='Add caption' class='textarea__post' name='caption' rows='6' cols='40'></textarea>
        <button class='button__post' type='submit' name='postpic'>Upload</button>
</form> <br>";
}
?>