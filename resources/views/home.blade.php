<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Circular Navigation - Demo 1 | Codrops</title>
        <meta name="description" content="Circular Navigation Styles - Building a Circular Navigation with CSS Transforms | Codrops " />
        <meta name="keywords" content="css transforms, circular navigation, round navigation, circular menu, tutorial" />
        <meta name="author" content="Sara Soueidan for Codrops" />
        <link rel="shortcut icon" href="../favicon.ico">
        <link rel="stylesheet" href="{{ elixir('css/menu/normalize.css') }}">
        <link rel="stylesheet" href="{{ elixir('css/menu/demo.css') }}">
        <link rel="stylesheet" href="{{ elixir('css/menu/component1.css') }}">
        <link rel="stylesheet" href="{{ elixir('css/bootstrap/bootstrap.css') }}">

        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet" type="text/css">

        <!-- CSS ============================================= -->
        <!-- Fontawesome -->
        <link rel="stylesheet" href="{{ elixir('css/slider/font-awesome.min.css') }}">
        <!-- Fancybox -->
        <link rel="stylesheet" href="{{ elixir('css/slider/jquery.fancybox.css') }}">
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ elixir('css/slider/owl.carousel.css') }}">
        <!-- Animate -->
        <link rel="stylesheet" href="{{ elixir('css/slider/animate.css') }}">
        <!-- Main Styles -->
        <link rel="stylesheet" href="{{ elixir('css/slider/main.css') }}">
        <!-- Main Responsive -->
        <link rel="stylesheet" href="{{ elixir('css/slider/responsive.css') }}">
        <link rel="stylesheet" href="{{ elixir('css/slider/slick.css') }}">
        <link rel="stylesheet" href="{{ elixir('css/slider/slick-theme.css') }}">
        <link rel="stylesheet" href="{{ elixir('css/default.css') }}">


        <!-- Modernizer Script for old Browsers -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/vendor/modernizr-2.6.2.min.js') }}"></script>

        <script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-7243260-2']);
_gaq.push(['_trackPageview']);
(function () {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(ga, s);
})();
        </script>
    </head>
    <body class="back-body">
        <form class="form-group">
            <div class="container">
                <div class="component">
                   @yield('content')  
                    <!-- Start Nav Structure -->
                    <button class="cn-button" id="cn-button">+</button>
                    <div class="cn-wrapper" id="cn-wrapper">
                        <ul>
                            <li><a href="#"><span class="icon-picture"></span></a></li>
                            <li><a href="#"><span class="icon-headphones"></span></a></li>
                            <li><a href="#"><span class="icon-home"></span></a></li>
                            <li><a href="#"><span class="icon-facetime-video"></span></a></li>
                            <li><a href="#"><span class="icon-envelope-alt"></span></a></li>
                        </ul>
                    </div>
                    <div id="cn-overlay" class="cn-overlay"></div>
                    <!-- End Nav Structure -->
                </div>
            </div><!-- /container -->
        </form>
        <script type="text/javascript" src="{{ URL::asset('js/jquery-1.10.2.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/bootstrap.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/menu/demo1.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/menu/polyfills.js') }}"></script>
        <!-- For the demo ad only -->   
        <script type="text/javascript" src="{{ URL::asset('js/slider/jquery.nav.js') }}"></script>

        <!-- Portfolio Filtering -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/jquery.mixitup.min.js') }}"></script>

        <!-- Fancybox -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/jquery.fancybox.pack.js') }}"></script>

        <!-- Parallax sections -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/jquery.parallax-1.1.3.js') }}"></script>
        <!-- jQuery Appear -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/jquery.appear.js') }}"></script>
        <!-- countTo -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/jquery-countTo.js') }}"></script>
        <!-- owl carousel -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/owl.carousel.min.js') }}"></script>
        <!-- WOW script -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/wow.min.js') }}"></script>
        <!-- theme custom scripts -->
        <script type="text/javascript" src="{{ URL::asset('js/slider/main.js') }}"></script>
        <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="{{ URL::asset('js/slick/slick.js') }}"></script>
        <script>
$(document).ready(function () {
    $('.home-results').slick({
        dots: false,
        infinite: true,
        speed: 3000,
        slidesToShow: 4,
        slidesToScroll: 4,
        autoplay: false,
        autoplaySpeed: 5000,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
});

        </script>
    </body>
</html>