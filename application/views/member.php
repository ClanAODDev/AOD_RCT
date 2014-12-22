<?php
$out = NULL;

if ($member = get_member($params['id'])) {

	$rank = $member['rank'];
	$name = ucwords($member['forum_name']);

	$platoon = $member['platoon_id'];
	$game_info = get_game_info($member['game_id']);
	$game_name = $game_info['full_name'] . " Division";
	$game_id = $game_info['id'];

	$breadcrumb = "
	<ul class='breadcrumb'>
		<li><a href='/'>Home</a></li>
		<li class='active'>{$name}</li>
	</ul>
	";

	$avatar = get_user_avatar($member['member_id'], 'large');

	$out .= "

	<div class='container'>
		{$breadcrumb}
		<div class='row page-header'>
			<div class='col-sm-10'>
				<h1><strong>{$rank} {$name}</strong></h1>
			</div>
			<div class='col-sm-2'><span class='pull-right'>{$avatar}</span>

			</div>
		</div>
		<br>
		<div class='row'>
			<div class='col-sm-3'>
				<!--left col-->
				<ul class='list-group'>
					<li class='list-group-item text-muted' contenteditable='false'>Profile</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Joined</strong></span> 2.13.2014</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Last seen</strong></span> Yesterday</li>
					<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Real name</strong></span> Joseph
						Doe</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Role: </strong></span> Pet Sitter

						</li>
					</ul>
					<div class='panel panel-default'>
						<div class='panel-heading'>Insured / Bonded?

						</div>
						<div class='panel-body'><i style='color:green' class='fa fa-check-square'></i> Yes, I am insured and bonded.

						</div>
					</div>
					<div class='panel panel-default'>
						<div class='panel-heading'>Website <i class='fa fa-link fa-1x'></i>

						</div>
						<div class='panel-body'><a href='http://bootply.com' class=''>bootply.com</a>

						</div>
					</div>

					<ul class='list-group'>
						<li class='list-group-item text-muted'>Activity <i class='fa fa-dashboard fa-1x'></i>

						</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Shares</strong></span> 125</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Likes</strong></span> 13</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Posts</strong></span> 37</li>
						<li class='list-group-item text-right'><span class='pull-left'><strong class=''>Followers</strong></span> 78</li>
					</ul>
					<div class='panel panel-default'>
						<div class='panel-heading'>Social Media</div>
						<div class='panel-body'>	<i class='fa fa-facebook fa-2x'></i>  <i class='fa fa-github fa-2x'></i> 
							<i class='fa fa-twitter fa-2x'></i> <i class='fa fa-pinterest fa-2x'></i>  <i class='fa fa-google-plus fa-2x'></i>

						</div>
					</div>
				</div>
				<!--/col-3-->
				<div class='col-sm-9' contenteditable='false' style=''>
					<div class='panel panel-default'>
						<div class='panel-heading'>Starfox221's Bio</div>
						<div class='panel-body'> A long description about me.

						</div>
					</div>
					<div class='panel panel-default target'>
						<div class='panel-heading' contenteditable='false'>Pets I Own</div>
						<div class='panel-body'>
							<div class='row'>
								<div class='col-md-4'>
									<div class='thumbnail'>
										<img alt='300x200' src='http://lorempixel.com/600/200/people'>
										<div class='caption'>
											<h3>
												Rover
											</h3>
											<p>
												Cocker Spaniel who loves treats.
											</p>
											<p>

											</p>
										</div>
									</div>
								</div>
								<div class='col-md-4'>
									<div class='thumbnail'>
										<img alt='300x200' src='http://lorempixel.com/600/200/city'>
										<div class='caption'>
											<h3>
												Marmaduke
											</h3>
											<p>
												Is just another friendly dog.
											</p>
											<p>

											</p>
										</div>
									</div>
								</div>
								<div class='col-md-4'>
									<div class='thumbnail'>
										<img alt='300x200' src='http://lorempixel.com/600/200/sports'>
										<div class='caption'>
											<h3>
												Rocky
											</h3>
											<p>
												Loves catnip and naps. Not fond of children.
											</p>
											<p>

											</p>
										</div>
									</div>

								</div>

							</div>

						</div>

					</div>
					<div class='panel panel-default'>
						<div class='panel-heading'>Starfox221's Bio</div>
						<div class='panel-body'> A long description about me.

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	";


} else {
	// member not found
	header('Location: /404/');
}


echo $out;

?>