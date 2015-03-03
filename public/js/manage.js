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


    // LOA ADD
    $("#loa-update").submit(function(e) {
        e.preventDefault();

        var url = "/application/ajax/update_loa.php";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: $("#loa-update").serialize(),
            success: function(data) {
                if (data.success) {
                    var $newRow = $("<tr class='new'><td>" + data.name + "</td><td>" + data.date + "</td><td>" + data.reason + "</td><td class='text-center'><i class='fa fa-check text-success fa-lg' title='Active'></i></td></tr>");

                    $("#loas tbody tr:last").after($newRow);
                    $newRow.effect("highlight", {}, 3000);

                    $('#loa-update')[0].reset();
                } else {
                    $(".alert").attr('class', 'alert alert-danger').html("<i class='fa fa-exclamation-triangle fa-lg'></i> " + data.message).show().delay(2000).fadeOut();
                }
            }
        });
        return false;
    });

});