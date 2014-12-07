<?php
if (isLoggedIn()) {

	// fetch member data
	$member_info = get_user_info($curUser);
	$avatar = get_user_avatar($member_info['member_id']);

	// fetch platoons (need to base on member-info -> game)
	$platoons = get_platoons();

	foreach ($platoons as $row) {
		$platoons_items .= "<li><a href='/bf4/platoon/{$row['number']}''>".$row['number']." - ".$row['name']."</a></li>";
	}

	$platoon_dropdown = '
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Platoons  <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li classs="disabled"><a>Battlefield 4</a></li>
			<li class="divider"></li>
			' . $platoons_items . '
		</ul>
	</li>
	';
}
?>

<html>
<head>
	<title>AOD | Squad Management</title>

	<meta name="viewport" content="width=device-width, initial-scale=.0, maximum-scale=1.0, user-scalable=no">
	
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>

	<link rel="stylesheet" type="text/css" href="/public/css/style.css">
	<link rel="stylesheet" type="text/css" href="/public/css/cyborg.min.css">				
	<link href='//fonts.googleapis.com/css?family=Open+Sans:200,400,700,800' rel='stylesheet' type='text/css'>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">	
	<link href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css" rel="stylesheet">

</head>
<body>
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

				<?php if (isLoggedIn()) { ?>
				
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">


						<!-- notifications menu -->


						<!-- end notifications menu -->


						<?php echo $platoon_dropdown; ?>
						<li class="dropdown">

							<a href="#" class="dropdown-toggle" data-toggle="dropdown">User CP<span class="caret"></span>
							</a>
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