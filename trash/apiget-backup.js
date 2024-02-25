$(document).ready(function() {
        $.ajax({

            type: "GET",
            url: "api/feedposts",
            processData : false,
            contentType: "application/json",
            data: '',
            success: function(r) {
                var res = r
                var text = res.replace(/'/g, "");
                var text  = text.replace( /[\r\n]+/gm, "" );
                var posts = JSON.parse(text);
                
                $.each(posts, function(index) {
                        var caption = posts[index].PostBody;
                        var caption = caption.replace(/2>j/g, '"');
                        var caption = caption.replace(/3>j/g, "'");

                        var ptCode = posts[index].ptc;
                        if ( ptCode === "wordpost" ) {
                            $('.timelineposts').html(
                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text r-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+' likeButton animated" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare'+posts[index].PostId+'" data-postid="'+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div><div class="modal sharebtnpost fade" id="postShare'+posts[index].PostId+'" role="dialog" tabindex="-1" style="padding-top: 70px;"></div></blockquote>'
                            );
                        }else if ( ptCode === "fullpost" ) {
                            $('.timelineposts').html(
                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location r-font mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare'+posts[index].PostId+'" data-postid="'+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-alt cursor-default"><p id="post-caption" class="post-caption r-font text-black"><span id="account-name" class="text-black">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div><div class="modal sharebtnpost fade" id="postShare'+posts[index].PostId+'" role="dialog" tabindex="-1" style="padding-top: 70px;"></div></blockquote>'
                            );
                        }else if ( ptCode === "imagepost" ) {
                            $('.timelineposts').html(
                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location r-font mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare'+posts[index].PostId+'" data-postid="'+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-alt cursor-default"><p id="post-caption" class="post-caption r-font text-black"><span id="account-name" class="text-black">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div><div class="modal sharebtnpost fade" id="postShare'+posts[index].PostId+'" role="dialog" tabindex="-1" style="padding-top: 70px;"></div></blockquote>'
                            );
                        }else if ( ptCode === "share" ) {
                            $('.timelineposts').html(
                                    $('.timelineposts').html() + '<blockquote><div class="post"><div class="postsharedby mb-1 flex-start" style="border-bottom: 1px solid #e5e5e5;"><p class="pt-3 pb-2 px-4 r-font-med mb-0" style="color: #9b9b9b;"><span class="mr-2"><i class="fa fa-paper-plane"></i></span>'+posts[index].sharedby+' shared</p></div><div class="post-head flex-start"><a href="accountprofile.php?handle='+posts[index].HisHandle+'" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image mainPost"><div class="post-text r-font-med cursor-default" style="height:100%;display:table;"><p id="post-caption" class="post-caption text-black niCap"><span id="account-name">@'+posts[index].HisHandle+'</span>'+caption+'</p></div></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+' likeButton animated" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="#" data-toggle="modal" data-target="#postShare'+posts[index].PostId+'" data-postid="'+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-time r-font-med"><p>'+posts[index].PostTime+'</p></div></div><div class="modal sharebtnpost fade" id="postShare'+posts[index].PostId+'" role="dialog" tabindex="-1" style="padding-top: 70px;"></div></blockquote>'
                            );
                        };
                        

                        $('[data-id]').click(function() {
                            var buttonid = $(this).attr('data-id');

                            $.ajax({

                                type: "POST",
                                url: "api/likes?id=" + $(this).attr('data-id'),
                                processData : false,
                                contentType: "application/json",
                                data: '', 
                                success: function(r) {
                                    act = r;
                                    var res = JSON.parse(r);

                                    $("[data-id='t"+buttonid+"']").html('<p id="reactions" class="likes mb-0 red">'+res.Likes+' likes</p>');

                                    $("[data-id='"+buttonid+"']").toggleClass('active');

                                    var timeoutHandler = null;
                                    $("[data-id='"+buttonid+"']").addClass('tada');

                                    if (timeoutHandler) clearTimeout(timeoutHandler);

                                    timeoutHandler = setTimeout(function() {
                                        $("[data-id='"+buttonid+"']").removeClass('tada');
                                    }, 1000);

                                    console.log(r);
                                },
                                error: function(r) {
                                    console.log(r);
                                }

                            }) 

                        });

                        $('.sharebtnpost').click(function() {

                            var postid = $(this).attr('data-postid');

                            $('#postShare'+posts[index].PostId).html(
                                    $('#postShare'+posts[index].PostId).html() + '<div class="modal-dialog" role="document"><div class="modal-content c-font-light text-center" style="border-radius: 1.5rem;"><div class="modal-header justify-content-center" style="padding: 2rem 1rem"><h4 class="modal-title">Share Post</h4></div><a href="createpost.php?type=snq&postid='+postid+'" class="modal-body dg-text modal-border-split"><h5 class="my-2 c-font">Share to your followers</h5><p class="description">The post will be seen by your followers and it will appear in your profile page</p></a><a href="#" class="modal-body dg-text modal-border-split"><h5 class="my-2 c-font">Share as Quote</h5><p class="description">Quote this post and say something about it.</p></a><a href="#" class="modal-body dg-text"><h5 class="my-2 c-font">Share as link</h5><p class="description">Share this post to other social media platforms.</p></a><div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal"></button></div></div></div>'
                            );
                            $('[data-dismiss]').click(function() {
                                var postid = " ";
                            });
                        });
                });
            },
            error: function(r) {
                console.log(r);
            }

        });
});