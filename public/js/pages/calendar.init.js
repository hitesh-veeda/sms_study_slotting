'use strict';

/* eslint-disable require-jsdoc */
/* eslint-env jquery */
/* global moment, tui, chance */
/* global findCalendar, CalendarList, ScheduleList, generateSchedule */

(function(window, Calendar) {
    var cal, resizeThrottled;
    var useCreationPopup = false;
    // var useCreationPopup = true;
    var useDetailPopup = true;
    var datePicker, selectedCalendar;

    // default keys and styles
    var themeConfig = {

        // month schedule style
        'month.schedule.borderRadius': '10px',
        // 'month.schedule.height': '30px',
        'month.schedule.marginTop': '5px',
        'month.schedule.marginLeft': '8px',
        'month.schedule.marginRight': '8px',
        
        // week daygrid schedule style
        'week.dayGridSchedule.borderRadius': '10px',
        // 'week.dayGridSchedule.height': '18px',
        'week.dayGridSchedule.marginTop': '5px',
        'week.dayGridSchedule.marginLeft': '10px',
        'week.dayGridSchedule.marginRight': '8px',
    };

    cal = new Calendar('#calendar', {
        defaultView: 'month',
        // disableClick: true,
        // disableDblClick: true,
        taskView: false,
        scheduleView: true,
        useCreationPopup: useCreationPopup,
        useDetailPopup: useDetailPopup,
        calendars: CalendarList,
        theme: themeConfig,
        month: {
            visibleScheduleCount: 5
        },
        week: {
            visibleScheduleCount: 5
        },
        day: {
            visibleScheduleCount: 5
        },
        template: {
            milestone: function(model) {
                return '<span class="calendar-font-icon ic-milestone-b"></span> <span style="background-color: ' + model.bgColor + '">' + model.title + '</span>';
            },
            // allday: function(schedule) {
            //     return getTimeTemplate(schedule, true);
            // },
            // time: function(schedule) {
            //     return getTimeTemplate(schedule, false);
            // },
            allday: function(schedule) {
                var color = (schedule.location == 'Shivalik') ? '#000000' : '#e6e6e6';
                return `<span style="color: ${color}">${schedule.title}</span>`;
            },
            time: function(schedule) {
                var color = (schedule.location == 'Shivalik') ? '#000000' : '#e6e6e6';
                return `<span style="color: ${color}">${schedule.title}</span>`;
            },
            popupDetailDate: function() {
                return '';
            },
        },
    });

    // event handlers
    cal.on({
        'clickMore': function(e) {
            console.log('clickMore', e);
        },
        'clickSchedule': function(e) {
            console.log('clickSchedule', e);
        },
        'clickDayname': function(date) {
            console.log('clickDayname', date);
        },
        'beforeCreateSchedule': function(e) {
            console.log('beforeCreateSchedule', e);
            e.guide.clearGuideElement();

            var selectedDate = new Date(e.start);
            var currentDate = new Date();
            var day = selectedDate.getDate();
            var month = selectedDate.getMonth() + 1;
            var year = selectedDate.getFullYear();
            var hour = currentDate.getHours();
            var minutes = currentDate.getMinutes();
            var flag = 0;

            if(day < 10) {
                day = '0' + day;
            }

            if(month < 10) {
                month = '0' + month;
            }

            if(minutes < 10) {
                minutes = '0' + minutes;
            }

            selectedDate = year + '-' + month + '-' + day + 'T' + hour + ':' + minutes;

            if(day == currentDate.getDate() && month == currentDate.getMonth() + 1 && year == currentDate.getFullYear()) {
                flag = 1;
            } else if(day > currentDate.getDate() && month >= currentDate.getMonth() + 1 && year >= currentDate.getFullYear()) {
                flag = 1;
            } else if(day < currentDate.getDate() && month > currentDate.getMonth() + 1 && year >= currentDate.getFullYear()) {
                flag = 1;
            }

            if(flag == 1) {
                $.ajax({
                    url: "/sms-admin/clinical-calendar/add/open-study-slot-modal-for-calendar",
                    method: 'POST',
                    data: {
                        'check_in_date_time': selectedDate,
                        'cr_location': $('#location option:selected').text().trim(),
                    },
                    success: function(data){
                        $('#showEditCalendarStudySlottingModal').html('');
                        $('#showCalendarStudySlottingModal').html(data);
                        $('#checkin_date_time').val(selectedDate);
                        $('#openCalendarStudySlottingModal').modal('show');
                    }
                });
            }

            // saveNewSchedule(e);
        },
        'beforeUpdateSchedule': function(e) {
            console.log('beforeUpdateSchedule', e);
            // e.schedule.start = e.start;
            // e.schedule.end = e.end;
            // cal.updateSchedule(e.schedule.id, e.schedule.calendarId, e.schedule);

            $.ajax({
                url: "/sms-admin/clinical-calendar/edit/open-edit-study-slot-modal-for-calendar/" + e.schedule.id,
                method: 'GET',
                success: function(data){
                    $('#showCalendarStudySlottingModal').html('');
                    $('#showEditCalendarStudySlottingModal').html(data);
                    $('#openEditCalendarStudySlottingModal').modal('show');
                }
            });
        },
        'beforeDeleteSchedule': function(e) {
            console.log('beforeDeleteSchedule', e);
            // cal.deleteSchedule(e.schedule.id, e.schedule.calendarId);

            $.ajax({
                url: "/sms-admin/clinical-calendar/delete/delete-clinical-calendar-slot/" + e.schedule.id,
                method: 'GET',
                success: function(data){
                    if(data == 'true') {
                        toastr.success('Clinical Slotting successfully deleted');
                        location.reload();
                    }
                }
            });
        },
        'afterRenderSchedule': function(e) {
            var schedule = e.schedule;
            // var element = cal.getElement(schedule.id, schedule.calendarId);
            // console.log('afterRenderSchedule', element);
        },
        'clickTimezonesCollapseBtn': function(timezonesCollapsed) {
            console.log('timezonesCollapsed', timezonesCollapsed);

            if (timezonesCollapsed) {
                cal.setTheme({
                    'week.daygridLeft.width': '77px',
                    'week.timegridLeft.width': '77px',
                });
            } else {
                cal.setTheme({
                    'week.daygridLeft.width': '60px',
                    'week.timegridLeft.width': '60px'
                });
            }

            return true;
        }
    });

    document.getElementById('calendar').addEventListener('keydown', e => {
        console.log('keydown', e);
    });

    /**
     * Get time template for time and all-day
     * @param {Schedule} schedule - schedule
     * @param {boolean} isAllDay - isAllDay or hasMultiDates
     * @returns {string}
     */
    function getTimeTemplate(schedule, isAllDay) {
        var html = [];
        var start = moment(schedule.start.toUTCString());
        if (!isAllDay) {
            html.push('<strong>' + start.format('HH:mm') + '</strong> ');
        }
        if (schedule.isPrivate) {
            html.push('<span class="calendar-font-icon ic-lock-b"></span>');
            html.push(' Private');
        } else {
            if (schedule.isReadOnly) {
                html.push('<span class="calendar-font-icon ic-readonly-b"></span>');
            } else if (schedule.recurrenceRule) {
                html.push('<span class="calendar-font-icon ic-repeat-b"></span>');
            } else if (schedule.attendees.length) {
                html.push('<span class="calendar-font-icon ic-user-b"></span>');
            } else if (schedule.location) {
                html.push('<span class="calendar-font-icon ic-location-b"></span>');
            }
            html.push(' ' + schedule.title);
        }

        return html.join('');
    }

    /**
     * A listener for click the menu
     * @param {Event} e - click event
     */
    function onClickMenu(e) {
        var target = $(e.target).closest('a[role="menuitem"]')[0];
        var action = getDataAction(target);
        var options = cal.getOptions();
        var viewName = '';

        console.log(target);
        console.log(action);
        switch (action) {
            case 'toggle-daily':
                viewName = 'day';
                break;
            case 'toggle-weekly':
                viewName = 'week';
                break;
            case 'toggle-monthly':
                options.month.visibleWeeksCount = 0;
                viewName = 'month';
                break;
            case 'toggle-weeks2':
                options.month.visibleWeeksCount = 2;
                viewName = 'month';
                break;
            case 'toggle-weeks3':
                options.month.visibleWeeksCount = 3;
                viewName = 'month';
                break;
            case 'toggle-narrow-weekend':
                options.month.narrowWeekend = !options.month.narrowWeekend;
                options.week.narrowWeekend = !options.week.narrowWeekend;
                viewName = cal.getViewName();

                target.querySelector('input').checked = options.month.narrowWeekend;
                break;
            case 'toggle-start-day-1':
                options.month.startDayOfWeek = options.month.startDayOfWeek ? 0 : 1;
                options.week.startDayOfWeek = options.week.startDayOfWeek ? 0 : 1;
                viewName = cal.getViewName();

                target.querySelector('input').checked = options.month.startDayOfWeek;
                break;
            case 'toggle-workweek':
                options.month.workweek = !options.month.workweek;
                options.week.workweek = !options.week.workweek;
                viewName = cal.getViewName();

                target.querySelector('input').checked = !options.month.workweek;
                break;
            default:
                break;
        }

        cal.setOptions(options, true);
        cal.changeView(viewName, true);

        setDropdownCalendarType();
        setRenderRangeText();
        // setSchedules();
    }

    function onClickNavi(e) {
        var action = getDataAction(e.target);

        switch (action) {
            case 'move-prev':
                cal.prev();
                break;
            case 'move-next':
                cal.next();
                break;
            case 'move-today':
                cal.today();
                break;
            default:
                return;
        }

        setRenderRangeText();
        // setSchedules();
    }

    function onNewSchedule() {
        var title = $('#new-schedule-title').val();
        var location = $('#new-schedule-location').val();
        var isAllDay = document.getElementById('new-schedule-allday').checked;
        var start = datePicker.getStartDate();
        var end = datePicker.getEndDate();
        var calendar = selectedCalendar ? selectedCalendar : CalendarList[0];

        if (!title) {
            return;
        }

        cal.createSchedules([{
            id: String(chance.guid()),
            calendarId: calendar.id,
            title: title,
            isAllDay: isAllDay,
            start: start,
            end: end,
            category: isAllDay ? 'allday' : 'time',
            dueDateClass: '',
            color: calendar.color,
            bgColor: calendar.bgColor,
            dragBgColor: calendar.bgColor,
            borderColor: calendar.borderColor,
            raw: {
                location: location
            },
            state: 'Busy'
        }]);

        $('#modal-new-schedule').modal('hide');
    }

    function onChangeNewScheduleCalendar(e) {
        var target = $(e.target).closest('a[role="menuitem"]')[0];
        var calendarId = getDataAction(target);
        changeNewScheduleCalendar(calendarId);
    }

    function changeNewScheduleCalendar(calendarId) {
        var calendarNameElement = document.getElementById('calendarName');
        var calendar = findCalendar(calendarId);
        var html = [];

        html.push('<span class="calendar-bar" style="background-color: ' + calendar.bgColor + '; border-color:' + calendar.borderColor + ';"></span>');
        html.push('<span class="calendar-name">' + calendar.name + '</span>');

        calendarNameElement.innerHTML = html.join('');

        selectedCalendar = calendar;
    }

    function createNewSchedule(event) {
        var start = event.start ? new Date(event.start.getTime()) : new Date();
        var end = event.end ? new Date(event.end.getTime()) : moment().add(1, 'hours').toDate();

        if (useCreationPopup) {
            cal.openCreationPopup({
                start: start,
                end: end
            });
        }
    }

    function saveNewSchedule(scheduleData) {
        var calendar = scheduleData.calendar || findCalendar(scheduleData.calendarId);
        var schedule = {
            id: String(chance.guid()),
            title: scheduleData.title,
            isAllDay: scheduleData.isAllDay,
            start: scheduleData.start,
            end: scheduleData.end,
            category: scheduleData.isAllDay ? 'allday' : 'time',
            dueDateClass: '',
            color: calendar.color,
            bgColor: calendar.bgColor,
            dragBgColor: calendar.bgColor,
            borderColor: calendar.borderColor,
            location: scheduleData.location,
            raw: {
                class: scheduleData.raw['class']
            },
            state: scheduleData.state
        };
        if (calendar) {
            schedule.calendarId = calendar.id;
            schedule.color = calendar.color;
            schedule.bgColor = calendar.bgColor;
            schedule.borderColor = calendar.borderColor;
        }

        cal.createSchedules([schedule]);

        refreshScheduleVisibility();
    }

    function onChangeCalendars(e) {
        var calendarId = e.target.value;
        var checked = e.target.checked;
        var viewAll = document.querySelector('.lnb-calendars-item input');
        var calendarElements = Array.prototype.slice.call(document.querySelectorAll('#calendarList input'));
        var allCheckedCalendars = true;

        if (calendarId === 'all') {
            allCheckedCalendars = checked;

            calendarElements.forEach(function(input) {
                var span = input.parentNode;
                input.checked = checked;
                span.style.backgroundColor = checked ? span.style.borderColor : 'transparent';
            });

            CalendarList.forEach(function(calendar) {
                calendar.checked = checked;
            });
        } else {
            findCalendar(calendarId).checked = checked;

            allCheckedCalendars = calendarElements.every(function(input) {
                return input.checked;
            });

            if (allCheckedCalendars) {
                viewAll.checked = true;
            } else {
                viewAll.checked = false;
            }
        }

        refreshScheduleVisibility();
    }

    function refreshScheduleVisibility() {
        var calendarElements = Array.prototype.slice.call(document.querySelectorAll('#calendarList input'));

        CalendarList.forEach(function(calendar) {
            cal.toggleSchedules(calendar.id, !calendar.checked, true);
        });

        cal.render(true);

        calendarElements.forEach(function(input) {
            var span = input.nextElementSibling;
            span.style.backgroundColor = input.checked ? span.style.borderColor : 'transparent';
        });
    }

    function setDropdownCalendarType() {
        var calendarTypeName = document.getElementById('calendarTypeName');
        var calendarTypeIcon = document.getElementById('calendarTypeIcon');
        var options = cal.getOptions();
        var type = cal.getViewName();
        var iconClassName;

        if (type === 'day') {
            type = 'Daily';
            iconClassName = 'calendar-icon ic_view_day';
        } else if (type === 'week') {
            type = 'Weekly';
            iconClassName = 'calendar-icon ic_view_week';
        } else if (options.month.visibleWeeksCount === 2) {
            type = '2 weeks';
            iconClassName = 'calendar-icon ic_view_week';
        } else if (options.month.visibleWeeksCount === 3) {
            type = '3 weeks';
            iconClassName = 'calendar-icon ic_view_week';
        } else {
            type = 'Monthly';
            iconClassName = 'calendar-icon ic_view_month';
        }

        calendarTypeName.innerHTML = type;
        calendarTypeIcon.className = iconClassName;
    }

    function setRenderRangeText() {
        var renderRange = document.getElementById('renderRange');
        var options = cal.getOptions();
        var viewName = cal.getViewName();
        var html = [];
        if (viewName === 'day') {
            html.push(moment(cal.getDate().getTime()).format('DD MMM YYYY'));
        } else if (viewName === 'month' &&
            (!options.month.visibleWeeksCount || options.month.visibleWeeksCount > 4)) {
            html.push(moment(cal.getDate().getTime()).format('MMM YYYY'));
        } else {
            html.push(moment(cal.getDateRangeStart().getTime()).format('DD MMM YYYY'));
            html.push(' ~ ');
            html.push(moment(cal.getDateRangeEnd().getTime()).format(' DD MMM YYYY'));
        }
        renderRange.innerHTML = html.join('');
    }

    function setSchedules() {
        cal.clear();
        var schedules = [];
        // generateSchedule(cal.getViewName(), cal.getDateRangeStart(), cal.getDateRangeEnd());
        // cal.createSchedules(ScheduleList);
        // var schedules = [
        //     {id: 489273, title: 'Workout for 2018-08-17', isAllDay: false, start: '2024-02-06T10:00+09:00', end: '2024-02-06T14:00:00+09:00', goingDuration: 30, comingDuration: 30, color: '#ffffff', isVisible: true, bgColor: '#69BB2D', dragBgColor: '#69BB2D', borderColor: '#69BB2D', calendarId: 'logged-workout', category: 'time', dueDateClass: '', customStyle: 'cursor: default;', isPending: false, isFocused: false, isReadOnly: true, isPrivate: false, location: '', attendees: '', recurrenceRule: '', state: ''},
        //     {id: 18073, title: 'completed with blocks', isAllDay: false, start: '2024-02-06T09:00:00+09:00', end: '2024-02-06T10:00:00+09:00', color: '#ffffff', isVisible: true, bgColor: '#54B8CC', dragBgColor: '#54B8CC', borderColor: '#54B8CC', calendarId: 'workout', category: 'time', dueDateClass: '', customStyle: '', isPending: false, isFocused: false, isReadOnly: false, isPrivate: false, location: '', attendees: '', recurrenceRule: '', state: ''}
        // ];
        // cal.createSchedules(schedules);

        $.ajax({
            url: '/sms-admin/clinical-calendar/view/clinical-calendar-list',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { location:$('#location').val() },
            success: function(data) {
                $.each(data, function (key, value) {
                    var checkinDateTime = moment(value.check_in_date_time).format('YYYY-MM-DD HH:mm:ss');
                    var calendarId = 0;
                    var body = '';
                    var maleWardNames = [];
                    var femaleWardNames = [];
                    var actualStartDate = null;

                    if(value.study_no.cr_location_name.location_name == 'Shivalik') {
                        calendarId = 1;
                    } else if(value.study_no.cr_location_name.location_name == 'Vedant') {
                        calendarId = 2;
                    } else if(value.study_no.cr_location_name.location_name == 'Mehsana') {
                        calendarId = 3;
                    }

                    $.each(value.male_clinical_wards, function(k, v){
                        maleWardNames.push(v.male_location_name.ward_name);
                    });

                    $.each(value.female_clinical_wards, function(k, v){
                        femaleWardNames.push(v.female_location_name.ward_name);
                    });

                    body += "<span style='font-weight: bold;'>Check In - </span>" + moment(value.check_in_date_time).format('DD MMM YYYY HH:mm') + '<br>';
                    body += "<span style='font-weight: bold;'>Check Out - </span>" + moment(value.check_out_date_time).format('DD MMM YYYY HH:mm') + '<br>';

                    if (maleWardNames.length > 0) {
                        body += "<span style='font-weight: bold;'>Male Clinical Wards - </span>" + maleWardNames.join(' | ') + "<br>";
                    }

                    if (femaleWardNames.length > 0) {
                        body += "<span style='font-weight: bold;'>Female Clinical Wards - </span>" + femaleWardNames.join(' | ') + "<br>";
                    }

                    $.each(value.study_no.drug_details, function(k, v) {
                        body += "<span style='font-weight: bold;'>Molecule Name - </span>" + v.drug_name.drug_name + "<br>";
                    });

                    if(value.study_no.schedule.length > 0) {
                        $.each(value.study_no.schedule, function(k, v) {
                            if (value.period_no == v.period_no) {
                                actualStartDate = v.actual_start_date;
                            }
                        });
                    }

                    body += "<span style='font-weight: bold;'>No Of Subjects - </span>" + value.study_no.no_of_subject;
                    body += "<br><span style='font-weight: bold;'>No Of Male Subjects - </span>" + value.study_no.no_of_male_subjects;
                    body += "<br><span style='font-weight: bold;'>No Of Female Subjects - </span>" + value.study_no.no_of_female_subjects;
                    body += "<br><span style='font-weight: bold;'>Period No - </span>" + value.period_no + "/" + value.study_no.no_of_periods;
                    body += "<br><span style='font-weight: bold;'>Washout Days - </span>" + value.study_no.washout_period;
                    body += "<br><span style='font-weight: bold;'>Pre Housing Hours - </span>" + value.study_no.pre_housing;
                    body += "<br><span style='font-weight: bold;'>Post Housing Hours - </span>" + value.study_no.post_housing;
                    body += "<br><span style='font-weight: bold;'>Type Of Population - </span>" + value.study_no.subject_type_name.para_value;
                    body += "<br><span style='font-weight: bold;'>Project Manager - </span>" + value.study_no.project_manager.name;
                    body += "<br><span style='font-weight: bold;'>Study Type - </span>" + value.study_no.study_type.para_value;

                    schedules.push({
                        'id': value.id,
                        'calendarId': calendarId,
                        'title': value.study_no.study_no + '(P'+ value.period_no +')',
                        'body': body,
                        'category': 'allday',
                        'dueDateClass': '',
                        'location': value.study_no.cr_location_name.location_name,
                        'isAllDay': true,
                        'isReadOnly': (actualStartDate != null) ? true : false,
                        'start': checkinDateTime,
                        'end': checkinDateTime,
                    })
                });
                cal.createSchedules(schedules);
            }
        });
        refreshScheduleVisibility();
    }

    $('#location').on('change', function(){
        setSchedules();
    });

    function setEventListener() {
        $('#menu-navi').on('click', onClickNavi);
        $('.dropdown-menu a[role="menuitem"]').on('click', onClickMenu);
        $('#lnb-calendars').on('change', onChangeCalendars);

        $('#btn-save-schedule').on('click', onNewSchedule);
        $('#btn-new-schedule').on('click', createNewSchedule);

        $('#dropdownMenu-calendars-list').on('click', onChangeNewScheduleCalendar);

        window.addEventListener('resize', resizeThrottled);
    }

    function getDataAction(target) {
        return target.dataset ? target.dataset.action : target.getAttribute('data-action');
    }

    resizeThrottled = tui.util.throttle(function() {
        cal.render();
    }, 50);

    window.cal = cal;

    setDropdownCalendarType();
    setRenderRangeText();
    setSchedules();
    setEventListener();
})(window, tui.Calendar);

// set calendars
(function() {
    var calendarList = document.getElementById('calendarList');
    var html = [];
    CalendarList.forEach(function(calendar) {
        html.push('<div class="lnb-calendars-item"><label>' +
            '<input type="checkbox" class="tui-full-calendar-checkbox-round" value="' + calendar.id + '" checked>' +
            '<span style="border-color: ' + calendar.borderColor + '; background-color: ' + calendar.borderColor + ';"></span>' +
            '<span>' + calendar.name + '</span>' +
            '</label></div>'
        );
    });
    calendarList.innerHTML = html.join('\n');
})();
