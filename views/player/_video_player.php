<section>
    <div class="video-container">
        <div class="video-countdown-container">
            <? if($termin): ?>
                <div class="livestream-countdown-container">
                    <h5 class="livestream-countdown" data-end="<?= $termin->end_time?>">00:00:00</h5>
                </div>
            <? endif; ?>
            <? if ($livestream_termin): ?>
                <div class="livestream-countdown-container">
                    <h5 class="livestream-countdown" data-end="<?= $livestream_termin ?>">00:00:00</h5>
                </div>
            <? endif; ?>
            <video 
                id="stream_video" 
                class="video-js vjs-default-skin" 
                data-setup='{
                    "fluid": true,
                    "autoplay": true,
                    "preload": true,
                    "controls": true       
                }'
            >
                <source id="video_source_1" src="https://devstreaming-cdn.apple.com/videos/streaming/examples/img_bipbop_adv_example_fmp4/master.m3u8" type='application/x-mpegurl' />
                <source id="video_source_1" src="<?= $player_url ?>" type='application/x-mpegurl' />
                <source id="video_source_2" src="<?= $player_url ?>" type='application/dash+xml' />
            </video>
            <div class="new-player">
                <?= \Icon::create('refresh', 'clickable', ['size' => '20', 
                                    'title' => $plugin->_('Player neu laden')])->asInput([
                                        'id' => 'player-reload-btn', 
                                        'class' => 'reload-player-btn']) ?>
                <p><?= $plugin->_('Falls Sie eine Fehlermeldung erhalten hat das 
                                    Live-Streaming wahrscheinlich noch nicht begonnen. 
                                    Der Player wird automatisch alle 30 Sekunden aktualisiert. 
                                    Sollte dies nicht der Fall sein können Sie den Player manuell neu laden.') ?></p>
            </div>
        </div>
        <? if (Navigation::hasItem("/community/blubber") && $mode == MODE_DEFAULT && $thread): ?>
        <div class="chatbox-container hide-chat">
            <input type="hidden" id="base_url" value="plugins.php/blubber/streams/">
			<input type="hidden" id="context_id" value="<?= htmlReady($thread->getId()) ?>">
			<input type="hidden" id="stream" value="thread">
			<input type="hidden" id="user_id" value="<?= htmlReady($GLOBALS['user']->id) ?>">
			<input type="hidden" id="stream_time" value="<?= time() ?>">
			<input type="hidden" id="browser_start_time" value="">
			<input type="hidden" id="orderby" value="mkdate">
			<div id="editing_question" style="display: none;"><?= _("Wollen Sie den Beitrag wirklich bearbeiten?") ?></div>
			
			<ul id="blubber_threads" class="coursestream singlethread" aria-live="polite" aria-relevant="additions">
				<?= $this->render_partial("player/_blubber.php", compact("thread")) ?>
			</ul>
		</div>
		<? endif ?>
	</div>
			
			<div class="zoom-info"><?= $plugin->_('Um das Video zu vergrößern/verkleinern, 
                                        halten Sie die Shift-Taste gedrückt und 
                                        benutzen Sie das Mausrad, oder nutzen Sie 
                                        die Funktionen in der Kontrollzeile') ?></div>
                                        
    <?= \Icon::create('search+add', 'info_alt', ['title' => _('Vergrößern'), 'class' => 'livestreaming-zoomin'])->asImg(16) ?>
    <?= \Icon::create('search+remove', 'info_alt', ['title' => _('Verkleinern'), 'class' => 'livestreaming-zoomout'])->asImg(16) ?>
    <?= \Icon::create('search', 'info_alt', ['title' => _('Standardgröße wiederherstellen'), 'class' => 'livestreaming-zoomdefault'])->asImg(16) ?>
    
    <script>
        let PLAYER_URL = "<?= $player_url ?>";
        window.onload = function () {

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
            $('#stream_video').prepend($('.zoom-info'));
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
                $('#stream_video').removeClass('livestreaming-zoomout-cursor');
                
                if ($('#stream_video').hasClass('livestreaming-zoomin-cursor')) {
                    $('#stream_video').removeClass('livestreaming-zoomin-cursor');
                } else {
                    $('#stream_video').addClass('livestreaming-zoomin-cursor');
                    $('#stream_video').data('cursor', 1);
                }
            });
            
            $(zoomOutButtonDom).on('click touchstart', function(e) {
                e.stopPropagation();
                $('#stream_video').removeClass('livestreaming-zoomin-cursor');
            
                if ($('#stream_video').hasClass('livestreming-zoomout-cursor')) {
                    $('#stream_video').removeClass('livestreaming-zoomout-cursor');
                } else {
                    $('#stream_video').addClass('livestreaming-zoomout-cursor');
                    $('#stream_video').data('cursor', 2);
                }
            });
            
            $(zoomDefaultButtonDom).on('click touchstart', function(e) {
                e.stopPropagation();
                $('#stream_video').removeClass('livestreaming-zoomin-cursor');
                $('#stream_video').removeClass('livestreaming-zoomout-cursor');

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
            
                $('#stream_video').removeClass('livestreaming-zoomin-cursor');
                $('#stream_video').removeClass('livestreaming-zoomout-cursor');
                
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
                    event.preventDefault();
                    $('#stream_video').removeClass('livestreaming-zoomin-cursor');
                    $('#stream_video').removeClass('livestreaming-zoomout-cursor');
                    $('#stream_video').data('cursor', null);
                }
            });
            
            /******************/
            /* MISC FUNCTIONS */
            /******************/
            
            // show livechat if video works only
            player.on('canplay', function(event) {
                if ($('.chatbox-container').hasClass('hide-chat')) {
                    $('.chatbox-container').removeClass('hide-chat');
                }
                
                // automatically scroll to bottom so new messages are always shown
                // unless user has scrolled up to read older messages
                var container = $('.chatbox-container');
                var scrollToBottomInterval = setScrollInterval(container);
                
                container.on('scroll', function(event) {
                    if (scrollToBottomInterval === 0 &&
                    container.scrollTop() + container[0].clientHeight === container[0].scrollHeight) {
                        scrollToBottomInterval = setScrollInterval(container);
                    }
                    
                    if (scrollToBottomInterval !== 0 && 
                    container.scrollTop() + container[0].clientHeight < container[0].scrollHeight - 40) {
                    
                        clearInterval(scrollToBottomInterval);
                        scrollToBottomInterval = 0;
                    }
                });
                
            });
            
            function setScrollInterval(container) {
                return setInterval(function() {
                    container.scrollTop(container[0].scrollHeight);
                }, 100);
            };
            
            // reload player every 30 seconds
            player.on('error', function(event) {
                if (!$('.chatbox-container').hasClass('hide-chat')) {
                    $('.chatbox-container').addClass('hide-chat');
                }
            
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
        };
        
        /***************************************/
        /* ZOOM IN AND OUT FUNCTIONS FOR VIDEO */
        /***************************************/
        
        // scaling factor for click-zoom
        const SCALINGFACTOR = 20;
        
        // initial scale
        let scale = 1;
        
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
    </script>
</section>

