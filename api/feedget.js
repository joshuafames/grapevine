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
                        console.log(text)
                        $.each(posts, function(index) {
                                var caption = posts[index].PostBody;
                                var caption = caption.replace(/2>j/g, '"');
                                var caption = caption.replace(/3>j/g, "'");
                                $('.timelineposts').html(
                                        $('.timelineposts').html() + '<blockquote><div class="post"><div class="post-head flex-start"><a href="#" class="post-compiler"><div class="profile-pic story-active"><img id="profile-picture" src="'+posts[index].authorIMG+'" alt="" style="height:40px;"></div><div class="post-by"><h5 class="post-by-title">'+posts[index].PostedBy+'</h5><p class="location mb-0">Somewhere On Earth</p></div></a><a href="#" class="post-settings-menu" data-toggle="modal" data-target="#postSettings"><i class="fa fa-ellipsis-v"></i></a></div><div class="post-image"><img id="post-image" src="'+posts[index].PostImg+'"></div><div class="post-tags mt-4"><div class="reaction-area"><button class="'+posts[index].LikeStyle+'" type="button" data-id="'+posts[index].PostId+'"><i class="reaction-icon-size far fa-heart"></i></button><a href="comment-page.php?postid='+posts[index].PostId+'" class="single-reaction"><i class="reaction-icon-size far fa-comment"></i></a><a href="" class="single-reaction"><i class="reaction-icon-size far fa-paper-plane"></i></a><a href="" class="single-reaction float-right"><i class="reaction-icon-size icon-bookmark-o"></i></a></div><div class="likes-section mt-2" data-id="t'+posts[index].PostId+'"><p id="reactions" class="likes mb-0 red">'+posts[index].Likes+' likes</p></div></div><div class="post-text c-font-light cursor-default"><p id="post-caption" class="post-caption text-black"><span id="account-name">'+posts[index].PostedBy+'</span>'+caption+'</p></div><div class="post-time c-font-med" style="color: #2e3236 !important;"><p>'+posts[index].PostTime+'</p></div></div></blockquote>'
                                );

                                $('[data-id]').click(function() {
                                    var buttonid = $(this).attr('data-id');

                                    $.ajax({

                                        type: "POST",
                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                        processData : false,
                                        contentType: "application/json",
                                        data: '', 
                                        success: function(r) {
                                            act = r
                                            var res = JSON.parse(act)
                                            $("[data-id='"+buttonid+"']").toggleClass('active')
                                            $("[data-id='t"+buttonid+"']").html('<p id="reactions" class="likes mb-0 red">'+res.Likes+' likes</p>')
                                            console.log(r)
                                        },
                                        error: function(r) {
                                            console.log(r)
                                        }

                                    }) 

                                })
                        })
                    },
                    error: function(r) {
                        console.log(r)
                    }

                });
        });
