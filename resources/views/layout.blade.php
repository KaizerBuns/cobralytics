<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $app['app_desc'] }}">
    <title>{{ $app['app_name'] }} | {{ ucfirst($app['controller_name']) }} - {{ $app['app_desc'] }}</title>
    <link rel="shortcut icon" href="/app/img/favicon-white.png" type="image/x-icon" />

    <!-- Bootstrap Core -->
    <link href="/theme/css/bootstrap.min.css" rel="stylesheet">
    <!-- Roboto Font -->
    <link href="//fonts.googleapis.com/css?family=Roboto:400,700,300,500" rel="stylesheet" type="text/css">
    <!-- Admin Core -->
    <link href="/theme/css/nifty.min.css" rel="stylesheet">
    
    <!-- Plugins CSS -->
    <!--Font Awesome [ OPTIONAL ]-->
    <link href="/theme/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    
    <!--Animate.css [ OPTIONAL ]-->
    <link href="/theme/plugins/animate-css/animate.min.css" rel="stylesheet">

    <link href="/app/js/select2-3.5.0/select2.css" rel="stylesheet"/>
	<link href="/app/css/select2-bootstrap.css" rel="stylesheet"/>

	<!-- jvectormap -->
    <link href="/app/js/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

    <!-- Daterange picker -->
    <link href="/app/js/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
		
	<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="/app/js/jquery.tablesorter/themes/blue/style.css">

    <!-- jQuery Version 2.1.1 -->
	<script src="/theme/js/jquery-2.1.1.min.js"></script>

    <!--Page load progress bar -->
    <script src="/theme/plugins/pace/pace.min.js"></script>
    <!-- ocean.min.css -->
    <link href="/theme/css/themes/type-c/theme-ocean.min.css" rel="stylesheet">
    <!-- tooltipster -->
    <link rel="stylesheet" type="text/css" href="/plugins/tooltipster/css/tooltipster.css" />
    <link href="/app/css/base.css" rel="stylesheet">

