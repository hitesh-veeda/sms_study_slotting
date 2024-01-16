<form class="custom-validation" action="{{ route('admin.saveStudySlot') }}" method="post" id="addStudySlotting" name="addStudySlotting">
    @csrf
    <input type="hidden" name="study_id" id="study_id" value="{{ $study->id }}">
    <input type="hidden" name="washout_period" id="washout_period" value="{{ $study->washout_period }}">
    <input type="hidden" name="pre_housing" id="pre_housing" value="{{ $study->pre_housing }}">
    <input type="hidden" name="post_housing" id="post_housing" value="{{ $study->post_housing }}">
    <input type="hidden" name="totalMale" id="totalMale" value="{{ $study->no_of_male_subjects }}">
    <input type="hidden" name="totalFemale" id="totalFemale" value="{{ $study->no_of_female_subjects }}">
    <input type="hidden" name="cr_location" id="cr_location" value="{{ $study->cr_location }}">

    <div class="form-group mb-3">
        <label>Period No<span class="mandatory">*</span></label>
        <select class="form-select select2" name="period_no" id="period_no" required>
            <option value="">Select Period No</option>
            @if($study->no_of_periods > 0)
                @for($i=1; $i<=$study->no_of_periods; $i++)
                    @if(!in_array($i, $studyPeriodNos))
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endif
                @endfor
            @endif
        </select>
        <span id="selectPeriodNo"></span>
    </div>

    @if($study->no_of_male_subjects > 0)
        <div class="form-group mb-3">
            <label>Male Clinical Ward Location<span class="mandatory">*</span></label>
            <select class="form-select select2 male_clinical_ward_location" name="male_clinical_ward_location[]" id="male_clinical_ward_location" multiple="multiple" required>
                <option value="" disabled>Select Male Clinical Ward location</option>
                @if(!is_null($crClinicalWardList))
                    @foreach ($crClinicalWardList as $cwlk => $cwlv)
                        <option value="{{ $cwlv->id }}">{{ $cwlv->ward_name }}</option>
                    @endforeach
                @endif
            </select>
            <span id="selectMaleClinicalWardLocation" class="custom-male-error" style="color: red;"></span>
        </div>
    @endif

    @if($study->no_of_female_subjects > 0)
        <div class="form-group mb-3">
            <label>Female Clinical Ward Location<span class="mandatory">*</span></label>
            <select class="form-select select2 female_clinical_ward_location" name="female_clinical_ward_location[]" id="female_clinical_ward_location" multiple="multiple" required>
                <option value="" disabled>Select Female Clinical Ward location</option>
                @if(!is_null($crClinicalWardList))
                    @foreach ($crClinicalWardList as $cwlk => $cwlv)
                        <option value="{{ $cwlv->id }}">{{ $cwlv->ward_name }}</option>
                    @endforeach
                @endif
            </select>
            <span id="selectFemaleClinicalWardLocation" class="custom-female-error" style="color: red;"></span>
        </div>
    @endif

    <div class="form-group mb-3">
        <label>Check In Date Time<span class="mandatory">*</span></label>
        <input type="datetime-local" class="form-control" min="{{ $expectedChekinDate }}" data-mindate="{{ ($expectedChekinDate != '') ? date('d-M-Y H:i', strtotime($expectedChekinDate)) : '' }}" autocomplete="off" name="check_in_date_time" id="check_in_date_time" required>
    </div>
</form>
<script type="text/javascript">

    function checkCapacity() {
        var checkInDate = $('#check_in_date_time').val();
        var maleClinicalWards = $('#male_clinical_ward_location').val();
        var femaleClinicalWards = $('#female_clinical_ward_location').val();

        if(checkInDate != '') {

            $.ajax({
                url: '/sms-admin/clinical-slotting/view/check-clinical-wards-capacity',
                method: 'POST',
                data: {
                    'checkin_date_time': checkInDate,
                    'no_of_male_subject': $('#totalMale').val(),
                    'no_of_female_subject': $('#totalFemale').val(),
                    'cr_location': $('#cr_location').val(),
                    'male_clinical_wards': maleClinicalWards,
                    'female_clinical_wards': femaleClinicalWards,
                },
                success: function(data) {
                    if(data.status == 'false') {
                        $('#selectMaleClinicalWardLocation').text(data.maleErrorMessage);
                        $('#selectFemaleClinicalWardLocation').text(data.femaleErrorMessage);
                    } else {
                        $('#selectMaleClinicalWardLocation, #selectFemaleClinicalWardLocation').text('');
                    }
                }
            });
        } else {
            $('#selectMaleClinicalWardLocation, #selectFemaleClinicalWardLocation').text('');
        }
    }

    $(document).ready(function(){

        $('.select2').select2({
            dropdownParent: $('#openStudySlottingModal')
        });

        $('.select2-container').css('width', '100%');

        $('#addStudySlotting').validate({
            errorElement: 'span',
            rules: {
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
                    min: $('#check_in_date_time').prop('min')
                }
            },
            errorPlacement: function(error, element) {

                if(element.attr("name") == 'period_no'){
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
                    min: 'Please select date and time greater or equal to ' + $('#check_in_date_time').data('mindate'),
                }
            }
        });

        $(document).on('click', '.saveStudySlot', function(){
            $('#addStudySlotting').submit();
        });

        $('#addStudySlotting').on('submit', function(e){

            if(($('#addStudySlotting').valid())) {
                $('#openStudySlottingModal').modal('hide');
            }
        });
    });

    $(document).ready(function(){
        
        $(document).on('change', '#period_no', function(){
            var periodNo = $(this).val();

            if(periodNo != '') {
                $('#period_no-error').text('');
            }
        });

        $(document).on('change', '.male_clinical_ward_location', function(e){
            var selectedItems = $(this).val();

            if (selectedItems.length > 0) {
                $('#male_clinical_ward_location-error').text('');
                $('#female_clinical_ward_location option').each(function(key, value){
                    if(selectedItems.indexOf($(this).val()) != -1) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                $('#female_clinical_ward_location option').each(function(key, value){
                    $(this).prop('disabled', false);
                });
            }

            if($('#check_in_date_time').val() != '') {
                checkCapacity();
            }
        });

        $(document).on('change', '.female_clinical_ward_location', function(e){
            var selectedItems = $(this).val();

            if (selectedItems.length > 0) {
                $('#female_clinical_ward_location-error').text('');
                $('#male_clinical_ward_location option').each(function(key, value){
                    if(selectedItems.indexOf($(this).val()) != -1) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                $('#male_clinical_ward_location option').each(function(key, value){
                    $(this).prop('disabled', false);
                });
            }

            if($('#check_in_date_time').val() != '') {
                checkCapacity();
            }
        });
    });

    $(document).ready(function(){

        $(document).on('change', '#check_in_date_time', function(e){
            if($(this).val() != '') {
                checkCapacity();
            }
        });
    });

</script>