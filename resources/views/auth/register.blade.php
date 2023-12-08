<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ env('APP_DESC') }}">
    <title>{{ ucfirst(env('APP_NAME')) }} | Register - {{ env('APP_DESC') }}</title>

    <!--STYLESHEET-->
    <!--=================================================-->

    <!--Open Sans Font [ OPTIONAL ] -->
     <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin" rel="stylesheet">

    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="/theme/css/bootstrap.min.css" rel="stylesheet">

    <!--Nifty Stylesheet [ REQUIRED ]-->
    <link href="/theme/css/nifty.min.css" rel="stylesheet">

    <!--Font Awesome [ OPTIONAL ]-->
    <link href="/theme/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!--SCRIPT-->
    <!--=================================================-->

    <!--Page Load Progress Bar [ OPTIONAL ]-->
    <link href="/theme/plugins/pace/pace.min.css" rel="stylesheet">
    <script src="/theme/plugins/pace/pace.min.js"></script>

</head>

<body>
	<div id="container" class="cls-container">


		<!-- BACKGROUND IMAGE -->
		<!--===================================================-->
		<div id="bg-overlay" class="bg-img img-balloon"></div>

		<!-- HEADER -->
		<!--===================================================-->
		<div class="cls-header cls-header-lg">
			<div class="cls-brand">
				<a class="box-inline" href="/">
					<!-- <img alt="Nifty Admin" src="img/logo.png" class="brand-icon"> -->
					<span class="brand-title">{{ __('Register') }}</span>
				</a>
			</div>
		</div>

		<!-- REGISTRATION FORM -->
		<!--===================================================-->
		<div class="cls-content">
			<div class="cls-content-lg panel">
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
					<p class="pad-btm">Create an account</p>
					<form id="frm-signin" action="{{ route('register') }}" method="POST">
					@csrf
					<input type="hidden" name="submit" value="1">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-male"></i></div>
										<input type="text" name='name' class="form-control" value="{{ old('name') }}" placeholder="Full name" required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-envelope"></i></div>
										<input type="text" name='email' class="form-control" value="{{ old('email') }}" placeholder="E-mail" required>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="password" name='password' class="form-control" placeholder="Password" required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-asterisk"></i></div>
										<input type="password" name='password_confirmation' class="form-control" placeholder="Confirm Password" required>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-8 text-left checkbox">
								<label class="form-checkbox form-icon">
									<input type="checkbox" name="terms" value="1"> I agree with the Terms and Conditions
								</label>
							</div>
							<div class="col-xs-4">
								<div class="form-group text-right">
									<button class="btn btn-success text-uppercase" type="submit">Sign Up</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="pad-ver">
				<a href="{{ url('/password/reset') }}" class="btn-link mar-rgt">Forgot password?</a>
				<a href="{{ url('/login') }}" class="btn-link mar-lft">Already have an account</a>
			</div>
		</div>

	</div>
	<!--===================================================-->
	<!-- END OF CONTAINER -->

    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--jQuery [ REQUIRED ]-->
    <script src="/theme/js/jquery-2.1.1.min.js"></script>

    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="/theme/js/bootstrap.min.js"></script>

    <!--Fast Click [ OPTIONAL ]-->
    <script src="/theme/plugins/fast-click/fastclick.min.js"></script>

    <!--Nifty Admin [ RECOMMENDED ]-->
    <script src="/theme/js/nifty.min.js"></script>
    </body>
</html>