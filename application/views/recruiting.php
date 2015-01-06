<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;

$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>Recruiting</li>
</ul>
";

$out .= "

<div class='container fade-in'>
	{$breadcrumb}

	<div class='page-header'>
		<h1><strong>Recruiting</strong> <small>Begin Recruiting Process</small></h1>
	</div>

	<div class='recruit-intro'>
		<div class='row'>
			<div class='col-md-12'>
				<p class='lead'>Ready to start a new recruit? To make things easier, you should have a couple items handy. Make sure you have them copied somewhere so you don't get backed up later:</p>

			</div>
		</div>

		<div class='row margin-top-20'>
			<div class='col-xs-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>AOD Forum ID</strong></div>
					<img src='/public/images/forum_id_ss.jpg' class='img-responsive'/>
				</div>
			</div>

			<div class='col-xs-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>Battlelog Name</strong></div>
					<img src='/public/images/bf4_name_ss.jpg' class='img-responsive'/>			
				</div>
			</div>
			<div class='col-xs-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'><strong>BF4DB ID</strong></div>
					<img src='/public/images/bf4db_id_ss.jpg' class='img-responsive'/>			
				</div>
			</div>
		</div>

		<div class='row margin-top-20'>
			<div class='col-md-12'>
				<p>This information is necessary for us to keep track of our players' activity, and also to provide integration with the AOD forums. If you do not know how to obtain this information, consult your leadership.</p>
				<p>Once you are ready to begin, click below to start. Be prepared to complete the process entirely once started. <span class='text-danger'>You cannot return to an unfinished recruit session.</span></p>
			</div>
		</div>

		<div class='row margin-top-50'>
			<div class='col-xs-6 text-center'>
				<a href='#' class='existing-init tool disabled' title='Use when adding an existing / ex clan member to division'><button type='button' class='btn btn-lg btn-default disabled'>Add existing member</button></a>

			</div>
			<div class='col-xs-6 text-center'>
				<a href='/recruiting/new-member' class='recruit-init tool' title='For brand new AOD members'><button type='button' class='btn btn-lg btn-info'>Add new member</button></a>

			</div>
		</div>
	</div><!-- end recruit intro -->
</div><!-- end container-->";

echo $out;

?>