$(function() {


    $(".recruit-init").click(function() {
        $(".recruit-intro").fadeOut();
        $("#rootwizard").delay(1000).fadeIn();
    })


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
                } else {
                    return true;
                }
            }

            if (index == 3) {
                $("[class$=group]").removeClass("has-error");
                $(".message").html("");
            }

        },
        onTabShow: function(tab, navigation, index) {

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
                    break

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
        game = document.getElementById("game").value;

    $(".thread-results").html('<img src="public/images/loading.gif " class="margin-top-20" />');

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