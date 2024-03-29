<form class="custom-validation" action="{{ route('admin.saveStudySlot') }}" method="post" id="addStudySlotting" name="addStudySlotting">
    @csrf
    <input type="hidden" name="studyId" id="studyId" value="{{ $study->id }}">
    <input type="hidden" name="studyNo" id="studyNo" value="{{ $study->study_no }}">
    <input type="hidden" name="crLocationName" id="crLocationName" value="{{ (($study->crLocationName != '') && ($study->crLocationName->location_name != '')) ? $study->crLocationName->location_name : ''}}">
    <input type="hidden" name="projectManagerName" id="projectManagerName" value="{{ (($study->projectManager != '') && ($study->projectManager->name != '')) ? $study->projectManager->name : ''}}">
    <input type="hidden" name="washoutPeriod" id="washoutPeriod" value="{{ $study->washout_period }}">
    <input type="hidden" name="preHousing" id="preHousing" value="{{ $study->pre_housing }}">
    <input type="hidden" name="postHousing" id="postHousing" value="{{ $study->post_housing }}">
    <input type="hidden" name="totalMale" id="totalMale" value="{{ $study->no_of_male_subjects }}">
    <input type="hidden" name="totalFemale" id="totalFemale" value="{{ $study->no_of_female_subjects }}">
    <input type="hidden" name="crLocation" id="crLocation" value="{{ $study->cr_location }}">

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
                        <label class="form-check-label" for="study_condition">Study Condition {{ ((!is_null($study->studyConditionName)) && ($study->studyConditionName->para_value != '')) ? '- ' .$study->studyConditionName->para_value : '' }}</label>
                        <span id="study_condition_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_subject" id="no_of_subject" required>
                        <label class="form-check-label" for="no_of_subject">No Of Subject {{'- ' .$study->no_of_subject }}</label>
                        <span id="no_of_subject_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_male_subject" id="no_of_male_subject" required>
                        <label class="form-check-label" for="no_of_male_subject">No Of Male Subject {{'- ' .$study->no_of_male_subjects }}</label>
                        <span id="no_of_male_subject_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_female_subject" id="no_of_female_subject" required>
                        <label class="form-check-label" for="no_of_female_subject">No Of Female Subject {{'- ' .$study->no_of_female_subjects }}</label>
                        <span id="no_of_female_subject_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="no_of_period" id="no_of_period" required>
                        <label class="form-check-label" for="no_of_period">No Of Period {{'- ' .$study->no_of_periods }}</label>
                        <span id="no_of_period_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="study_design" id="study_design" required>
                        <label class="form-check-label" for="study_design">Study Design {{ ((!is_null($study->studyDesignName)) && ($study->studyDesignName->para_value != '')) ? '- ' .$study->studyDesignName->para_value : '' }}</label>
                        <span id="study_design_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="pre_housing" id="pre_housing" required>
                        <label class="form-check-label" for="pre_housing">Pre Housing {{'- ' .$study->pre_housing. ' hours' }}</label>
                        <span id="pre_housing_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="post_housing" id="post_housing" required>
                        <label class="form-check-label" for="post_housing">Post Housing {{'- ' .$study->post_housing. ' hours' }}</label>
                        <span id="post_housing_error"></span>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="washout_period" id="washout_period" required>
                        <label class="form-check-label" for="washout_period">Washout Period {{'- ' .$study->washout_period. ' days' }}</label>
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
                        <label>Check In Date And Time<span class="mandatory">*</span></label>
                        <input type="datetime-local" class="form-control" min="{{ $expectedChekinDate }}" autocomplete="off" name="check_in_date_time" id="check_in_date_time" required>
                    </div>

                    <div class="form-group mb-2">
                        <label>Period No<span class="mandatory">*</span></label>
                        <select class="form-select select2" name="period_no" id="period_no" required>
                            <option value="">Select Period No</option>
                            @if($study->no_of_periods > 0)
                                @for($i=$studyPeriodStartFrom; $i<=$study->no_of_periods; $i++)
                                    <option @if($i != $studyPeriodStartFrom) disabled @endif value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            @endif
                        </select>
                        <span id="selectPeriodNo"></span>
                    </div>

                    @if($study->no_of_male_subjects > 0)
                        <div class="hideMaleWards" style="display: none;">
                            <div class="form-group mb-2">
                                <label>Male Clinical Ward Location<span class="mandatory">*</span></label>
                                <select class="form-select select2 male_clinical_ward_location" name="male_clinical_ward_location[]" id="male_clinical_ward_location" multiple="multiple" required>
                                    <option disabled>Select Male Clinical Ward location</option>
                                    @if(!is_null($crClinicalWardList))
                                        @foreach ($crClinicalWardList as $cwlk => $cwlv)
                                            <option value="{{ $cwlv->id }}" class="selectable">{{ $cwlv->ward_name. ' (' .$cwlv->no_of_beds. ')' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span id="selectMaleClinicalWardLocation" class="custom-male-error" style="color: red;"></span>
                            </div>
                        </div>
                    @endif

                    @if($study->no_of_female_subjects > 0)
                        <div class="hideFemaleWards" style="display: none;">
                            <div class="form-group mb-2">
                                <label>Female Clinical Ward Location<span class="mandatory">*</span></label>
                                <select class="form-select select2 female_clinical_ward_location" name="female_clinical_ward_location[]" id="female_clinical_ward_location" multiple="multiple" required>
                                    <option disabled>Select Female Clinical Ward location</option>
                                    @if(!is_null($crClinicalWardList))
                                        @foreach ($crClinicalWardList as $cwlk => $cwlv)
                                            <option value="{{ $cwlv->id }}" class="selectable">{{ $cwlv->ward_name. ' (' .$cwlv->no_of_beds. ')' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span id="selectFemaleClinicalWardLocation" class="custom-female-error" style="color: red;"></span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">

    function checkCapacity() {
        var checkInDate = $('#check_in_date_time').val();

        if(checkInDate != '') {
            $.ajax({
                url: '/sms-admin/study-slot/view/check-clinical-wards-capacity',
                method: 'POST',
                data: {
                    'checkin_date_time': checkInDate,
                    'no_of_male_subject': $('#totalMale').val(),
                    'no_of_female_subject': $('#totalFemale').val(),
                    'cr_location': $('#crLocation').val(),
                    'male_clinical_wards': $('#male_clinical_ward_location').val(),
                    'female_clinical_wards': $('#female_clinical_ward_location').val(),
                },
                success: function(data) {
                    $('#male_clinical_ward_location option:not(:first)').prop('disabled', false).addClass('selectable').removeClass('notSelectable');
                    $('#female_clinical_ward_location option:not(:first)').prop('disabled', false).addClass('selectable').removeClass('notSelectable');

                    if (data.remainingWardsCapacity.length > 0) {
                        $.each(data.remainingWardsCapacity, function (key, element) {
                            $.each(element, function (k, v) {
                                var maleOption = $('#male_clinical_ward_location option[value=' + k + ']');
                                var isMaleOptionSelected = $('#male_clinical_ward_location option[value=' + k + ']').prop('selected');
                                var femaleOption = $('#female_clinical_ward_location option[value=' + k + ']');
                                var isFemaleOptionSelected = $('#female_clinical_ward_location option[value=' + k + ']').prop('selected');

                                if (v == 0) {
                                    maleOption.text(function (i, txt) {
                                        return txt.replace(/\((\d+)\)/, '(' + v + ')');
                                    }).prop('disabled', true).prop('selected', false).removeClass('selectable').addClass('notSelectable');

                                    femaleOption.text(function (i, txt) {
                                        return txt.replace(/\((\d+)\)/, '(' + v + ')');
                                    }).prop('disabled', true).prop('selected', false).removeClass('selectable').addClass('notSelectable');
                                } else {
                                    maleOption.text(function (i, txt) {
                                        return txt.replace(/\((\d+)\)/, '(' + v + ')');
                                    }).prop('disabled', isFemaleOptionSelected).removeClass('notSelectable').addClass('selectable');

                                    femaleOption.text(function (i, txt) {
                                        return txt.replace(/\((\d+)\)/, '(' + v + ')');
                                    }).prop('disabled', isMaleOptionSelected).removeClass('notSelectable').addClass('selectable');
                                }
                            });
                        });
                    }

                    if(data.maleWardSelection == false) {
                        $('#male_clinical_ward_location option.selectable').each(function(key, value) {
                            if(!this.selected) {
                                $(this).prop('disabled', true).removeClass('selectable').addClass('notSelectable');
                            } else {
                                $('#female_clinical_ward_location option[value=' + $(this).val() + ']').prop('disabled', true).removeClass('selectable').addClass('notSelectable');
                            }
                        });
                    }

                    if(data.femaleWardSelection == false) {
                        $('#female_clinical_ward_location option.selectable').each(function(key, value) {
                            if(!this.selected) {
                                $(this).prop('disabled', true).removeClass('selectable').addClass('notSelectable');
                            } else {
                                $('#male_clinical_ward_location option[value=' + $(this).val() + ']').prop('disabled', true).removeClass('selectable').addClass('notSelectable');
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

                    $('.select2').select2({
                        dropdownParent: $('#openStudySlottingModal')
                    });
                }
            });
        }
    }

    $(document).ready(function(){

        $('.select2').select2({
            dropdownParent: $('#openStudySlottingModal')
        });

        $('.modal-title').text('Study Slotting : ' + $('#studyNo').val() + ' (' + $('#crLocationName').val() + ') - ' + $('#projectManagerName').val());

        $('.select2-container').css('width', '100%');

        $('#addStudySlotting').validate({
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
                period_no: {
                    required: true,
                },
                'male_clinical_ward_location[]': {
                    required: true,
                },
                'female_clinical_ward_location[]': {
                    required: true,
                },
                check_in_date_time: {
                    required: true,
                    min: false,
                }
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
                } else if(element.attr("name") == 'period_no'){
                    error.insertAfter('#selectPeriodNo');
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
                period_no: {
                    required: 'Please select period no',
                },
                'male_clinical_ward_location[]': {
                    required: 'Please select male clinical ward location',
                },
                'female_clinical_ward_location[]': {
                    required: 'Please select female clinical ward location',
                },
                check_in_date_time: {
                    required: 'Please select check in date and time',
                }
            },
            submitHandler: function(form) {

                if(($('#totalMale').val() > 0) && ($('#totalFemale').val() > 0)) {
                    if(($('#selectMaleClinicalWardLocation').text().trim() == '') && ($('#selectFemaleClinicalWardLocation').text().trim() == '')) {
                        $('#openStudySlottingModal').modal('hide');
                        form.submit();
                    }
                } else {
                    if($('#totalMale').val() > 0) {
                        if($('#selectMaleClinicalWardLocation').text().trim() == '') {
                            $('#openStudySlottingModal').modal('hide');
                            form.submit();
                        }
                    } else {
                        if($('#selectFemaleClinicalWardLocation').text().trim() == '') {
                            $('#openStudySlottingModal').modal('hide');
                            form.submit();
                        }
                    }
                }
            }
        });

        $(document).on('click', '.saveStudySlot', function(){
            $('#addStudySlotting').submit();
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

        $(document).on('change', '#check_in_date_time', function(){
            if($(this).val() != '') {
                $('.hideMaleWards').show();
                $('.hideFemaleWards').show();
                checkCapacity();
            } else {
                $('.male_clinical_ward_location').val(null).trigger('change');
                $('.female_clinical_ward_location').val(null).trigger('change');
                $('.hideMaleWards').hide();
                $('.hideFemaleWards').hide();
            }
        });
        
        $(document).on('change', '#period_no', function(){
            var periodNo = $(this).val();

            if(periodNo != '') {
                $('#period_no-error').text('');
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