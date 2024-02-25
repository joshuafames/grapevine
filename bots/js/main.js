$(document).ready(function() {
    const inputs = document.querySelectorAll('.input');

    function focusFunc(){
        let parent = this.parentNode.parentNode;
        parent.classList.add('focus');
    }

    function blurFunc(){
        let parent = this.parentNode.parentNode;
        if(this.value == ""){
            parent.classList.remove('focus');
        }
    }

    inputs.forEach(input => {
        input.addEventListener('focus', focusFunc);
        input.addEventListener('blur', blurFunc);
    });


    // SM NAVIGATION
      var responsiveNav = $('#responsive-nav');

      $(document).click(function(event) {
        if (!$(event.target).closest(responsiveNav).length) {
          if (responsiveNav.hasClass('open')) {
            responsiveNav.removeClass('open');
            $('#sm-navigation').removeClass('shadow');
          } else {
            if ($(event.target).closest('#nav-toggle').length) {
              $('#sm-navigation').addClass('shadow');
              responsiveNav.addClass('open');
            }
          }
        }
      });

    ////
    $allLeftCompo = 280;

    $height = $('.postimg').height();
    $(".wrap-comment100").css("height", $height);

    var nheight = $height - $allLeftCompo;
    $(".comments-section").css("height", nheight);
    $(".nunFound-img").css("height", nheight);

    //// POST IMAGE PREVIEW

    const chooseFile = document.getElementById("choose-postimg");
    const imgPreview = document.getElementById("img-preview");

    chooseFile.addEventListener("change", function () {
      getImgData();
    });

    function getImgData() {
      const files = chooseFile.files[0];

      if (files) {

        const fileReader = new FileReader();

        fileReader.readAsDataURL(files);
        fileReader.addEventListener("load", function () {
          imgPreview.style.display = "block";
          imgPreview.innerHTML = '<img src="' + this.result + '" />';
          $('#removeButton').addClass('d-block');
        });

      }
    }

    ///// AUTO RESIZE TEXTAREA

    /// Owl Carousel 

    $(".site-main .testimonial-area .owl-carousel").owlCarousel({
        loop: true,
        autoplay: true,
        dots: true,
        nav: false,
        responsive:{
            0: {
                items: 1
            },
            544: {
                items: 2
            }
        }
    });

    //owl Caraousel 2

    $(".site-main .benefits-area .owl-carousel").owlCarousel({
        loop: true,
        autoplay: true,
        dots: true,
        nav: false,
        responsive:{
            0: {
                items: 1
            },
            544: {
                items: 1
            }
        }
    });

    //owl Caraousel 3

    $(".site-main .stories-area .owl-carousel").owlCarousel({
        loop: false,
        autoplay: false,
        dots: false,
        nav: false,
        responsive:{
            0: {
                items: 4
            },
            769: {
                items: 8
            }
        }
    });

    $(".full-w-area .fw-stories-area .owl-carousel").owlCarousel({
        loop: false,
        autoplay: false,
        dots: false,
        nav: false,
        responsive:{
            0: {
                items: 13
            },
            544: {
                items: 13
            }
        }
    });
    
    //owl Caraousel 4

    $(".site-main .stories-area-alt .owl-carousel").owlCarousel({
        loop: false,
        autoplay: false,
        dots: false,
        nav: false,
        responsive:{
            0: {
                items: 6
            },
            544: {
                items: 6
            }
        }
    });

    // sticky navigation menu 
    
    let nav_offset_top = $('.nav_bar').height() + 50;

    function navbarFixed(){
        if($('.nav_bar').length){
            $(window).scroll(function(){
                let scroll = $(window).scrollTop();
                if(scroll >= nav_offset_top){
                    $('.nav_bar .main-menu').addClass('navbar_fixed');
                }else{
                    $('.nav_bar .main-menu').removeClass('navbar_fixed');
                }
            })
        }
    };

    navbarFixed();
    
    ///Tabs 
    // Select all tabs
    /*$('.nav-tabs.pp-main-section-nav a').click(function(){
    $(this).tab('show');
    })

    // Select tab by name
    $('.nav-tabs a[href="#photos"]').tab('show');

    // Select first tab
    $('.nav-tabs a:first').tab('show');

    // Select last tab
    $('.nav-tabs a:last').tab('show');

    // Select fourth tab (zero-based)
    $('.nav-tabs li:eq(3) a').tab('show');*/

    $('.nav-item .cn-link').click(function() {
            $('.cn-link.active').removeClass('active');
            $(this).addClass('youuu');
    });

    $('[data-fancybox]').fancybox();


});

    

