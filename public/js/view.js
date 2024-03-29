$(function() {

    // auto select values
    var sqdldr = $("#cur_sqd").val(),
        plt = $("#cur_plt").val(),
        pos = $("#cur_pos").val();

    $("#platoon option[value=" + plt + "]").attr("selected", "selected");
    $("#sqdldr option[value=" + sqdldr + "]").attr("selected", "selected");
    $("#position option[value=" + pos + "]").attr("selected", "selected");

    $("#edit-form").submit(function(event) {
        event.preventDefault();

        $("#edit-form :submit").html("Loading").attr('class', 'btn btn-block btn-default disabled');

        var uid = $("#uid").val(),
            mid = $("#member_id").val(),
            fname = $("#forum_name").val(),
            platoon = $("#platoon").val(),
            sqdldr = $("#sqdldr").val(),
            blog = $("#battlelog").val(),
            recruiter = $("#recruiter").val(),
            position = $("#position").val();

        updateMember(uid, mid, fname, blog, platoon, sqdldr, position, recruiter);
    });

});

function updateMember(uid, mid, fname, blog, platoon, sqdldr, position, recruiter) {
    setTimeout(function() {
        $.post("/application/ajax/update_member.php", {
                uid: uid,
                mid: mid,
                fname: fname,
                blog: blog,
                platoon: platoon,
                squad: sqdldr,
                position: position,
                recruiter: recruiter
            },

            function(data) {
                $("#edit-form :submit").html("Submit Info").attr('class', 'btn btn-block btn-success');
                if (data.success === false) {
                    if (data.battlelog === false) {
                        $("#edit-form .battlelog-group").addClass("has-error");
                    }
                    $("#edit-form .message").html(data.message).addClass("alert-danger").show();

                    return false;
                } else {
                    $("#edit-form .message").show().html(data.message).removeClass("alert-danger").addClass('alert-success').delay(1000).fadeOut();
                    $(".has-error").removeClass("has-error");
                }

            }, "json")
    }, 1000)
}
