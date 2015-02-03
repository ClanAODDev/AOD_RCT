<?php

if ($userRole > 1) {
	



} else {
	header('Location: /404/');
}


?>


<div class='container fade-in'>


	<div class='page-header'>
		<h3><strong>Administration</strong></h3>
	</div>
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
							<li class="active"><a href="#email-form" role="tab" data-toggle="tab">Member Email Tool</a></li>
							<li class=""><a href="#tab2" role="tab" data-toggle="tab">Future Tool</a></li>
							<li class=""><a href="#tab3" role="tab" data-toggle="tab">Future Tool</a></li>
							<li class=""><a href="#tab4" role="tab" data-toggle="tab">Future Tool</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>

		<div class="col-sm-10">

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane active" id="email-form">




					<div class="col-md-12">

						<form class="form" action="" method="post">



							<div class="panel panel-default">





								<div class="panel-heading"><i class="fa fa-envelope"></i> Send email</div>
								<div class="panel-body">



									<!-- Email input-->
									<div class="form-group">
										<label class="control-label" for="email">Member E-mail</label>
										<input id="email" name="email" type="text" placeholder="Member email" class="form-control">
									</div>

									<!-- Subject line -->
									<div class="form-group">
										<label class="ccontrol-label" for="subject">Subject</label>
										<input class="form-control" id="subject" name="subject" placeholder="Subject Line" rows="1"></input>
									</div>

									<!-- Message body -->
									<div class="form-group">
										<label class="control-label" for="message">Your message</label>
										<textarea class="form-control" id="message" name="message" placeholder="Please enter your message here..." rows="5"></textarea>
									</div>



								</div>



								<div class="panel-footer clearfix">
									<div class="form-group">
										<div class="col-md-12 text-right">
											<button type="reset" class="btn btn-secondary">Reset</button>
											<button type="submit" class="btn btn-primary">Submit</button>
										</div>
									</div>
								</div>





							</div><!-- end panel -->


						</form>

					</div><!-- end col md 12 -->

				</div><!-- end email form-->







				<div role="tabpanel" class="tab-pane" id="tab2">

					asdfasdfasdfasdf1

				</div><!-- end tab 2 -->






				<div role="tabpanel" class="tab-pane" id="tab3">

					asdfasdfasdfasdf2

				</div><!-- end tab 3 -->






				<div role="tabpanel" class="tab-pane" id="tab4">

					asdfasdfasdfasdf3

				</div><!-- end tab 4 -->


			</div>

		</div><!-- end tab content-->

	</div><!-- end row -->



</div><!-- end container -->
