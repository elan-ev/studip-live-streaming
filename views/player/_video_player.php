<section>
    <div class="video-container">
        <? if($termin): ?>
            <div class="livestream-countdown-container">
                <h5 class="livestream-countdown" data-end="<?= $termin->end_time?>">00:00:00</h5>
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
            
            <source id="video_source_1" src="<?= $player_url ?>" type='application/x-mpegurl' />
            <source id="video_source_2" src="<?= $player_url ?>" type='application/dash+xml' />
        </video>
        <div class="new-player">
            <?= \Icon::create('refresh', 'clickable', ['size' => '20', 'title' => $plugin->_('Player neu laden')])->asInput(['id' => 'player-reload-btn', 'class' => 'reload-player-btn']) ?>
            <p><?= $plugin->_('Falls Sie eine Fehlermeldung erhalten hat das Live-Streaming wahrscheinlich noch nicht begonnen. Der Live-Stream beginnt dann leider nicht automatisch sondern Sie müssen den Player neu laden.') ?></p>
        </div>
    </div>
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
        }
    </script>
</section>

