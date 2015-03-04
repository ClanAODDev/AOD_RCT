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
                    var $newRow = $("<tr data-id='" + data.id + "'><td>" + data.name + "</td><td>" + data.reason + "</td><td>" + data.date + "</td><td class='text-center'><h4><span class='label bg-success'><i class='fa fa-check fa-lg' title='Active'></i> Active</span></h4></td><td class='text-center loa-actions' style='opacity: .2;'><div class='btn-group'><a class='btn btn-default popup-link' href='http://www.clanaod.net/forums/private.php?do=newpm&amp;u=" + data.id + "'>PM</a></div></td></tr>");

                    $("#loas tbody tr:last").after($newRow);
                    $newRow.effect("highlight", {}, 3000);
                    $('#loa-update')[0].reset();

                } else {
                    $(".loa-alerts").attr('class', 'alert alert-danger loa-alerts').html("<i class='fa fa-exclamation-triangle fa-lg'></i> " + data.message).show().delay(2000).fadeOut();
                }
            }
        });
        return false;
    });

    var revoke_confirm = "<div class='viewer fadeIn animate'><div class='modal-header'><strong>Are you sure?</strong></div><div class='modal-body'><p>Once a player's LOA is revoked, their status must be updated on the forums. Additionally, if this is a revocation, the member should be flagged for removal.</p></div><div class='modal-footer'><button type='button' data-dismiss='modal' class='btn btn-primary' id='delete'>Revoke LOA</button><button type='button' data-dismiss='modal' class='btn'>Cancel</button></div></div></div>";


    // revoke LOA
    $(".revoke-loa-btn").click(function(e) {

        e.preventDefault();

        $(".viewPanel .viewer").html(revoke_confirm);

        var url = "/application/ajax/update_loa.php",
            id = $(this).closest('#loas tbody tr').attr('data-id');

        $('.modal').modal({
            backdrop: 'static',
            keyboard: false
        })
            .one('click', '#delete', function(e) {
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    data: {
                        remove: true,
                        id: id
                    },

                    success: function(data) {
                        if (data.success) {
                            $('*[data-id="' + id + '"]').effect('highlight').hide("fade", {
                                direction: "out"
                            }, "slow");
                            $(".loa-alerts").attr('class', 'alert alert-success loa-alerts').html("<i class='fa fa-check fa-lg'></i> " + data.message).show().delay(2000).fadeOut();

                        } else {
                            $(".loa-alerts").attr('class', 'alert alert-danger loa-alerts').html("<i class='fa fa-exclamation-triangle fa-lg'></i> " + data.message).show().delay(2000).fadeOut();
                        }
                    }
                });
            });

    })


    // approve LOA
    $(".approve-loa-btn").click(function(e) {

        e.preventDefault();

        var url = "/application/ajax/update_loa.php",
            id = $(this).closest('#ploas tbody tr').attr('data-id');

        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                approve: true,
                id: id
            },

            success: function(data) {
                if (data.success) {
                    $('*[data-id="' + id + '"]').effect('highlight').hide("fade", {
                        direction: "out"
                    }, "slow");
                    $(".loa-alerts").attr('class', 'alert alert-success loa-alerts').html("<i class='fa fa-check fa-lg'></i> " + data.message).show().delay(2000).fadeOut();

                } else {
                    $(".loa-alerts").attr('class', 'alert alert-danger loa-alerts').html("<i class='fa fa-exclamation-triangle fa-lg'></i> " + data.message).show().delay(2000).fadeOut();
                }
            }
        });

    })

});