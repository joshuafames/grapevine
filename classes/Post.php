<?php

class Post {

        public static function createPost($postbody, $loggedInUserId, $profileUserId) {

                if (strlen($postbody) > 360 || strlen($postbody) < 1) {
                        die('Incorrect length! Theres no caption though?!');
                }

                $postbody = preg_replace("/\r\n|\r|\n/", ' 8ccc1o ', $postbody);

                $topics = self::getTopics($postbody);
                $hashtags = explode(" ", $topics);

                foreach ($hashtags as $tag) {
                    if (null == (DB::query('SELECT topic FROM topics WHERE topic=:topic', array(':topic'=>$tag)))) {
                        DB::query('INSERT INTO topics(topic, numposts) VALUES (:tag, :numposts)', array(':tag'=>$tag, ':numposts'=>1));
                    }else {
                        DB::query('UPDATE topics SET numposts=numposts+1 WHERE topic = :topic', array(':topic'=>$tag));
                    }                    
                }


                if ($loggedInUserId == $profileUserId) {

                        if (count(Notify::createNotify($postbody)) != 0) {

                            foreach (Notify::createNotify($postbody) as $key => $n) {

                                    $s = $loggedInUserId;
                                    $r = DB::query('SELECT id FROM users WHERE handle=:handle', array(':handle'=>$key))[0]['id'];
                                    if ($r != 0) {
                                        DB::query('INSERT INTO notifications(type, receiver, sender, extra, time) VALUES (:type, :receiver, :sender, :extra, NOW())', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                                    }
                                    
                            }
                        }


                        DB::query('INSERT INTO posts(body, posted_at, user_id, likes, postimg, topics, shared_postid) VALUES (:postbody, NOW(), :userid, 0, NULL, :topics, NULL)', array(':postbody'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));

                } else {
                        die('Incorrect user!');
                }
        }

        public static function createImgPost($postbody, $loggedInUserId, $profileUserId) {

                if (strlen($postbody) > 160) {
                        die('Incorrect length! Do you have an image?');
                }
                $postbody = preg_replace("/\r\n|\r|\n/", ' 8ccc1o ', $postbody);

                $topics = self::getTopics($postbody);
                $hashtags = explode(" ", $topics);

                foreach ($hashtags as $tag) {
                    if (null == (DB::query('SELECT topic FROM topics WHERE topic=:topic', array(':topic'=>$tag)))) {
                        DB::query('INSERT INTO topics(topic, numposts)  VALUES (:tag, :numposts)', array(':tag'=>$tag, ':numposts'=>1));
                    }else {
                        DB::query('UPDATE topics SET numposts=numposts+1 WHERE topic = :topic', array(':topic'=>$tag));
                    }                    
                }

                if ($loggedInUserId == $profileUserId) {

                    if (count(Notify::createNotify($postbody)) != 0) {

                            foreach (Notify::createNotify($postbody) as $key => $n) {

                                    $s = $loggedInUserId;
                                    $r = DB::query('SELECT id FROM users WHERE handle=:handle', array(':handle'=>$key))[0]['id'];

                                    if ($r != 0) {
                                        DB::query('INSERT INTO notifications(type, receiver, sender, extra, postid, time) VALUES (:type, :receiver, :sender, :extra, NULL, NOW())', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                                    }
                                    
                            }
                    }

                        DB::query('INSERT INTO posts(body, posted_at, user_id, likes, postimg, topics, shared_postid) VALUES (:postbody, NOW(), :userid, 0, NULL, :topics, NULL)', array(':postbody'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));
                        $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
                        return $postid;
                } else {
                        die('Incorrect user!');
                }
        }

        public static function likePost($postId, $likerId) {

                if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                        DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('INSERT INTO post_likes(post_id, user_id) VALUES (:postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
                        Notify::createNotify("", $postId, "", $likerId, "");
                } else {
                        DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
                }
        }

        public static function getTopics($text) {
            
                $text = explode(" ", $text);
                $topics = "";

                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "#") {
                                $topics .= substr($word, 1)." ";
                        }
                }
                $topics = substr($topics, 0, strlen($topics)-1);
                return $topics;
        }

        public static function link_add($text) {
                $text = explode(" ", $text);
                $newstring = "";
                foreach ($text as $word) {

                    if (substr($word, 0, 1) == "@") {
                            $newstring .= "<a class='text-black c-font' href='profile-page.php?handle=".substr($word, 1)."'>".htmlspecialchars($word)." </a>";

                    }elseif (substr($word, 0, 1) == "#") {
                            $newstring .= "<a class='text-black c-font' href='hashtag.php?hashtag=".substr($word, 1)."'>".htmlspecialchars($word)." </a>";
                            
                    }else {
                            $newstring .= htmlspecialchars($word)." ";
                    }
                }

                return $newstring;
        }

        public static function displayPosts($userid, $username, $loggedInUserId) {
                $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                $posts = "";

                foreach($dbposts as $p) {

                        if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {

                                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&react=True&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='like' value='Like'>
                                        <span>".$p['likes']." likes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                    $posts .= "<input type='submit' name='deletepost' value='x'>";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";

                        } else {
                                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&react=True&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='unlike' value='Unlike'>
                                        <span>".$p['likes']." likes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                    $posts .= "<input type='submit' name='deletepost' value='x'>";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";
                        }
                }

                return $posts;
        }

