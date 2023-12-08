<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ env('APP_DESC') }}">
    <title>{{ ucfirst(env('APP_NAME')) }} | Reset Password - {{ env('APP_DESC') }}</title>
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
	<!--<div id="bg-overlay" class="bg-img img-balloon" style="background-image: url('/theme/images/bg-img/bg-img-3.jpg');"></div>!-->

	<!-- HEADER -->
	<div class="cls-header cls-header-lg">
		<div class="cls-brand">
			<a class="box-inline" href="index.html">
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
				<p class="pad-btm">Reset Password</p>
				<form id="frm-signin" action="{{ url('/password/email') }}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="submit" value="1">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-user"></i></div>
							<input type="text" name='email' class="form-control" value="{{ old('email') }}" placeholder="E-mail" required>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-4">
							<div class="form-group text-right">
							<button class="btn btn-success text-uppercase" type="submit">Send Password Reset Link</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="pad-ver">
			<a href="{{ url('/login') }}" class="btn-link mar-rgt">I remember my password!</a>
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