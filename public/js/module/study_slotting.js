$(document).ready(function(){
    $(document).on('click', '.addStudySlot', function(){
        var id = $(this).data('id');
    
        $.ajax({
            url: "/sms-admin/clinical-slotting/add/add-study-slot/" + id,
            method:'GET',
            success: function(data){
                $('#showStudySlottingModal').html(data);
                $('#openStudySlottingModal').modal('show');
            }
        });
    });
});