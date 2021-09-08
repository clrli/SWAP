<?php
include('./classes/DB.php');
include('./classes/Login.php');
$tokenIsValid = False;

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
} 
if (Login::isLoggedIn()) {
    if (isset($_POST['changepassword'])){
        $oldpassword = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];
        $verifypassword = $_POST['verifypassword'];
        $user_id = Login::isLoggedIn();

        if (password_verify($oldpassword, DB::query('SELECT password FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['password'])) {
            if ($newpassword == $verifypassword) {
                if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                    DB::query('UPDATE usertable SET password=:newpassword WHERE id=:user_id', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':user_id'=>$user_id));
                    header("Location: ../change-password.php?signin=success");
                } else {
                    header("Location: ../change-password.php?signin=invpass");
                }
            } else {
                header("Location: ../change-password.php?signin=notmatch");
            }
        } else {
            header("Location: ../change-password.php?signin=oldincorrect");
        }
    }
} else {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        if (DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id']) {
            $user_id = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
            $tokenIsValid = True;
            if (isset($_POST['changepassword'])){
                $newpassword = $_POST['newpassword'];
                $verifypassword = $_POST['verifypassword'];
        
                if ($newpassword == $verifypassword) {
                    if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                        header("Location: ../change-password.php?signin=success");
                        DB::query('UPDATE usertable SET password=:newpassword WHERE id=:user_id', 
                        array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':user_id'=>$user_id));
                        DB::query('DELETE FROM password_tokens WHERE user_id=:user_id', array(':user_id'=>$user_id));
                    } 
                } else {
                    header("Location: ../change-password.php?signin=notmatch");
                }
            } 
        } else {
            die('Token invalid');
        }
    } else {
        die('Not logged in');
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWAP</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar__container">
            <a href="/" id="navbar__logo">SWAP</a>
            <div class="navbar__toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
            <ul class="navbar__menu">
                <li class="navbar__item">
                    <a href="/" class="navbar__links">
                    Home
                    </a>
                </li>
                <li class="navbar__item">
                    <a href="/art.php" class="navbar__links">
                    Art
                    </a>
                </li>
                <li class="navbar__item">
                    <a href="/essays.html" class="navbar__links">
                    Essays
                    </a>
                </li>
                <li class="navbar__item">
                    <a href="/search.php" class="navbar__links">
                    Community
                    </a> 
                </li>                
                <li class="navbar__item">
                    <a href="/profile.php?username=<?=$user?>" class="navbar__links">
                    Profile
                    </a>
                </li>                
                <li class="navbar__btn">
                    <a href="/account.php" class="button">
                    Account
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="div__box" style="height: 300px;"><center><h1 style="color:#9c09d1;">Change Password</h1></center>
    <center><form action="<?php if (!$tokenIsValid) {echo 'change-password.php';} else {echo 'change-password.php?token='.$token.'';} ?>" method="POST">
        <p>
            <?php if (!$tokenIsValid) {echo '<input class="input__login" style="border-bottom: none;" type="password" name="oldpassword" placeholder="Current Password" value=""><br>';} ?>
            <input class="input__login" style="border-bottom: none;" type="password" name="newpassword" placeholder="New Password" value=""><br>
            <input class="input__login" type="password" name="verifypassword" placeholder="Verify Password" value=""><br><br>
            <input class="button__submit" type="submit" name="changepassword" value="Change Password" value="">
        </p>
    </form></center></div>
    <?php
        $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (strpos($fullUrl, "signin=notmatch") == true) {
            echo "<center><p style='color: #9c09d1; font-size: 20px;'>Passwords do not match</p></center>";
            exit();
        } else if (strpos($fullUrl, "signin=oldincorrect") == true) {
            echo "<center><p style='color: #9c09d1; font-size: 20px;'>Incorrect old password</p></center>";
            exit();
        } else if (strpos($fullUrl, "signin=success") == true) {
            echo "<center><p style='color: #9c09d1; font-size: 20px;'>Password changed successfully!</p></center>";
            exit();
        } else if (strpos($fullUrl, "signin=invpass") == true) {
            echo "<center><p style='color: #9c09d1; font-size: 20px;'>Invalid password</p></center>";
        }
    ?>
</body>
</html> 
