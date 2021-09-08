<?php
include('./classes/Login.php');
include('./classes/DB.php');
include('./classes/Post.php');
include('./classes/Comment.php');
$img_url = "";
$user = "";
$follower_id = Login::isLoggedIn();

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
}
if (isset($_GET['username'])) {
    if (Login::isLoggedIn() != False) {
        if (isset($_GET['postid']) && $_GET['comment'] != 'yes') {
            Post::likePost($_GET['postid'], $follower_id, 'img');
        } 
        if (isset($_POST['comment'])) {
            Comment::createComment($_POST['commentbody'], $_GET['postid'], $user_id, $_GET['type']);
        }        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Art</title>
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
</body>
</html>

<?php
$user_id = DB::query('SELECT id FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
$imgposts = DB::query('SELECT imgtable.id, imgtable.user_id, imgtable.img_url, imgtable.caption, imgtable.likes, usertable.username FROM imgtable, usertable
WHERE imgtable.user_id=usertable.id ORDER BY likes DESC', array(':user_id'=>$user_id));
$p = "";
foreach($imgposts as $p) {
    $pic = "";
    $imgurl = "img/".$p['img_url'];?>
    <div>
        <?php $pic .= "<img class='img' src='$imgurl'>";
if (!DB::query('SELECT post_id FROM img_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$p['id'], ':user_id'=>$follower_id)) && Login::isLoggedIn()) {
    $pic .= "<form action='art.php?username=".$p['username']."&postid=".$p['id']."&type=img' method='POST'>
        <input class='likeunlike' type='image' src='icons/unlike.png' name='like' value='Like'>";
} else if (Login::isLoggedIn()) {
    $pic .= "<form action='art.php?username=".$p['username']."&postid=".$p['id']."&type=img' method='POST'>
        <input class='likeunlike' type='image' src='icons/like.png' name='unlike' value='Unlike' onclick='showModalPopupUpdate();return false;'>";
} else {
    $pic .= "<br>";
}
if ($p['likes'] == 1) {
    $pic .= $p['likes']." like";
} else {
    $pic .= $p['likes']." likes";
}
if (!Login::isLoggedIn()) {
    $pic .= "<br>";
}
$pic .= "</form>";

$pic .= "<a href='/profile.php?username=".$p['username']."' style='text-decoration: none; color: #9c09d1'>@".$p['username']."</a>
        ".$p['caption'];
if (Login::isLoggedIn()) {
    $pic .= "<center><form action='art.php?username=$username&comment=yes&postid=".$p['id']."' method='POST'>
    <input class='button__comment' type='submit' name='opencom' value='Comments'></form><hr>";    
}
if (Login::isLoggedIn() && $_GET['comment'] == 'yes') {
    $pic .= "<form action='art.php?username=$username&postid=".$p['id']."&comment=yes&type=pic' method='POST'>
        <textarea class='textarea__comment' name='commentbody' rows='2' cols='25'></textarea>
        <input class='button__comment' type='submit' name='comment' value='Comment'>
    </form></div></div></center>";
} 
if ($_GET['comment'] == 'yes') {
    $comments = DB::query('SELECT comments.comment, usertable.`username`, comments.id FROM comments, usertable
    WHERE post_id = :postid
    AND comments.user_id = usertable.id
    ORDER BY comments.posted_at DESC', array(':postid'=>$p['id']));
    foreach($comments as $comment) {
        $pic .= "<center><form action='art.php?username=$username&postid=".$p['id']."&comid=".$comment['id']."&delete=com&comment=yes&type=pic' method='POST'>
        <div style='font-size: 14px;'>".$comment['comment'].' ~ '.$comment['username'];
        if ($user_id == $follower_id) {
            $pic .= "<button class='button__trash' type='submit' name='deletecom' value='deletecom'><img src='icons/trash.png' alt='deletecom'></button></div></form><hr />";
        } else {
            $pic .= "</div></form><hr /></center>";
        }
    }
} ?>
    </div>
    <?php echo "<center><div class='text__post'>".$pic."</div><br></center>";
}   
?>


