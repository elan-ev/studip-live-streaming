/**
 * videoplayer.js - videoplayer javascript file for Stud.IP
 * @author    Farbod Zamani Boroujeni <zamani@elan-ev.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */
import Hls from 'hls.js';
window.Hls = Hls;
import OpenPlayerJS from 'openplayerjs';
window.OpenPlayerJS = OpenPlayerJS;
import opOptions from './lib/openplayer-options';
import opSetupZoom from './lib/openplayer-zoom';


window.onload = function() {
    const PLAYER_URL = $('input[type="hidden"]#player_url').val();
    const STUDIP_VERSION = parseFloat($('input[type="hidden"]#studip_version').val());
    // Zoom icons from DOM.
    const ZOOM_IN_ICON = document.getElementById('livestreaming-zoomin');
    const ZOOM_IN_OVERLAY_ICON = document.getElementById('livestreaming-zoomin-overlay');
    const ZOOM_OUT_ICON = document.getElementById('livestreaming-zoomout');
    const ZOOM_OUT_OVERLAY_ICON = document.getElementById('livestreaming-zoomout-overlay');
    const ZOOM_DEFAULT_ICON = document.getElementById('livestreaming-zoomdefault');
    const ZOOM_RESET_ICON = document.getElementById('livestreaming-zoomreset');

    let zooms = {
        in: ZOOM_IN_ICON,
        in_overlay: ZOOM_IN_OVERLAY_ICON,
        out: ZOOM_OUT_ICON,
        out_overlay: ZOOM_OUT_OVERLAY_ICON,
        default: ZOOM_DEFAULT_ICON,
        reset: ZOOM_RESET_ICON
    };

    let player = new OpenPlayerJS('stream_video', opOptions);
    opSetupZoom(player, zooms);

    player.init();
    // player.play();

    appendZoomInfo(player.getContainer());
   
    document.getElementById('player-reload-btn').addEventListener("click", function(e) {
        e.preventDefault();
        resetPlayer();
    });

    player.getElement().addEventListener('playererror', function(e) {
        console.log('Fail to load stream!');
        setTimeout(() => { 
            resetPlayer();
        }, 30000);
    });

    // When the hls is loaded. 
    player.getElement().addEventListener('hlsManifestLoaded', function(e) {
        // Remove the zoom info text.
        $('#zoom-info-main').show();
    });

    player.getElement().addEventListener('controlshidden', function(e) {
        $('#zoom-info-main').hide();
    });

    /*************/
    /* FUNCTIONS */
    /*************/

    function resetPlayer() {
        var player = OpenPlayerJS.instances['stream_video'];
        player.src = [
            {
                src: PLAYER_URL,
                type: 'application/x-mpegurl'
            },
            {
                src: PLAYER_URL,
                type: 'application/dash+xml'
            },
        ];

        player.load();
        player.play();
    }

    function appendZoomInfo(videoWrapper) {
        var zoomInfo = document.createElement('div');
        zoomInfo.setAttribute('id', 'zoom-info-main');
        zoomInfo.innerText = $('#zoom-info').val();
        videoWrapper.insertBefore(zoomInfo, document.getElementById('stream_video'));
    }

    /*********/
    /* MISC */
    /********/

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
};
