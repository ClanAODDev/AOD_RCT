$(function() {

    // allow bf4db search with forum name
    $(".bf4dbid-search").click(function(e) {
        e.preventDefault();
        var battlelog = $("#battlelog").val();
        if (battlelog == '') {
            $(".battlelog-group").addClass("has-error").effect("bounce");
            $(".message").html("A battlelog name is required to search with.").effect("bounce");
            return false;
        } else {
            $(".battlelog-group").removeClass("has-error");
            $(".message").html('');
            window.open($(this).attr("href") + battlelog, "popupWindow", "width=800,height=600,scrollbars=yes");
        }
    })


    $('#rootwizard').bootstrapWizard({
        onNext: function(tab, navigation, index) {
            if (index == 2) {
                // Make sure we entered the name
                if (!$('#member_id').val() || !$('#battlelog').val() || !$('#bf4db').val() || !$('#forumname').val()) {
                    $(".message").html("All fields are required.").effect("bounce");
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

            }


            if (index == 3) {
                // have the division threads loaded?
                if ($('.thread-list').is(':visible')) {
                    // do the number of threads match 
                    // the number of successful results?
                    if ($('li.thread').length != $('.thread span.alert-success').length) {
                        $(".thread-status").html("Recruit must complete all threads.")
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
                    $(".tab-title strong").html("Final Details")
                    break;
                case 4:
                    $(".tab-title strong").html("Confirm Information")
                    break;
                case 5:
                    $(".tab-title strong").html("Finishing Up")
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
        game = document.getElementById("game").value;


    // apply name to final slide
    $(".player-name").html("AOD_Rct_" + ucwords(battlelog));

    if (player) {
        $(".search-subject").html("<p class='text-muted'>Searching threads for <code>" + player + "</code></p>");
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