<!-- Piwik -->
<?//if(!TESTING){?>
<!--<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//track.cobralytics.com/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//track.cobralytics.com/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
!-->
<?//}?>
<!-- End Piwik Code -->
</head>
<body>
<!--  add class to show additional side menu aside-in aside-bright !-->
<div id="container" class="effect {{ $settings['pref_quick_menu'] == 0 ? 'mainnav-sm' : 'mainnav-lg' }} mainnav-fixed">
	<!-- NAVBAR -->
	<header id="navbar">
		<div id="navbar-container" class="boxed">
			<!-- BRAND LOGO & TEXT -->
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					<img alt="Nifty Admin" src="/app/img/logo-white.png" class="brand-icon">
					<span class="brand-title">
						<span class="brand-text">&nbsp;&nbsp;&nbsp;{{ $app['app_name'] }}</span>
					</span>
				</a>
			</div>
			<!-- END OF BRAND LOGO & TEXT -->

			<!-- NAVBAR DROPDOWN -->
			<div class="navbar-content clearfix">
				<ul class="nav navbar-top-links pull-left">


					<!-- MAINMENU TOGGLE BUTTON -->
					<li class="tgl-menu-btn">
						<a id="toggle-mainnav-btn" href="#">
							<i class="fa fa-navicon fa-lg"></i>
						</a>
					</li>
					<!-- MESSAGES DROPDOWN -->
					<li class="dropdown" style="display:none">

						<!-- Dropdown button -->
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-envelope fa-lg"></i>
							<span class="badge badge-header badge-warning">9</span>
						</a>
						<!-- Dropdown menu -->
						<div class="dropdown-menu dropdown-menu-md with-arrow">
							<div class="pad-all bord-btm">
								<p class="text-lg text-muted text-thin mar-no">You have 3 messages.</p>
							</div>
							<div class="nano scrollable">
								<div class="nano-content">
									<ul class="head-list">
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av2.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Andy sent you a message</div>
													<small class="text-muted">15 minutes ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av4.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Lucy sent you a message</div>
													<small class="text-muted">2 hours ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av3.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Jackson sent you a message</div>
													<small class="text-muted">2 hours ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av5.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Linda sent you a message</div>
													<small class="text-muted">Yesterday</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av6.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Dona sent you a message</div>
													<small class="text-muted">Yesterday</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av4.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Lucy sent you a message</div>
													<small class="text-muted">2 hours ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="media-left">
													<img class="img-circle img-sm" src="/theme/img/av3.png" alt="Profile Picture">
												</span>
												<div class="media-body">
													<div class="text-nowrap">Jackson sent you a message</div>
													<small class="text-muted">2 hours ago</small>
												</div>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<!-- Dropdown footer -->
							<div class="pad-all bord-top">
								<a class="btn-link text-dark box-block" href="#">
									<i class="fa fa-angle-right fa-lg pull-right"></i> Show All Messages
								</a>
							</div>
						</div>
					</li>
					<!-- END OF MESSAGES DROPDOWN -->
					<!-- NOTIFICATION DROPDOWN -->
					<li class="dropdown" style="display:none">
						<!-- Dropdown button -->
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-bell fa-lg"></i>
							<span class="badge badge-header badge-danger">5</span>
						</a>
						<!-- Dropdown menu -->
						<div class="dropdown-menu dropdown-menu-md with-arrow">
							<div class="pad-all bord-btm">
								<p class="text-lg text-muted text-thin mar-no">You have 5 notifications</p>
							</div>
							<div class="nano scrollable">
								<div class="nano-content">
									<ul class="head-list">
										<!-- Dropdown list -->
										<li>
											<a href="#">
												<div class="clearfix">
													<p class="pull-left">Database Repair</p>
													<p class="pull-right">70%</p>
												</div>
												<div class="progress progress-sm">
													<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: 70%;">
														<span class="sr-only">70% Complete</span>
													</div>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a href="#">
												<div class="clearfix">
													<p class="pull-left">Upgrade Progress</p>
													<p class="pull-right">10%</p>
												</div>
												<div class="progress progress-sm">
													<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: 10%;">
														<span class="sr-only">10% Complete</span>
													</div>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<div class="media-left">
													<span class="icon-wrap icon-circle bg-primary">
														<i class="fa fa-comment fa-lg"></i>
													</span>
												</div>
												<div class="media-body">
													<div class="text-nowrap">New comments waiting approval</div>
													<small class="text-muted">15 minutes ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="badge badge-success pull-right">90%</span>
												<div class="media-left">
													<span class="icon-wrap icon-circle bg-danger">
														<i class="fa fa-hdd-o fa-lg"></i>
													</span>
												</div>
												<div class="media-body">
													<div class="text-nowrap">HDD is Full</div>
													<small class="text-muted">50 minutes ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<div class="media-left">
													<span class="icon-wrap bg-info">
														<i class="fa fa-file-word-o fa-lg"></i>
													</span>
												</div>
												<div class="media-body">
													<div class="text-nowrap">Write a news article</div>
													<small class="text-nowrap text-muted">Last Update 8 hours ago</small>
												</div>
											</a>
										</li>
										<!-- Dropdown list -->
										<li>
											<a class="media" href="#">
												<span class="label label-danger pull-right">New</span>
												<div class="media-left">
													<span class="icon-wrap bg-purple">
														<i class="fa fa-comment fa-lg"></i>
													</span>
												</div>
												<div class="media-body">
													<div class="text-nowrap">Comment Sorting</div>
													<small class="text-nowrap text-muted">Last Update 8 hours ago</small>
												</div>
											</a>
										</li>
										<li>
											<a href="#" class="media">
												<div class="media-left">
													<span class="icon-wrap bg-pink">
													<i class="fa fa-user fa-lg"></i>
													</span>
												</div>
												<div class="media-body">
													<div class="text-nowrap">New User Registered</div>
													<small class="text-nowrap text-muted">4 minutes ago</small>
												</div>
											</a>
										</li>
									</ul>
								</div>
							</div>

							<!-- Dropdown footer -->
							<div class="pad-all bord-top">
								<a class="btn-link text-dark box-block" href="#">
									<i class="fa fa-angle-right fa-lg pull-right"></i>Show All Notifications
								</a>
							</div>
						</div>
					</li>
					<!-- END OF NOTIFICATION DROPDOWN -->
					<!-- MEGA MENU  -->
					<li class="dropdown mega-dropdown" style="display:none">

						<!-- Megamenu button -->
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-th-large fa-lg"></i>
						</a>
						<!-- Megamenu Dropdown menu -->
						<div class="dropdown-menu mega-dropdown-menu">
							<div class="clearfix">

								<div class="col-sm-12 col-md-3">
									<div class="text-center bg-purple pad-all">
										<h3 class="text-thin mar-no">Weekend shopping</h3>
										<span class="pad-ver box-inline">
											<span class="icon-wrap icon-wrap-lg icon-circle bg-trans-light">
												<i class="fa fa-shopping-cart fa-4x"></i>
											</span>
										</span>

										<p class="pad-btm">Members get <span class="text-lg text-bold">50%</span> more points. Lorem ipsum dolor sit amet!</p>

										<a href="#" class="btn btn-purple">Learn More...</a>
									</div>
								</div>
								<div class="col-sm-4 col-md-3">
									<ul class="list-unstyled">
										<li class="dropdown-header">Pages</li>
										<li><a href="#">Profile</a></li>
										<li><a href="#">Search Result</a></li>
										<li><a href="#">FAQ</a></li>
										<li><a href="#">Screen Lock</a></li>
										<li><a href="#" class="disabled-link">Disabled</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Icons</li>
										<li><a href="#"><span class="pull-right badge badge-purple">479</span> Font Awesome</a></li>
										<li><a href="#">Skycons</a></li>
									</ul>
								</div>

								<div class="col-sm-4 col-md-3">
									<ul class="list-unstyled">
										<li class="dropdown-header">Mailbox</li>
										<li><a href="#"><span class="pull-right label label-danger">Hot</span>Indox</a></li>
										<li><a href="#">Read Mail</a></li>
										<li><a href="#">Sent Mail</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Feature</li>
										<li><a href="#">Smart navigation</a></li>
										<li><a href="#"><span class="pull-right badge badge-success">6</span>Exclusive plugins</a></li>
										<li><a href="#">Lot of themes</a></li>
										<li><a href="#">Transition effects</a></li>
									</ul>
								</div>

								<div class="col-sm-4 col-md-3">
									<ul class="list-unstyled">
										<li class="dropdown-header">Components</li>
										<li><a href="#">Table</a></li>
										<li><a href="#">Charts</a></li>
										<li><a href="#">Forms</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Newsletter</li>
										<form role="form" class="form">
											<div class="form-group">
												<input type="email" placeholder="Enter email" id="email" class="form-control">
											</div>
											<button class="btn btn-primary btn-block" type="submit">Submit</button>
										</form>
									</ul>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<ul class="nav navbar-top-links pull-right">
					<!-- USER DROPDOWN -->
					<li id="dropdown-user" class="dropdown">
						<!-- Dropdown button -->
						<a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
							<span class="pull-right">
								<img class="img-circle img-user media-object" src="/theme/img/av1.png" alt="Profile Picture">
							</span>
						<div class="username hidden-xs">{{ $user->get_name() }}</div>
						</a>
						<!-- Dropdown menu -->
						<div class="dropdown-menu dropdown-menu-md dropdown-menu-right with-arrow panel-default">

							<!-- Dropdown heading  -->
							<ul class="head-list">
								<!-- Dropdown list -->
								<li>
									<a href="/member/account/">
										<i class="fa fa-user fa-fw fa-lg"></i>
										<span class="text-nowrap">Profile</span>
									</a>
								</li>
								<!-- Dropdown list -->
								<li style="display:none">
									<a class="clearfix" href="#">
										<span class="badge badge-danger pull-right">9</span>
										<span class="pull-left">
											<i class="fa fa-envelope fa-fw fa-lg"></i>
											<span>Messages</span>
										</span>
									</a>
								</li>
								<!-- Dropdown list -->
								<li style="display:none">
									<a class="clearfix" href="#">
										<span class="label label-success pull-right">New</span>
										<span class="pull-left">
											<i class="fa fa-gear fa-fw fa-lg"></i>
											<span>Settings</span>
										</span>
									</a>
								</li>
								<!-- Dropdown list -->
								<li style="display:none">
									<a href="#">
										<i class="fa fa-question-circle fa-fw fa-lg"></i>
										<span>Help</span>
									</a>
								</li>
							</ul>
							<!-- Dropdown footer -->
							<div class="pad-all bord-top">
								<a href="{{ url('/logout') }}" class="btn btn-sm btn-primary btn-labeled fa fa-sign-out icon-lg">Logout</a>
							</div>
						</div>
					</li>
					<!-- END OF USER DROPDOWN -->
				</ul>
			</div>
			<!-- END OF NAVBAR DROPDOWN -->
		</div>
	</header>
	<!-- END OF NAVBAR -->
    <!-- CONTENT -->
    <div class="boxed">
        <!-- CONTENT CONTAINER -->
        <div id="content-container">
        	@include('partials.header')
        	@include('partials.breadcrumbs')
            <!-- PAGE CONTENT -->
            <div id="page-content">
				@include('partials.alerts')
				@yield('content')
            </div>
            <!-- END OF PAGE CONTENT -->         

        </div>
        <!-- END OF CONTENT CONTAINER -->
        @include('partials.menu')
		<!-- ADDITIONAL SIDEBAR -->
		<? //$this->render('_side_menu_additional.php', false)?>
    </div>
    <!--END OF CONTENT -->

	 <!-- FOOTER -->
        <!--===================================================-->
        <footer id="footer">

            <!-- Visible when footer positions are fixed -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="show-fixed pull-right">
                <ul class="footer-list list-inline">
                    <li>
                        <p class="text-sm">SEO Proggres</p>
                        <div class="progress progress-sm progress-light-base">
                            <div style="width: 80%" class="progress-bar progress-bar-danger"></div>
                        </div>
                    </li>

                    <li>
                        <p class="text-sm">Online Tutorial</p>
                        <div class="progress progress-sm progress-light-base">
                            <div style="width: 80%" class="progress-bar progress-bar-primary"></div>
                        </div>
                    </li>
                    <li>
                        <button class="btn btn-sm btn-dark btn-active-success">Checkout</button>
                    </li>
                </ul>
            </div>



            <!-- Visible when footer positions are static -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="hide-fixed pull-right pad-rgt">Currently v2.2.3</div>



            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <!-- Remove the class name "show-fixed" and "hide-fixed" to make the content always appears. -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

            <p class="pad-lft">&#0169; <?=date("Y")?> Cobralytics</p>



        </footer>
        <!--===================================================-->
        <!-- END FOOTER -->


	<!-- SCROLL TOP BUTTON -->
	 <button id="scroll-top" class="btn"><i class="fa fa-chevron-up"></i></button>
