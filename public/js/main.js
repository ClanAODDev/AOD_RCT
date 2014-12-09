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
        window.location.href = "/logout";
    });

    $('#login').submit(function(e) {
        e.preventDefault();
        $.post("/application/controllers/login.php",
            $(this).serialize(),
            function(data) {
                if (data['success'] === true) {
                    $('#login-panel').effect("clip");
                    $('.msg').removeClass('alert-danger').addClass('alert alert-success').html("<i class=\"fa fa-check-square-o\"></i> <small>" + data['message'] + "</small>").delay(1000).fadeIn();
                    $('.status-text').delay(1500).html("<small>You will now be redirected to the admin panel...</small>").fadeIn();

                    setTimeout(function() {
                        window.location.href = "/";
                    }, 4000);

                } else if (data['success'] === false) {
                    console.log(data);
                    $('.msg').addClass('alert alert-danger').html("<i class=\"fa fa-times-circle\"></i> <small>" + data['message'] + "</small>");
                    $('.msg').effect("bounce");


                }
            }, "json");

    });



    $('#register').submit(function(e) {
        e.preventDefault();
        $.post("/application/controllers/register.php",
            $(this).serialize(),
            function(data) {
                if (data['success'] === true) {
                    $('#register-panel').effect("clip");
                    $('.msg').removeClass('alert-danger').addClass('alert alert-success').html("<i class=\"fa fa-check-square-o\"></i> <small>" + data['message'] + "</small>").delay(1000).fadeIn();
                    $('.status-text').delay(1500).html("<small>You will now be redirected to the login form...</small>").fadeIn();

                    setTimeout(function() {
                        window.location.href = "/";
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
            duration: 3000,
            easing: "easeOutQuart",
            step: function() {
                if ($this.hasClass('percentage')) {
                    $this.text(formatNumber(Math.ceil(this.Counter) + "%"));
                } else {
                    $this.text(formatNumber(Math.ceil(this.Counter)));
                }
            }
        });
    });

    $('.follow-tool').powerTip({
        followMouse: true
    });

    $('#members-table').DataTable({
        paging: false,
        ordering: false
    });

    $("#members-table_filter input").appendTo("#playerFilter").removeClass('input-sm');
    $("#playerFilter input").attr("placeholder", "Search Players");
    $("#members-table_filter label").remove();
    $("#members-table_info").parent().removeClass("col-sm-6");
    $("#members-table_info").parent().addClass("col-md-12 text-center");


    // update users online
    (function() {
        var aod_rct_active_count = readCookie('aod_rct_active_count');
        if (aod_rct_active_count < 30) {
            $.post("/application/controllers/users_online.php", function(list) {
                $(".userList").html(list);
                $('.tool').powerTip({
                    placement: 'ne',
                    smartPlacement: true
                });
            });
            setTimeout(arguments.callee, 20000);
        }
    }())

});

function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}

function readCookie(name) {
    var cookiename = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(cookiename) == 0) return c.substring(cookiename.length, c.length);
    }
    return null;
}


function loadThreadCheck() {

    /*  var player = document.getElementById("
            player ").value,
        game = document.getElementById("
            game ").value;

    $(".thread - results ").html('<img src="
            public / images / loading.gif " class="
            margin - top - 20 " />');

    $.ajax({
        url: " / application / check_threads.php ",
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
        $(".thread - results ").empty().prepend(html);
    });
*/
}