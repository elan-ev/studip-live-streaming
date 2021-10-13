/**
 * livestream.js - LiveStream javascript file for Stud.IP
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

$(function(){
    if ($('h5.livestream-countdown').is(':visible')) {
        var end_timestamp = parseInt($('h5.livestream-countdown').data('end')) * 1000;
        var livestream_countdown = setInterval(() => {
            $('h5.livestream-countdown').text(getCountdown(end_timestamp));
        }, 1000);
        var now = new Date().getTime();
        var difference = end_timestamp - now;
        setTimeout(() => {
            clearInterval(livestream_countdown);
            $('h5.livestream-countdown').hide();
        }, difference);
    }

    if ($('.upcoming-countdown').is(':visible')) {
        var start_timestamp = parseInt($('.upcoming-countdown').data('start')) * 1000;
        var upcomming_countdown = setInterval(() => {
            $('.upcoming-countdown').text(getCountdown(start_timestamp));
        }, 1000);

        var now = new Date().getTime();
        var difference = start_timestamp - now;
        setTimeout(() => {
            clearInterval(upcomming_countdown);
            $('.upcoming-livestream').hide();
        }, difference);
    }

    if ($('.live-countdown').is(':visible')) {
        var live_countdown = setInterval(function() {
            var end_timestamp = $('.live-countdown').data('end') * 1000;
            $('.live-countdown').text(getCountdown(end_timestamp));
        }, 1000);
    }

    if ($('.livestream-mode-selection .selectable').is(':visible')) {
        $('.livestream-mode-selection .selectable').on('click', function(e) {
            e.preventDefault();
            var mode = $(this).data('mode');
            var input = $('input[type="hidden"][name="livestream-mode"]');
            if (input && mode) {
                input.val(mode);
                input.parent().submit();
            }
        });
    }

    if ($('.clipboard-btn').is(':visible')) {
        $('.clipboard-btn').on('click', function(e) {
            e.preventDefault();
            var target_id = $(this).data('target-id');
            var target_title = $(this).data('target-title') ? $(this).data('target-title') : 'Das Feld'.toLocaleString();
            var copied = copyToClipboard(target_id);
            var message = `${target_title} wurde in die Zwischenablage kopiert.`.toLocaleString();
            var type = 'success';
            if (!copied) {
                message = `${target_title} konnte nicht in die Zwischenablage kopiert werden.`.toLocaleString();
                type = 'error';
            }
            showMessageBox(message, type, target_id);
        });
    }

    $(document).on('change', '#countdown_active', function(event) {
        if ($(event.target).is(':checked')) {
            $('#livestream_next').show(() => {
                toggleDateTimeInputAttributes();
            });
        } else {
            $('#livestream_next').hide(() => {
                toggleDateTimeInputAttributes();
            });
        }
    });

    $(document).on('change', '#livestream_next', function(event) {
        toggleDateTimeInputAttributes();
    });
});

function toggleDateTimeInputAttributes() {
    var countdown = $('#countdown_active').is(':checked');
    var manuell = $('input[name="manuell"]:checked').val();
    if (countdown) {
        if (manuell == 1) {
            $('input[name="next_livestream_date"]').removeAttr('disabled').attr('required', true);
            $('input[name="next_livestream_starttime"]').removeAttr('disabled').attr('required', true);
            $('input[name="next_livestream_endtime"]').removeAttr('disabled').attr('required', true);
        } else {
            $('input[name="next_livestream_date"]').removeAttr('required').attr('disabled', true);
            $('input[name="next_livestream_starttime"]').removeAttr('required').attr('disabled', true);
            $('input[name="next_livestream_endtime"]').removeAttr('required').attr('disabled', true);
        }
    } else {
        $('input[name="next_livestream_date"]').removeAttr('required').attr('disabled', true);
        $('input[name="next_livestream_starttime"]').removeAttr('required').attr('disabled', true);
        $('input[name="next_livestream_endtime"]').removeAttr('required').attr('disabled', true);
    }
}

function getCountdown(end_timestamp) {
    var now = new Date().getTime();
    var distance = end_timestamp - now;
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    days = (days <= 0) ? 0 : days;
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    hours = (hours <= 0) ? 0 : hours;
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    minutes = (minutes <= 0) ? 0 : minutes;
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    seconds = (seconds <= 0) ? 0 : seconds;

    var countdown = [];
    if (days > 0) {
        days = (days < 10) ? '0' + days : days;
        countdown.push(days);
    }
    hours = (hours < 10) ? '0' + hours : hours;
    countdown.push(hours);
    minutes = (minutes < 10) ? '0' + minutes : minutes;
    countdown.push(minutes);
    seconds = (seconds < 10) ? '0' + seconds : seconds;
    countdown.push(seconds);

    return countdown.join(':');
}

function copyToClipboard(target_id) {
    var is_copied = false;
    try {
        let targeted_span_element = document.getElementById(target_id);
        var temp_textarea = document.createElement("textarea");
        temp_textarea.value = targeted_span_element.textContent;
        document.body.appendChild(temp_textarea);
        temp_textarea.select();
        document.execCommand("Copy");
        temp_textarea.remove();
        is_copied = true;
    } catch (error) {
        console.log(error);
    }
    return is_copied;
}

function showMessageBox(message, type, target_id) {
    var messagebox_element = $(`#${target_id}`).closest('form').find('.messagebox');
    if (messagebox_element.length) {
        messagebox_element.removeClass();
        var class_type = `messagebox_${type}`;
        messagebox_element.addClass('messagebox ' + class_type);
        messagebox_element.text(message);
        messagebox_element.show();
    }
}
