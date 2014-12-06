<html>
<head>
	<title>AOD | Squad Management</title>

	<meta name="viewport" content="width=device-width, initial-scale=.0, maximum-scale=1.0, user-scalable=no">
	
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

	<link rel="stylesheet" type="text/css" href="/aod_rct/public/css/cyborg.min.css">		
	<link rel="stylesheet" type="text/css" href="/aod_rct/public/css/style.css">		
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">	

</head>
<body>
	<div id="wrap">
		<div class="push-top"></div>
		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/aod_rct"><i class="fa fa-check-square-o"></i> <strong>AOD</strong> <small>Game Management</small></a>
				</div>

				<?php if (isLoggedIn()) { ?>
				
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">User CP<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#" data-toggle="pill">Action</a></li>
								<li><a href="#" data-toggle="pill">Another action</a></li>
								<li class="divider"></li>
								<li><a href="#" data-toggle="pill" class="logout-btn">Logout</a></li>
							</ul>
						</li>
					</ul>

				</div><!--/.nav-collapse -->

				<?php } else { ?>

				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a class="text-muted">Not logged in</a></li>
					</ul>
				</div>

				<?php } ?>		
			</div>
		</div>