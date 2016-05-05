<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CUIN : @yield('title')</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    @yield('styles')

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <img src="/assets/img/logo_sm.png" alt="CUIN">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                @if (Auth::check())
                    <div class="form-group navbar-form navbar-left" role="search">
                        <input type="text" id="searchkey" class="form-control" placeholder="Search">
                        <img src="/assets/img/loading.gif" class="media-middle srch-loader hidden" width="24px" alt="loading" >
                        <div style="margin-top: -1px;position: absolute; z-index: 100; background: #f8f8f8; padding: 30px; display: none;" id="navSrchBox" class="table-bordered col-md-3"></div>
                    </div>
                @endif

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li><a href="{{ url('/processes') }}">Processes</a></li>
                        <li><a href="{{ url('/customers') }}">Customers</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/company/profile') }}"><i class="fa fa-btn fa-user"></i>Edit profile</a></li>
                                <li><a href="{{ url('/company/profile/settings') }}"><i class="fa fa-btn fa-cog"></i>Settings</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Stripe -->
    <script src="https://js.stripe.com/v2/"></script>

    @yield('scripts')
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script>
        //Header Nav bar Search
        $('#searchkey').keyup( function () {
            var val = $(this).val();
            if (val.length >= 2) {
                $('.srch-loader').toggleClass('hidden');
                $.get("/search", {key: val})
                        .done(function (data) {
                            if (data.result != '') {
                                $('.srch-loader').toggleClass('hidden');
                                $('#navSrchBox').html(data.result).show();
                            }
                            else {
                                $('.srch-loader').toggleClass('hidden');
                                $('#navSrchBox').html('<div class="alert alert-danger" role=alert><span>No results</span></div>').show();
                            }
                        });
            }
            else {
                $("#navSrchBox").hide();
                $("#navSrchBox").html('');
            }
        });

        //Toggle search results when clicking inside input holds previous keyword
        $('#searchkey').click( function () {
            if($("#navSrchBox").html()!='' && $("#navSrchBox").css('display') == 'none')
            {
                $('#navSrchBox').show();
            }
        });

        //Toggle search container display on page click
        $(document).mouseup(function (e)
        {
            var container = $("#navSrchBox");
            // if the target of the click isn't the container...nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0)
            {
                container.hide();
            }
        });
    </script>
</body>
</html>
