$(function() {


    // powers live search for members
    $('#member-search').keyup(function(e) {
        clearTimeout($.data(this, 'timer'));
        if (e.keyCode == 13) {
            member_search();
        } else {
            $(this).data('timer', setTimeout(member_search, 900));
        }

        if (!$('#member-search').val()) {
            $('#member-search-results').empty();
        }
    })


    $(".alert").alert()

    $('.alert').bind('closed.bs.alert', function() {
        var id = $(this).data('id'),
            user = $(this).data('user');

        $.post("/application/controllers/update_alert.php", {
            id: id,
            user: user
        });
    });

    // popup link
    $(".popup-link").click(function(e) {
        e.preventDefault();
        windowOpener($(this).attr("href"), "AOD Squad Tracking", "width=600,height=600,scrollbars=yes");
    });


    $(".edit-member").click(function() {
        var member_id = $(this).parent().attr('data-member-id');

        $(".viewPanel .viewer").load("/application/controllers/ajax-view-member.php", {
            id: member_id
        });
        $(".viewPanel").modal();
    });


    $(".divGenerator").click(function() {
        $(".viewPanel .viewer").load("/application/vendor/division-structure/index.php");
        $(".viewPanel").modal();
    });


    $(".container").on("click", ".reload", function() {
        loadThreadCheck();
    });

    $('#rctTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $("#members-table tbody tr").click(function() {
        window.location.href = "/member/" + $(this).attr('data-id');
    })


    /**
     * navigation links for user cp
     */
    $('.logout-btn').click(function(e) {
        e.preventDefault();
        window.location.href = "/logout";
    });
    $('.settings-btn').click(function(e) {
        e.preventDefault();
        window.location.href = "/user/settings";
    });
    $('.profile-btn').click(function(e) {
        e.preventDefault();
        window.location.href = "/user/profile";
    });
    $('.messages-btn').click(function(e) {
        e.preventDefault();
        window.location.href = "/user/messages";
    });


    $('#login').submit(function(e) {
        e.preventDefault();

        $.post("/application/controllers/login.php",
            $(this).serialize(),
            function(data) {
                if (data['success'] === true) {
                    $('.login-btn').removeClass('btn-primary').addClass('btn-success').text('Success!');
                    $('.msg').fadeOut();

                    setTimeout(function() {
                        window.location.href = "/";
                    }, 1000);

                } else if (data['success'] === false) {
                    $('#login-panel').addClass('has-error');
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
                    $('.register-btn').removeClass('btn-primary').addClass('btn-success').text('Success!');
                    $('.msg').fadeOut();

                    setTimeout(function() {
                        window.location.href = "/";
                    }, 1000);

                } else if (data['success'] === false) {
                    $('#register-panel').addClass('has-error');
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

    $('.tool').powerTip({
        placement: 'n'
    });

    $('.tool-s').powerTip({
        placement: 's'
    });

    $('.tool-e').powerTip({
        placement: 'e'
    });

    $('.tool-ne').powerTip({
        placement: 'ne'
    });

    var platoonNum = parseInt($('.platoon-number').text());

    var formattedDate = new Date();
    var d = formattedDate.getDate();
    var m = (formattedDate.getMonth() + 1);
    var y = formattedDate.getFullYear();
    var nowDate = y + "-" + m + "-" + d;

    var selected = new Array();

    var table = $('#members-table').DataTable({
        "autoWidth": true,
        "sDom": 'T<"clear">tfrip',
        "order": [],
        "columnDefs": [{
            "targets": 'no-search',
            "searchable": false
        }, {
            "targets": 'col-hidden',
            "visible": false,
            "searchable": false
        }, {
            "iDataSort": 7, // sort rank by rank id
            "aTargets": [1]
        }, {
            "iDataSort": 8, // sort activity by last login date
            "aTargets": [3]
        }],
        stateSave: true,
        paging: false,
        "bServerSide": false,
        "drawCallback": function(settings) {
            $("#member-footer").empty();
            $("#members-table_info").contents().appendTo("#member-footer");
        },

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
                "mColumns": "visible",
                "aButtons": ["select_all", "select_none", {
                    "sExtends": "pdf",
                    "sPdfOrientation": "landscape",
                    "sFileName": "AOD Plt " + platoonNum + "_" + nowDate + ".pdf",
                    "mColumns": "visible"
                }, {
                    "sExtends": "csv",
                    "sFileName": "AOD Plt " + platoonNum + "_" + nowDate + ".csv",
                    "mColumns": "visible"
                }],
                "bSelectedOnly": true
            }]
        }



    });

    $('#members-table tbody').on('click', 'tr', function() {
        console.log(table.row(this).data());
    });


    /*    // if true, exists and don't show tour
    var tour_info = readCookie('tour_cookie_new');
    if (tour_info) {
        $('.tour-intro').hide();
    }

    $('.hide-tour').click(function() {
        setCookie('tour_cookie_new', 'true', 99999);
        $('.tour-intro').fadeOut();
    });*/


    $("#members-table_paginate").addClass('text-center');
    $("#members-table_filter input").appendTo("#playerFilter").removeClass('input-sm');
    $("#playerFilter input").attr({
        "placeholder": "Search Players",
        "class": "form-control input-lg"
    });
    $("#members-table_filter label").remove();

    $(".DTTT_container .DTTT_button").removeClass('DTTT_button');
    $(".DTTT_container").appendTo('.download-area');
    $(".DTTT_container a").addClass('btn btn-xs btn-info tool').attr('title', 'Download table data').text("Export").css('margin-top', '5px');

    $(".no-sort").removeClass("sorting");


    // update users online
    (function() {
        var active_count = readCookie('active_count');
        if (active_count < 31) {

            setTimeout(function() {
                $.post("/application/controllers/online_users.php", function(list) {
                    $(".userList").html(list);
                    $('.tool-user').powerTip({
                        placement: 'n'
                    });
                })

            }, 2500)


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

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function member_search() {
    if ($('#member-search').val()) {
        $.ajax({
            url: 'application/controllers/member_search.php',
            type: 'get',
            data: {
                name: $('input#member-search').val()
            },
            success: function(response) {
                $('#member-search-results').html(response);
            }
        });
    }
}



/**
 * ZeroClipboard support
 */

var client = new ZeroClipboard($('.copy-button'));

client.on("ready", function(readyEvent) {
    // alert( "ZeroClipboard SWF is ready!" );

    client.on("aftercopy", function(event) {
        // `this` === `client`
        // `event.target` === the element that was clicked
        // event.target.style.display = "none";
        alert("Copied text to clipboard");
        // : " + event.data["text/plain"]
    });
});


function windowOpener(url, name, args) {

    if (typeof(popupWin) != "object" || popupWin.closed) {
        popupWin = window.open(url, name, args);
    } else {
        popupWin.location.href = url;
    }

    popupWin.focus();
}


function selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
    }
}


$(".draggable").draggable({
    connectToSortable: 'ul',
    revert: 'invalid',
    scroll: true,
    scrollSensitivity: 100
});



$(".view-profile").click(function() {
    var userId = $(this).closest('.list-group-item').attr('data-user-id');
    location.href = "/member/" + userId;
});

var itemMoved, targetplatoon, sourcePlatoon, action = null;
$(".sortable").sortable({
    revert: true,
    connectWith: 'ul',
    placeholder: "ui-state-highlight",
    receive: function(event, ui) {
        itemMoved = $(ui.item).attr('data-member-id');
        targetList = $(this).attr('id');

        if (targetList == "flagged-inactives") {
            $(ui.item).find('.removed-by').show().html("Flagged by you");
            action = 1;
            context = " flagged for removal.";

            var flagCount = parseInt($(".flagCount").text()) + 1,
                inactiveCount = parseInt($(".inactiveCount").text()) - 1;

            $(".flagCount").text(flagCount);
            $(".inactiveCount").text(inactiveCount);


        } else {
            $(ui.item).find('.removed-by').empty();
            context = " no longer flagged for removal."
            action = 0;

            var flagCount = parseInt($(".flagCount").text()) - 1,
                inactiveCount = parseInt($(".inactiveCount").text()) + 1;

            $(".flagCount").text(flagCount);
            $(".inactiveCount").text(inactiveCount);

        }

        $.ajax({
            type: 'POST',
            url: '/application/controllers/update_flagged.php',
            data: {
                action: action,
                id: itemMoved
            },
            dataType: 'json',
            success: function(response) {

                if (response.success === false) {

                    message = response.message;
                    $(".alert-box").stop().html("<div class='alert alert-danger'><i class='fa fa-times'></i> " + message + "</div>").effect('highlight').delay(1000).fadeOut();
                } else {

                    message = "Player " + itemMoved + context;
                    $(".alert-box").stop().html("<div class='alert alert-success'><i class='fa fa-check'></i> " + message + "</div>").effect('highlight').delay(1000).fadeOut();
                }



            },

            // fail: function()
        });

    }
});

$(".draggable").draggable({
    connectToSortable: 'ul'
});

var itemMoved, targetplatoon, sourcePlatoon;
$(".sortable").sortable({
    connectWith: 'ul',
    placeholder: "ui-state-highlight",
    receive: function(event, ui) {
        itemMoved = $(ui.item).attr('data-id');
        targetPlatoon = $(this).attr('id');
        alert("Player " + itemMoved + " now belongs to " + targetPlatoon);

    }
});

$(function() {

    $(".progress-bar-rct").attr("class", "bar progress-bar progress-bar-striped progress-bar-danger active");

    $('#rootwizard').bootstrapWizard({
        onNext: function(tab, navigation, index) {

            /**
             * slide validation
             */

            if (index == 2) {

                $(".progress-bar").attr("class", "bar progress-bar progress-bar-striped progress-bar-warning active");

                // Validate fields
                if (!$('#member_id').val() || !$('#battlelog').val() || !$('#forumname').val()) {
                    $(".message").html("<i class='fa fa-times'></i>  All fields are required.").effect("bounce");
                    $('[class$=group]').each(function() {
                        var $this = $(this);
                        if ($this.find('input').val() == '') {
                            $(this).addClass("has-error");
                        }
                    });

                    return false;
                }

                // grab values since we know they exist
                var forumName = $('#forumname').val(),
                    battlelog = $('#battlelog').val(),
                    platoon = $('#platoon').val(),
                    squadLdr = $('#squadLdr').val(),
                    member_id = $('#member_id').val();

                if (/\D/.test(member_id)) {
                    $(".message").html("<i class='fa fa-times'></i> Member id must be a number.").effect("bounce");
                    return false;
                }

                // no errors, so clear any error states
                $(".has-error").removeClass("has-error");
                $(".message").html("");


                // check for matching forum name / battlelog
                if (battlelog != forumName) {
                    if (!confirm("The member's forum name does not match the ingame name. Are you sure you wish to continue with this information?")) {
                        return false;
                    }
                }

                // post member data to db
                var flag = 0;
                alert('Fetching BF4DB ID. Browser may freeze momentarily...');

                $.ajax({
                    type: 'POST',
                    url: '/application/controllers/store_member.php',
                    data: {
                        name: forumName,
                        battlelog: battlelog,
                        member_id: member_id,
                        platoon: platoon,
                        squadLdr: squadLdr
                    },
                    dataType: 'json',
                    async: false,
                    success: function(response) {
                        if (response.success === false) {
                            flag = 0;
                            message = response.message;
                            if (response.battlelog === true) {
                                $(".battlelog-group").addClass('has-error');
                            } else if (response.memberExists === true) {
                                $(".memberid-group").addClass('has-error');
                            }
                        } else {
                            flag = 1;
                        }
                    }
                });

                // have to declare a flag so it's not undefined...
                if (flag == 0) {
                    $(".message").html("<i class='fa fa-times'></i> " + message).effect('bounce');
                    return false;
                } else {
                    $(".alert-box").append("<div class='alert alert-success' style='z-index: 5;'><i class='fa fa-check fa-2x'></i> Your new recruit has been added to the database!</div>").delay(2000).fadeOut();
                    return true;
                }

                $(".progress-bar").attr("class", "bar progress-bar progress-bar-striped progress-bar-warning active");
            }


            if (index == 3) {

                // have the division threads loaded?
                if ($('.thread-list').is(':visible')) {

                    // do the number of threads match the number of successful results?
                    if ($('li.thread').length != $('.thread span.alert-success').length) {
                        $(".thread-status").html(" <i class='fa fa-times'></i> Recruit must complete all threads. ").effect('highlight')
                        return false
                    }
                } else {
                    return false
                }
            }

            if (index == 4) {
                $(".progress-bar").attr("class", "bar progress-bar progress-bar-striped progress-bar-success active");
            }

        },
        onTabShow: function(tab, navigation, index) {

            // panel titles
            switch (index) {
                case 0:
                    $(".tab-title strong").html("Recruiting Introduction")
                    break;
                case 1:
                    $(".tab-title strong").html("Add new member information")
                    break;
                case 2:
                    $(".tab-title strong").html("Rules and Regulations Threads")
                    loadThreadCheck();
                    break;
                case 3:
                    $(".tab-title strong").html("Finishing Up With Your Recruit")
                    break;
                case 4:
                    $(".tab-title strong").html("\"Dreaded Paperwork\"")
                    break;
                case 5:
                    $(".tab-title strong").html("Recruitment Complete")
                    break;
            }

            var $total = navigation.find('li').length;
            var $current = index + 1;
            var $percent = ($current / $total) * 100;
            $('#rootwizard').find('.bar').css({
                width: $percent + '%'
            });
        }
    });
});


function loadThreadCheck() {


    // setting these here since we know we have them
    var player = $('#forumname').val(),
        battlelog = $('#battlelog').val(),
        game = $("#game").val(),
        member_id = $("#member_id").val(),

        // division structure
        postCode = "Please add:<br />Full-time<br />AOD_Rct_" + ucwords(player) + "<br />http://www.clanaod.net/forums/member.php?u=" + member_id + "<br />http://battlelog.battlefield.com/bf4/user/" + battlelog,
        postCopy = "Please add:\r\nFull-time\r\nAOD_Rct_" + ucwords(player) + "\r\nhttp://www.clanaod.net/forums/member.php?u=" + member_id + "\r\nhttp://battlelog.battlefield.com/bf4/user/" + battlelog,

        // welcome PM -- needs to come from the DB
        welcomeCode = "[b]Congratulations " + ucwords(player) + ", you've been accepted to join AOD![/b] I hope your stay so far has been positive.<br /><br />Here's a couple of key points to get you started and keep you going:<br /><br />[list][*]**Most Important** You'll need to be on Teamspeak any time you are in game. You dont have to be talking to others (we have a quiet room) but we use vent to communicate and organize ourselves.[*]AOD has a military structure. We do not enforce it oppressively, but we do require a minimum level of respect. Check it out here.http://www.clanaod.net/forums/showthread.php?t=3326[*]AOD has a Code of Conduct. The summary is, be respectful of others, they will already be respecting you, otherwise they wouldnt be here. http://www.clanaod.net/forums/showthread.php?t=3327[*]AOD has several games. If you play any of the games AOD supports, make sure you throw on the AOD tags.[*]*** We check the forums periodically for member activity to make sure we aren't carrying dead weight. AOD is a huge clan, and it's important that we clean house periodically. If you feel that you will be absent for any reason, you can request an LOA and your membership status will be maintained. There is LOTS of cool stuff, events, and announcements in the Battlefield 4 forums. ***[*]We like to screenshot when we take each other's tags. We post the screenshots here on our wall of shame. I hope very much to personally add your tags to the wall! =) ... The wall is HERE: http://www.clanaod.net/forums/showthread.php?t=75595[/list]<br /><br />IMPORTANT:<br />Also remember, your forum username will change in a day or so to AOD_" + player + ", so don't panic! It is our way of keeping track of who has been processed in properly, and given access to the member only forums. You will NOT receive an email reminding you of this, however if you need to do a password recovery, it will remind you that your username is not exactly what you registered with.<br /><br />Most importantly, HAVE FUN, and PLAY TOGETHER. Feel free to PM Me, BL Chat me, or pull me aside in Teamspeak if you have ANY questions at all. You can also feel free to contact any other NCO with questions.",

        welcomeCopy = "[b]Congratulations " + ucwords(player) + ", you've been accepted to join AOD![/b] I hope your stay so far has been positive.\r\n\r\nHere's a couple of key points to get you started and keep you going:\r\n\r\n[list][*]**Most Important** You'll need to be on Teamspeak any time you are in game. You dont have to be talking to others (we have a quiet room) but we use vent to communicate and organize ourselves.[*]AOD has a military structure. We do not enforce it oppressively, but we do require a minimum level of respect. Check it out here.http://www.clanaod.net/forums/showthread.php?t=3326[*]AOD has a Code of Conduct. The summary is, be respectful of others, they will already be respecting you, otherwise they wouldnt be here. http://www.clanaod.net/forums/showthread.php?t=3327[*]AOD has several games. If you play any of the games AOD supports, make sure you throw on the AOD tags.[*]*** We check the forums periodically for member activity to make sure we aren't carrying dead weight. AOD is a huge clan, and it's important that we clean house periodically. If you feel that you will be absent for any reason, you can request an LOA and your membership status will be maintained. There is LOTS of cool stuff, events, and announcements in the Battlefield 4 forums. ***[*]We like to screenshot when we take each other's tags. We post the screenshots here on our wall of shame. I hope very much to personally add your tags to the wall! =) ... The wall is HERE: http://www.clanaod.net/forums/showthread.php?t=75595[/list]\r\n\r\nIMPORTANT:\r\nAlso remember, your forum username will change in a day or so to AOD_" + player + ", so don't panic! It is our way of keeping track of who has been processed in properly, and given access to the member only forums. You will NOT receive an email reminding you of this, however if you need to do a password recovery, it will remind you that your username is not exactly what you registered with.\r\n\r\nMost importantly, HAVE FUN, and PLAY TOGETHER. Feel free to PM Me, BL Chat me, or pull me aside in Teamspeak if you have ANY questions at all. You can also feel free to contact any other NCO with questions.";


    if (battlelog) {
        $(".rank-name").html("AOD_Rct_" + ucwords(battlelog));
        $(".player-name").html(ucwords(battlelog));

        // full name copy
        $('.player-name-copy').attr("data-clipboard-text", "AOD_Rct_" + ucwords(battlelog))

        // division structure
        $("#division-post .post-code").html(postCode);
        $('.division-code-btn').attr("data-clipboard-text", postCopy);

        // final member id for request
        $(".final_member_id").html(member_id);
        $('.member-status-btn').attr("data-clipboard-text", member_id);

        // welcome PM
        $("#welcome-pm .welcome-code").html(welcomeCode);
        $('.welcome-pm-btn').attr("data-clipboard-text", welcomeCopy);
        $(".pm-link").click(function(e) {
            e.preventDefault();
            windowOpener($(this).attr("href") + member_id, "AOD Squad Tracking", "width=1000,height=600,scrollbars=yes");
        });

    }

    if (player) {
        $(".search-subject").html("<p class='text-muted'>Searching threads for posts by: <code>" + ucwords(player) + "</code></p>");
    }

    $(".thread-results").html('<img src="/public/images/loading.gif " class="margin-top-20" />');

    $.ajax({
        url: "/application/controllers/recruit_thread_check.php",
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
        $(".thread-results ").empty().prepend(html);

        $('.tool').powerTip({
            placement: 'n'
        });


    });
}


function ucwords(str) {
    return (str + '')
        .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
}


function windowOpener(url, name, args) {

    if (typeof(popupWin) != "object" || popupWin.closed) {
        popupWin = window.open(url, name, args);
    } else {
        popupWin.location.href = url;
    }

    popupWin.focus();
}

