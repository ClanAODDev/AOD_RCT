$(function() {

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

    console.log('request received')

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
                url: '/application/ajax/update_flagged.php',
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




    // manage division

    $(".draggable").draggable({
        connectToSortable: 'ul'
    });

    var itemMoved, targetplatoon, sourcePlatoon;
    $(".sortable").sortable({
        connectWith: 'ul',
        placeholder: "ui-state-highlight",
        receive: function(event, ui) {
            itemMoved = $(ui.item).attr('data-member-id');
            targetPlatoon = $(this).attr('id');
            alert("Player " + itemMoved + " now belongs to " + targetPlatoon);

        }
    });


});