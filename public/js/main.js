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

    $('#login').submit(function(e) {
        e.preventDefault();
        $.post("/aod_rct/application/controllers/login.php",
            $(this).serialize(),
            function(data) {
                if (data['success'] === true) {
                    alert('User successfully logged in');
                } else if (data['success'] === false) {
                    alert('ERROR: ' + data['message']);
                }
            }, "json");

    });


    $('.fade-in').fadeIn('slow');
});



function loadThreadCheck() {

    /*  var player = document.getElementById("player").value,
        game = document.getElementById("game").value;

    $(".thread-results").html('<img src="public/images/loading.gif" class="margin-top-20" />');

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

    .done(function(html) {
        $(".thread-results").empty().prepend(html);
    });
*/
}