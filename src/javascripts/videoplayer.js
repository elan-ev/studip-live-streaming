/**
 * videoplayer.js - videoplayer javascript file for Stud.IP
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

import videojs from 'video.js';
window.videojs = videojs;

// scaling factor for click-zoom
const SCALINGFACTOR = 20;
// initial scale
let scale = 1;

$(function(){
    const PLAYER_URL = $('input[type="hidden"]#player_url').val();
    const STUDIP_VERSION = parseFloat($('input[type="hidden"]#studip_version').val());
    const ZOOM_IN_CURSOR = $('input[type="hidden"]#livestreaming_zoomin_cursor').val();
    const ZOOM_OUT_CURSOR = $('input[type="hidden"]#livestreaming_zoomout_cursor').val();
    const ZOOM_DEFAULT_CURSOR = $('input[type="hidden"]#livestreaming_zoomdefault_cursor').val();
    let player = videojs('stream_video');
    player.play();
    document.getElementById('player-reload-btn').addEventListener("click", function(e) {
        e.preventDefault();
        player.reset();
        player.src([
            {
                src: PLAYER_URL,
                type: 'application/x-mpegurl'
            },
            {
                src: PLAYER_URL,
                type: 'application/dash+xml'
            },
        ]);
    });

    // add overlay info about zoom functions
    $('#stream_video').prepend($('.zoom-info').text($('#zoom_info').val()));
    // remove zoom info after video plays for the first time
    player.one('play', function() {
        $('.zoom-info').remove();
    });

    /************************/
    /*** ADD ZOOM BUTTONS ***/
    /************************/
    
    // zoom in button
    var zoomInButton = player.controlBar.addChild("button", {}, player.controlBar.children().length - 1);
    var zoomInButtonDom = zoomInButton.el();
    $(zoomInButtonDom).append($('.livestreaming-zoomin'));
    
    // zoom out button 
    var zoomOutButton = player.controlBar.addChild("button", {}, player.controlBar.children().length - 1);
    var zoomOutButtonDom = zoomOutButton.el();
    $(zoomOutButtonDom).append($('.livestreaming-zoomout'));
    
    // default zoom button 
    var zoomDefaultButton = player.controlBar.addChild("button", {}, player.controlBar.children().length - 1);
    var zoomDefaultButtonDom = zoomDefaultButton.el();
    $(zoomDefaultButtonDom).append($('.livestreaming-zoomdefault'));

    /*********************************/
    /*** ADD ZOOM BUTTON FUNCTIONS ***/
    /*********************************/
    
    // zoom in function
    $(zoomInButtonDom).on('click touchstart', function(e) {
        e.stopPropagation();
        var cursor = ZOOM_IN_CURSOR ? ZOOM_IN_CURSOR : 'n-resize';
        $('#stream_video').css('cursor', '');
        $('#stream_video').css('cursor', cursor);
        $('#stream_video').data('cursor', 1);
    });
    
    $(zoomOutButtonDom).on('click touchstart', function(e) {
        e.stopPropagation();
        var cursor = ZOOM_OUT_CURSOR ? ZOOM_OUT_CURSOR : 's-resize';
        $('#stream_video').css('cursor', '');
        $('#stream_video').css('cursor', cursor);
        $('#stream_video').data('cursor', 2);
    });
    
    $(zoomDefaultButtonDom).on('click touchstart', function(e) {
        e.stopPropagation();
        $('#stream_video').css('cursor', '');

        default_zoom($(player.children()).first());
    });
    
    $(zoomInButtonDom).add(zoomOutButtonDom).add(zoomDefaultButtonDom).on('mouseenter', function(e) {
        $(this).css('cursor', 'pointer');
    });
    
    $(zoomInButtonDom).add(zoomOutButtonDom).add(zoomDefaultButtonDom).on('mouseleave', function(e) {
        $(this).css('cursor', 'default');
    });

    /***********************/
    /* VIDEO ZOOM HANDLERS */
    /***********************/
    
    player.on('click', function(event) {
        if (Number.isInteger($('#stream_video').data('cursor'))) {
            event.preventDefault();

            // zoom according to which zoom option was picked
            callZoomFunction(event);
        }   
    });
    
    // for mobile 
    player.on('touchstart', function(event) {
        if (Number.isInteger($('#stream_video').data('cursor'))) { 
            
            // call zoom function with mouse pointer coordinates for mobile
            callZoomFunction(event, event.touches[0].pageX, event.touches[0].pageY);
            
            // prevent video pause / resume
            if(player.paused()){
                player.play();
            }
            else{
                player.pause();
            }
        }
    });
    
    // picks zoom factor and calls zoom function
    function callZoomFunction(event, pageX, pageY) {
    
        // $('#stream_video').removeClass('livestreaming-zoomin-cursor');
        // $('#stream_video').removeClass('livestreaming-zoomout-cursor');
        $('#stream_video').css('cursor', '');

        // choose zoom factor
        let zoomFactor = 0;
        if ($('#stream_video').data('cursor') == 1) {
            zoomFactor = -1 * SCALINGFACTOR;
        }
        if ($('#stream_video').data('cursor') == 2) {
            zoomFactor = SCALINGFACTOR;
        }
        // call the zoom function
        livestreaming_zoom(event, $(player.children()).first(), zoomFactor, pageX, pageY);
        
        if(player.paused()){
            player.play();
        }
        else{
            player.pause();
        }
    }
    
    // cancel zoom if right mouse button clicked
    player.on('contextmenu', function(e) {
        if (Number.isInteger($('#stream_video').data('cursor'))) {
            e.preventDefault();
            $('#stream_video').removeClass('livestreaming-zoomin-cursor');
            $('#stream_video').removeClass('livestreaming-zoomout-cursor');
            $('#stream_video').data('cursor', null);
        }
    });
    
    /******************/
    /* MISC FUNCTIONS */
    /******************/
    
    player.on('canplay', function(event) {
        $('.video-container').show();
    });
    
    // reload player every 30 seconds
    player.on('error', function(event) {
        setTimeout(() => { 
            player.reset();
            player.src([
                {
                    src: PLAYER_URL,
                    type: 'application/x-mpegurl'
                },
                {
                    src: PLAYER_URL,
                    type: 'application/dash+xml'
                },
            ]); 
        }, 30000);
    });
    
    // Blubber Modifications
    if (STUDIP_VERSION < 4.5 && STUDIP.Blubber) {
        // Extend the Blubber 
        STUDIP.Blubber.handleScrollForLiveStream = function (new_posts_count) {
            var current_posts_count = $('#current_posts_count').val();
            if (current_posts_count == undefined) {
                current_posts_count = 0;
            }
            current_posts_count = parseInt(current_posts_count);
            if (new_posts_count > current_posts_count) {
                var chat_container = $('.chatbox-container');
                if (chat_container.is(':visible')) {
                    chat_container.scrollTop(chat_container[0].scrollHeight);
                }
            }
            $('#current_posts_count').val(new_posts_count);
        }
    }
});
/***************************************/
/* ZOOM IN AND OUT FUNCTIONS FOR VIDEO */
/***************************************/

