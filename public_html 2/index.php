<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
$showTimeline = False;
$user = "";
$profilepic = "";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $showTimeline = True;
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
    $profilepic = DB::query('SELECT profilepic FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['profilepic'];
} 
if (isset($_GET['postid']) && !isset($_GET['comment'])) {
    Post::likePost($_GET['postid'], $user_id, $_GET['type']);
}
if (isset($_POST['comment'])) {
    Comment::createComment($_POST['commentbody'], $_GET['postid'], $user_id, 'text');
}
if (isset($_GET['commented'])) {
    Comment::createComment($_POST['commentbody'], $_GET['postid'], $user_id, 'pic');
}
$followingposts = DB::query('SELECT posts.id, posts.body, posts.likes, usertable.username, posts.user_id FROM usertable, posts, followers
WHERE posts.user_id = followers.user_id 
AND usertable.id = posts.user_id 
AND follower_id = :user_id
ORDER BY posts.posted_at DESC', array(':user_id'=>$user_id));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWAP</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="trash.png">
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
                    <! ––<?php echo "/index.php?" . (String) rand(0,100000)?>-->
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
    <div class="div__left">
    </div>
    <?php
    if ($showTimeline == false) {
        echo '<center><h1>Not logged in</h1></center>';
        echo '<center></center><form method="GET" action="/login.php?">
            <center><button class="button__submit" style="border-bottom: none;" type="submit">Login</button></center>
        </form></center>';
    } else {
        $user_id = Login::isLoggedIn();
        $name = DB::query('SELECT first FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['first'];
        ?>
        <center><h1><?php

        if ($profilepic == NULL) {
            echo "<input class='profilepic' type='image' src='icons/profile.jpg' name='profile' value='profile'>";   
        } else {
            echo "<input class='profilepic' type='image' src='profilepic/".$profilepic."' name='profile' value='profile'>";
        }      
        echo " ".$name; ?>'s Timeline<?php if ($verified) {echo '*';} ?></h1></center>
        <?php 
        $imgposts = DB::query('SELECT imgtable.id, imgtable.img_url, imgtable.likes, imgtable.caption, usertable.username, imgtable.user_id FROM imgtable, usertable, followers 
        WHERE imgtable.user_id = followers.user_id
        AND usertable.id = imgtable.user_id
        AND follower_id = :user_id
        ORDER BY imgtable.id DESC', array(':user_id'=>$user_id));
        $p = "";
        foreach($imgposts as $p) {
            $imgurl = "img/".$p['img_url'];
            $pic = "";?>
            <div>
                <?php $pic .= "<img class='img' src='$imgurl'><br>";?>
            </div><?php
            $profilepic = DB::query('SELECT profilepic FROM usertable WHERE id=:user_id', array(':user_id'=>$p['user_id']))[0]['profilepic'];
            $pic .= "<form style='display: inline;' action='../profile.php?username=".$p['username']."' method='POST'>
            <input class='likeunlike' type='image' src='profilepic/".$profilepic."' name='profile' value='profile'></form>";
            if (!DB::query('SELECT post_id FROM img_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$p['id'], ':user_id'=>$user_id))) {
                $pic .= "<form style='display: inline;' action='index.php?type=img&postid=".$p['id']."' method='POST'>
                    <input class='likeunlike' type='image' src='icons/unlike.png' name='like' value='Like'></form>";
            } else {
                $pic .= "<form style='display: inline;' action='index.php?type=img&postid=".$p['id']."' method='POST'>
                    <input class='likeunlike' type='image' src='icons/like.png' name='unlike' value='Unlike'></form>";
            }
            $pic .= "<form style='display: inline;' action='index.php?comment=yes&postid=".$p['id']."' method='POST'>
            <input class='likeunlike' type='image' src='icons/comment.png' name='opencom' value='Comments'></form><br>";
            if ($p['likes'] == 1) {
                $pic .= $p['likes']." like";
            } else {
                $pic .= $p['likes']." likes";
            }
            $pic .= "</form><br>";
            $pic .= "<a href='/profile.php?username=".$p['username']."' style='text-decoration: none; color: #9c09d1'>@".$p['username']." </a>".$p['caption'];
            if (Login::isLoggedIn() && $_GET['comment'] == 'yes') {
                $pic .= "<center><form action='index.php?comment=yes&commented=yes&postid=".$p['id']."' method='POST'>
                    <textarea class='textarea__comment' name='commentbody' rows='2' cols='25'></textarea>
                    <input class='button__comment' type='submit' name='comment' value='Comment'>
                </form></div></div></center>";
                $comments = DB::query('SELECT comments.comment, usertable.`username`, comments.id FROM comments, usertable
                WHERE post_id = :postid
                AND comments.user_id = usertable.id
                ORDER BY comments.posted_at DESC', array(':postid'=>$p['id']));
                foreach($comments as $comment) {
                    $pic .= "<form action='index.php' method='POST'>
                    <center><div style='font-size: 14px;'>".$comment['comment'].' ~ '.$comment['username'].'</center>';
                    if ($user_id == $follower_id) {
                        $pic .= "<button class='button__trash' type='submit' name='deletecom' value='deletecom'><img src='icons/trash.png' alt='deletecom'></button></div></form><hr />";
                    } else {
                        $pic .= "</div></form><hr />";
                    }
                }
            }
            echo "<center><div class='text__post'>".$pic."</div><br></center>"; 
        }
        
        $p = "";
        foreach($followingposts as $post) {
            $p .= '<center><div class="text__post"><div class="padding"><a href="/profile.php?username='.$post["username"].'"style="text-decoration: none;"> @'.$post['username'].'</a> '.$post['body'].'<br>';
            $profilepic = DB::query('SELECT profilepic FROM usertable WHERE id=:user_id', array(':user_id'=>$post['user_id']))[0]['profilepic'];
            $p .= "<form style='display: inline;' action='../profile.php?username=".$post['username']."' method='POST'>
            <input class='likeunlike' type='image' src='profilepic/".$profilepic."' name='profile' value='profile'></form> ";
            $p .= "<form style='display: inline;' action='index.php?postid=".$post['id']."&type=text' method='POST'>";
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$post['id'], ':user_id'=>$user_id))) {            
                $p .= "<input class='likeunlike' type='image' src='icons/unlike.png' name='like' value='Like'> ";
            } else {
                $p .= "<input class='likeunlike' type='image' src='icons/like.png' name='unlike' value='Unlike'> ";
            }
            $p .= "</form><form style='display: inline;' action='index.php?comment=yes&postid=".$post['id']."' method='POST'>
            <input class='likeunlike' type='image' src='icons/comment.png' name='opencom' value='Comments'></form><br>";
            if ($post['likes'] == 1) {
                $p .= $post['likes']." like";
            } else {
                $p .= $post['likes']." likes";
            }
            
            if (Login::isLoggedIn() && $_GET['comment'] == 'yes') {
                $p .= "<center><form action='index.php?comment=yes&commented=yes&postid=".$post['id']."' method='POST'>
                    <textarea class='textarea__comment' name='commentbody' rows='2' cols='25'></textarea>
                    <input class='button__comment' type='submit' name='comment' value='Comment'>
                </form></div></div></center>";
                $p .= "<center>".Comment::displayComments($post['id'])."</center>";
            } else if (Login::isLoggedIn()) {
                $p .= "</div></div>";
            }
            $p .= "</br />";
            echo $p;
            $p = "";
        }
    } 
    ?>
    <center><form method="GET" action="/create-account.php?">
        <button class="button__submit" type="submit">Create Account</button>
    </form></center>
</body>
</html> 