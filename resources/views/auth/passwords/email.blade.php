<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ ucfirst(env('APP_NAME')) }}">
    <title>{{ ucfirst(env('APP_NAME')) }} | Login - {{ env('APP_DESC') }}</title>
    <link rel="shortcut icon" href="/app/img/favicon-white.png" type="image/x-icon" />
    <!-- Bootstrap Core -->
    <link href="/theme/css/bootstrap.min.css" rel="stylesheet">
    <!-- Roboto Font -->
    <link href="//fonts.googleapis.com/css?family=Roboto:400,700,300,500" rel="stylesheet" type="text/css">
    <!-- Admin Core -->
    <link href="/theme/css/nifty.min.css" rel="stylesheet">

    <!-- Plugins CSS -->
    <link href="/theme/css/plugins.min.css" rel="stylesheet">

    <!--Font Awesome [ OPTIONAL ]-->
    <link href="/theme/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!--Page load progress bar -->
    <script src="/theme/js/pace.min.js"></script>

</head>
<body>
<div id="container" class="cls-container">

    <!-- BACKGROUND IMAGE -->
    <!--<div id="bg-overlay" class="bg-img img-balloon" style="background-image: url('/theme/images/bg-img/bg-img-3.jpg');"></div>!-->

    <!-- HEADER -->
    <div class="cls-header cls-header-lg">
        <div class="cls-brand">
            <a class="box-inline" href="/">
                <span class="brand-title">Reset Password</span>
            </a>
        </div>
    </div>

    <!-- LOGIN FORM -->
    <div class="cls-content">
        <div class="cls-content-sm panel">
            <div class="panel-body">
                 @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <p class="pad-btm">{{ __('Reset Password') }}</p>
                <form id="frm-signin" action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                            <input type="text" name='email' class="form-control" value="{{ old('email') }}" placeholder="E-mail" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group text-right">
                            <button type="submit" class="btn btn-success">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="pad-ver">
            <a href="{{ url('/password/reset') }}" class="btn-link mar-rgt">Forgot password?</a>
            <a href="{{ url('/register') }}" class="btn-link mar-lft">Create a new account</a>
        </div>
    </div>
    <!-- END OF LOGIN FORM -->
</div>
<!-- END OF CONTAINER -->

<!-- MAIN PLUGIN -->

<!-- jQuery Version 2.1.1 -->
<script src="/theme/js/jquery-2.1.1.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="/theme/js/bootstrap.min.js"></script>

<!-- ADMIN PLUGIN & DEMO -->

<!-- Plugins -->
<script src="/theme/js/plugins.min.js"></script>

<!-- Admin Core -->
<script src="/theme/js/nifty.min.js"></script>

<script src="/theme/js/demo/bg-images.js"></script>
</body>
</html>










@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
