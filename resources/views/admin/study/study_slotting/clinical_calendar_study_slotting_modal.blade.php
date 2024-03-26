<form class="custom-validation" action="{{ route('admin.saveOpenStudySlotModalForCalendar') }}" method="post" id="addCalendarStudySlotting" name="addCalendarStudySlotting">
    @csrf
    <input type="hidden" name="checkin_date_time" id="checkin_date_time">
    <input type="hidden" name="totalMale" id="totalMale">
    <input type="hidden" name="totalFemale" id="totalFemale">

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="verify_all" id="verify_all">
                        <label class="form-check-label" for="verify_all">Verify All</label>
                    </div>
                    <hr>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="study_condition" id="study_condition" required>
                        <label class="form-check-label studyCondition" for="study_condition">Study Condition -</label>
                        <span id="study_condition_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_subject" id="no_of_subject" required>
                        <label class="form-check-label noOfSubject" for="no_of_subject">No Of Subject -</label>
                        <span id="no_of_subject_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_male_subject" id="no_of_male_subject" required>
                        <label class="form-check-label noOfMaleSubject" for="no_of_male_subject">No Of Male Subject -</label>
                        <span id="no_of_male_subject_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_female_subject" id="no_of_female_subject" required>
                        <label class="form-check-label noOfFemaleSubject" for="no_of_female_subject">No Of Female Subject -</label>
                        <span id="no_of_female_subject_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_period" id="no_of_period" required>
                        <label class="form-check-label noOfPeriod" for="no_of_period">No Of Period -</label>
                        <span id="no_of_period_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="study_design" id="study_design" required>
                        <label class="form-check-label studyDesign" for="study_design">Study Design -</label>
                        <span id="study_design_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="pre_housing" id="pre_housing" required>
                        <label class="form-check-label preHousing" for="pre_housing">Pre Housing -</label>
                        <span id="pre_housing_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="post_housing" id="post_housing" required>
                        <label class="form-check-label postHousing" for="post_housing">Post Housing -</label>
                        <span id="post_housing_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="washout_period" id="washout_period" required>
                        <label class="form-check-label washoutPeriod" for="washout_period">Washout Period -</label>
                        <span id="washout_period_error"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <span style="color:red;float:right;">* is mandatory</span>
                    </div>

                    <div class="form-group mb-2">
                        <label>Check In Date And Time</label>
                        <input type="text" class="form-control" readonly autocomplete="off" name="check_in_date_time" id="check_in_date_time" value="{{ $checkinDateTime }}">
                    </div>

                    <div class="form-group mb-2">
                        <label>Study No<span class="mandatory">*</span></label>
                        <select class="form-select select2" name="study_id" id="study_id" required>
                            <option value="">Select Study No</option>
                            @if(!is_null($studies))
                                @foreach($studies as $sk => $sv)
                                    @if($sv->study_slotting_count < $sv->no_of_periods)
                                        <option value="{{ $sv->study_id }}">{{ $sv->study_no }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <span id="selectStudyNo"></span>
                    </div>

                    <div class="hidePeriodNo" style="display: none;">
                        <div class="form-group mb-2">
                            <label>Period No</label>
                            <input class="form-control" type="text" name="period_no" id="period_no" required readonly>
                        </div>
                    </div>

                    <div class="hideMaleWards" style="display: none;">
                        <div class="form-group mb-2">
                            <label>Male Clinical Ward Location<span class="mandatory">*</span></label>
                            <select class="form-select select2 male_clinical_ward_location" name="male_clinical_ward_location[]" id="male_clinical_ward_location" multiple="multiple" required>
                                <option disabled>Select Male Clinical Ward location</option>
                            </select>
                            <span id="selectMaleClinicalWardLocation" class="custom-male-error" style="color: red;"></span>
                        </div>
                    </div>

                    <div class="hideFemaleWards" style="display: none;">
                        <div class="form-group mb-2">
                            <label>Female Clinical Ward Location<span class="mandatory">*</span></label>
                            <select class="form-select select2 female_clinical_ward_location" name="female_clinical_ward_location[]" id="female_clinical_ward_location" multiple="multiple" required>
                                <option disabled>Select Female Clinical Ward location</option>
                            </select>
                            <span id="selectFemaleClinicalWardLocation" class="custom-female-error" style="color: red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">

    function checkCapacity() {
        $.ajax({
            url: "/sms-admin/clinical-calendar/view/get-study-and-ward-data",
            method: 'POST',
            data: {
                checkin_date_time: $('#checkin_date_time').val(),
                study_id: $('#study_id').val(),
                male_clinical_wards: $('#male_clinical_ward_location').val(),
                female_clinical_wards: $('#female_clinical_ward_location').val(),
            },
            success: function(data){

                $('#male_clinical_ward_location option:not(:first)').prop('disabled', false).addClass('selectable').removeClass('notSelectable');
                $('#female_clinical_ward_location option:not(:first)').prop('disabled', false).addClass('selectable').removeClass('notSelectable');

                if (data.clinicalWards.length > 0) {
                    $.each(data.clinicalWards, function(key, element) {
                        $.each(element, function(k, v) {
                            var maleOption = $('#male_clinical_ward_location option[value=' + k + ']');
                            var isMaleOptionSelected = $('#male_clinical_ward_location option[value=' + k + ']').prop('selected');
                            var femaleOption = $('#female_clinical_ward_location option[value=' + k + ']');
                            var isFemaleOptionSelected = $('#female_clinical_ward_location option[value=' + k + ']').prop('selected');
                            var capacity = v.match(/\((\d+)\)/);

                            if(capacity[1] == 0) {
                                maleOption.text(v).prop('disabled', true).prop('selected', false).removeClass('selectable').addClass('notSelectable');
                                femaleOption.text(v).prop('disabled', true).prop('selected', false).removeClass('selectable').addClass('notSelectable');
                            } else {
                                if(data.maleSelectedWards.indexOf(parseInt(k)) != -1) {
                                    femaleOption.text(v).prop('disabled', true).prop('selected', false).removeClass('selectable').addClass('notSelectable');
                                } else if(data.femaleSelectedWards.indexOf(parseInt(k)) != -1) {
                                    maleOption.text(v).prop('disabled', true).prop('selected', false).removeClass('selectable').addClass('notSelectable');
                                } else {
                                    maleOption.text(v).prop('disabled', isFemaleOptionSelected).removeClass('notSelectable').addClass('selectable');
                                    femaleOption.text(v).prop('disabled', isMaleOptionSelected).removeClass('notSelectable').addClass('selectable');
                                }
                            }
                        });
                    });
                }

                if(data.maleWardSelection == false) {
                    $('#male_clinical_ward_location option.selectable').each(function(key, value) {
                        if(!this.selected) {
                            $(this).prop('disabled', true).removeClass('selectable').addClass('notSelectable');
                        }
                    });
                }

                if(data.femaleWardSelection == false) {
                    $('#female_clinical_ward_location option.selectable').each(function(key, value) {
                        if(!this.selected) {
                            $(this).prop('disabled', true).removeClass('selectable').addClass('notSelectable');
                        }
                    });
                }

                if((data.maleErrorMessage != '') && ($('#male_clinical_ward_location option:selected').length > 0)) {
                    $('#selectMaleClinicalWardLocation').text(data.maleErrorMessage);
                } else {
                    $('#selectMaleClinicalWardLocation').text('');
                }

                if((data.femaleErrorMessage != '') && ($('#female_clinical_ward_location option:selected').length > 0)) {
                    $('#selectFemaleClinicalWardLocation').text(data.femaleErrorMessage);
                } else {
                    $('#selectFemaleClinicalWardLocation').text('');
                }
            }
        });
    }

    $(document).ready(function(){

        $('.select2').select2({
            dropdownParent: $('#openCalendarStudySlottingModal')
        });

        $('.select2-container').css('width', '100%');

        $('#addCalendarStudySlotting').validate({
            errorElement: 'div',
            rules: {
                study_condition: {
                    required: true,
                },
                no_of_subject: {
                    required: true,
                },
                no_of_male_subject: {
                    required: true,
                },
                no_of_female_subject: {
                    required: true,
                },
                no_of_period: {
                    required: true,
                },
                study_design: {
                    required: true,
                },
                pre_housing: {
                    required: true,
                },
                post_housing: {
                    required: true,
                },
                washout_period: {
                    required: true,
                },
                study_id: {
                    required: true,
                },
                'male_clinical_ward_location[]': {
                    required: true,
                },
                'female_clinical_ward_location[]': {
                    required: true,
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == 'study_condition'){
                    error.insertAfter('#study_condition_error');
                } else if (element.attr("name") == 'no_of_subject'){
                    error.insertAfter('#no_of_subject_error');
                } else if (element.attr("name") == 'no_of_male_subject'){
                    error.insertAfter('#no_of_male_subject_error');
                } else if (element.attr("name") == 'no_of_female_subject'){
                    error.insertAfter('#no_of_female_subject_error');
                } else if (element.attr("name") == 'no_of_period'){
                    error.insertAfter('#no_of_period_error');
                } else if (element.attr("name") == 'study_design'){
                    error.insertAfter('#study_design_error');
                } else if (element.attr("name") == 'pre_housing'){
                    error.insertAfter('#pre_housing_error');
                } else if (element.attr("name") == 'post_housing'){
                    error.insertAfter('#post_housing_error');
                } else if (element.attr("name") == 'washout_period'){
                    error.insertAfter('#washout_period_error');
                } else if(element.attr("name") == 'study_id'){
                    error.insertAfter('#selectStudyNo');
                } else if(element.attr("name") == 'male_clinical_ward_location[]'){
                    error.insertAfter('#selectMaleClinicalWardLocation');
                } else if (element.attr("name") == 'female_clinical_ward_location[]'){
                    error.insertAfter('#selectFemaleClinicalWardLocation');
                } else {
                    error.insertAfter(element);
                }
            },
            messages: {
                study_condition: {
                    required: 'Please verify study condition',
                },
                no_of_subject: {
                    required: 'Please verify no of subject',
                },
                no_of_male_subject: {
                    required: 'Please verify no of male subject',
                },
                no_of_female_subject: {
                    required: 'Please verify no of female subject',
                },
                no_of_period: {
                    required: 'Please verify no of period',
                },
                study_design: {
                    required: 'Please verify study design',
                },
                pre_housing: {
                    required: 'Please verify pre housing',
                },
                post_housing: {
                    required: 'Please verify post housing',
                },
                washout_period: {
                    required: 'Please verify washout period',
                },
                study_id: {
                    required: 'Please select study no',
                },
                'male_clinical_ward_location[]': {
                    required: 'Please select male clinical ward location',
                },
                'female_clinical_ward_location[]': {
                    required: 'Please select female clinical ward location',
                },
            },
            submitHandler: function(form) {

                if(($('#totalMale').val() > 0) && ($('#totalFemale').val() > 0)) {
                    if(($('#selectMaleClinicalWardLocation').text().trim() == '') && ($('#selectFemaleClinicalWardLocation').text().trim() == '')) {
                        $('#openCalendarStudySlottingModal').modal('hide');
                        form.submit();
                    }
                } else {
                    if($('#totalMale').val() > 0) {
                        if($('#selectMaleClinicalWardLocation').text().trim() == '') {
                            $('#openCalendarStudySlottingModal').modal('hide');
                            form.submit();
                        }
                    } else {
                        if($('#selectFemaleClinicalWardLocation').text().trim() == '') {
                            $('#openCalendarStudySlottingModal').modal('hide');
                            form.submit();
                        }
                    }
                }
            }
        });

        $(document).on('click', '.saveCalendarStudySlot', function() {
            $('#addCalendarStudySlotting').submit();
        });
    });

    $(document).ready(function(){

        $(document).on('change', '#verify_all', function(){
            if($(this).is(':checked')) {
                $('.form-check-input').each(function(key, value){
                    $(this).prop('checked', true);
                    $(this).valid();
                });
            } else {
                $('.form-check-input').each(function(key, value){
                    $(this).prop('checked', false);
                    $(this).valid();
                });
            }
        });

        $(document).on('change', '#study_id', function(){
            var studyId = $(this).val();

            if(studyId != '') {
                $('#study_id-error').text('');

                $.ajax({
                    url: "/sms-admin/clinical-calendar/view/get-study-and-ward-data",
                    method: 'POST',
                    data: {
                        checkin_date_time: $('#checkin_date_time').val(),
                        study_id: $('#study_id').val(),
                    },
                    success: function(data){
                        $('.male_clinical_ward_location option').not(':first').remove().trigger('change');
                        $('.female_clinical_ward_location option').not(':first').remove().trigger('change');

                        $('#selectMaleClinicalWardLocation').text('');
                        $('#selectFemaleClinicalWardLocation').text('');

                        $('#totalMale').val(data.studyData.no_of_male_subjects);
                        $('#totalFemale').val(data.studyData.no_of_female_subjects);

                        $('.studyCondition').text('Study Condition - ' + data.studyData.study_condition_name.para_value);
                        $('.noOfSubject').text('No Of Subject - ' + data.studyData.no_of_subject);
                        $('.noOfMaleSubject').text('No Of Male Subject - ' + data.studyData.no_of_male_subjects);
                        $('.noOfFemaleSubject').text('No Of Female Subject - ' + data.studyData.no_of_female_subjects);
                        $('.noOfPeriod').text('No Of Period - ' + data.studyData.no_of_periods);
                        $('.studyDesign').text('Study Design - ' + data.studyData.study_design_name.para_value);
                        $('.preHousing').text('Pre Housing - ' + data.studyData.pre_housing);
                        $('.postHousing').text('Post Housing - ' + data.studyData.post_housing);
                        $('.washoutPeriod').text('Washout Period - ' + data.studyData.washout_period);

                        $('.hidePeriodNo').show();

                        if(data.studyData.study_slotting_count == 0) {
                            $('#period_no').val(1);
                        } else {
                            $('#period_no').val(data.studyData.study_slotting_count + 1);
                        }

                        if(data.studyData.no_of_male_subjects > 0) {
                            $('.hideMaleWards').show();

                            if(data.clinicalWards.length > 0) {
                                $.each(data.clinicalWards, function(key, value) {
                                    $.each(value, function(k, v) {
                                        var maleCapacity = v.match(/\((\d+)\)/);
                                        // console.log(maleCapacity[1]);

                                        if((data.femaleSelectedWards.indexOf(parseInt(k)) != -1) || (maleCapacity[1] == 0)) {
                                            $('.male_clinical_ward_location').append($("<option class=notSelectable disabled></option>").attr("value", k).text(v));
                                        } else {
                                            $('.male_clinical_ward_location').append($("<option class=selectable></option>").attr("value", k).text(v));
                                        }
                                    })
                                })
                            }
                        } else {
                            $('.hideMaleWards').hide();
                        }

                        if(data.studyData.no_of_female_subjects > 0) {
                            $('.hideFemaleWards').show();

                            if(data.clinicalWards.length > 0) {
                                $.each(data.clinicalWards, function(key, value) {
                                    $.each(value, function(k, v) {
                                        var femaleCapacity = v.match(/\((\d+)\)/);
                                        // console.log(femaleCapacity[1]);

                                        if((data.maleSelectedWards.indexOf(parseInt(k)) != -1) || (femaleCapacity[1] == 0)) {
                                            $('.female_clinical_ward_location').append($("<option class=notSelectable disabled></option>").attr("value", k).text(v));
                                        } else {
                                            $('.female_clinical_ward_location').append($("<option class=selectable></option>").attr("value", k).text(v));
                                        }
                                    })
                                })
                            }
                        } else {
                            $('.hideFemaleWards').hide();
                        }

                        $('.select2').select2({
                            dropdownParent: $('#openCalendarStudySlottingModal')
                        });

                        $('.select2-container').css('width', '100%');
                    }
                });
            } else {
                $('.male_clinical_ward_location option').not(':first').remove().trigger('change');
                $('.female_clinical_ward_location option').not(':first').remove().trigger('change');
                $('.studyCondition').text('Study Condition -');
                $('.noOfSubject').text('No Of Subject -');
                $('.noOfMaleSubject').text('No Of Male Subject -');
                $('.noOfFemaleSubject').text('No Of Female Subject -');
                $('.noOfPeriod').text('No Of Period -');
                $('.studyDesign').text('Study Design -');
                $('.preHousing').text('Pre Housing -');
                $('.postHousing').text('Post Housing -');
                $('.washoutPeriod').text('Washout Period -');
                $('.hidePeriodNo').hide();
                $('.hideMaleWards').hide();
                $('.hideFemaleWards').hide();
            }
        });

        $(document).on('change', '.male_clinical_ward_location', function(){
            var selectedItems = $(this).val();

            if (selectedItems.length > 0) {
                $('#male_clinical_ward_location-error').text('');
                $('#female_clinical_ward_location option.selectable').each(function(key, value){
                    if(selectedItems.indexOf($(this).val()) != -1) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                $('#female_clinical_ward_location option.selectable').each(function(key, value){
                    $(this).prop('disabled', false);
                });
            }

            checkCapacity();
        });

        $(document).on('change', '.female_clinical_ward_location', function(){
            var selectedItems = $(this).val();

            if (selectedItems.length > 0) {
                $('#female_clinical_ward_location-error').text('');
                $('#male_clinical_ward_location option.selectable').each(function(key, value){
                    if(selectedItems.indexOf($(this).val()) != -1) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                $('#male_clinical_ward_location option.selectable').each(function(key, value){
                    $(this).prop('disabled', false);
                });
            }

            checkCapacity();
        });
    });

</script>