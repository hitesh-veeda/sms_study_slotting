$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Study Switch Status Change
$(document).on('change', '.studyStatus', function(){
    if(this.checked){
        option = 1;
    } else {
        option = 0;
    }
    var id = $(this).data('id');

    $.ajax({
        url: "/sms-admin/study-master/view/change-study-status",
        method:'POST',
        data:{ option: option,id:$(this).data('id')},
        success: function(data){
            if(data == 'true'){
                if(option == 1){
                    toastr.success('Study successfully activated');    
                } else if(option == 0){
                    toastr.success('Study successfully deactivated');    
                } else {
                    toastr.error('Something Went Wrong!');    
                }
            } else {
                toastr.success('Study status successfully changed');
            }
        }
    });
});

// Remove Error Message after selecting an option for sponsor
$('.select_sponsor').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#sponsor-error').text('');
    }
});

$('.selectScope').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#scope-error').text('');
    }
});

$('.selectStudyDesign').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#study_design-error').text('');
    }
});

$('.selectStudySubType').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#study_sub_type-error').text('');
    }
});

$('.selectSubjectType').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#subject_type-error').text('');
    }
});

$('.selectBlindingStatus').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#blinding_status-error').text('');
    }
});

$('.selectCrLocation').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#cr_location-error').text('');
    }
});

$('.selectRegulatorySubmission').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#regulatory_submission-error').text('');
    }
});

$('.selectStudyType').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#study_type-error').text('');
    }
});

$('.selectComplexity').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#complexity-error').text('');
    }
});

$('.selectStudyCondition').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#study_condition-error').text('');
    }
});

/*$('.selectPriority').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#priority-error').text('');
    }
});*/

$('.selectBrLocation').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#br_location-error').text('');
    }
});

/*$('.study_no_allocation_date').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#study_no_allocation_date-error').text('');
    }
});

$('.tentative_study_start_date').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#tentative_study_start_date-error').text('');
    }
});

$('.tentative_study_end_date').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#tentative_study_end_date-error').text('');
    }
});

$('.tentative_imp_date').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#tentative_imp_date-error').text('');
    }
});*/

$('.principleInvestigator').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#principle_investigator-error').text('');
    }
});

$('.projectManager').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#project_manager-error').text('');
    }
});

/*$('.bioanalyticalInvestigator').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#bioanalytical_investigator-error').text('');
    }
});

$('.studyResult').on('change',function(){
    var val = $(this).val();
    if(val != ''){
        $('#study_result-error').text('');
    }
});*/

