<?php
class Login {
    public static function isLoggedIn() {
        if (isset($_COOKIE['SID'])) {
            if (DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array('token'=>sha1($_COOKIE['SID'])))) {
                $user_id = DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array('token'=>sha1($_COOKIE['SID'])))[0]['user_id'];
                
                if (isset($_COOKIE['SID_'])) {
                    return $user_id;
                } else {
                    //$cstrong = True;
                    //$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                    //DB::query('INSERT INTO login_tokens VALUES (0, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
                    //DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SID'])));
                    //setcookie("SID", $token, time() + 60*60*24*7, '/', NULL, NULL, TRUE);
                    //setcookie("SID_", '1', time() + 60*60*24*3, '/', NULL, NULL, TRUE);
                    //return $user_id;    
                }
            }
        }
        return false;
    }
}
?>