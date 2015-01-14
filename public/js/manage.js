$(function() {


    $("a.member").click(function(event) {
        event.preventDefault();
        $('div.box').fadeOut();
        $(this).children('div.box').fadeIn();

        viewMember($(this).attr('data-user-id'), $(this).text());
    });


    // manage squad quick tools
    $(".pm-button").click(function(e) {
        e.preventDefault();
        window.open($(this).attr("href") + $(this).closest('tr.member').attr("data-member-id"), "popupWindow", "width=1000,height=600,scrollbars=yes");
    });

    $(".profile-button").click(function(e) {
        e.preventDefault();
        location.href = "/member/" + $(this).closest('tr.member').attr("data-user-id");
    });

    $("#edit-form-squad").submit(function(event) {
        event.preventDefault();

        var uid = $("#uid").val(),
            mid = $("#member_id").val(),
            fname = $("#forum_name").val(),
            blog = $("#battlelog").val();

        $("#edit-form").attr('disabled', 'disabled').addClass('disabled');
        updateMember(uid, mid, fname, blog);
    });

});



function viewMember(id, name) {

    $(".data-box .panel-body .edit-form").hide();
    $(".data-box .panel-body p.loading").show();
    $(".data-box .panel-body p.intro").hide();
    $(".actions-box").hide();
    $("#edit-form-squad .message").hide();

    setTimeout(function() {
        $.post("/application/controllers/ajax-view-member.php", {
                id: id
            },

            function(data) {
                if (data.success === false) {
                    $(".data-box .panel-body p.loading").hide();
                    $(".data-box .panel-body p.intro").html(data.message).show();
                } else {
                    console.log(data)
                    var member_info = data.member_info;

                    /*                    $('#member_status option[value=' + member_info.status_id + ']').attr("selected", "selected");

                    $('#member_position option[value=' + member_info.bf4_position_id + ']').attr("selected", "selected");
*/
                    $("#member_id").val(member_info.member_id);
                    $("#battlelog").val(member_info.battlelog_name);
                    $("#forum_name").val(member_info.forum_name);
                    $("#uid").val(member_info.id);
                    $(".data-box .panel-body p.loading").hide();
                    $(".data-box .panel-body .edit-form").fadeIn();
                    $(".actions-box").show();
                }

            }, "json")
    }, 1000)

}

function updateMember(uid, mid, fname, blog) {
        setTimeout(function() {
        $.post("/application/controllers/update_member.php", {
                trans: "s",
                uid: uid,
                mid: mid,
                fname: fname,
                blog: blog
            },

            function(data) {
                if (data.success === false) {
                    if (data.battlelog === false) {
                        $("#edit-form-squad .battlelog-group").addClass("has-error");
                    }
                    $("#edit-form-squad .message").html(data.message).addClass("alert-danger").show();
                    return false;
                } else {
                    $("#edit-form-squad .message").show().html(data.message).removeClass("alert-danger").addClass('alert-success').delay(1000).fadeOut();
                    $(".has-error").removeClass("has-error");
                }

            }, "json")
    }, 1000)
}