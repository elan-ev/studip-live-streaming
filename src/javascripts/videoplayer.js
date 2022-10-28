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

    var videosections = document.getElementsByClassName('video-section');
    const MULTI_PLAYER = videosections.length;
    if (videosections.length) {
        setTimeout(function() {
            for (let section of videosections) {
                initSection(section);
            }
        }, 300, videosections);
    }

    function initSection(section) {
        var player_index = section.dataset.playe_index;
        if (player_index == undefined) {
            return;
        }
        var player_url_input = document.getElementById(`player_url_${player_index}`);
        if (player_url_input == undefined) {
            return;
        }
        var player_url = player_url_input.value;
        // Zoom icons from DOM.
        var zoom_in_icon = document.getElementById(`livestreaming-zoomin_${player_index}`);
        var zoom_in_overlay_icon = document.getElementById(`livestreaming-zoomin-overlay_${player_index}`);
        var zoom_out_icon = document.getElementById(`livestreaming-zoomout_${player_index}`);
        var zoom_out_overlay_icon = document.getElementById(`livestreaming-zoomout-overlay_${player_index}`);
        var zoom_default_icon = document.getElementById(`livestreaming-zoomdefault_${player_index}`);
        var zoom_reset_icon = document.getElementById(`livestreaming-zoomreset_${player_index}`);

        var zooms = {
            in: zoom_in_icon,
            in_overlay: zoom_in_overlay_icon,
            out: zoom_out_icon,
            out_overlay: zoom_out_overlay_icon,
            default: zoom_default_icon,
            reset: zoom_reset_icon
        };
        
        var player = new OpenPlayerJS(`stream_video_${player_index}`, opOptions);
        opSetupZoom(player, zooms);

        player.init();
        // player.play();

        appendZoomInfo(player.getContainer(), player_index);

        document.getElementById(`player-reload-btn_${player_index}`).addEventListener("click", function(e) {
            e.preventDefault();
            resetPlayer(player_index, player_url);
        }, player_index, player_url);

        player.getElement().addEventListener('playererror', function(e) {
            // We want to reload only when, there is one player!
            if (MULTI_PLAYER == 1) {
                setTimeout(() => {
                    resetPlayer(player_index, player_url);
                }, 30000, player_index, player_url);
            }
        }, player_index, player_url);

        // When the hls is loaded. 
        player.getElement().addEventListener('hlsManifestLoaded', function(e) {
            // Remove the zoom info text.
            $(`#zoom-info-main_${player_index}`).show();
        }, player_index);

        player.getElement().addEventListener('controlshidden', function(e) {
            $(`#zoom-info-main_${player_index}`).hide();
        }, player_index);
    }

    function resetPlayer(player_index, player_url) {
        var player = OpenPlayerJS.instances[`stream_video_${player_index}`];
        player.src = [
            {
                src: player_url,
                type: 'application/x-mpegurl'
            },
            {
                src: player_url,
                type: 'application/dash+xml'
            },
        ];

        player.load();
        player.play();
    }

    function appendZoomInfo(videoWrapper, player_index) {
        var zoomInfo = document.createElement('div');
        zoomInfo.setAttribute('id', `zoom-info-main_${player_index}`);
        zoomInfo.setAttribute('class', 'zoom-info-main');
        zoomInfo.innerText = $(`#zoom-info_${player_index}`).val();
        videoWrapper.insertBefore(zoomInfo, document.getElementById(`stream_video_${player_index}`));
    }

    /*********/
    /* MISC */
    /********/

    // Blubber Modifications
    let STUDIP_VERSION = parseFloat($('input[type="hidden"]#studip_version').val());
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
