$(document).ready(function(){
    $(window).scroll(function(){
        if(this.scrollY > 20){
            $('.navbar').addClass("sticky");
        } else {
            $('.navbar').removeClass("sticky");
        }
        if(this.scrollY > 100){
             $('.scroll-up-btn').addClass("show");   
        }else{
          $('.scroll-up-btn').removeClass("show");   
        }
    });
    // slide-up script
    $('.scroll-up-btn').click(function(){
        $('html').animate({scrollTop: 0});
      
    });

    // toggle menu/navbar script
    $('.menu-btn').click(function(){
        $('.navbar .menu').toggleClass("active");
        $('.menu-btn i').toggleClass("active");
    });

    // close mobile menu when a link is clicked
    $('.navbar .menu li a').click(function(){
        $('.navbar .menu').removeClass("active");
        $('.menu-btn i').removeClass("active");
    });
    // typed animation script
    var typed = new Typed(".typing", {
        strings: ["Web Developer", "WordePress Developer", "Designer", "Freelancer"],
        typeSpeed: 100,
        backSpeed: 60,
        loop: true
    });
    var typed = new Typed(".typing-2", {
        strings: ["Web Developer", "WordePress Developer", "Designer", "Freelancer"],
        typeSpeed: 100,
        backSpeed: 60,
        loop: true
          });
    // owl carousel script
 $('.carousel').owlCarousel({
    margin:20,
    loop: true,
    dots: true,
    autoplay:true,
    autoplayTimeout:2000,
    autoplayHoverPause:true,
    responsive:{
        0:{
            items:1,
            nav:false
        },
        600:{
            items:2,
            nav:false
        },
        1000:{
            items:3,
            nav:false
        }
    }
});

    // contact form — opens email client with the message
    $('#contact-form').on('submit', function(e){
        e.preventDefault();
        var name = $(this).find('[name="name"]').val().trim();
        var email = $(this).find('[name="email"]').val().trim();
        var subject = $(this).find('[name="subject"]').val().trim();
        var message = $(this).find('[name="message"]').val().trim();
        var body = 'Name: ' + name + '\nEmail: ' + email + '\n\n' + message;
        var mailto = 'mailto:mustafsaidewaz@gmail.com'
            + '?subject=' + encodeURIComponent(subject)
            + '&body=' + encodeURIComponent(body);
        window.location.href = mailto;
    });
      
});
