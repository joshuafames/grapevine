<?php

class Notify {
	
	public static function createNotify($text = "", $postid = 0, $fid = "", $rid = "") {

                $text = explode(" ", $text);
                $notify = array();
            
                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "@") {
                                $notify[substr($word, 1)] = array("type"=>1, "extra"=>' { "postbody": "'.htmlentities(implode($text, " ")).'" } ');
                        }
                }

                if (count($text) == 1 && $postid != 0) {
                	$temp = DB::query('SELECT posts.user_id AS receiver, post_likes.user_id AS sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id = :postid', array(':postid'=>$postid));
                	$r = $temp[0]['receiver'];
                	$s = $rid;

                	DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>"", ':postid'=>$postid));

                }elseif (count($text) == 1 && $postid == 0 && $fid = "") {
                        $s = DB::query('SELECT username FROM users WHERE id=:fid', array(':fid'=>$fid))[0]['username']; #the one who is following you

                        DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW()', array(':type'=>3, ':receiver'=>$rid, ':sender'=>$s, ':extra'=>"", ':postid'=>""));
                }

                return $notify;
        }
        public static function likeComment($commentId, $postId){

                $temp = DB::query('SELECT comments.user_id AS receiver, comment_likes.liker_id AS sender FROM comments, comment_likes WHERE comments.id = comment_likes.comment_id AND comments.id = :commentid', array(':commentid'=>$commentId));
                        $r = $temp[0]['receiver'];
                        $s = $temp[0]['sender'];
                if ($r != $s) {
                     DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>5, ':receiver'=>$r, ':sender'=>$s, ':extra'=>"", ':postid'=>$postId));   
                }                
        }
        public static function notifyTagComment($text) {
                $text = explode(" ", $text);
                $notify = array();

                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "@") {
                                $notify[substr($word, 1)] = array("type"=>6, "extra"=>' { "postbody": "'.htmlentities(implode($text, " ")).'" } ');
                        }
                }
                return $notify;
        }
}

?>