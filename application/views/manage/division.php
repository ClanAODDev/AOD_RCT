<?php
$breadcrumb = "
<ul class='breadcrumb'>
	<li><a href='/'>Home</a></li>
	<li class='active'>Manage inactive players</li>
</ul>";



$out = "
	<div class='container fade-in'>
		<div class='row'>{$breadcrumb}</div>

		<div class='row'>

			<div class='col-md-2'>
				<h3>Platoon 1 </h3>
				<ul class='sortable' id='platoon1'>
					<li class='ui-state-default draggable' data-id='1'>player 1</li>
					<li class='ui-state-default draggable' data-id='2'>player 2</li>
					<li class='ui-state-default draggable' data-id='3'>player 3</li>
					<li class='ui-state-default draggable' data-id='4'>player 4</li>
					<li class='ui-state-default draggable' data-id='5'>player 5</li>
				</ul>
			</div>

			<div class='col-md-2'>
				<h3>Platoon 2</h3>	
				<ul class='sortable' id='platoon2'>
					<li class='ui-state-default draggable' data-id='6'>player 6</li>
					<li class='ui-state-default draggable' data-id='7'>player 7</li>
					<li class='ui-state-default draggable' data-id='8'>player 8</li>
					<li class='ui-state-default draggable' data-id='9'>player 9</li>
					<li class='ui-state-default draggable' data-id='10'>player 10</li>
				</ul>

			</div>


			<div class='col-md-2'>
				<h3>Platoon 3</h3>
				<ul class='sortable' id='platoon3'>
					<li class='ui-state-default draggable' data-id='11'>player 11</li>
					<li class='ui-state-default draggable' data-id='12'>player 12</li>
					<li class='ui-state-default draggable' data-id='13'>player 13</li>
					<li class='ui-state-default draggable' data-id='14'>player 14</li>
					<li class='ui-state-default draggable' data-id='15'>player 15</li>
				</ul>

			</div>
			<div class='col-md-2'>
				<h3>Platoon 4</h3>
				<ul class='sortable' id='platoon4'>
					<li class='ui-state-default draggable' data-id='16'>player 16</li>
					<li class='ui-state-default draggable' data-id='17'>player 17</li>
					<li class='ui-state-default draggable' data-id='18'>player 18</li>
					<li class='ui-state-default draggable' data-id='19'>player 19</li>
					<li class='ui-state-default draggable' data-id='20'>player 20</li>
				</ul>
			</div>


			<div class='col-md-2'>
				<h3>Platoon 5</h3>
				<ul class='sortable' id='platoon5'>
					<li class='ui-state-default draggable' data-id='21'>player 21</li>
					<li class='ui-state-default draggable' data-id='22'>player 22</li>
					<li class='ui-state-default draggable' data-id='23'>player 23</li>
					<li class='ui-state-default draggable' data-id='24'>player 24</li>
					<li class='ui-state-default draggable' data-id='25'>player 25</li>
				</ul>
			</div>

		</div>
	</div>

	";


	echo $out;

?>

<script type="text/javascript" src="/public/js/manage.js"></script>

</body>
</html>
