<?php $errors = array('hell', 'shucks', 'daggumit', 'phooey', 'Deity smells!'); ?>

<div class="page-404">	

	<div class="error-code">404</div>

	<div class="error-text">
		<span class="oops">Aww, <?php echo $errors[array_rand($errors)]; ?></span><br>
		<span class="hr"></span>
		<br>
		THAT PAGE DOESN'T EXIST... OR YOU DON'T HAVE ACCESS TO IT
	</div> <!-- / .error-text -->
	<div class="margin-top-50"></div>
	<a href="/" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> Take Me Home</a>

</div>



