<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
include "db_conn.php";
$username = "";
$verified = False;
$isFollowing = False;
$follower_id = Login::isLoggedIn();
$first = "";
$user = "";
$profilepic = "";

if (Login::isLoggedIn()) {
    $user_id = Login::isLoggedIn();
    $user = DB::query('SELECT username FROM usertable WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
}
if (isset($_GET['username'])) {
    $profilepic = DB::query('SELECT profilepic FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['profilepic'];
    if (DB::query('SELECT username FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        $user_id = DB::query('SELECT id FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
        $first = DB::query('SELECT first FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['first'];

        if (isset($_POST['follow'])) {
            if ($follower_id) {
                if ($user_id != $follower_id) { 
                    if ($follower_id) {
                        if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id'=>$user_id, ':follower_id'=>$follower_id))) {
                            if ($follower_id == 37) {
                                DB::query('UPDATE usertable SET verified=1 WHERE id=:user_id', array(':user_id'=>$user_id));
                            } else {
                                DB::query('UPDATE usertable SET verified=0 WHERE id=:user_id', array(':user_id'=>$user_id));
                            }
                            DB::query('INSERT INTO followers VALUES(0, :user_id, :follower_id)', array(':user_id'=>$user_id, ':follower_id'=>$follower_id));
                        } else {
                            echo 'Already following';
                        }
                        $isFollowing = True;
                    } else {
                        echo 'Not logged in';
                    }
                }
            } else {
                echo 'Not logged in';
            }
        }
        if (isset($_POST['unfollow'])) {
            if ($user_id != $follower_id) {
                if ($follower_id) {
                    if (DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id'=>$user_id, ':follower_id'=>$follower_id))) {
                        if ($follower_id == 21) {
                            DB::query('UPDATE usertable SET verified=0 WHERE id=:user_id', array(':user_id'=>$user_id));
                        }
                        DB::query('DELETE FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id'=>$user_id, ':follower_id'=>$follower_id));
                    } 
                    $isFollowing = False;
                } else {
                    echo 'Not logged in';
                }
            }
        }
        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id'=>$user_id, ':follower_id'=>$follower_id))) {
            $isFollowing = True;    
        }
        if(isset($_POST['post'])) {
            Post::createPost($_POST['postbody'], Login::isLoggedIn(), $user_id);
        }
        if (Login::isLoggedIn() != False) {
            if (isset($_POST['deletepost'])) {
                if ($_GET['type'] == 'img'){
                    if (DB::query('SELECT id FROM imgtable WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$follower_id))) {
                        DB::query('DELETE FROM imgtable WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$follower_id));   
                        DB::query('DELETE FROM img_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                    }
                } else if ($_GET['type'] == 'text') {
                    if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$follower_id))) {
                        DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$follower_id));   
                        DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                        DB::query('DELETE FROM comments WHERE post_id=:postid', array(':postid'=>$_GET['postid'])); 
                    }
                }
            } else if (isset($_GET['postid']) && isset($_GET['delete']) != 'com' && $_GET['comment'] != 'yes') {
                Post::likePost($_GET['postid'], $follower_id, $_GET['type']);
            } else if (isset($_GET['postid']) && isset($_GET['delete']) == 'com') {
                if (DB::query('SELECT id FROM comments WHERE id=:comid', array(':comid'=>$_GET['comid']))) {
                    DB::query('DELETE FROM comments WHERE id=:comid', array(':comid'=>$_GET['comid']));   
                }
            }
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
    </nav><center>
    <h1><?php 
    if ($profilepic == NULL && Login::isLoggedIn()) {
        echo "<input class='profilepic' type='image' src='icons/profile.jpg' name='profile' value='profile'>";   
    } else if (Login::isLoggedIn()) {
        echo "<input class='profilepic' type='image' src='profilepic/".$profilepic."' name='profile' value='profile'>";
    }    
    if ($first != Null) {
        echo " ".$first; ?>'s Profile<?php if ($verified==1) {echo '*';} ?></h1><?php
    } else {
        echo "Not Logged In</h1>";
    }
    if ($follower_id == $user_id && Login::isLoggedIn()) {
        echo '<form method="POST" action="/create-post.php?username='.$username.'">
            <button class="button__submit" type="submit">Post</button>
        </form>
        <form method="GET" action="/profile-pic.php?username='.$username.'">
            <button class="button__submit" type="submit">Change Profile Picture</button>
        </form>';
    }?>
    <form action="profile.php?username=<?php echo $username; ?>" method="POST">
        <?php
        if (Login::isLoggedIn()) {
            if ($user_id != $follower_id) {
                if ($isFollowing) {
                    echo '<input class="button__post" type="submit" name="unfollow" value="Unfollow">';
                } else {
                    echo '<input class="button__post" type="submit" name="follow" value="Follow">';
                }
            }
        }
        ?>
    </center></form>
</body>
</html> 

<?php 
$user_id = DB::query('SELECT id FROM usertable WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
$imgposts = DB::query('SELECT * FROM imgtable WHERE user_id=:user_id ORDER BY id DESC', array(':user_id'=>$user_id));
$p = "";
foreach($imgposts as $p) {
    $imgurl = "img/".$p['img_url'];
    $pic = "";?>
    <div>
        <?php $pic .= "<img class='img' src='$imgurl'>";?>
    </div><?php
    if (!DB::query('SELECT post_id FROM img_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$p['id'], ':user_id'=>$follower_id)) && Login::isLoggedIn()) {
        $pic .= "<form action='profile.php?username=$username&postid=".$p['id']."&type=img' method='POST'>
            <input class='likeunlike' type='image' src='icons/unlike.png' name='like' value='Like'>";
    } else if (Login::isLoggedIn()) {
        $pic .= "<form action='profile.php?username=$username&postid=".$p['id']."&type=img' method='POST'>
            <input class='likeunlike' type='image' src='icons/like.png' name='unlike' value='Unlike'>";
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
    if ($user_id == $follower_id) {
        $pic .= "<button class='button__trash' type='submit' name='deletepost' value='deletepost'><img src='icons/trash.png' alt='delete'></button>";
    }
    $pic .= "</form>";
    $pic .= $p['caption'];
    $pic .= "<center><form action='profile.php?username=$username&comment=yes&postid=".$p['id']."' method='POST'>
    <input class='button__comment' type='submit' name='opencom' value='Comments'></form><hr></center>";
    if ($user_id == $follower_id && $_GET['comment'] == 'yes') {
        $pic .= "<center><form action='profile.php?username=$username&postid=".$p['id']."&comment=yes&type=pic' method='POST'>
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
            $pic .= "<center><form action='profile.php?username=$username&postid=".$p['id']."&comid=".$comment['id']."&delete=com&comment=yes&type=pic' method='POST'>
            <div style='font-size: 14px;'>".$comment['comment'].' ~ '.$comment['username'];
            if ($user_id == $follower_id) {
                $pic .= "<button class='button__trash' type='submit' name='deletecom' value='deletecom'><img src='icons/trash.png' alt='deletecom'></button></div></form><hr />";
            } else {
                $pic .= "</div></form><hr /></center>";
            }
        }
    }
    echo "<center><div class='text__post'>".$pic."</div><br></center>";
}   
?>
<div class="posts">
    <?php echo "<center>".Post::displayPosts($user_id, $username, $follower_id, $_GET['postid'])."</center>"; ?>
</div>