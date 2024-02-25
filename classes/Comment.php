<?php
class Comment {
        public static function createComment($commentBody, $postId, $userId) {

                if (strlen($commentBody) > 160 || strlen($commentBody) < 1) {
                        die('Incorrect length!');
                }

                if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
                        echo 'Invalid post ID';
                } else {
                        #Notify::notifyTagComment($commentBody);
                        DB::query('INSERT INTO comments(comment, user_id, posted_at, post_id) VALUES (:comment, :userid, NOW(), :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':postid'=>$postId));
                }

        }
        public static function likeComment($likerId, $commentId, $postId) {

                if (!DB::query('SELECT liker_id FROM comment_likes WHERE post_id=:postid AND liker_id=:userid AND comment_id = :commentid', array(':commentid'=>$commentId, ':userid'=>$likerId, ':postid'=>$postId))) {
                        DB::query('INSERT INTO comment_likes VALUES (:commentid, :likerid, :postid, NOW())', array(':commentid'=>$commentId, ':likerid'=>$likerId, ':postid'=>$postId));

                        $temp = DB::query('SELECT comments.user_id AS receiver, comment_likes.liker_id AS sender FROM comments, comment_likes WHERE comments.id = comment_likes.comment_id AND comments.id = :commentid', array(':commentid'=>$commentId));
                        $r = $temp[0]['receiver'];
                        $s = $temp[0]['sender'];

                    DB::query('INSERT INTO notifications(type, receiver, sender, extra, time) VALUES (:type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>5, ':receiver'=>$r, ':sender'=>$s, ':extra'=>"", ':postid'=>$postId));
                } else {
                        DB::query('DELETE FROM comment_likes WHERE comment_id=:commentid AND liker_id=:userid AND post_id=:postid', array(':commentid'=>$commentId, ':userid'=>$likerId, ':postid'=>$postId));
                }
        }

        public static function displayComments($postId) {

                $comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid'=>$postId));
                foreach($comments as $comment) {
                        echo $comment['comment']." ~ ".$comment['username']."<br />";
                }
        }
        public static function displayDesComments($postId, $userId) {

                $comments = DB::query('SELECT comments.*, users.username, users.profileimg FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id ORDER BY posted_at DESC', array(':postid'=>$postId));
                $single_com = "";
                foreach($comments as $comment) {

                        $caption = Post::link_add($comment['comment']);
                        $caption = Extra::emojiAdd($caption, "14");                        
                        $caption = preg_replace("/\r\n|\r|\n/", '<br/> ', $caption);
                        $combody = preg_replace("/8ccc1o/", '<br/> ', $caption);

                        $posted_hour = DB::query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM comments WHERE id=:commentid', array(':commentid'=>$comment['id']))[0]['hours_since'];

                        $posted_minute = DB::query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM comments WHERE id=:commentid', array(':commentid'=>$comment['id']))[0]['minutes_since'];

                        $roundDay = 24;
                        $posted_day = round($posted_hour/$roundDay);

                        if (!DB::query('SELECT liker_id FROM comment_likes WHERE post_id=:postid AND liker_id=:userid AND comment_id = :commentid', array(':postid'=>$postId, ':userid'=>$userId, ':commentid'=>$comment['id']))) {
                            $btnStyle = ""; 
                        }else {
                            $btnStyle = 'style="color: #ff2259 !important;font-weight: 600;"';
                        }

                        if ($posted_hour < 1) {
                                $timeNumber = $posted_minute;
                                $when = "m";
                        }else if ($posted_hour > 24) {
                                    $timeNumber = $posted_day;
                                    $when = "d";
                        }else {
                            $timeNumber = $posted_hour;
                            $when = "h";
                        }

                        $comTime = "$timeNumber$when";
                        $defaultimg = "./img/default/undraw_profile_pic_ic5t.png";
                        if (is_null($comment['profileimg'])) {
                            $commenterDP = $defaultimg;
                        }else {
                            $commenterDP = $comment['profileimg'];
                        }

                        $single_com .= '<div class="single-comment">
                                    <div class="commentContainer">
                                        <a href="" class="commenter-dp">
                                            <img src='.$commenterDP.'>
                                        </a>
                                        <div class="theComment">
                                            <p class="mb-2"><span class="commenterName">'.$comment['username'].'</span>'.$combody.'</p>
                                            <div class="grey-area flex-start">
                                                <p class="postedTime mr-4">'.$comTime.'</p>
                                                <p class="mr-4">3 likes</p>
                                                <a href="#">Reply</a>
                                            </div>
                                        </div>
                                        <form class="ml-auto" action="comment-page.php?postid='.$postId.'&&cid='.$comment['id'].'" method="post"><button type="submit" class="no-bg like-heart"><i class="far fa-heart" '.$btnStyle.'></i></button></form>
                                    </div>
                                </div>';
                }
                return $single_com;
        }
}
?>
