<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
} 
if (isset($_POST['confirm'])) {
    if (isset($_POST['alldevices'])) {
        DB::query('DELETE FROM login_tokens WHERE user_id=:user_id', array(':user_id'=>Login::isLoggedIn()));
        header("Location: ../logout.php?logout=success");
    } else {
        if (isset($_COOKIE['SID'])){
            DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SID'])));
            header("Location: ../logout.php?logout=success");
        }
        setcookie('SID', '1', time()-3600);
        setcookie('SID_', '1', time()-3600);
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
    <div class="div__box" style="height: 235px;">
    <center><h1 style="color:#9c09d1;">Logout</h1>
    <p style="font-size: 18px;">Are you sure you'd like to logout?</p></center>
    <center><form action="logout.php" method="post">
        <p style="font-size: 18px;">Logout of all devices?
        <input type="checkbox" name="alldevices" value="alldevices"><br><br>
        <input class="button__submit" type="submit" name="confirm" value="Confirm">
    </form></center></div>
    <?php
        $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (strpos($fullUrl, "logout=success") == true) {
            echo "<center><p style='color: #9c09d1; font-size: 20px;'>Logged out!</p></center>";
        } 
    ?>
</body>
</html> 

