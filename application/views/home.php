<?php

// we are assuming you are logged in at this point

// variables for dig
$player = (isset($_GET['player']) && (!empty($_GET['player']))) ? $_GET['player'] : NULL;
$gameid = (isset($_GET['game']) && (!empty($_GET['game']))) ? $_GET['game'] : NULL;


$out = NULL;

$out .= "
<div class='container fade-in '>
	<div class='row'>
		<h3>Welcome</h3>
		<p>Filler stuff</p>
	</div>
	<div class='row'>

	</div>
</div>
";


echo $out;

?>