</div>
<!-- END OF CONTAINER -->

<!-- Bootstrap Core JavaScript -->
<script src="/theme/js/bootstrap.min.js"></script>

<!-- Plugins -->
<!--Fast Click [ OPTIONAL ]-->
<script src="/theme/plugins/fast-click/fastclick.min.js"></script>

<!--Bootbox [ OPTIONAL ]-->
<script src="/theme/plugins/bootbox/bootbox.min.js"></script>

<!--Sparkline [ OPTIONAL ]-->
<script src="/theme/plugins/sparkline/jquery.sparkline.min.js"></script>

<!--Skycons [ OPTIONAL ]-->
<script src="/theme/plugins/skycons/skycons.min.js"></script>

<!-- Admin Core -->
<script src="/theme/js/nifty.min.js"></script>

<!-- jvectormap -->
<script src="/app/js/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
<script src="/app/js/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>

<script src="/app/js/select2-3.5.0/select2.min.js"></script>

<!-- daterangepicker -->
<script src="/app/js/daterangepicker/moment.js" type="text/javascript"></script>
<script src="/app/js/daterangepicker/daterangepicker.js" type="text/javascript"></script>

<!-- TableSorter -->
<script type="text/javascript" charset="utf8" src="/app/js/jquery.tablesorter/jquery.tablesorter.min.js"></script>
		
