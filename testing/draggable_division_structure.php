<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Draggable with Multiple Sortables</title>

	<style type="text/css">body{font:62.5% Verdana,Arial,sans-serif}</style>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />

	<script src="/public/js/libraries/jquery-2.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<script src="/public/js/libraries/jquery-2.1.1.min.js"></script>
	<script src="/public/js/libraries/jquery-ui.min.js"></script>

	<style type="text/css">
		.demo ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 0px; }
		.demo li { margin: 0px; padding: 5px; width: 150px; cursor: move; }
		.sortable { background-color: grey; min-height: 100px;}
		.ui-state-highlight { height: 2em; line-height: 1.2em; }
	</style>

</head>
<body>

	<div class="demo">
		<div class='col-md-2'>
			<h2>Platoon 1 </h2>
			<ul class="sortable" id="platoon1">
				<li class="ui-state-default draggable" data-id="1">player 1</li>
				<li class="ui-state-default draggable" data-id="2">player 2</li>
				<li class="ui-state-default draggable" data-id="3">player 3</li>
				<li class="ui-state-default draggable" data-id="4">player 4</li>
				<li class="ui-state-default draggable" data-id="5">player 5</li>
			</ul>
		</div>

		<div class='col-md-2'>
			<h2>Platoon 2</h2>	
			<ul class="sortable" id="platoon2">
				<li class="ui-state-default draggable" data-id="6">player 6</li>
				<li class="ui-state-default draggable" data-id="7">player 7</li>
				<li class="ui-state-default draggable" data-id="8">player 8</li>
				<li class="ui-state-default draggable" data-id="9">player 9</li>
				<li class="ui-state-default draggable" data-id="10">player 10</li>
			</ul>

		</div>


		<div class='col-md-2'>
			<h2>Platoon 3</h2>
			<ul class="sortable" id="platoon3">
				<li class="ui-state-default draggable" data-id="11">player 11</li>
				<li class="ui-state-default draggable" data-id="12">player 12</li>
				<li class="ui-state-default draggable" data-id="13">player 13</li>
				<li class="ui-state-default draggable" data-id="14">player 14</li>
				<li class="ui-state-default draggable" data-id="15">player 15</li>
			</ul>

		</div>
		<div class='col-md-2'>
			<h2>Platoon 4</h2>
			<ul class="sortable" id="platoon4">
				<li class="ui-state-default draggable" data-id="16">player 16</li>
				<li class="ui-state-default draggable" data-id="17">player 17</li>
				<li class="ui-state-default draggable" data-id="18">player 18</li>
				<li class="ui-state-default draggable" data-id="19">player 19</li>
				<li class="ui-state-default draggable" data-id="20">player 20</li>
			</ul>
		</div>


		<div class='col-md-2'>
			<h2>Platoon 5</h2>
			<ul class="sortable" id="platoon5">
				<li class="ui-state-default draggable" data-id="21">player 21</li>
				<li class="ui-state-default draggable" data-id="22">player 22</li>
				<li class="ui-state-default draggable" data-id="23">player 23</li>
				<li class="ui-state-default draggable" data-id="24">player 24</li>
				<li class="ui-state-default draggable" data-id="25">player 25</li>
			</ul>
		</div>


	</div>


	<script type="text/javascript">

		$(".draggable").draggable({
			connectToSortable: 'ul'
		});

		var itemMoved,  targetplatoon, sourcePlatoon;
		$(".sortable").sortable({
			connectWith: 'ul',
			placeholder: "ui-state-highlight",
			receive: function(event, ui) {
				itemMoved = $(ui.item).attr('data-id');
				targetPlatoon = $(this).attr('id');
				alert("Player " + itemMoved + " now belongs to " + targetPlatoon);	

			}
		});
	</script>

</body>
</html>
