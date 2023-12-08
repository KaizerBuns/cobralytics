<script>
function bulk_rotator_edit() {
    var bulk_ids = [];
    var i = 0;
    $('.tblrotators_chkbox_selector:checked').each(function(){
        bulk_ids[i++] = $(this).val();
    });

    if(i <= 1) {
        bootbox.alert("Please select more than 1 rotator to bulk edit");
        return;
    }

    bootbox.confirm('Are you sure you want to bulk edit these Rotators?', function(result) { if (result===true) {  
            window.location = "/member/rule/?view=edit&bulk=1&rotator=true&type={{ $object->get_type() }}&type_id={{ $object->id }}&ids=" + bulk_ids.join()+'&rotator=1';
    }});
}

function bulk_rotator_delete() {
    var bulk_ids = [];
    var i = 0;
    $('.tblrotators_chkbox_selector:checked').each(function(){
        bulk_ids[i++] = $(this).val();
    });

    if(i <= 1) {
        bootbox.alert("Please select more than 1 rotator to bulk delete");
        return;
    }

    bootbox.confirm('Are you sure you want to bulk delete these Rotators?', function(result) { if (result===true) {  
            window.location = "/member/rule/?view=delete&type={{ $object->get_type() }}&type_id={{ $object->id }}&ids=" + bulk_ids.join();
    }});
}

function bulk_edit() {
    var bulk_ids = [];
    var i = 0;
    $('.tblrules_chkbox_selector:checked').each(function(){
        bulk_ids[i++] = $(this).val();
    });

    if(i <= 1) {
        bootbox.alert("Please select more than 1 rule to bulk edit");
        return;
    }

    bootbox.confirm('Are you sure you want to bulk edit these Rules?', function(result) { if (result===true) {  
            window.location = "/member/rule/?view=edit&bulk=1&type={{ $object->get_type() }}&type_id={{ $object->id }}&ids=" + bulk_ids.join();
    }});
}

function bulk_delete() {
    var bulk_ids = [];
    var i = 0;
    $('.tblrules_chkbox_selector:checked').each(function(){
        bulk_ids[i++] = $(this).val();
    });

    if(i <= 1) {
        bootbox.alert("Please select more than 1 rule to bulk delete");
        return;
    }

    bootbox.confirm('Are you sure you want to bulk delete these Rules?', function(result) { if (result===true) {  
            window.location = "/member/rule/?view=delete&type={{ $object->get_type() }}&type_id={{ $object->id }}&ids=" + bulk_ids.join();
    }});
}
</script>