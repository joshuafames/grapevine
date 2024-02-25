<?php
    include('./classes/DB.php');
    include('./classes/Login.php');

    if (Login::isLoggedIn()) {
            $userid = Login::isLoggedIn();
            
            $type = $_GET['type'];
            $postid = $_GET['postid'];

            if (!empty($postid)) {
                if ($type ==="snq") {
                    
                        DB::query('INSERT INTO posts(body, posted_at, user_id, likes, postimg, topics, shared_postid) VALUES (NULL, NOW(), :userid, 0, NULL, NULL, :postid)', array(':userid'=>$userid, ':postid'=>$postid));
                        echo "Post Shared!";
                                  
                    
                }elseif ($type === "saq") {
                    if (isset($_POST['postcom'])) {

                        $caption = $_POST['comment'];
                        DB::query('INSERT INTO posts(body, posted_at, user_id, likes, postimg, topics, shared_postid) VALUES (:body, NOW(), :userid, 0, NULL, NULL, :postid)', array(':body'=>$caption, ':userid'=>$userid, ':postid'=>$postid));

                    }else {
                        echo '<form method="post" action="createpost.php?type=saq&postid='.$postid.'"><input type="text" name="comment"><input type="submit" name="postcom"></form>';
                    }
                    echo "Post shared as Quote!";

                }else {
                    echo "Not Sure of the type";
                }
            }else {
                echo "No Post is found!"; 
            }
            
    } else {
            header('Location: login-page.php');
    }
?>