$(document).on('click','.addNewDrug',function(){

    var id = $(this).data('id');
    var value = $(this).data('value');

    $.ajax({
        url: "/sms-admin/study-master/view/select-drug-details",
        method:'POST',
        data:{ value:value, id:id },
        success: function(data){
            $('.new_drug_details').append(data.html);
            $('.select2').select2();
            $('input.width').keyup(function() {
                match = (/(\d{0,40})[^.]*((?:\.\d{0,10})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
                this.value = match[1] + match[2];
            });
        }
    });

    id++; value++;
    $(this).data('value',value);
    $(this).data('id',id);
});

$(document).on('click','.remove',function(){ 
    var id = $('#drug_id_'+$(this).data('id')).val();
    $(this).closest('.removeRow').remove();
});

$(document).on('change', '.select_drug', function(){ 

    var drugName = $(this).val();

    if(drugName != '') {
        $(this).css('color', 'blue');
        $(this).next('span').hide();
    } else {
        $(this).css('color', 'black');
        $(this).next('span').show();
    }
});

$(document).on('change', '.select_dosage_form', function(){ 

    var dosageFrom = $(this).val();

    if(dosageFrom != ''){ 
        $(this).css('color', 'blue');
        $(this).next('span').hide();
    } else { 
        $(this).css('color', 'black');
        $(this).next('span').show();
    }
});

$(document).on('input', '.dosage', function(){ 

    var dosage = $(this).val().trim();

    if(dosage != ''){ 
        $(this).css('color', 'blue');
        $(this).next('span.dosage_error').hide();
    } else { 
        $(this).css('color', 'black');
        $(this).next('span.dosage_error').show();
    }
});

$(document).on('change', '.selectUOM', function(){ 

    var uom = $(this).val();

    if(uom != ''){ 
        $(this).css('color', 'blue');
        $(this).next('span.select_uom_error').hide();
    } else { 
        $(this).css('color', 'black');
        $(this).next('span.select_uom_error').show();
    }
});

$(document).on('change', '.selectType', function(){ 

    var type = $(this).val();

    if(type != ''){ 
        $(this).css('color', 'blue');
        $(this).next('span.select_type_error').hide();
    } else { 
        $(this).css('color', 'black');
        $(this).next('span.select_type_error').show();
    }
});

$(document).on('input', '.manufacture', function(){ 

    var manufacture = $(this).val().trim();

    if(manufacture != ''){ 
        $(this).css('color', 'blue');
        $(this).next('span.manufacture_error').hide();
    } else { 
        $(this).css('color', 'black');
        $(this).next('span.manufacture_error').show();
    }
});

$(document).on('input', '.remark', function(){ 

    var remark = $(this).val().trim();

    if(remark != ''){ 
        $(this).css('color', 'blue');
        $(this).next('span.remark_error').hide();
    } else { 
        $(this).css('color', 'black');
        $(this).next('span.remark_error').show();
    }
});

$(document).on('change','.studyResult',function(){
    var id = $(this).data('id');
    var result = $(this).val();
    
    $.ajax({
        url: "/sms-admin/study-master/view/study-result",
        method:'POST',
        data:{ id:id, result:result },
        success: function(data){
            if(data == 'true'){
                toastr.success('Study result successfully updated');    
            } else {
                toastr.success('Something went wrong');
            }
        }
    });
});

$(document).on('change','.studyHoldStatus',function(){
    var id = $(this).data('id');
    var status = $(this).val();
    
    $.ajax({
        url: "/sms-admin/study-master/view/study-status",
        method:'POST',
        data:{ id:id, status:status },
        success: function(data){
            if(data == 'true'){
                toastr.success('Study status successfully updated');    
            } else {
                toastr.success('Something went wrong');
            }
        }
    });
});

$(document).on('change','.studyProjected',function(){
    var id = $(this).data('id');
    var projected = $(this).val();
    
    $.ajax({
        url: "/sms-admin/study-master/view/study-projected",
        method:'POST',
        data:{ id:id, projected:projected },
        success: function(data){
            if(data == 'true'){
                toastr.success('Study slotted successfully updated');    
            } else {
                toastr.success('Something went wrong');
            }
        }
    });
});

$(document).on('change','.tentativeClinicalDate',function(){
    var id = $(this).data('id');
    var date = $(this).val();
    
    $.ajax({
        url: "/sms-admin/study-master/view/study-tentative-clinical-date",
        method:'POST',
        data:{ id:id, date:date },
        success: function(data){
            if(data == 'true'){
                toastr.success('Study tentative clinical date successfully updated');    
            } else {
                toastr.success('Something went wrong');
            }
        }
    });
});

$(document).on('click','.deleteBtn',function(e){
    e.preventDefault();
    var delete_id = $(this).val();
    $('#study_id').val(delete_id);
    $('#openDeleteStudyModal').modal('show');
});

$("#addProject").submit(function(e){

    var totalSubjects = parseInt($('.totalSubject').val());
    var femaleSubjects = parseInt($('.femaleSubject').val());
    var maleSubjects = parseInt($('.maleSubject').val());
    var maleFemale = maleSubjects + femaleSubjects;
    var isFormValid = true;

    if(totalSubjects == maleFemale){
    } else {
        $('.maleSubject').val('');
        $('.femaleSubject').val('');
        toastr.error('Please enter correct male & female subjects');
    }
    
    $('.select_drug').each(function(){
        if($(this).val() != '') {
            $(this).next('span.select_drug_error').hide();
        } else {
            isFormValid = false;
            $(this).next('span.select_drug_error').show();
        }
    });

    $('.select_dosage_form').each(function(){
        if($(this).val() != '') {
            $(this).next('span.select_dosage_form_error').hide();
        } else { 
            isFormValid = false;
            $(this).next('span.select_dosage_form_error').show();
        }
    });

    $('.dosage').each(function(){
        if($(this).val().trim() != '') {
            $(this).next('span.dosage_error').hide();
        } else { 
            isFormValid = false;
            $(this).next('span.dosage_error').show();
        }
    });

    $('.selectUOM').each(function(){
        if($(this).val() != '') {
            $(this).next('span.select_uom_error').hide();
        } else { 
            isFormValid = false;
            $(this).next('span.select_uom_error').show();
        }
    });

    $('.selectType').each(function(){
        if($(this).val() != '') {
            $(this).next('span.select_type_error').hide();
        } else {
            isFormValid = false;
            $(this).next('span.select_type_error').show();
        }
    });

    $('.manufacture').each(function(){
        if($(this).val().trim() != '') {
            $(this).next('span.manufacture_error').hide();
        } else {
            isFormValid = false;
            $(this).next('span.manufacture_error').show();
        }
    });

    if(!isFormValid) {
        e.preventDefault();
        $(window).scrollTop(0);
    }
});

$(document).on('change','.projectionStatus',function(){
    var studyId = $(this).data('id');
    var status = $(this).val();

    $.ajax({
        url: "/sms-admin/study-master/view/pre-study-projection-status",
        method:'POST',
        data:{ study_id:studyId, projection_status:status },
        success: function(data){
            if(data == 'true'){
                toastr.success('Pre study status successfully updated');
            } else {
                toastr.success('Something went wrong');
            }
        }
    });
});