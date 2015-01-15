<html>
<head>
	<title>AOD | Squad Management</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<script src="/public/js/libraries/jquery-2.1.1.min.js"></script>
	<script src="/public/js/libraries/jquery-ui.min.js"></script>
	<script src="/public/js/libraries/jquery.easing.min.js"></script>
	<script src="/public/js/libraries/jquery.powertip.min.js"></script>

	<script src="/public/js/libraries/bootstrap.min.js"></script>
	<script src="/public/js/libraries/jquery.dataTables.min.js"></script>
	<script src="/public/js/libraries/dataTables.bootstrap.js"></script>
	<script src="/public/js/libraries/dataTables.tableTools.min.js"></script>
	<script src="/public/js/libraries/jquery.bootstrap.wizard.min.js"></script>

	<!--<link rel="stylesheet" type="text/css" href="/public/css/lumen.min.css">		-->
	<link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css">	
	<link rel="stylesheet" type="text/css" href="/public/css/bootstrap-theme.min.css">	
	<link rel="stylesheet" type="text/css" href="/public/css/jquery.powertip.min.css">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">	
	<link href="//cdn.datatables.net/responsive/1.0.3/css/dataTables.responsive.css" rel="stylesheet">	
	<link href="/public/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="/public/css/dataTables.tableTools.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="/public/css/style.css">
</head>
<body>

	<!-- modal for ajax dialogs -->
	<div class="modal viewPanel fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="viewer fadeIn animate"></div>
			</div>
		</div>
	</div>

	<div id="wrap">
		<div class="push-top"></div>
		
		<div class="navbar navbar-default navbar-nav navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/"><i class="fa fa-check-square-o"></i> <strong>AOD</strong> <small>Squad Management</small></a>
				</div>

				<?php if (isLoggedIn() && ($userRole > 0)) { ?>
				
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">

						<!-- divisions -->
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Divisions <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<?php echo $game_list ?>								
							</ul>
						</li>


						<!-- notifications menu -->

						<!--
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<span class="count">100+</span> Notifications <span class="caret"></span>
							</a>
							<div class="popup dropdown-menu">
								<ul class="activity">
									<li>
										<i class="fa fa-clock-o fa-2x text-danger"></i>
										<div>You have <a href="#">3 recruits</a> awaiting promotion!
											<span>14 minutes ago</span>
										</div>
									</li>
									<li>
										<i class="fa fa-angle-double-up fa-2x text-success"></i>
										<div>
											<a href="#">CupOHemlock</a> promoted <a href="#">GinaLou</a> to Master Super General
											<span>14 minutes ago</span>
										</div>
									</li>
									<li>
										<i class="fa fa-user fa-2x text-success"></i>
										<div><a href="#">31drew31</a> added <a href="#">Rct Jonesgirl</a> to <a href="#">Platoon 1</a>
											<span>About 2 hours ago</span>
										</div>
									</li>
									<li>
										<i class="fa fa-comment text-primary fa-2x"></i>
										<div>
											<a href="#">Redguard</a> posted a <a href="#">comment</a> on Platoon 2's <a href="#">notes</a>
											<span>5 minutes ago</span>
										</div>
									</li>

									<li>
										<i class="fa fa-flag fa-2x text-danger"></i>
										<div><a href="#">Guybrush</a> removed <a href="#">JoeSchmoe</a> from <a href="#">Platoon 2</a>
											<span>About 7 hours ago</span>
										</div>
									</li>

									<li>
										<i class="fa fa-angle-double-up fa-2x text-success"></i>
										<div>
											<a href="#">CupOHemlock</a> promoted <a href="#">GinaLou</a> to Master Super General
											<span>14 minutes ago</span>
										</div>
									</li>
									<li>
										<i class="fa fa-comment text-primary fa-2x"></i>
										<div>
											<a href="#">Redguard</a> posted a <a href="#">comment</a> on Platoon 2's <a href="#">discussion feed</a>
											<span>35 minutes ago</span>
										</div>
									</li>
									<li>
										<i class="fa fa-flag fa-2x text-danger"></i>
										<div><a href="#">Guybrush</a> removed <a href="#">JoeSchmoe</a> from <a href="#">Platoon 2</a>
											<span>About 2 hours ago</span>
										</div>
									</li>
								</ul>
							</div>
						</li> -->
						<!-- end notifications menu -->

						<li class="dropdown">

							<a href="#" class="dropdown-toggle" data-toggle="dropdown">User CP<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li class="disabled"><a href="#" disabled><?php echo $curUser ?><span class="pull-right"><?php echo $avatar; ?></span></a></li>
								<li class="divider"></li>
								<li><a href="#" data-toggle="pill" class="messages-btn"> Messages<span class="badge pull-right">42</span></a></li>
								<li><a href="#" data-toggle="pill" class="settings-btn"> Settings</a></li>
								<li><a href="http://www.clanaod.net/forums/member.php?u=<?php echo $forumId; ?>" target="_blank"> Forum profile</a></li>
								<li class="divider"></li>
								<li><a href="#" data-toggle="pill" class="logout-btn"><i class="fa fa-lock pull-right"></i> Logout</a></li>
							</ul>
						</li>

						<li>
							<a href="/admin" role="button">Admin CP</a>
							
						</li>

					</ul>
				</div><!--/.nav-collapse -->

				<?php } else if (isLoggedIn()) { ?>

				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">User CP<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li class="disabled"><a href="#" disabled><?php echo ucwords($curUser) . " (". getUserRoleName($userRole). ")"; ?></a></li>
								<li class="divider"></li>
								<li><a href="#" data-toggle="pill" class="logout-btn"><i class="fa fa-lock pull-right"></i> Logout</a></li>
							</ul>
						</li>

					</ul>
				</div>

				<?php } else { ?>

				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="navbar-text text-muted">Not logged in</li>
					</ul>
				</div>

				<?php } ?>		
			</div>
		</div>