$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Occasion Switch Status Change
$(document).on('change', '.teamMemberStatus', function(){
    if(this.checked){
        option = 1;
    } else {
        option = 0;
    }
    var id = $(this).data('id');

    $.ajax({
        url: "/sms-admin/team-member/view/change-team-member-status",
        method:'POST',
        data:{ option: option,id:$(this).data('id')},
        success: function(data){
            if(data == 'true'){
                if(option == 1){
                    toastr.success('Team member successfully activated');    
                } else if(option == 0){
                    toastr.success('Team member successfully deactivated');    
                } else {
                    toastr.error('Something Went Wrong!');    
                }
            } else {
                toastr.success('Team member status successfully changed');
            }
        }
    });
});

// Remove error message for select role
$(document).on('change', '#role_id', function(){
    var value = $('#role_id').val();
    if(value != ''){
        $('#role_id-error').text('');
    }
});

// Remove error message for select location
$(document).on('change', '#location_id', function(){
    var value = $('#location_id').val();
    if(value != ''){
        $('#location_id-error').text('');
    }
});