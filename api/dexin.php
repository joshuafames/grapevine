<?php
require_once("DB.php");
require_once("Post.php");
require_once("Extra.php");

$db = new DB("127.0.0.1", "SocialNetwork", "root", "");

if ($_SERVER['REQUEST_METHOD'] == "GET") {
		
		http_response_code(200);

		if($_GET['url'] == "auth") {

		}elseif ($_GET['url'] == "users") {

		}elseif ($_GET['url'] == "feedposts") {

				$token = $_COOKIE['SNID'];
				$userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

				$followingposts = $db->query('SELECT DISTINCT posts.*, users.`username` FROM users, posts, followers
				WHERE (posts.user_id = followers.user_id
				OR posts.user_id = :userid)
				AND users.id = posts.user_id
				AND follower_id = :userid
				ORDER BY posts.posted_at DESC;', array(':userid'=>$userid, ':userid'=>$userid));

				$response = "[";

				foreach($followingposts as $post) {

						if (empty($post['postimg'])) {
			                if (is_null($post['shared_postid'])) {
			                    if (is_null($post['body'])) {
			                        $ptc = "nonexistant";
			                    }else {
			                        $ptc = "wordpost";
			                    }                            
			                }else {
			                    if (is_null($post['body'])) {
			                        $ptc =  "share";
			                    }else {
			                        $ptc = "quotepost";
			                    }                    
			                }

			            }else {
			                if (is_null($post['shared_postid'])) {
			                    if (is_null($post['body'])) {
			                        $ptc = "imagepost";
			                    }else {
			                        $ptc = "fullpost";
			                    }
			                }
			            }

			            if ($ptc == "share") {
			            	$postid = $post['shared_postid'];

			            	$pbid = $db->query('SELECT user_id FROM posts WHERE id = :pid', array(':pid'=>$post['shared_postid']))[0]['user_id'];
	                        $pdn = $db->query('SELECT username FROM users WHERE id = :uid', array(':uid'=>$pbid))[0]['username'];

	                        $postbyname = "111";
	                        $hisHandle = $db->query('SELECT handle FROM users WHERE id = :uid', array(':uid'=>$pbid))[0]['handle'];

			            	$profileIMG = $db->query('SELECT profileimg FROM users WHERE id = :uid', array(':uid'=>$pbid))[0]['profileimg'];
		                        if (empty($profileIMG)) {
		                                $profilePic = "./img/default/undraw_profile_pic_ic5t.png";
		                        }else {
		                                $profilePic = $profileIMG;
	                        }

			            	$postbody = $db->query('SELECT body FROM posts WHERE id = :pid', array(':pid'=>$post['shared_postid']))[0]['body'];
			            	$postimg = $db->query('SELECT postimg FROM posts WHERE id = :pid', array(':pid'=>$post['shared_postid']))[0]['postimg'];

			            	$posted_hour = $db->query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$post['shared_postid']))[0]['hours_since'];
                        	$posted_minute = $db->query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$post['shared_postid']))[0]['minutes_since'];

			            	if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['shared_postid'], ':userid'=>$userid))) {
	                        	$likeClasses = 'likeButton single-reaction react-btn animated';
	                        }else {
	                        	$likeClasses = 'likeButton single-reaction react-btn animated active';
	                        }
	                        
	                        $postlikes = $db->query('SELECT likes FROM posts WHERE id = :pid', array(':pid'=>$post['shared_postid']))[0]['likes'];

	                        #### GET DATE ####
					        $day = $db->query('SELECT DATE_FORMAT(posted_at, \'%d\') as day_posted FROM posts WHERE id=:postid', array(':postid'=>$post['shared_postid']))[0]['day_posted'];
					        $month = $db->query('SELECT DATE_FORMAT(posted_at, \'%m\') as month_posted FROM posts WHERE id=:postid', array(':postid'=>$post['shared_postid']))[0]['month_posted'];
					        $year = $db->query('SELECT DATE_FORMAT(posted_at, \'%y\') as year_posted FROM posts WHERE id=:postid', array(':postid'=>$post['shared_postid']))[0]['year_posted'];

			            }else {
			            	$postid = $post['id'];
			            	$profileIMG = $db->query('SELECT profileimg FROM users WHERE id=:authorid', array(':authorid'=>$post['user_id']))[0]['profileimg'];
		                        if (empty($profileIMG)) {
		                                $profilePic = "./img/default/undraw_profile_pic_ic5t.png";
		                        }else {
		                                $profilePic = $profileIMG;
	                        }
			            	$dpimage = $profilePic;
			            	$postimg = $post['postimg'];

			            	$posted_hour = $db->query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['hours_since'];
                        	$posted_minute = $db->query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['minutes_since'];

			            	if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
	                        	$likeClasses = 'likeButton single-reaction react-btn animated';
	                        }else {
	                        	$likeClasses = 'likeButton single-reaction react-btn animated active';
	                        }

	                        $pbid = $db->query('SELECT user_id FROM posts WHERE id = :pid', array(':pid'=>$post['id']))[0]['user_id'];
	                        $postbyname = $db->query('SELECT username FROM users WHERE id = :uid', array(':uid'=>$pbid))[0]['username'];
	                        $hisHandle = $db->query('SELECT handle FROM users WHERE username = :username', array(':username'=>$post['username']))[0]['handle'];
	                        $postlikes = $db->query('SELECT likes FROM posts WHERE id = :pid', array(':pid'=>$post['id']))[0]['likes'];

	                        #### GET DATE ####
					        $day = $db->query('SELECT DATE_FORMAT(posted_at, \'%d\') as day_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['day_posted'];
					        $month = $db->query('SELECT DATE_FORMAT(posted_at, \'%m\') as month_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['month_posted'];
					        $year = $db->query('SELECT DATE_FORMAT(posted_at, \'%y\') as year_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['year_posted'];

					        $postbody = $post['body'];
			            }

                        $roundDay = 24;
				        $posted_day = round($posted_hour/$roundDay);					        

				        $since = Extra::postedTimestamp($posted_hour, $posted_minute, $day, $month, $year);

                        $hisHandle = $db->query('SELECT handle FROM users WHERE username = :username', array(':username'=>$post['username']))[0]['handle'];

                        $caption = Post::link_add($postbody);
                        $caption = Extra::emojiAdd($caption, "21");                        
                        $caption = preg_replace("/\r\n|\r|\n/", '<br/> ', $caption);
                        $caption = preg_replace("/8ccc1o/", '<br/> ', $caption);
                        $caption = str_replace('"', '2>j', $caption);
                        $caption = str_replace("'", '3>j', $caption);

                        


    			////////////////////////////////////////////////////////////////////////////////////////////////////

					$response .= "{";
						$response .= '"PostId": "'.$postid.'",';
						$response .= '"ptc": "'.$ptc.'",';
						$response .= '"authorIMG": "'.$profilePic.'",';
						$response .= '"PostBody": "'.$caption.'",';
						$response .= '"PostImg": "'.$postimg.'",';
						$response .= '"PostTime": "'.$since.'",';
						$response .= '"LikeStyle": "'.$likeClasses.'",';
						$response .= '"PostedBy": "'.$postbyname.'",';
						$response .= '"HisHandle": "'.$hisHandle.'",';
						$response .= '"Likes": "'.$postlikes.'"';
					$response .= "},";
				}

				$response = substr($response, 0, strlen($response)-1);
				$response .= "]";

				http_response_code(200);
				echo $response;

		}elseif ($_GET['url'] == "userposts") {

				$token = $_COOKIE['SNID'];
				$userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

				$loggedInUserPosts = $db->query('SELECT posts.*, users.`username` FROM posts, users WHERE users.id = posts.user_id AND user_id=:userid ORDER BY posts.posted_at DESC', array(':userid'=>$userid));

				$response = "[";

				foreach($loggedInUserPosts as $post) {


						$profileIMG = $db->query('SELECT profileimg FROM users WHERE id=:authorid', array(':authorid'=>$post['user_id']))[0]['profileimg'];
	                        if (empty($profileIMG)) {
	                                $profilePic = "./img/default/undraw_profile_pic_ic5t.png";
	                        }else {
	                                $profilePic = $profileIMG;
                        }

                        $posted_hour = $db->query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['hours_since'];
                        $posted_minute = $db->query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['minutes_since'];

                        $roundDay = 24;
				        $posted_day = round($posted_hour/$roundDay);

				        #### GET DATE ####
				        $day = $db->query('SELECT DATE_FORMAT(posted_at, \'%d\') as day_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['day_posted'];
				        $month = $db->query('SELECT DATE_FORMAT(posted_at, \'%m\') as month_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['month_posted'];
				        $year = $db->query('SELECT DATE_FORMAT(posted_at, \'%y\') as year_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['year_posted'];

				        $since = Extra::postedTimestamp($posted_hour, $posted_minute, $day, $month, $year);

                        if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
                        	$likeClasses = 'single-reaction react-btn';
                        }else {
                        	$likeClasses = 'single-reaction react-btn active';
                        }

                        $caption = Post::link_add($post['body']);
                        $caption = preg_replace("/\r\n|\r|\n/", '<br/> ', $caption);
                        $caption = Extra::emojiAdd($caption, "21");                        
                        $caption = preg_replace("/8ccc1o/", '<br/> ', $caption);
                        $caption = str_replace('"', '2>j', $caption);
                        $caption = str_replace("'", '3>j', $caption);

                        if (empty($post['postimg'])) {
                        	if (is_null($post['shared_postid'])) {
                        		if ($caption) {
                        			$ptc = "wordpost";
                        		}                        		
                        	}else {

                        		if (is_null($caption)) {
                        			$ptc = "nonexistant";
                        		}else {
                        			$ptc = "quotepost";
                        		}
                        	}

                        }else {
                        	if ($caption) {
                        		if (is_null($post['shared_postid'])) {
                        			$ptc = "fullpost";
                        		}
                        	}
                        }

    			////////////////////////////////////////////////////////////////////////////////////////////////////

					$response .= "{";
						$response .= '"PostId": "'.$post['id'].'",';
						$response .= '"authorIMG": "'.$profilePic.'",';
						$response .= '"PostBody": "'.$caption.'",';
						$response .= '"PostImg": "'.$post['postimg'].'",';
						$response .= '"PostTime": "'.$since.'",';
						$response .= '"LikeStyle": "'.$likeClasses.'",';
						$response .= '"ptc": "'.$ptc.'",';
						$response .= '"PostedBy": "'.$post['username'].'",';
						$response .= '"Likes": "'.$post['likes'].'"';
					$response .= "},";
				}

				$response = substr($response, 0, strlen($response)-1);
				$response .= "]";

				http_response_code(200);
				echo $response;

		} else if ($_GET['url'] == "profileposts") {
                $start = (int)$_GET['start'];
                $userid = $db->query('SELECT id FROM users WHERE handle=:handle', array(':handle'=>$_GET['handle']))[0]['id'];
                $token = $_COOKIE['SNID'];
				$loggedInUserid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];


                $followingposts = $db->query('SELECT posts.*, users.`username`, users.`handle` FROM users, posts
                WHERE users.id = posts.user_id
                AND users.id = :userid
                ORDER BY posts.posted_at DESC
                LIMIT 5
                OFFSET '.$start.';', array(':userid'=>$userid));
                $response = "[";

                foreach($followingposts as $post) {


                        $profileIMG = $db->query('SELECT profileimg FROM users WHERE id=:authorid', array(':authorid'=>$post['user_id']))[0]['profileimg'];
	                        if (empty($profileIMG)) {
	                                $profilePic = "./img/default/undraw_profile_pic_ic5t.png";
	                        }else {
	                                $profilePic = $profileIMG;
                        	}

                        $hisHandle = $db->query('SELECT handle FROM users WHERE username = :username', array(':username'=>$post['username']))[0]['handle'];

                        $posted_hour = $db->query('SELECT timestampdiff(HOUR, posted_at, now() ) as hours_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['hours_since'];
                        $posted_minute = $db->query('SELECT timestampdiff(MINUTE, posted_at, now() ) as minutes_since FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['minutes_since'];

                        $roundDay = 24;
				        $posted_day = round($posted_hour/$roundDay);

				        #### GET DATE ####
				        $day = $db->query('SELECT DATE_FORMAT(posted_at, \'%d\') as day_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['day_posted'];
				        $month = $db->query('SELECT DATE_FORMAT(posted_at, \'%m\') as month_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['month_posted'];
				        $year = $db->query('SELECT DATE_FORMAT(posted_at, \'%y\') as year_posted FROM posts WHERE id=:postid', array(':postid'=>$post['id']))[0]['year_posted'];

				        $since = Extra::postedTimestamp($posted_hour, $posted_minute, $day, $month, $year);

                        if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$loggedInUserid))) {
                        	$likeClasses = 'single-reaction react-btn';
                        }else {
                        	$likeClasses = 'single-reaction react-btn active';
                        }
                        
                        $caption = Post::link_add($post['body']);
                        $caption = preg_replace("/\r\n|\r|\n/", '<br/> ', $caption);
                        $caption = Extra::emojiAdd($caption, "21");                        
                        $caption = preg_replace("/8ccc1o/", '<br/> ', $caption);
                        $caption = str_replace('"', '2>j', $caption);
                        $caption = str_replace("'", '3>j', $caption);

                        if (empty($post['postimg'])) {
			                if (is_null($post['shared_postid'])) {
			                    if (is_null($post['body'])) {
			                        $ptc = "nonexistant";
			                    }else {
			                        $ptc = "wordpost";
			                    }                            
			                }else {
			                    if (is_null($post['body'])) {
			                        $ptc =  "share";
			                    }else {
			                        $ptc = "quotepost";
			                    }                    
			                }

			            }else {
			                if (is_null($post['shared_postid'])) {
			                    if (is_null($caption)) {
			                        $ptc = "imagepost";
			                    }else {
			                        $ptc = "fullpost";
			                    }
			                }
			            }

    			////////////////////////////////////////////////////////////////////////////////////////////////////

					$response .= "{";
						$response .= '"PostId": "'.$post['id'].'",';
						$response .= '"authorIMG": "'.$profilePic.'",';
						$response .= '"PostBody": "'.$caption.'",';
						$response .= '"PostImg": "'.$post['postimg'].'",';
						$response .= '"PostTime": "'.$since.'",';
						$response .= '"LikeStyle": "'.$likeClasses.'",';
						$response .= '"PostedBy": "'.$post['username'].'",';
						$response .= '"HisHandle": "'.$hisHandle.'",';
						$response .= '"ptc": "'.$ptc.'",';
						$response .= '"Likes": "'.$post['likes'].'"';
					$response .= "},";
				}

                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";

                http_response_code(200);
                echo $response;

        } else if ($_GET['url'] == "followers") {

			$userid = $db->query('SELECT id FROM users WHERE handle=:handle', array(':handle'=>$_GET['handle']))[0]['id'];
			#HIM ^^^

        	$countedf = $db->query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];

			echo $countedf;
        }

} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {

		if($_GET['url'] == "auth") {
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);

			$username = $postBody->username;
			$email = $postBody->email;
			$password = $postBody->password;

			if (!$db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {

				if (strlen($username) >= 3 && strlen($username) <= 32) {

					if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

						if (strlen($password) >= 6 && strlen($password) <= 60) {
							if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

								if (!$db->query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {

									$db->query('INSERT INTO users VALUES (\'\', :username, :password, :email, \'0\', \'\')', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));

									echo "Success!";
										$cstrong = True;
										$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));								
										$user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
										$db->query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

										setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
										setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
									echo "Logged In";
								} else {
									echo "Email Already In Use";
								}
							}else {
								echo "Invalid Email";
							}
						} else {
							echo "Your Password Must Have More Than 6 Characters";
						}

							
					}else {
						echo "Invalid Username";
					}			
				} 
				else {
					#echo 'User Already Exists';
				}
			}
		}
	
		if($_GET['url'] == "auth") {
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);

			$username = $postBody->username;			
			$password = $postBody->password;

			if ($db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
				if (password_verify($password, $db->query('SELECT password FROM users WHERE username=:username', array(':username'=>$username)) [0]['password'])) {

					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));								
					$user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
					$db->query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

					echo '{ "Token" : "'.$token.'" }';

				}else {
					echo '{ "Error" : "Invalid username or password!" }';
					http_response_code(401);
				}
			}else {
				echo '{ "Error" : "Invalid username or password!" }';
				http_response_code(401);
			}
		}else if ($_GET['url'] == "likes") {

			$postId = $_GET['id'];
			$token = $_COOKIE['SNID'];
			$likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];


			if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
					$db->query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
                    $db->query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
                    
                    
                    $temp = $db->query('SELECT posts.user_id AS receiver, post_likes.user_id AS sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id = :postid', array(':postid'=>$postId));
                	$r = $temp[0]['receiver'];
                	$s = $likerId;                	

                	if ($r != $s) {
                		if (!$recentAlert = $db->query('SELECT timestampdiff(HOUR, `time`, now() ) as hours_since FROM `notifications` WHERE notifications.type = 2 AND notifications.receiver = :rec AND notifications.sender = :sen ORDER BY `id` DESC LIMIT 1' , array(':rec'=>$r, ':sen'=>$s))[0]['hours_since']) 
                		{
                			$db->query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>"", ':postid'=>$postId));
                		} else if ($recentAlert > 24) {                		
                			$db->query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>"", ':postid'=>$postId));
                		}              		
                	}
                	
            } else {
                    $db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                    $db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
            }
            echo "{";
            echo '"Likes":';
            echo $db->query('SELECT COUNT(*) AS `likes` FROM post_likes WHERE post_id=:postid', array(':postid'=>$postId))[0]['likes'];
            echo "}";

		}else if ($_GET['url'] == "follow") {
			#$token = $_COOKIE['SNID'];
			#$followerID = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id']; 
			#YOU ^^^
			$followerID = "1";

			$userid = $db->query('SELECT id FROM users WHERE handle=:handle', array(':handle'=>$_GET['handle']))[0]['id'];
			#HIM ^^^

			if ($userid != $followerID) {
				if (!$db->query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerID))) {

                        if ($followerID == 6) {
                            $db->query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                        }
                        $db->query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerID));

                        $s = $db->query('SELECT username FROM users WHERE id=:fid', array(':fid'=>$followerID))[0]['username']; #the one who is following 

                        if ($db->query('SELECT * FROM notifications WHERE type = 3 AND receiver = :uid AND sender = :fid', array(':uid'=>$userid, ':fid'=>$followerID))) {

                        	$recentAlert = $db->query('SELECT timestampdiff(HOUR, `time`, now() ) as hours_since FROM `notifications` WHERE notifications.type = 3 AND notifications.receiver = :userid AND notifications.sender = :fid ORDER BY `id` DESC LIMIT 1', array(':userid'=> $userid, ':fid'=>$followerID))[0]['hours_since'];
                        	echo "Recent Alert = ".$recentAlert;

                        	if ($recentAlert > 24) {

                        		$db->query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>3, ':receiver'=>$userid, ':sender'=>$followerID, ':extra'=>"", ':postid'=>""));
                        	}
                        }else {
                        	$db->query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra, :postid, NOW())', array(':type'=>3, ':receiver'=>$userid, ':sender'=>$followerID, ':extra'=>"", ':postid'=>""));
                        }

                        

                }
            }
            $countedf = $db->query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];

			echo "{";
	        echo '"nf":';
	        echo $db->query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
	        echo "}";

		}else if ($_GET['url'] == "unfollow") {
		
			$token = $_COOKIE['SNID'];
			$followerID = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

			$userid = $db->query('SELECT id FROM users WHERE handle=:handle', array(':handle'=>$_GET['handle']))[0]['id'];

			if ($userid != $followerID) {
				 if ($db->query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerID))) {
                        if ($followerID == 6) {
                            $db->query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                        }
                        $db->query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerID));
                    }               
			}
			echo "{";
	        echo '"nofollowers":';
	        echo $db->query('SELECT COUNT(*) FROM followers WHERE user_id = :userid', array(':userid'=>$userid))[0]['COUNT(*)'];
	        echo "}";
		}

} elseif ($_SERVER['REQUEST_METHOD'] == "DELETE") {
		if($_GET['url'] == "auth") {
				if (isset($_GET['token'])) {
					if ($db->query('SELECT token FROM login_tokens WHERE token=:token', array(':token'=>sha1($_GET['token'])))) {
						$db->query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_GET['token'])));
						echo ' { "Status" : "Success" }';
						http_response_code(200);
					}else {
						echo ' { "Error" : "Invalid Token" }';
						http_response_code(400);
					}						
				}else {
					echo ' { "Error" : "Malformed request" }';
					http_response_code(400);
				}
		}
}else {
		http_response_code(405);
}

?>