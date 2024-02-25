<?php 

class Extra {

      	public static function postedTimestamp($posted_hour, $posted_minute, $day, $month, $year) {
      		$roundDay = 24;
                      $posted_day = round($posted_hour/$roundDay);

                      $monthNum  = $month;
                      $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                      $monthName = $dateObj->format('F'); // March

              	if ($posted_hour < 1) {

      			if ($posted_minute == 0) {
      				$timeNumber = "Just";
      				$when = " Now";
      			}else {
      				$timeNumber = $posted_minute;
      	        	$when = " Min Ago";
      			}
                      }else if ($posted_hour == 1) {
                              $timeNumber = $posted_hour;
                              $when = " Hr Ago";
                      }else if ($posted_hour > 24) {
                      		if ($posted_day > 3) {
                      			if ($nowYear = $year) {
                      				$timeNumber = "$day $monthName $year";
                      				$when = "";
                      			}else {
                      				$timeNumber = "$day $monthName";
                      				$when = "";
                      			}
                      		}else {
                      			$timeNumber = $posted_day;
              	                $when = " Dys Ago";
                      		}	                
                      }else {
                              $timeNumber = $posted_hour;
                              $when = " Hrs Ago";
                      }

              $since = "$timeNumber $when";
              return $since;
      	}

        public static function emojiAdd($content, $ts) {
               $content = str_replace(":-)", '<span><img src="./vendors/emoticons/png/happy.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(";)", '<span><img src="./vendors/emoticons/png/wink.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(">:(", '<span><img src="./vendors/emoticons/png/mad.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(">:|", '<span><img src="./vendors/emoticons/png/angry-1.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(":|", '<span><img src="./vendors/emoticons/png/emoticons.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(":/", '<span><img src="./vendors/emoticons/png/suspicious-1.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(':o', '<span><img src="./vendors/emoticons/png/surprised.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(":*", '<span><img src="./vendors/emoticons/png/kissing.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(":) ", '<span><img src="./vendors/emoticons/png/happy-1.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace(":)/", '<span><img src="./vendors/emoticons/png/smile.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               $content = str_replace("**/", '<span><img src="./vendors/emoticons/png/heart.png" style="height:'.$ts.'px !important; width: '.$ts.'px !important;"></span>', $content);
               

               return $content;
        }

        public static function textFilterAdd($textContent) {
          $textContent = Post::link_add($post['body']);
          $textContent = self::emojiAdd($textContent);
          $textContent = preg_replace("/\r\n|\r|\n/", '<br/> ', $textContent);
          $textContent = preg_replace("/8ccc1o/", '<br/> ', $textContent);
          $textContent = str_replace('"', '2>j', $textContent);
          $textContent = str_replace("'", '3>j', $textContent);

          return $textContent;
        }

}

?>