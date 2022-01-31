<?php
include('classes/DB.php');
include('./classes/Login.php');
$username = "";
$user = "";

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
} 
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (DB::query('SELECT username FROM usertable WHERE username=:username', array(':username'=>$username))) {
        echo 'in';
        if (password_verify($password, DB::query('SELECT password FROM usertable WHERE username=:username', array(':username'=>$username))[0]['password'])) {
            $cstrong = True;
            $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
            $user_id = DB::query('SELECT id FROM usertable WHERE username=:username', array(':username'=>$username))[0]['id'];
            DB::query('INSERT INTO login_tokens VALUES (0, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
            //setcookie('SID', NULL, -1, "/", "swap0nline.com", 0);
            //setcookie('SID_', NULL, -1, "/", "swap0nline.com", 0);
            
            setcookie("SID", $token, time() + 60*60*24*7, '/', "swap0nline.com", 0);
            setcookie("SID_", '1', time() + 60*60*24*3, '/', "swap0nline.com", 0);
            header("Location: ../login.php?signin=success");
        } else {
            header("Location: ../login.php?signin=invpass");
        }
    } else {
        header("Location: ../login.php?signin=invuser");
    }
}
?>

<link rel="stylesheet" href="styles.css">
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
                    <a href="/index.php" class="navbar__links">
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
<div class="div__box">
    <div class="container__small">
        <div class="center">
            <h1 style="color:#9c09d1;">LOGIN</h1>
        </div>
    </div>
    <div class="container__medium">
        <div class="center">
            <form action="login.php" method="post">
                <input class="input__login" style="border-bottom: none;" type="text" name="username" value="" placeholder="Username">
                <input class="input__login" type="password" name="password" value="" placeholder="Password"><p /><br>
                <input class="button__post" type="submit" name="login" value="Submit"><p />
            </form>
        </div>
    </div>
</div>
<div class="div__smallbox">
    <div class="container__buttons">
        <div class="center">
            <a href="/forgot-password.php" style="color: #9c09d1; text-decoration: none;">Forgot password?</a><br>
            <div style="padding: 5px;"></div>
            <a style="color: #9c09d1; text-decoration: none;" href="/create-account.php">Create Account</a>
        </div>
    </div>
</div>
<?php
    $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (strpos($fullUrl, "signin=invpass") == true) {
        echo "<center><p style='color: #9c09d1; font-size: 20px;'>Incorrect password</p></center>";
        exit();
    } else if (strpos($fullUrl, "signin=invuser") == true) {
        echo "<center><p style='color: #9c09d1; font-size: 20px;'>User not registered</p></center>";
        exit();
    } else if (strpos($fullUrl, "signin=success") == true) {
        echo "<center><p style='color: #9c09d1; font-size: 20px;'>Logged in!</p></center>";
    }
?>