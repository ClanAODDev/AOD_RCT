<?php

$out = NULL;

if (isLoggedIn()) {

	header('Location: ./');

} else {


$out .= "
<div class='container game-select fade-in' style='z-index: 5;'>
	<div class='row'>
		<h3>Leader Login</h3>
		<p>Login with AOD forum credentials</p>
	</div>
	<div class='row'>
		<form class='form-horizontal' role='form' id='login'>
			<fieldset>
				<div class='form-group'>
					<label class='sr-only' for='user'>Username</label>
					<input type='text' class='form-control' id='user' name='user' placeholder='Username' required />
				</div>

				<div class=\"form-group\">
					<label class='sr-only' for='password'>Email</label>
					<input type='password' class='form-control' id='password' name='password' placeholder='Password' required />
				</div>
				<button type='submit' class='btn btn-primary'>Login</button>
			</fieldset>
		</form>
	</div>

	<small class='text-muted'>Application is currently under construction.</small>
</div>


";

}



echo $out;

?>