        public static function displayFollowingPosts($userid) {
                $followingposts = DB::query('SELECT posts.id, posts.body, posts.likes, posts.user_id, users.`username` FROM users, posts, followers
                        WHERE posts.user_id = followers.user_id
                        AND users.id = posts.user_id
                        AND follower_id = :userid
                        ORDER BY posts.likes DESC;', array(':userid'=>$userid));
                $posts = "";
                
                $head_forPost = '<div class="post" style="width: 530px; padding-bottom:1rem;">
                                            <div class="post-head flex-start">
                                                <a href="#" class="post-compiler">
                                                    <div class="profile-pic story-active">
                                                        <img id="profile-picture" style="height: 40px;" src=';
                $image_after =                          'alt="">
                                                    </div>
                                                    <div class="post-by">
                                                        <h5 class="post-by-title">';
                $secondaryHead_forPost =                '</h5>
                                                        <p class="location mb-0">Somewhere On Earth</p>
                                                    </div>
                                                </a>
                                                <a href="#" class="post-settings-menu">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                            </div>';
                $postimg ='<div class="post-image"><img id="post-image" src=';
                $postimg_aft = '></div>';


                                            
                $reactionArea_forPost = '
                                                                <a href="" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a>
                                                                <a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a>
                                                                <a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a>
                                                            </form>
                                                            <div class="likes-section mt-2">
                                                                <p id="reactions" class="likes mb-0 red">';
                $reactionArea_close = ' likes</p></div></div>';
                
                $posttext = '<div class="post-text" style="font-family: Poppins-Light;"><p class="post-caption"><span class="account-name">';
                $posttext_close = '</p></div>';
                
                $timestamp_forPost = '<div class="post-time mt-1"><p class="mb-0">';

                foreach($followingposts as $p) {

                        $postAuthorId = $p['user_id'];

                        $myprofilePic = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$userid));
                        $profilePic = DB::query('SELECT users.profileimg FROM users, posts WHERE users.id=:authorid AND posts.user_id = :authorid', array(':userid'=>$postAuthorId));

                    if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$userid))) {
                        $likeButtonRed = '<div class="post-tags mt-4">
                                                                <form name="reactform" class="reaction-area" id="react" action="profile.php?username=$username&postid='.$p['id'].'" method="post">
                                                                    <a href="javascript: submitform()" class="single-reaction"><i class="reaction-icon-size far fa-heart"></i></a>';
                        
                    }else {
                        $likeButtonRed = '<div class="post-tags mt-4">
                                                                <form name="reactform" class="reaction-area" id="react" action="profile.php?username=$username&postid='.$p['id'].'" method="post">
                                                                    <a href="javascript: submitform()" class="single-reaction active"><i class="reaction-icon-size far fa-heart"></i></a>';
                    }
                    

                    $posted_hour = DB::query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$p['id']))[0]['hours_since'];
                    $posted_minute = DB::query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$p['id']))[0]['minutes_since'];
                    $roundDay = 24;
                    $posted_day = round($posted_hour/$roundDay);

                    if ($posted_hour < 1) {
                            $timeNumber = $posted_minute;
                            $when = " Minutes Ago";
                    }else if ($posted_hour == 1) {
                            $timeNumber = $posted_hour;
                            $when = " Hour Ago";
                    }else if ($posted_hour > 24) {
                            $timeNumber = $posted_day;
                            $when = " Days Ago";
                    }else {
                        $timeNumber = $posted_hour;
                        $when = " Hours Ago";
                    }

                    $since = "$timeNumber $when";

                    $posts .= $head_forPost.$p['profileimg'].$image_after.$p['username'].$secondaryHead_forPost.$postimg.$p['postimg'].$postimg_aft.$likeButtonRed.$reactionArea_forPost.$p['likes'].$reactionArea_close.$posttext.$p['username']."</span>".htmlspecialchars($p['body']).$posttext_close.$timestamp_forPost.$since."</p></div></div>";
                }

                return $posts;
        }

        public static function displayPTF($userid) {
                $pTF = "";
                $peopleToFollow = DB::query('SELECT username, profileimg FROM users, followers WHERE followers.follower_id != :userid AND users.id != :userid  LIMIT 3', array(':userid'=>$userid));

                foreach ($peopleToFollow as $ptf) {

                        $FstLine = '<div class="single-section-item mt-10 flex-start">';
                        $SndLine = '<div class="user-profile-image">';
                        $TrdLine = '<a href="#"><img src=';
                        
                        $Tclose = '></a>';
                        $FthLine = '</div><a href="';
                        
                        $profileLink = 'profile-page.php?username=';

                        $FTopen = '" class="v-align single-item-name ml-10">';
                        $FTclose = '</a></div>';

                        $profilePic = DB::query('SELECT profileimg FROM users WHERE username=:username', array(':username'=>$ptf['username']))[0]['profileimg'];
                        if (is_null($profilePic)) {
                            $dp = "./img/default/undraw_profile_pic_ic5t.png";
                        }else {
                            $dp = $ptf['profileimg'];
                        }
                        $ptf .= $FstLine.$SndLine.$TrdLine.$dp.$Tclose.$FthLine.$profileLink.$ptf['username'].$FTopen.$ptf['username'].$FTclose;
                }
                return $pTF;
        }

        public static function dpCheck($hisId) {
                $meimage = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$hisId))[0]['profileimg'];

                if (empty($meimage)) {
                        $dp = "./img/default/undraw_profile_pic_ic5t.png";
                }else {
                        $dp = $meimage;
                }
                return $dp; 
        }


}
?>
