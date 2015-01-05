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

    var player = $('#forumname').val(),
        battlelog = $('#battlelog').val(),
        game = $("#game").val(),
        forum_id = $("#member_id").val();

    if (battlelog && forum_id) {
        $(".rank-name").html("AOD_Rct_" + ucwords(battlelog));
        $(".player-name").html(ucwords(battlelog));

        var post = "[b]Please add[/b]:
        Full-time
        [COLOR=\"#FFD700\"]AOD_Rct_" + player + " - http://www.clanaod.net/forums/member.php?u=" + forum_id + "http://battlelog.battlefield.com/bf4/user/" + battlelog + "[/COLOR]";
        $("#division-post code").html(post);
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
    });
}


function ucwords(str) {
    return (str + '')
        .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
}


function generateDivisionStructurePost(name, forum_id, battlelog_name) {

}