<?php
if (isLoggedIn()) {

$out = NULL;

$out .= "
<div class='container fade-in margin-top-20'>
	<div class='row'>

		<div class='jumbotron'>
			<h1>Howdy <strong>{$curUser}</strong>!</h1>
			<p>This is the squad administration tool for the AOD organization. To get started, select an action from the shortcut menu below.</p>
		</div>

	</div>
</div>
";


echo $out;

}

?>