<!-- HighCharts --> 
<script src="/app/js/Highcharts-4.0.4/js/highcharts.js"></script>
<script src="/app/js/Highcharts-4.0.4/js/themes/grid-light.js"></script>

<link rel="stylesheet" href="/app/js/jquery.validationEngine/css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<script src="/app/js/jquery.validationEngine/jquery.validationEngine-en.js" type="text/javascript" language="javascript"></script>
<script src="/app/js/jquery.validationEngine/jquery.validationEngine.js" type="text/javascript" language="javascript"></script>
<script src="/app/js/jquery-validation-1.13.0/dist/jquery.validate.min.js" type="text/javascript" language="javascript"></script>
<script src="/app/js/jquery-validation-1.13.0/dist/additional-methods.min.js" type="text/javascript" language="javascript"></script>

 <!-- Tooltipster -->  
<script type="text/javascript" src="/plugins/tooltipster/js/jquery.tooltipster.min.js"></script>

<script src="/app/js/base.js"></script>
<script>
	
		$(function() {
			$('.cmd-tip').tooltip();	
			$('.cobra-select2').select2(); 

			$('#toggle-mainnav-btn').on('click', function(){
				$.niftyNav('colExpToggle');
				$.ajax({url: '/ajax/switch_menu/'});
			});
		});
	
</script>
</body>
</html>