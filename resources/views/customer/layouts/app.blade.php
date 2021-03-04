<!DOCTYPE html>
<html>
    <head>
		<title>@yield('title') - {{config('configs.company_name')}}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('landing/images/favicon.ico')}}" type="image/x-icon">
        <link rel="icon" href="{{asset('landing/images/favicon.ico')}}" type="image/x-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900" rel="stylesheet">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="{{asset('landing/css/assets/bootstrap.min.css')}}">

		<!-- Fontawesome Icon -->
        <link rel="stylesheet" href="{{asset('landing/css/assets/font-awesome.min.css')}}">

		<!-- Animate Css -->
        <link rel="stylesheet" href="{{asset('landing/css/assets/animate.css')}}">

        <!-- Owl Slider -->
        <link rel="stylesheet" href="{{asset('landing/css/assets/owl.carousel.min.css')}}">

        <!-- Custom Style -->
        <link rel="stylesheet" href="{{asset('landing/css/assets/normalize.css')}}">
        <link rel="stylesheet" href="{{asset('landing/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('landing/css/assets/responsive.css')}}">

		@yield('stylesheets')
    </head>
    <body>
        <!-- Preloader -->
        <div class="preloader">
            <div class="load-list">
                <div class="load"></div>
                <div class="load load2"></div>
            </div>
        </div>
        <!-- End Preloader -->

        <!-- Top Bar -->
        <section class="top-bar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-4">
                        <div class="top-left d-flex">
                            <div class="lang-box">
                                <span><img src="{{asset('landing/images/fl-id.png')}}" alt="">Indonesia</span>
                            </div>
                            <div class="mny-box">
                                <span>IDR</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-8">
                        <div class="top-right text-right">
                            <ul class="list-unstyled list-inline">
                                @guest
                                    <li class="list-inline-item"><a href="{{route('login')}}"><img src="{{asset('landing/images/login.png')}}" alt="">Login</a></li>  
                                @else
                                    <li class="list-inline-item"><a href=""><img src="{{asset('landing/images/user.png')}}" alt="">Akun Saya</a></li>
                                    <li class="list-inline-item"><a href=""><img src="{{asset('landing/images/checkout.png')}}" alt="">Checkout</a></li>
                                    <li class="list-inline-item"><a href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                      document.getElementById('logout-form').submit();" alt="Logout"><img src="{{asset('landing/images/login.png')}}" alt=""> Logout
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Top Bar -->

        <!-- Logo Area -->
        <section class="logo-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="logo">
                            <a href=""><img src="{{asset(config('configs.app_logo'))}}" alt="" class="img-fluid"></a>
                        </div>
                    </div>
                    <div class="col-md-5 padding-fix">
                        <form action="#" class="search-bar">
                            <input type="text" name="search-bar" placeholder="Ketik produk yang anda cari...">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="carts-area d-flex">
                            <div class="call-box d-flex">
                                <div class="call-ico">
                                    <img src="{{asset('landing/images/call.png')}}" alt="">
                                </div>
                                <div class="call-content">
                                    <span>Kontak Kami</span>
                                    <p>{{config('configs.company_phone')}}</p>
                                </div>
                            </div>
                            <div class="cart-box ml-auto text-center">
                                <a href="" class="cart-btn">
                                    <img src="{{asset('landing/images/cart.png')}}" alt="cart">
                                    <span>0</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Logo Area -->
        @yield('content')
        <!-- Footer Area -->
        <section class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="f-contact">
                            <h5>Informasi Kontak</h5>
                            <div class="f-add">
                                <i class="fa fa-map-marker"></i>
                                <span>Alamat :</span>
                                <p>{{config('configs.company_address')}}</p>
                            </div>
                            <div class="f-email">
                                <i class="fa fa-envelope"></i>
                                <span>Email :</span>
                                <p>{{config('configs.company_email')}}</p>
                            </div>
                            <div class="f-phn">
                                <i class="fa fa-phone"></i>
                                <span>Telepon :</span>
                                <p>{{config('configs.company_phone')}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-sup">
                            <h5>Layanan Pelanggan</h5>
                            <ul class="list-unstyled">
                                <li><a href=""><i class="fa fa-angle-right"></i>Hubungi Kami</a></li>
                                <li><a href=""><i class="fa fa-angle-right"></i>Kebijakan Privasi</a></li>
                                <li><a href=""><i class="fa fa-angle-right"></i>Syarat Dan Ketentuan</a></li>
                                <li><a href=""><i class="fa fa-angle-right"></i>FAQ</a></li>
                                <li><a href=""><i class="fa fa-angle-right"></i>Informasi Pengiriman</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-social">
                            <h5>Ikuti Kami</h5>
                            <ul class="list-unstyled">
                                <li><a href=""><i class="fa fa-facebook"></i>Facebook</a></li>
                                <li><a href=""><i class="fa fa-twitter"></i>Twitter</a></li>
                                <li><a href=""><i class="fa fa-instagram"></i>Instagram</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-down">
                            <h5>Akan Datang</h5>
                            <img src="https://enviostore.com/assets/img/get_in_google_play.png" style="width: 160px;">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="footer-btm">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p>Copyright {{config('configs.app_copyright')}} - {{config('configs.company_name')}}</p>
                    </div>
                </div>
            </div>
            <div class="back-to-top text-center">
                <img src="{{asset('landing/images/backtotop.png')}}" alt="" class="img-fluid">
            </div>
        </section>
        <!-- End Footer Area -->
        <!-- =========================================
        JavaScript Files
        ========================================== -->

        <!-- jQuery JS -->
        <script src="{{asset('landing/js/assets/vendor/jquery-1.12.4.min.js')}}"></script>

        <!-- Bootstrap -->
        <script src="{{asset('landing/js/assets/popper.min.js')}}"></script>
        <script src="{{asset('landing/js/assets/bootstrap.min.js')}}"></script>

        <!-- Owl Slider -->
        <script src="{{asset('landing/js/assets/owl.carousel.min.js')}}"></script>

        <!-- Wow Animation -->
        <script src="{{asset('landing/js/assets/wow.min.js')}}"></script>

        <!-- Price Filter -->
        <script src="{{asset('landing/js/assets/price-filter.js')}}"></script>

        <!-- Mean Menu -->
        <script src="{{asset('landing/js/assets/jquery.meanmenu.min.js')}}"></script>

        <!-- Custom JS -->
        <script src="{{asset('landing/js/plugins.js')}}"></script>
        <script src="{{asset('landing/js/custom.js')}}"></script>
        @stack('scripts')
    </body>
</html>