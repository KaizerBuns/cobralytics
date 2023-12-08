<!-- MAIN NAVIGATION : MENU -->
<!--===================================================-->
<nav id="mainnav-container" data-sm="mainnav-sm" data-all="mainnav-lg">
	<div id="mainnav">
		<!--Shortcut buttons-->
		<!--================================-->
		<div id="mainnav-shortcut">
			<!--<ul class="list-unstyled">
				<li class="col-xs-4" data-content="Additional Sidebar">
					<a id="demo-toggle-aside" class="shortcut-grid" href="#">
						<i class="fa fa-magic"></i>
					</a>
				</li>
				<li class="col-xs-4" data-content="Notification">
					<a id="demo-alert" class="shortcut-grid" href="#">
						<i class="fa fa-bullhorn"></i>
					</a>
				</li>
				<li class="col-xs-4" data-content="Page Alerts">
					<a id="demo-page-alert" class="shortcut-grid" href="#">
						<i class="fa fa-bell"></i>
					</a>
				</li>
			</ul>!-->
		</div>
		<!--================================-->
		<!--End shortcut buttons-->
		<div id="mainnav-menu-wrap">
			<div class="nano">
				<div class="nano-content" style="{{ $user->has_first_project() ? '' : 'display:none;' }}">
					<ul id="mainnav-menu" class="list-group">
						<!-- Menu list item-->
						<li class="{{ ($app['controller_name'] == 'project' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-th"></i>
								<span class="menu-title">
									<strong>Projects</strong>
								</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'project' ? 'in':'') }}">
								@foreach($projects as $p)
									<li><a href='/member/project/?view=set&project_id={{ $p->id }}'>{!! $my_project->id == $p->id ? '<i class="fa fa-check"></i>' : '' !!} {{ $p->name }}</a></li>
								@endforeach
								<li class="list-divider"></li>
								<li><a href="/member/project/?view=manage"> Manage</a></li>
								<li><a href="/member/project/?view=new"> New Project</a></li>
							</ul>
						</li>
						<li class="{{ ($app['controller_name'] == 'dashboard' ? 'active-link active':'') }}">
							<a href="/member/dashboard/">
								<i class="fa fa-dashboard"></i>
								<span class="menu-title">Dashboard</span>
								<i class="fa arrow"></i>
							</a>
						</li>
						@if($user->is_enabled('reports'))
						<li class="{{ ($app['controller_name'] == 'report' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-bar-chart-o"></i>
								<span class="menu-title">Reports</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'report' ? 'in':'') }}">
								<li><a href="/member/report/?view=overview&tab=project">Overview</a></li>
								<li><a href="/member/report/?view=custom">Custom</a></li>
							</ul>
						</li>
						@endif
						@if($user->is_enabled('analytics'))
						<!--<li class="{{ ($app['controller_name'] == 'analytics' ? 'active-link active':'') }}">
							<a href="/member/analytics/">
								<i class="fa fa-bar-chart-o"></i>
								<span class="menu-title">Analytics</span>
								<i class="fa arrow"></i>
							</a>
						</li>
						!-->
						@endif
						@if($user->is_enabled('campaigns'))
						<li class="list-header">Affiliate Tools</li>
						<li class="{{ ($app['controller_name'] == 'campaign' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-link"></i>
								<span class="menu-title">Campaigns</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'campaign' ? 'in':'') }}">
								<li><a href="/member/campaign/?view=new">New</a></li>
								<li><a href="/member/campaign/?view=manage">Manage</a></li>
							</ul>
						</li>
						<li class="{{ ($app['controller_name'] == 'offer' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-cubes"></i>
								<span class="menu-title">Offers</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'offer' ? 'in':'') }}">
								<li><a href="/member/offer/?view=new">New</a></li>
								<li><a href="/member/offer/?view=manage">Manage</a></li>
								<li><a href="/member/offer/?view=bulk">Bulk Upload</a></li>
							</ul>
						</li>
						<li class="{{ ($app['controller_name'] == 'pixel' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-bolt"></i>
								<span class="menu-title">Pixels</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'pixel' ? 'in':'') }}">
								<li><a href="/member/pixel/?view=new">New</a></li>
								<li><a href="/member/pixel/?view=manage">Manage</a></li>
							</ul>
						</li>
						@endif
						<li class="list-header">Management Tools</li>
						<li class="{{ ($app['controller_name'] == 'domain' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-puzzle-piece"></i>
								<span class="menu-title">Domains</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'domain' ? 'in':'') }}">
								<li><a href="/member/domain/?view=new">New</a></li>
								<li><a href="/member/domain/?view=manage">Manage</a></li>
                                <li>
                                    <a href="#">Manage A-Z<i class="arrow"></i></a>
                                    <!--Submenu-->
                                    <ul class="collapse">
                                       	<li><a href="/member/domain/?view=manage&search[group]=09">Search 0-9</a></li>
										<li><a href="/member/domain/?view=manage&search[group]=AE">Search A-E</a></li>
										<li><a href="/member/domain/?view=manage&search[group]=FJ">Search F-J</a></li>
										<li><a href="/member/domain/?view=manage&search[group]=KO">Search K-O</a></li>
										<li><a href="/member/domain/?view=manage&search[group]=PT">Search P-T</a></li>
										<li><a href="/member/domain/?view=manage&search[group]=UZ">Search U-Z</a></li>
                                    </ul>
                                </li>
							</ul>
						</li>
						@if($user->is_enabled('campaigns'))
						<li class="{{ ($app['controller_name'] == 'traffic' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-signal"></i>
								<span class="menu-title">Sources</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'traffi' ? 'in':'') }}">
								<li><a href="/member/traffic/?view=new">New</a></li>
								<li><a href="/member/traffic/?view=manage">Manage</a></li>
							</ul>
						</li>
						<!--
						<li>
							<a href="#">
								<i class="fa fa-eye-slash"></i>
								<span class="menu-title">Creatives</span>
								<i class="fa arrow"></i>
							</a>
							<ul class="collapse">
								<li><a href="/member/creative/?view=new">New</a></li>
								<li><a href="/member/creative/?view=manage">Manage</a></li>
							</ul>
						</li>
						!-->
						@endif
						@if($user->is_enabled('offers'))
						<li class="{{ ($app['controller_name'] == 'advertiser' ? 'active-link active':'') }}">
							<a href="#">
								<i class="fa fa-paper-plane"></i>
								<span class="menu-title">Advertisers</span>
								<i class="fa arrow"></i>
							</a>
							<!-- Menu sub -->
							<ul class="collapse {{ ($app['controller_name'] == 'advertiser' ? 'in':'') }}">
								<li><a href="/member/advertiser/?view=new">New</a></li>
								<li><a href="/member/advertiser/?view=manage">Manage</a></li>
							</ul>
						</li>
						@endif
						@if($user->is_admin())
							<li class="list-header">Admin Tools</li>
							<li class="{{ ($app['controller_name'] == 'service' ? 'active-link active':'') }}">
								<a href="#">
									<i class="fa fa-wrench"></i>
									<span class="menu-title">Services</span>
									<i class="fa arrow"></i>
								</a>
								<ul class="collapse {{ ($app['controller_name'] == 'service' ? 'in':'') }}">
									<li><a href="/member/service/?view=new">New</a></li>
									<li><a href="/member/service/?view=manage">Manage</a></li>
								</ul>
							</li>
							<li class="{{ ($app['controller_name'] == 'vertical' ? 'active-link active':'') }}">
								<a href="#">
									<i class="fa fa-wrench"></i>
									<span class="menu-title">Verticals</span>
									<i class="fa arrow"></i>
								</a>
								<!-- Menu sub -->
								<ul class="collapse {{ ($app['controller_name'] == 'vertical' ? 'in':'') }}">
									<li><a href="/member/vertical/?view=new">New</a></li>
									<li><a href="/member/vertical/?view=manage">Manage</a></li>
								</ul>
							</li>
							<li class="{{ ($app['controller_name'] == 'admin' ? 'active-link active':'') }}">
								<a href="#">
									<i class="fa fa-wrench"></i>
									<span class="menu-title">All Users</span>
									<i class="fa arrow"></i>
								</a>
								<ul class="collapse {{ ($app['controller_name'] == 'service' ? 'in':'') }}">
									<li><a href="/member/admin/?section=user&view=new">New</a></li>
									<li><a href="/member/admin/?section=user&view=manage">Manage</a></li>
								</ul>
							</li>
							<li>
								<a href="/member/admin/?section=domains&view=manage">
									<i class="fa fa-wrench"></i>
									All Domains
								</a>
							</li>
							<!--
							<li>
								<a href="/member/admin/?section=dnswings&view=manage">
									DNSWings
								</a>
							</li>
							!-->
							@if($user->is_enabled('monitors'))
							<!--
								<li class="{{ ($app['controller_name'] == 'monitor' ? 'active-link active':'') }}">
									<a href="#">
										<i class="fa fa-exchange"></i>
										<span class="menu-title">Monitors</span>
										<i class="fa arrow"></i>
									</a>
									<ul class="collapse {{ ($app['controller_name'] == 'monitor' ? 'in':'') }}">
										<li><a href="/member/monitor/?view=new">New</a></li>
										<li><a href="/member/monitor/?view=manage">Manage</a></li>
									</ul>
								</li>
							!-->
							@endif
						@endif
					</ul>
					@if($user->is_admin())
					<!-- MAIN NAVIGATION : WIDGET -->
					<!--===================================================-->
					<div class="mainnav-widget">
						<!-- Show the button on small navigation -->
						<div class="show-small">
							<a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
								<i class="fa fa-desktop"></i>
							</a>
						</div>
						<!-- hide element on small navigation -->
						<div id="demo-wg-server" class="hide-small mainnav-widget-content">
							<ul class="list-group">
								<li class="list-header pad-no pad-ver">Server Status</li>
								<li class="mar-btm">
									<div class="clearfix">
										<p class="pull-left"><small>Name</small></p>
										<p class="pull-right label label-primary"><?=gethostname()?></p>
									</div>
								</li>
									<li class="mar-btm">
									<div class="clearfix">
										<p class="pull-left"><small>Report</small></p>
										<p class="pull-right label label-info"><?=env('APP_REPORT')?></p>
									</div>
								</li>
								<li class="mar-btm">
									<div class="clearfix">
										<p class="pull-left"><small>MySQL Logging</small></p>
										<p class="pull-right label label-success"><?=env('LOG_MYSQL') == 1 ? 'On' : 'Off'?></p>
									</div>
								</li>
								<li class="mar-btm">
									<div class="clearfix">
										<p class="pull-left"><small>CrateDB Logging</small></p>
										<p class="pull-right label label-success"><?=env('LOG_CRATEDB') == 1 ? 'On' : 'Off'?></p>
									</div>
								</li>
							</ul>
						</div>
					</div>
					@endif
					<!-- END OF MAIN NAVIGATION : WIDGET -->
				</div>
			</div>
		</div>
	<!-- END OF MAIN NAVIGATION : MENU -->
	</div>
</nav>
<!-- END OF MAIN NAVIGATION -->