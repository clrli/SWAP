<?php
class Post {
    public static function createPost($postbody, $loggedInUserId, $profileUserId) {
        if (strlen($postbody) > 300 || strlen($postbody) < 1) {
            die('Incorrect length');
        }
        if ($loggedInUserId == $profileUserId) {
            DB::query('INSERT INTO posts VALUES (0, :postbody, NOW(), :user_id, 0)', array(':postbody'=>$postbody, ':user_id'=>$profileUserId));
        } else {
            die('Incorrect user');
        }
    }

    public static function likePost($postid, $likerId, $imgOrText) {
        if ($imgOrText == 'text'){
            if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$postid, ':user_id'=>$likerId))) {
                DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postid));
                DB::query('INSERT INTO post_likes VALUES (0, :postid, :user_id)', array(':postid'=>$postid, ':user_id'=>$likerId));
            } else {
                DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postid));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$postid, ':user_id'=>$likerId));
            }
        } else if ($imgOrText == 'img') {
            if (!DB::query('SELECT user_id FROM img_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$postid, ':user_id'=>$likerId))) {
                DB::query('UPDATE imgtable SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postid));
                DB::query('INSERT INTO img_likes VALUES (0, :postid, :user_id)', array(':postid'=>$postid, ':user_id'=>$likerId));
            } else {
                DB::query('UPDATE imgtable SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postid));
                DB::query('DELETE FROM img_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$postid, ':user_id'=>$likerId));
            }
        }
    }

    public static function displayPosts($user_id, $username, $loggedInUserId, $postId) {
        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:user_id ORDER BY id DESC', array(':user_id'=>$user_id));
        $posts = "";
        foreach($dbposts as $p) {
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:user_id', array(':postid'=>$p['id'], ':user_id'=>$loggedInUserId))) {            
                $posts .= "<div class='padding'><div class='text__post'>";
                $posts .= htmlspecialchars($p['body']);
                if (Login::isLoggedIn()) {
                    $posts .= "<form action='profile.php?username=$username&postid=".$p['id']."&type=text' method='POST'>
                        <input class='likeunlike' type='image' src='icons/unlike.png' name='like' input='hidden'><input type='hidden' name='like' value='' input='hidden'>
                        <span>";
                } else {
                    $posts .= "<br>";
                }
            } else {
                $posts .= "<div class='padding'><div class='text__post'>";
                $posts .= htmlspecialchars($p['body']);
                if (Login::isLoggedIn()) {
                    $posts .= "<form action='profile.php?username=$username&postid=".$p['id']."&type=text' method='POST'>
                        <input class='likeunlike' type='image' src='icons/like.png' name='unlike' input='hidden'><input type='hidden' name='unlike' value='' input='hidden'>
                        <span>";
                } else {
                    $posts .= "<br>";
                }
            }
            if ($p['likes'] == 1) {
                $posts .= $p['likes']." like</span>";
            } else {
                $posts .= $p['likes']." likes</span>";
            }
            if ($user_id == $loggedInUserId) {
                $posts .= "
                <button class='button__trash' type='submit' name='deletepost' value='deletepost'><img src='icons/trash.png' alt='delete'></button>";
            }
            $posts .= "</form></div></div>";
            $posts .= "<form action='profile.php?username=$username&comment=yes&postid=".$p['id']."&type=text' method='POST'>
                <input class='button__comment' type='submit' name='opencom' value='Comments'></form><hr>";
            if ($_GET['comment'] == 'yes') {
                if ($user_id == $loggedInUserId) {
                    $posts .= "<form action='profile.php?username=$username&postid=".$p['id']."&comment=yes&type=text' method='POST'>
                        <textarea class='textarea__comment' name='commentbody' rows='2' cols='25'></textarea>
                        <input class='button__comment' type='submit' name='comment' value='Comment'>
                    </form></div></div>";
                }
                $comments = DB::query('SELECT comments.comment, usertable.`username`, comments.id FROM comments, usertable
                WHERE post_id = :postid
                AND comments.user_id = usertable.id
                ORDER BY comments.posted_at DESC', array(':postid'=>$p['id']));
                foreach($comments as $comment) {
                    $posts .= "<center><form action='profile.php?username=$username&postid=".$p['id']."&comid=".$comment['id']."&delete=com&comment=yes&type=text' method='POST'>
                    <div style='font-size: 14px;'>".$comment['comment'].' ~ '.$comment['username'];
                    if ($user_id == $loggedInUserId) {
                        $posts .= "<button class='button__trash' type='submit' name='deletecom' value='deletecom'><img src='icons/trash.png' alt='deletecom'></button></div></form><hr />";
                    } else {
                        $posts .= "</div></form></center><hr />";
                    }
                }
            }                       
        }    
        return $posts; 
    }
}
?>