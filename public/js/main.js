$(function() {
    loadThreadCheck();
    /*	var auto_refresh = setInterval(loadThreadCheck, 8000);*/

    $(".alert").alert()


    $('.alert').bind('closed.bs.alert', function() {
        var id = $(this).data('id'),
            user = $(this).data('user');

        $.post("/application/controllers/alertUpdate.php", {
            id: id,
            user: user
        });
    });


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
            duration: 1500,
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

    $('.tool').powerTip({
        placement: 'ne'
    });


    var table = $('#members-table').DataTable({
        "autoWidth": false,
        "order": [],
        "columnDefs": [{
            "targets": 'no-search',
            "searchable": false
        }, {
            "targets": [5],
            "visible": false,
            "searchable": false
        }, {
            "iDataSort": 5,
            "aTargets": [1]
        }],

        paging: false,
        responsive: true,
        "bServerSide": false,
        "drawCallback": function(settings) {
            $("#member-footer").empty();
            $("#members-table_info").contents().appendTo("#member-footer");
        },
        "sDom": 'T<"clear">lfrtip',
        "oTableTools": {
            "sRowSelect": "multi",

            "sSwfPath": "/public/swf/copy_csv_xls_pdf.swf",
            "aButtons": [{

                "sExtends": "text",
                "fnSelect": function(nButton, oConfig, nRow) {
                    console.log($(nRow).data('id') + " clicked")
                },
                "sExtends": "collection",
                "sButtonText": "",
                "mColumns": 'visible',
                "aButtons": ["select_all", "select_none", "xls", "pdf"],
                "bSelectedOnly": true
            }]
        }
    });

    $('#members-table tbody').on('click', 'tr', function() {
        console.log(table.row(this).data());
    });



    $("#members-table_filter input").appendTo("#playerFilter").removeClass('input-sm');
    $("#playerFilter input").attr("placeholder", "Search Players");
    $("#members-table_filter label").remove();

    $(".DTTT_container .DTTT_button").removeClass('DTTT_button');
    $(".DTTT_container").appendTo('.download-area');
    $(".DTTT_container a").addClass('btn btn-xs btn-info tool').attr('title', 'Download table data').text("Export").css('margin-top', '5px');

    $(".no-sort").removeClass("sorting");


    // update users online
    (function() {
        var active_count = readCookie('active_count');
        console.log("Activity counter: " + active_count);
        if (active_count < 31) {
            $.post("/application/controllers/usersOnline.php", function(list) {
                $(".userList").html(list);
                $('.tool-user').powerTip({
                    placement: 'n'
                });
            });
            
        } else {
            $(".userList").html('<i class="fa fa-clock-o"></i> Idle. No longer refreshing.');
            clearTimeout(arguments.callee);
        }
        setTimeout(arguments.callee, 30000);
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

function setCookie(name, value, expires, path, domain, secure) {
    cookieStr = name + "=" + escape(value) + "; ";

    if (expires) {
        expires = setExpiration(expires);
        cookieStr += "expires=" + expires + "; ";
    }
    if (path) {
        cookieStr += "path=" + path + "; ";
    }
    if (domain) {
        cookieStr += "domain=" + domain + "; ";
    }
    if (secure) {
        cookieStr += "secure; ";
    }

    document.cookie = cookieStr;
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