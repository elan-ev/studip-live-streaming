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
            <source id="video_source_1" src="https://devstreaming-cdn.apple.com/videos/streaming/examples/img_bipbop_adv_example_fmp4/master.m3u8" type='application/x-mpegurl' />
            <source id="video_source_1" src="<?= $player_url ?>" type='application/x-mpegurl' />
            <source id="video_source_2" src="<?= $player_url ?>" type='application/dash+xml' />
        </video>
        <div class="new-player">
            <?= \Icon::create('refresh', 'clickable', ['size' => '20', 'title' => $plugin->_('Player neu laden')])->asInput(['id' => 'player-reload-btn', 'class' => 'reload-player-btn']) ?>
            <p><?= $plugin->_('Falls Sie eine Fehlermeldung erhalten hat das Live-Streaming wahrscheinlich noch nicht begonnen. Der Player wird automatisch alle 30 Sekunden aktualisiert. 
                                Sollte dies nicht der Fall sein können Sie den Player manuell neu laden.') ?></p>
        </div>
        
        <? if (Navigation::hasItem("/community/blubber") && $mode == MODE_DEFAULT && $thread && $chat_active): ?>
            <? if (StudipVersion::olderThan('4.5')): ?>
                <?= $this->render_partial("player/_livechat_studip4.4.php") ?>
            <? else: ?>
                <?= $this->render_partial("player/_livechat_studip4.5up.php") ?>
            <? endif ?>
        <? endif ?>
        
    </div>
    
    <div class="zoom-info"><?= $plugin->_('Um das Video zu vergrößern/verkleinern, 
                                        halten Sie die Shift-Taste gedrückt und 
                                        benutzen Sie das Mausrad, oder nutzen Sie 
                                        die Funktionen in der Kontrollzeile') ?></div>
                                        
    <?= \Icon::create('search+add', 'info_alt', ['title' => _('Vergrößern'), 'class' => 'livestreaming-zoomin'])->asImg(16) ?>
    <?= \Icon::create('search+remove', 'info_alt', ['title' => _('Verkleinern'), 'class' => 'livestreaming-zoomout'])->asImg(16) ?>
    <?= \Icon::create('search', 'info_alt', ['title' => _('Standardgröße wiederherstellen'), 'class' => 'livestreaming-zoomdefault'])->asImg(16) ?>

    <div class="zoom-styles">
        <input type="hidden" id="livestreaming_zoomin_cursor" value="url('<?= \Icon::create('search+add', 'info_alt')->asImagePath() ?>'), auto">
        <input type="hidden" id="livestreaming_zoomout_cursor" value="url('<?= \Icon::create('search+remove', 'info_alt')->asImagePath() ?>'), auto">
        <input type="hidden" id="livestreaming_zoomdefault_cursor" value="url('<?= \Icon::create('search', 'info_alt')->asImagePath() ?>'), auto">
    </div>
    <div class="player-info">
        <input type="hidden" id="player_url" value="<?= $player_url ?>">
        <input type="hidden" id="studip_version" value="<?= StudipVersion::getStudipVersion(true) ?>">
    </div>
</section>