// trigger zoom only when shift button is pressend when user is scrolling
$('#stream_video').on('wheel', function(e) {
    if (e.shiftKey) {
        livestreaming_zoom(e, this);
    }
});

// scale the video according to scroll direction and move towards mouse position
function livestreaming_zoom(e, elem, zoomFactor, pageX, pageY) {
    e.preventDefault();
    $(elem).parent().data('cursor', null);

    let oldScale = scale; 
    
    let posX = pageX ? pageX : e.pageX;
    let posY = pageY ? pageY : e.pageY;
    
    let top = posY - $(elem).parent().offset().top;
    let left = posX - $(elem).parent().offset().left;
    let centerY = $(elem).height() / 2;
    let centerX = $(elem).width() / 2;

    scale += zoomFactor ? zoomFactor * -0.01 : event.deltaY * -0.01;
    scale = Math.min(Math.max(.125, scale), 4);
    
    left = parseInt($(elem).css('left')) + (centerX - left) * (scale - oldScale);
    top = parseInt($(elem).css('top')) + (centerY - top) * (scale - oldScale);

    $(elem).css('top', top);
    $(elem).css('left', left);

    $(elem).css('transform', `scale(${scale})`);
};

function default_zoom(elem) {
    $(elem).css('transform', 'scale(1.0)');
    $(elem).css('top', 0);
    $(elem).css('left', 0);
    scale = 1;
}
