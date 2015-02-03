<?php

if ($userRole > 1) {
	



} else {
	header('Location: /404/');
}


?>

<div class="row">
	<div class="col-sm-2">
		<div class="sidebar-nav">
			<div class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<span class="visible-xs navbar-brand">Sidebar menu</span>
				</div>
				<div class="navbar-collapse collapse sidebar-navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Member Email Tool</a></li>
						<li class="active"><a href="#">Future Tool</a></li>
						<li class="active"><a href="#">Future Tool</a></li>
						<li class="active"><a href="#">Future Tool</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class='container fade-in'>
		<div class="col-sm-9">
			<div class='page-header'>
				<h1><strong>Email Active Member</strong></h1>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-md-8">
						<div class="well well-sm">
							<form class="form-horizontal" action="" method="post">
								<fieldset>
									<!-- Email input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="email">Member E-mail</label>
										<div class="col-md-9">
											<input id="email" name="email" type="text" placeholder="Member email" class="form-control">
										</div>
									</div>

									<!-- Subject line -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="subject">Subject</label>
										<div class="col-md-9">
											<input class="form-control" id="subject" name="subject" placeholder="Subject Line" rows="1"></input>
										</div>
									</div>

									<!-- Message body -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="message">Your message</label>
										<div class="col-md-9">
											<textarea class="form-control" id="message" name="message" placeholder="Please enter your message here..." rows="5"></textarea>
										</div>
									</div>


									<div class="form-group">
										<div class="col-md-12 text-center">
											<button type="submit" class="btn btn-primary btn-lg">Submit</button>
											<button type="reset" class="btn btn-secondary btn-lg">Reset</button>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
