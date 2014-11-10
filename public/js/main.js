		
$(function() {

	loadThreadCheck();
	/*	var auto_refresh = setInterval(loadThreadCheck, 8000);*/

	$(".container").on("click", ".reload", function() {
		loadThreadCheck();
	});		

	$('#rctTab a').click(function(e) {
		e.preventDefault();
		$(this).tab('show');
	});	


	$('.fade-in').fadeIn('slow');
});





function loadThreadCheck() {

	var player =  document.getElementById("player").value,
	game = document.getElementById("game").value;

	$( ".thread-results" ).html('<img src="public/images/loading.gif" class="margin-top-20" />');

	$.ajax({
		url: "/aod_rct/application/check_threads.php",
		data: {
			player: player,
			game: game
		},
		cache: false,
		beforeSend: function() {
			$('#content').hide();
			$('#loading').show();
		},
	})

	.done(function( html ) {
		$( ".thread-results" ).empty().prepend( html );
	});

}
