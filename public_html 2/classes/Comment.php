<?php
class Comment {
    public static function createComment($commentBody, $postId, $userId, $imgOrText) {
        if (strlen($commentBody) > 300 || strlen($commentBody) < 1) {
            die('Incorrect length');
        }

        if(!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
            if (!DB::query('SELECT id FROM imgtable WHERE id=:postid', array(':postid'=>$postId))) {
                echo 'Invalid ID';
            } else if ($imgOrText == 'pic') {
                DB::query('INSERT INTO comments VALUES(0, :comment, :user_id, NOW(), :postid)', array(':comment'=>$commentBody, ':user_id'=>$userId, ':postid'=>$postId));
            }
        } else if ($imgOrText == 'text') {
            DB::query('INSERT INTO comments VALUES(0, :comment, :user_id, NOW(), :postid)', array(':comment'=>$commentBody, ':user_id'=>$userId, ':postid'=>$postId));
        } 
    }

    public static function displayComments($postId) {
        $comments = DB::query('SELECT comments.comment, usertable.`username` FROM comments, usertable
        WHERE post_id = :postid
        AND comments.user_id = usertable.id
        ORDER BY comments.posted_at DESC', array(':postid'=>$postId));
        foreach($comments as $comment) {
            echo '<center><div style="font-size: 14px;">'.$comment['comment']." ~ ".$comment['username']."</div><hr /></center>";
        }
    }
}
?>