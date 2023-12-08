<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ env('APP_DESC') }}">
    <title>{{ ucfirst(env('APP_NAME')) }} | Login - {{ env('APP_DESC') }}</title>
    <link rel="shortcut icon" href="/app/img/favicon-white.png" type="image/x-icon" />
    <!-- Bootstrap Core -->
    <link href="/theme/css/bootstrap.min.css" rel="stylesheet">
    <!-- Roboto Font -->
    <link href="//fonts.googleapis.com/css?family=Roboto:400,700,300,500" rel="stylesheet" type="text/css">
    <!-- Admin Core -->
    <link href="/theme/css/nifty.min.css" rel="stylesheet">

    <!--Font Awesome [ OPTIONAL ]-->
    <link href="/theme/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!--Page load progress bar -->
    <script src="/theme/plugins/pace/pace.min.js"></script>

</head>
<body>
<div id="container" class="cls-container">

	<!-- BACKGROUND IMAGE -->
	<div id="bg-overlay" class="bg-img img-balloon" style="background-image: url('/theme/img/bg-img/bg-img-3.jpg');"></div>

	<!-- HEADER -->
	<div class="cls-header cls-header-lg">
		<div class="cls-brand">
			<a class="box-inline" href="/">
				<span class="brand-title">{{ ucfirst(env('APP_NAME')) }} <span class="text-thin">Admin</span></span>
			</a>
		</div>
	</div>

	<!-- LOGIN FORM -->
	<div class="cls-content">
		<div class="cls-content-sm panel">
			<div class="panel-body">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Whoops!</strong> There were some problems with your input.<br><br>
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				<p class="pad-btm">Sign In to your account</p>
				<form id="frm-signin" action="{{ url('/login') }}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="submit" value="1">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-user"></i></div>
							<input type="text" name='email' class="form-control" value="{{ old('email') }}" placeholder="E-mail" required>
						</div>
					</div>


					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-asterisk"></i></div>
							<input type="password" name='password' class="form-control" placeholder="Password" required>
						</div>
					</div>


					<div class="row">
						<div class="col-xs-8 text-left checkbox">
							<label class="form-checkbox form-icon form-text">
							<input type="checkbox" name="remember"> Remember me
							</label>
						</div>

						<div class="col-xs-4">
							<div class="form-group text-right">
							<button class="btn btn-success text-uppercase" type="submit">Sign In</button>
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

<!--Fast Click [ OPTIONAL ]-->
<script src="/theme/plugins/fast-click/fastclick.min.js"></script>

<!-- Admin Core -->
<script src="/theme/js/nifty.min.js"></script>

<script src="/theme/js/demo/bg-images.js"></script>
</body>
</html>