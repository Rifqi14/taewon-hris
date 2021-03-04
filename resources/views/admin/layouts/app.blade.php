<!DOCTYPE html>
<html>

<head>
    <title>@yield('title') - {{config('configs.app_name')}}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{asset('adminlte/component/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/component/Ionicons/css/ionicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/component/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/component/select2/css/select2.bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/component/iCheck/all.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/component/gritter/css/jquery.gritter.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/css/style.min.css')}}">
    @yield('stylesheets')
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="sidebar-mini accent-{{config('configs.app_theme')}} text-sm layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-{{config('configs.app_theme')}} navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>

            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.logout') }}"
                onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                </li>
            </ul>

        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light-{{config('configs.app_theme')}} elevation-4">
            <!-- Brand Logo -->
            <a href="{{route('admin.home')}}" class="brand-link navbar-{{config('configs.app_theme')}}">
                <img src="{{asset(config('configs.app_icon'))}}" alt="{{config('configs.app_name')}}" class="brand-image img-circle elevation-3">
                <span class="brand-text text-light">{{config('configs.app_name')}}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    {{-- <div class="image">
                        <img src="{{asset('adminlte/images/user2-160x160.jpg')}}" class="img-circle elevation-2"
                            alt="{{ Auth::guard('admin')->user()->name }}">
                    </div> --}}
                    <div class="info">
                        <a href="{{route('user.info')}}" class="d-block">{{ Auth::guard('admin')->user()->name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        {!!buildMenuAdmin($menuaccess,0,@$menu_active)!!}
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark"> @yield('title')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
                                @stack('breadcrump')
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
            <div class="modal fade" id="select-role" class="modal hide fade in" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Pilih Unit</h4>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                            @foreach(Auth::guard('admin')->user()->sites()->get() as $site)
                                <li class="list-group-item ">
                                    <a href="{{url('admin/site/set/'.$site->id)}}" class="font-bold"><strong>{{$site->name}}</strong></a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('admin.logout') }}"
                            onclick="event.preventDefault();
                                          document.getElementById('logout-form').submit();" class="btn btn-{{config('configs.app_theme')}} text-white" ><i class="fas fa-power-off"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="main-footer">
            <strong>Copyright &copy; {{config('configs.app_copyright')}}.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 0.0.1
            </div>
        </footer>
    </div>
    <script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('adminlte/component/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('adminlte/component/inputmask/jquery.inputmask.js')}}"></script>
    <script src="{{asset('adminlte/component/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('adminlte/component/gritter/js/jquery.gritter.min.js')}}"></script>
    <script src="{{asset('adminlte/component/iCheck/icheck.min.js')}}"></script>
    <script src="{{asset('js/helper.js')}}"></script>
    <script src="{{asset('adminlte/component/bootbox/bootbox.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            var url = "{!! url('admin/' . 'attendanceapproval') !!}";
            var urlNow = window.location.href;
            if (urlNow.indexOf(url) === -1) {
                localStorage.clear();
            }
        });
        function openURL(href){
            window.history.pushState({href: href}, '', href);
            document.location = href; 
        }
        function backurl(){
            window.history.go(-1)
        }
        $(function () {
            $(document).on("keydown", function (e) {
                if (e.which === 8 && !$(e.target).is("input, textarea")) {
                    e.preventDefault();
                    backurl();
                }
            });
            // $(document).on('click', 'a', function () {
            //     openURL($(this).attr("href"));
            //     return false; //intercept the link
            // });  
            @if(!$sitesession)
                //$('#select-role').modal('show');
            @endif
            // $(".nav-sidebar").find("a[href='{{@$menu_active}}']").addClass("active");
            // $(".nav-sidebar").find("a[href='{{@$menu_active}}']").closest('.has-treeview').find("a:first")
            //     .addClass('active');
            // $(".nav-sidebar").find("a[href='{{@$menu_active}}']").closest('.has-treeview').addClass(
            //     "menu-open");
        })

    </script>
    <script src="{{asset('adminlte/js/adminlte.min.js')}}"></script>
    @stack('scripts')
</body>

</html>
