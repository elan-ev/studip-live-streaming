<section class="video-section" data-playe_index="<?= $player_index ?>">
    <div class="video-container">
        <video 
            id="stream_video_<?= $player_index ?>"
            class="op-player__media stream-video"
            playsinline
            controls
        >
            <source id="video_source_1_<?= $player_index ?>" src="<?= $player_url ?>" type='application/x-mpegurl' />
            <source id="video_source_2_<?= $player_index ?>" src="<?= $player_url ?>" type='application/dash+xml' />
        </video>
        <div class="new-player">
            <?= \Icon::create('refresh', 'clickable', ['size' => '20', 'title' => $plugin->_('Player neu laden')])->asInput(['id' => 'player-reload-btn_' . $player_index, 'class' => 'reload-player-btn']) ?>
            <p><?= $plugin->_('Falls Sie eine Fehlermeldung erhalten hat das Live-Streaming wahrscheinlich noch nicht begonnen. Der Player wird automatisch alle 30 Sekunden aktualisiert. 
                                Sollte dies nicht der Fall sein können Sie den Player manuell neu laden.') ?></p>
        </div>
        
    </div>

    <div class="zoom-styles">
        <?= \Icon::create('search+add', 'info_alt', ['title' => _('Vergrößern'), 'id' => 'livestreaming-zoomin_' . $player_index])->asImg(16) ?>
        <?= \Icon::create('search+add', 'info', ['title' => _('Vergrößern'), 'id' => 'livestreaming-zoomin-overlay_' . $player_index])->asImg(16) ?>
        <?= \Icon::create('search+remove', 'info_alt', ['title' => _('Verkleinern'), 'id' => 'livestreaming-zoomout_' . $player_index])->asImg(16) ?>
        <?= \Icon::create('search+remove', 'info', ['title' => _('Verkleinern'), 'id' => 'livestreaming-zoomout-overlay_' . $player_index])->asImg(16) ?>
        <?= \Icon::create('search', 'info_alt', ['title' => _('Zoomen'), 'id' => 'livestreaming-zoomdefault_' . $player_index])->asImg(16) ?>
        <?= \Icon::create('checkbox-unchecked', 'info_alt', ['title' => _('Zoom zurücksetzen'), 'id' => 'livestreaming-zoomreset_' . $player_index])->asImg(16) ?>
        <input type="hidden" id="zoom-info_<?= $player_index ?>"
            value="<?= $plugin->_('Um das Video zu vergrößern/verkleinern, nutzen Sie die Funktionen in der Kontrollzeile oder die oberen Buttons.') ?>">
    </div>
    <div class="player-info">
        <input type="hidden" id="player_url_<?= $player_index ?>" value="<?= $player_url ?>">
    </div>
</section>
