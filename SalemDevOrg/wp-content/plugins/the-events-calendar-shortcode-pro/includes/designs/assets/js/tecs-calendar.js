(function($){
    $(document).ready(function(){
        for (var key in tecsEvents) {
            if (tecsEvents.hasOwnProperty(key)) {
                $('#' + key).fullCalendar({
                    editable: false,
                    fixedWeekCount: !tecEventCalendarSettings[key]['hide_extra_days'],
                    showNonCurrentDates: !tecEventCalendarSettings[key]['hide_extra_days'],
                    firstDay: (parseInt(tecEventCalendarSettings[key]['first_day_of_week']) >= 1 && parseInt(tecEventCalendarSettings[key]['first_day_of_week']) <= 7) ? parseInt(tecEventCalendarSettings[key]['first_day_of_week']) : 0,
                    header: {
                        left: 'title',
                        center: '',
                        right: 'today prev,next'
                    },
                    loading: (function (events_key) {
                        return function( isLoading, view ) {
                            if (isLoading) {
                                $('#' + events_key + '-loading').show();
                            } else {
                                $('#' + events_key + '-loading').hide();
                            }
                        }
                    })(key),
                    defaultDate: tecEventCalendarSettings[key]['startdate'],
                    defaultView: $(window).width() < 514 ? 'listMonth' : (tecEventCalendarSettings[key]['defaultview'] ? tecEventCalendarSettings[key]['defaultview'] : 'month'),
                    windowResize: function(view) {
                        if ($(window).width() < 514) {
                            $(this).fullCalendar( 'changeView', 'listMonth' );
                        } else {
                            $(this).fullCalendar( 'changeView', tecEventCalendarSettings[getTECSCalendarId(view)]['defaultview'] ? tecEventCalendarSettings[getTECSCalendarId(view)]['defaultview'] : 'month');
                        }
                    },
                    eventRender: function( event, element, view ) {
                        var title = element.find('.fc-title, .fc-list-item-title');
                        title.html(title.text());
                        if (event.hasOwnProperty("categories")) {
                            element.addClass(event.categories);
                        }
                    },
                    eventMouseover: (function (events_key) {
                        return function(calEvent, jsEvent) {
                            var tooltip = '<div id="tecs-tooltipevent" class="tooltip-' + events_key + '" style="padding:5px;box-shadow:3px 3px 15px #dadada;width:320px;background:#fff;color:#0a0a0a;position:absolute;z-index:10001;"><h4 class="ecs-title entry-title summary">' + calEvent.title + '</h4><div class="ecs-calendar-event-body"><div class="ecs-calendar-duration">' + JSON.parse(calEvent.details).dateDisplay + '</div>' +
                                (tecEventCalendarSettings[events_key]['thumb'] === 'true' && JSON.parse(calEvent.details).imageTooltipSrc ? '<div class="ecs-calendar-thumb"><img style="float:left;max-width:150px;max-height:150px;padding:4px 5px 0 0;" src="' + JSON.parse(calEvent.details).imageTooltipSrc + '"</div>' : '' ) +
                                '<div class="ecs-calendar-excerpt">' + calEvent.excerpt + '</div></div>';
                            $("body").append(tooltip);
                            $(this).mouseover(function(e) {
                                if ($(window).width() >= 514) {
                                    $(this).css('z-index', 10001);
                                    $('#tecs-tooltipevent').fadeIn('500');
                                    $('#tecs-tooltipevent').fadeTo('10', 1.9);
                                    $('#tecs-tooltipevent').css('top', e.pageY + 10);
                                    $('#tecs-tooltipevent').css('left', e.pageX + 20);
                                }
                            }).mousemove(function(e) {
                                if ($(window).width() >= 514) {
                                    $('#tecs-tooltipevent').css('top', e.pageY + 10);
                                    $('#tecs-tooltipevent').css('left', e.pageX + 20);
                                }
                            });
                        }
                    })(key),
                    eventMouseout: function(calEvent, jsEvent) {
                        $(this).css('z-index', 8);
                        $('#tecs-tooltipevent').remove();
                    },
                    events: (function (events_key) {
                            return function(start, end, timezone, callback) {
                                // load from local events without AJAX on first page load
                                if (tecEventCalendarSettings[events_key]['first_load'] === true) {
                                    tecEventCalendarSettings[events_key]['first_load'] = false;
                                    callback(tecsEvents[events_key]);
                                    return;
                                }

                                tecEventCalendarSettings[events_key]['fromdate'] = start.format('YYYY-MM-DD');
                                tecEventCalendarSettings[events_key]['todate'] = end.format('YYYY-MM-DD');

                                $.ajax({
                                    url: tecEventCalendarSettings[events_key]['ajaxurl'],
                                    type: 'POST',
                                    data: tecEventCalendarSettings[events_key],
                                    success: function(data) {
                                        try {
                                            callback($.parseJSON(data));
                                        } catch (e) {
                                        }
                                    }
                                });
                            }
                        }(key))
                });
                if (tecEventCalendarSettings[key].hasOwnProperty('height') && tecEventCalendarSettings[key].height) {
                    $('#' + key).fullCalendar('option', 'height', tecEventCalendarSettings[key].height);
                }
            }
        }
        function getTECSCalendarId(view) {
            return view.el.parent().parent().attr('id');
        }
    });
})(jQuery);