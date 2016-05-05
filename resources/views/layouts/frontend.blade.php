<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
                <a class="navbar-brand" href="{{ url('/'.Auth::guard('customer')->user()->hash) }}">
                    <img src="/assets/img/logo_sm.png" alt="CUIN">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    @if(isset($processDetails) && $processDetails->light == 1)
                     <li><svg width="50" height="50" style="padding-top: 10px">
                        <circle cx="15" cy="15" r="10" stroke="black" stroke-width="1" fill="red" />
                    </svg></li>
                @endif
                @if(isset($processDetails) && $processDetails->light == 2)
                    <li><svg width="50" height="50" style="padding-top: 10px">
                        <circle cx="15" cy="15" r="10" stroke="black" stroke-width="1" fill="orange" />
                    </svg></li>
                @endif
                @if(isset($processDetails) && $processDetails->light == 3)
                    <li><svg width="50" height="50" style="padding-top: 10px">
                        <circle cx="15" cy="15" r="10" stroke="black" stroke-width="1" fill="green" />
                    </svg></li>
                @endif
                    @if(isset($processes) && $processes)
                    <li class="dropdown-menu" style="border: none; box-shadow: none; background-color: transparent;">
                        <div class="btn-group">
                        {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Processes <span class="caret"></span></a>--}}
                        <button type="button" class="btn btn-default">Processes</button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>

                            <ul class="dropdown-menu">
                                @foreach($processes as $process)
                                <li><a href="{{ url('/'.$process->hash.'?pid='.$process->id) }}">{{ title_case($process->title) .' ('.$process->reference_id.')' }}</a></li>
                                @endforeach
                            </ul>

                        </div>
                    </li>
                    @endif
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ title_case(Auth::guard('customer')->user()->name) }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/customer/password/change') }}"><i class="fa fa-btn fa-cog"></i>Change Password</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="{{ url('/customer/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
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

    @yield('scripts')
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
