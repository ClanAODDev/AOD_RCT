<?php

if (!isset($_SESSION['secure_access']) || (isset($_SESSION['secure_access']) && $_SESSION['secure_access'] !== true)) { header("Location: /404/"); }

$out = NULL;

if (isLoggedIn()) {

	header('Location: /');

} else {

	$out .= "	
	<div class='container register-form fade-in' style='z-index: 5;'>
		<div class='panel panel-primary' id='register-panel'>
			<div class='panel-heading text-left'><small><strong>AOD</strong> Leader Registration<i class='fa fa-user pull-right fa-2x'></i></small></div>
			<div class='panel-body'>
				<form class='form' role='form' id='register'>  

					<div class='form-group'>
						<label class='sr-only' for='user'>Username</label>
						<input type='text' class='form-control' id='user' name='user' placeholder='Forum username' required />
					</div>

					<div class='form-group'>
						<label class='sr-only' for='email'>E-mail</label>
						<input type='email' class='form-control' id='email' name='email' placeholder='Email' required />
					</div>

					<div class=\"form-group\">
						<label class='sr-only' for='password'>Password</label>
						<input type='password' class='form-control' id='password' name='password' placeholder='Password' required />
					</div>

					<div class=\"form-group\">
						<label class='sr-only' for='password'>Password Confirmation</label>
						<input type='password' class='form-control' id='passVerify' name='passVerify' placeholder='Confirm password' required />
					</div>

					<button type='reset' class='btn btn-default'>Reset</button>
					<button type='submit' class='btn btn-primary register-btn'>Register</button>
					
				</form> 
				
			</div>
			<div class='panel-footer text-muted'><small>Your username should be the one you use for the AOD Forums, <strong>without the AOD prefix</strong>.</small></div>
		</div>
		<div class='msg'></div>
		<div class='status-text'></div>
	</div>

	";

}



echo $out;

?>

