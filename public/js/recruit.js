$(function() {

    // allow bf4db search with forum name
    $(".bf4dbid-search").click(function(e) {
        e.preventDefault();
        var battlelog = $("#battlelog").val();
        if (battlelog == '') {
            $(".battlelog-group").addClass("has-error").effect("bounce");
            $(".message").html("<i class='fa fa-times'></i>  A battlelog name is required to search with.").effect("bounce");
            return false;
        } else {
            $(".battlelog-group").removeClass("has-error");
            $(".message").html('');
            window.open($(this).attr("href") + battlelog, "popupWindow", "width=800,height=600,scrollbars=yes");
        }
    })


    $('#rootwizard').bootstrapWizard({
        onNext: function(tab, navigation, index) {

            /**
             * slide validation
             */

            if (index == 2) {

                // Make sure we entered the name
                if (!$('#member_id').val() || !$('#battlelog').val() || !$('#bf4db').val() || !$('#forumname').val()) {
                    $(".message").html("<i class='fa fa-times'></i>  All fields are required.").effect("bounce");
                    $('[class$=group]').each(function() {
                        var $this = $(this);
                        if ($this.find('input').val() == '') {
                            $(this).addClass("has-error");
                        }
                    });


                    return false;
                }

                $(".has-error").removeClass("has-error");
                $(".message").html("");

                // check for matching forum name / battlelog
                if ($('#battlelog').val() != $('#forumname').val()) {
                    if (!confirm("The member's forum name does not match the ingame name. Are you sure you wish to continue with this information?\r\nYou will have the option of requesting a name change in addition to the new member status request at the end of the recruitment process.")) {
                        return false;
                    }
                }

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

    console.log("Thread check init.");

    // setting these here since we know we have them
    var player = $('#forumname').val(),
        battlelog = $('#battlelog').val(),
        game = $("#game").val(),
        member_id = $("#member_id").val(),

        // division structure
        postCode = "Please add:<br />Full-time<br />AOD_Rct_" + ucwords(player) + "<br />http://www.clanaod.net/forums/member.php?u=" + member_id + "<br />http://battlelog.battlefield.com/bf4/user/" + battlelog,
        postCopy = "Please add:\r\nFull-time\r\nAOD_Rct_" + ucwords(player) + "\r\nhttp://www.clanaod.net/forums/member.php?u=" + member_id + "\r\nhttp://battlelog.battlefield.com/bf4/user/" + battlelog,

        // welcome PM -- needs to come from the DB
        welcomeCode = "[strong]Congratulations " + ucwords(player) + ", you've been accepted to join AOD![/strong] I hope your stay so far has been positive.<br /><br />Here's a couple of key points to get you started and keep you going:<br /><br />[list][*]**Most Important** You'll need to be on Ventrilo any time you are in game. You dont have to be talking to others (we have a quiet room) but we use vent to communicate and organize ourselves.[*]AOD has a military structure. We do not enforce it oppressively, but we do require a minimum level of respect. Check it out here.http://www.clanaod.net/forums/showthread.php?t=3326[*]AOD has a Code of Conduct. The summary is, be respectful of others, they will already be respecting you, otherwise they wouldnt be here. http://www.clanaod.net/forums/showthread.php?t=3327[*]AOD has several games. If you play any of the games AOD supports, make sure you throw on the AOD tags.[*]*** We check the forums periodically for member activity to make sure we aren't carrying dead weight. AOD is a huge clan, and it's important that we clean house periodically. If you feel that you will be absent for any reason, you can request an LOA and your membership status will be maintained. There is LOTS of cool stuff, events, and announcements in the Battlefield 4 forums. ***[*]We like to screenshot when we take each other's tags. We post the screenshots here on our wall of shame. I hope very much to personally add your tags to the wall! =) ... The wall is HERE: http://www.clanaod.net/forums/showthread.php?t=75595[/list]<br /><br />IMPORTANT:<br />Also remember, your forum username will change in a day or so to AOD_" + player + ", so don't panic! It is our way of keeping track of who has been processed in properly, and given access to the member only forums. You will NOT receive an email reminding you of this, however if you need to do a password recovery, it will remind you that your username is not exactly what you registered with.<br /><br />Most importantly, HAVE FUN, and PLAY TOGETHER. Feel free to PM Me, BL Chat me, or pull me aside in Ventrilo if you have ANY questions at all. You can also feel free to contact any other NCO with questions.",

        welcomeCopy = "[strong]Congratulations " + ucwords(player) + ", you've been accepted to join AOD![/strong] I hope your stay so far has been positive.\r\n\r\nHere's a couple of key points to get you started and keep you going:\r\n\r\n[list][*]**Most Important** You'll need to be on Ventrilo any time you are in game. You dont have to be talking to others (we have a quiet room) but we use vent to communicate and organize ourselves.[*]AOD has a military structure. We do not enforce it oppressively, but we do require a minimum level of respect. Check it out here.http://www.clanaod.net/forums/showthread.php?t=3326[*]AOD has a Code of Conduct. The summary is, be respectful of others, they will already be respecting you, otherwise they wouldnt be here. http://www.clanaod.net/forums/showthread.php?t=3327[*]AOD has several games. If you play any of the games AOD supports, make sure you throw on the AOD tags.[*]*** We check the forums periodically for member activity to make sure we aren't carrying dead weight. AOD is a huge clan, and it's important that we clean house periodically. If you feel that you will be absent for any reason, you can request an LOA and your membership status will be maintained. There is LOTS of cool stuff, events, and announcements in the Battlefield 4 forums. ***[*]We like to screenshot when we take each other's tags. We post the screenshots here on our wall of shame. I hope very much to personally add your tags to the wall! =) ... The wall is HERE: http://www.clanaod.net/forums/showthread.php?t=75595[/list]\r\n\r\nIMPORTANT:\r\nAlso remember, your forum username will change in a day or so to AOD_" + player + ", so don't panic! It is our way of keeping track of who has been processed in properly, and given access to the member only forums. You will NOT receive an email reminding you of this, however if you need to do a password recovery, it will remind you that your username is not exactly what you registered with.\r\n\r\nMost importantly, HAVE FUN, and PLAY TOGETHER. Feel free to PM Me, BL Chat me, or pull me aside in Ventrilo if you have ANY questions at all. You can also feel free to contact any other NCO with questions.";




    if (battlelog) {
        $(".rank-name").html("AOD_Rct_" + ucwords(battlelog));
        $(".player-name").html(ucwords(battlelog));

        // full name copy
        $('.player-name-copy').attr("data-clipboard-text", "AOD_Rct_" + ucwords(battlelog))

        // division structure
        $("#division-post .post-code").html(postCode);
        $('.division-code-btn').attr("data-clipboard-text", postCopy);

        // welcome PM
        $("#welcome-pm .welcome-code").html(welcomeCode);
        $('.welcome-pm-btn').attr("data-clipboard-text", welcomeCopy);
        $("#welcome-pm .pm-link").attr("href", "http://www.clanaod.net/forums/private.php?do=newpm&u=" + member_id);
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

        /**
         * ZeroClipboard support
         */

        var client = new ZeroClipboard($('.copy-link'));

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
    });
}


function ucwords(str) {
    return (str + '')
        .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
}


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