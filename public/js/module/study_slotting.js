$(document).ready(function(){
    $('.locationSelect2').select2();

    $(document).on('click', '.addStudySlot', function(){
        var id = $(this).data('id');
    
        $.ajax({
            url: "/sms-admin/study-slot/add/add-study-slot/" + id,
            method:'GET',
            success: function(data){
                $('#showStudySlottingModal').html(data);
                $('#openStudySlottingModal').modal('show');
            }
        });
    });
});