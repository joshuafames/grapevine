<?php
    include('classes/DB.php');
    include('classes/Login.php');
    include('classes/Post.php');
    include('classes/Notify.php');
    include('classes/Image.php');
    include('classes/Comment.php');

    $showTimeline = False;
    if (Login::isLoggedIn()) {
            $userid = Login::isLoggedIn();

            $myusername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))['0']['username'];
            $meimage = DB::query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['profileimg'];
            $myhandle = DB::query('SELECT handle FROM users WHERE id = :userid', array(':userid'=>Login::isLoggedIn()))[0]['handle'];

            if (empty($meimage)) {
                    $myprofilePic = "./img/default/undraw_profile_pic_ic5t.png";
            }else {
                    $myprofilePic = $meimage;
            }
            $defaultimg = 'https://i.imgur.com/K8NcRRz.png';

            if (isset($_GET['uid'])) {
                $Suid = $_GET['uid'];
            }           


    } else {
            header('Location: login-page.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Grapevine - Messenger</title>

        <!-- bootstrap.min css -->
        <link rel="stylesheet" href="./vendor/bootstrap/css/bootstrap.min.css">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="./vendors/fontawesome/css/all.min.css">

        <!-- Themify Icons -->
        <link rel="stylesheet" href="./css/themify-icons.css">

        <!--- Custom CSS --->
        <link rel="stylesheet" href="./scss/style.css">
        <link rel="stylesheet" href="css/util.css">

        <!-- Icomooon Icons -->
        <link rel="stylesheet" href="./fonts/icomoon/style.css">

        <!--- Custom CSS --->
        <link rel="stylesheet" href="./scss/style.css">
        <link rel="stylesheet" href="./css/mobile.css">  
        <link rel="stylesheet" href="./css/addsIn.css">
</head>
<body>

        <!--================ Start Header Area =================-->
        <header class="header_area profile-page">
            <div class="main-menu social-site">
                <nav class="navbar fixed paddingNull navbar-expand-lg navbar-light">
                    <div class="container">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <a class="navbar-brand logo w-150 logo-text" href="index.html">
                            <img src="./img/core-img/Grapevine-alt.png" alt="" srcset="">
                        </a>

                        <!-- Collect the nav links, forms, and other content for toggling --> 
                        <div class="navbar-underscreen offset m-lr-auto" id="navbarSupportedContent">
                            <div>
                                <ul class="nav navbar-nav menu_nav nice_iconic">
                                    <li class="nav-item"><a class="nav-link" href="indexfeed.php"><i class="ti-home"></i></a></li>
                                    <li class="nav-item"><a class="nav-link" href="about.html"><i class="ti-search"></i></a></li>
                                    <li class="nav-item d-lg-none"><a class="nav-link" href="portfolio.html"><i class="ti-plus"></i></a></li>
                                    <li class="nav-item"><a class="nav-link" href="portfolio.html"><i class="ti-heart"></i></a></li>
                                    <li class="nav-item"><a class="nav-link" href="contact.html"><i class="ti-bell"></i></a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Other Additional nav content-->
                        <div class="user-area mt-0">
                            <div class="right-corner-nav flex-start v-align">
                                <div class="rc-nav-li"><a href="#" class="rc-nav-link"><i class="ti-feed"></i></a><span class="division-border"></span></div>     
                            </div>
                            <div class="profile-picture ml-20">
                                <img src='<?php echo($myprofilePic); ?>' alt="dp" srcset="" style="height: 30px;">
                            </div>                                      
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <!--================ End Header Area =================-->

        <!--================ Start Main Area =================-->
        <main class="site-main messenger r-font">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="people-section sticky-container-5">
                            <div class="chats pb-1">
                                <div class="person">
                                    <div class="person-dp flex-start mb-3">
                                        <img class="dp" src="./img/people/person_3.jpg" alt="dp" srcset="">
                                        <div class="p-d ml-3 v-align flex-start">
                                            <div class="mid-section">
                                                <h6 class="acc_holder mb-0">Jimmy James</h6>
                                                <p class="latest-text mb-0 text-primary-grey para">Bro I need a new...</p>
                                            </div> 
                                            <p class="timestamp mb-0 ml-4 c-font v-align para">15m</p>                                               
                                        </div>    
                                    </div>
                                    
                                </div>
                                <div class="person">
                                    <div class="person-dp flex-start mb-3">
                                        <img class="dp" src="./img/people/person_5.jpg" alt="dp" srcset="">
                                        <div class="p-d ml-3 v-align flex-start">
                                            <div class="mid-section">
                                                <h6 class="acc_holder mb-0">Kevin Whale</h6>
                                                <p class="latest-text mb-0 text-primary-grey r-font-reg">Its alright I got it ...</p>
                                            </div> 
                                            <p class="timestamp mb-0 ml-4 c-font v-align">32m</p>                                               
                                        </div>    
                                    </div>
                                    
                                </div>
                                <div class="person">
                                    <div class="person-dp flex-start mb-3">
                                        <img class="dp" src="./img/people/person_2.jpg" alt="dp" srcset="">
                                        <div class="p-d ml-3 v-align flex-start">
                                            <div class="mid-section">
                                                <h6 class="acc_holder mb-0">Trevor Green</h6>
                                                <p class="latest-text mb-0 text-primary-grey r-font-reg">Turn it to a ballon...</p>
                                            </div> 
                                            <p class="timestamp mb-0 ml-4 c-font v-align">1hr</p>                                               
                                        </div>    
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="active-friends p-rel pt-3">
                                <h5 class="lc-section-heading r-font-tbold text-primary-grey mt-2 mb-4">Active Followers</h5>
                                <div class="person mb-2 mt-1 flex-start">
                                    <div class="person-dp active">
                                        <img src="./img/post/person_5.jpg" alt="dp" srcset="" class="dp">                                    
                                    </div>
                                    <div class="p-d v-align">
                                        <h6 class="hisname mb-0 ml-3">Jakalyn May</h6>
                                    </div>
                                </div>
                                <div class="person mb-2 flex-start">
                                    <div class="person-dp active">
                                        <img src="./img/post/person_2.jpg" alt="dp" srcset="" class="dp">                                    
                                    </div>
                                    <div class="p-d v-align">
                                        <h6 class="hisname mb-0 ml-3">Gwen Stacy</h6>
                                    </div>
                                </div>
                                <div class="person mb-2 flex-start">
                                    <div class="person-dp active">
                                        <img src="./img/post/person_3.jpg" alt="dp" srcset="" class="dp">                                    
                                    </div>
                                    <div class="p-d v-align">
                                        <h6 class="hisname mb-0 ml-3">Marilyn Skyy</h6>
                                    </div>
                                </div>
                                <div class="person mb-2 flex-start">
                                    <div class="person-dp active">
                                        <img src="./img/people/person_6.jpg" alt="dp" srcset="" class="dp">                                    
                                    </div>
                                    <div class="p-d v-align">
                                        <h6 class="hisname mb-0 ml-3">Tom Barber</h6>
                                    </div>
                                </div>
                                <div class="ab-section text-center">
                                    <button class="add-btn cyclic btn"><i class="icon-add"></i></button>
                                </div>
                            </div>
                        </div>                            
                    </div>
                    <div class="col-lg-6 bx-divider">
                        <div class="conversation mt-1">
                            <div class="chatting-to bb-divider-np p-3 flex-start">
                                <img src="./img/post/person_5.jpg" alt="" srcset="" class="dp" style="width: 50px;">
                                <div class="ct-d v-align">                                  
                                </div>
                                
                            </div>
                            <div class="chat-bubbles mt-3 px-3" id="chat">
                                <!--<div class="his-text mb-3 single-chat">
                                    <div class="contents flex-start">
                                        <div class="hisdp mr-1">
                                            <img src="./img/post/person_5.jpg" alt="" srcset="" class="dp" width="30px">
                                        </div>
                                        <div class="message">
                                            <div class="chat-bubble">
                                                <p class="para mb-0">Hi There BehMan</p>
                                                <p class="para chat-timestamp mb-0">11:12</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mytext mb-3 single-chat">
                                    <div class="contents flex-start">
                                        <div class="message">
                                            <div class="chat-bubble">
                                                <p class="para mb-0">Hey Stace</p>
                                                <p class="para chat-timestamp mb-0">11:13</p>
                                            </div>
                                        </div>
                                        <div class="hisdp ml-1">
                                            <img src="./img/people/person_7.jpg" alt="" srcset="" class="dp" width="30px">
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="his-text mb-3 single-chat">
                                    <div class="contents flex-start">
                                        <div class="hisdp mr-1">
                                            <img src="./img/post/person_5.jpg" alt="" srcset="" class="dp" width="30px">
                                        </div>
                                        <div class="message">
                                            <div class="chat-bubble">
                                                <p class="para mb-0">How're you friend?</p>
                                                <p class="para chat-timestamp mb-0">11:15</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mytext mb-3 single-chat">
                                    <div class="contents flex-start">
                                        <div class="message">
                                            <div class="chat-bubble">
                                                <p class="para mb-0">Pretty good Stace I just learned how to program an app</p>
                                                <p class="para chat-timestamp mb-0">11:20</p>
                                            </div>
                                        </div>
                                        <div class="hisdp ml-1">
                                            <img src="./img/people/person_7.jpg" alt="" srcset="" class="dp" width="30px">
                                        </div>                                        
                                    </div>
                                </div>-->
                            </div>
                            <div class="message-entry bt-divider">
                                <div class="form-inline mt-2">
                                    <div class="pin-options flex-start">
                                        <input type="file" name="" id="pin-file" class="send-file d-none">
                                        <label for="pin-file" class="hover mr-1 label-btn cyclic"> 
                                            <i class="icon-paperclip"></i>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="message" id="messagecontent" placeholder="Type a message" class="form-control">
                                    </div>
                                    <div class="submit-area ml-1">
                                        <button id="sendmessage" class="cyclic" style="cursor: pointer;">
                                            <i class="icon-send"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        
                    </div>
                </div>
            </div>
        </main>
        <!--================ End Main Area =================-->
        

        <!-- Jquery js file -->
        <script src="./js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript">
            SENDER = <?php echo($Suid); ?>;
            USERNAME = "";
            function getProfilePic() {
                    $.ajax({

                            type: "GET",
                            url: "api/users?dp",
                            processData: false,
                            contentType: "application/json",
                            data: '',
                            success: function(r) {
                                    DP = r;
                            }
                    })
            }
            $(document).ready(function(){
            	function fetchMessages() {
            		$('#chat').empty();
                	$.ajax({

		                type: "GET",
		                url: "api/messages?sender="+SENDER,
		                processData: false,
		                contentType: "application/json",
		                data: '',
		                success: function(r) {
		                	console.log(r);
		                        r = JSON.parse(r)
		                        //console.log(r);

		                        $.ajax({
		                                type: "GET",
		                                url: "api/users",
		                                processData: false,
		                                contentType: "application/json",
		                                data: '',
		                                success: function(u) {
		                                    var u = u.replace(/'/g, "");
		                                    var USERNAME = u.replace( /[\r\n]+/gm, "" );
		                                    
		                                    
		                                    
		                                        $.each(r, function(index) {
		                                            SID = r[index].Sender;
		                                            RID = r[index].Receiver;

		                                            if (SID == USERNAME) {
		                                                $('#chat').append('<div class="mytext mb-3 single-chat"><div class="contents flex-start"> <div class="message"><div class="chat-bubble"><p class="para mb-0">'+r[index].body+'</p><p class="para chat-timestamp mb-0">11:13</p></div></div><div class="hisdp ml-1"><img src="./img/people/person_7.jpg" alt="" srcset="" class="dp" width="30px"></div></div></div>')
		                                                
		                                            } else if (RID == USERNAME) {
		                                                $('#chat').append('<div class="his-text mb-3 single-chat"><div class="contents flex-start"><div class="hisdp mr-1"><img src="./img/post/person_5.jpg" alt="" srcset="" class="dp" width="30px"></div><div class="message"><div class="chat-bubble"><p class="para mb-0">'+r[index].body+'</p><p class="para chat-timestamp mb-0">11:12</p></div></div></div></div>')
		                                            }
		                                        })
		                                }
		                        })
		                },
		                error: function(r) {
		                        console.log(r);
		                }
		        });
                }
            
                $('#sendmessage').click(function() {
                        $.ajax({

                                type: "POST",
                                url: "api/message",
                                processData: false,
                                contentType: "application/json",
                                data: '{ "body": "'+ $("#messagecontent").val() +'", "receiver": "'+ SENDER +'" }',
                                success: function(r) {
                                	console.log(r);
                                	fetchMessages();
                                        //location.reload()
                                },
                                error: function(r) {
                                }
                        })
                })

                $.ajax({
                        type: "GET",
                        url: "api/users?uid="+ <?php echo($Suid); ?>,
                        processData: false,
                        contentType: "application/json",
                        data: '',
                        success: function(u) {
                            var info = JSON.parse(u);

                            $.each(info, function(i) {
                                var name = info[i].Username;
                                $('.ct-d').html(
                                    $('.ct-d').html() + '<h5 class="acc-name text-primary-grey ml-3 mb-0 para-med">'+name+'</h5>'
                                );

                            })
                        },
                        error: function(u) {
                            console.log(u);
                        }
                });
                
                fetchMessages();                
                setInterval(fetchMessages, 10000);

		        
            })
        </script>
</body>
</html>
