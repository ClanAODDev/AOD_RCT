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

    $('.logout-btn').click(function(e) {
        e.preventDefault();
        window.location.href = "/aod_rct/logout";
    });

    $('#login').submit(function(e) {
        e.preventDefault();
        $.post("/aod_rct/application/controllers/login.php",
            $(this).serialize(),
            function(data) {
                if (data['success'] === true) {
                    $('#login-panel').effect("clip");
                    $('.msg').removeClass('alert-danger').addClass('alert alert-success').html("<i class=\"fa fa-check-square-o\"></i> <small>" + data['message'] + "</small>").delay(1000).fadeIn();
                    $('.status-text').delay(1500).html("<small>You will now be redirected to the admin panel...</small>").fadeIn();

                    setTimeout(function() {
                        window.location.href = "/aod_rct";
                    }, 4000);

                } else if (data['success'] === false) {
                    $('.msg').addClass('alert alert-danger').html("<i class=\"fa fa-times-circle\"></i> <small>" + data['message'] + "</small>");
                    $('.msg').effect("bounce");

                }
            }, "json");

    });



    $('#register').submit(function(e) {
        e.preventDefault();
        $.post("/aod_rct/application/controllers/register.php",
            $(this).serialize(),
            function(data) {
                if (data['success'] === true) {
                    $('#register-panel').effect("clip");
                    $('.msg').removeClass('alert-danger').addClass('alert alert-success').html("<i class=\"fa fa-check-square-o\"></i> <small>" + data['message'] + "</small>").delay(1000).fadeIn();
                    $('.status-text').delay(1500).html("<small>You will now be redirected to the login form...</small>").fadeIn();

                    setTimeout(function() {
                        window.location.href = "/aod_rct";
                    }, 4000);

                } else if (data['success'] === false) {
                    $('.msg').addClass('alert alert-danger').html("<i class=\"fa fa-times-circle\"></i> <small>" + data['message'] + "</small>");
                    $('.msg').effect("bounce");

                }
            }, "json");

    });


    $('.fade-in').fadeIn('slow');


    $('.count-animated').each(function() {
        var $this = $(this);
        jQuery({
            Counter: 0
        }).animate({
            Counter: $this.text()
        }, {
            duration: 2000,
            easing: "easeOutQuart",
            step: function() {
                if ($this.hasClass('percentage')) {
                    $this.text(Math.ceil(this.Counter) + "%");
                } else {
                    $this.text(Math.ceil(this.Counter));
                }
            }
        });
    });
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