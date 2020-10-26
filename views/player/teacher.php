<section>
    <div class="dozent-view">
        <? if($select_mode): ?>
            <p class="mode-selection-question">
                <?= $plugin->_('Welchen Livestream-Modus möchten Sie verwenden?') ?>
            </p>
        <?endif;?>
        <form action="<?= PluginEngine::getLink('LiveStreaming/player/select_mode') ?>" method="post">
            <?= CSRFProtection::tokenTag() ?>    
            <input type="hidden" name="livestream-mode" value="<?=$mode?>">
        </form>
        <div class="livestream-mode-selection">
            <div data-mode="default" class="selectable <?= ( $mode == MODE_DEFAULT ) ? 'active' : '' ?>">
                <h3><?= $plugin->_('Live Streaming von Zuhause') ?></h3>
                <br>
                <p><?= $plugin->_('Sie können mit OBS Studio oder anderer Software selber einen Live-Stream für ihre Studierenden senden.') ?></p>
            </div>
            <? if($select_mode): ?>
                <div data-mode="opencast" class="selectable <?= ( $mode == MODE_OPENCAST ) ? 'active' : '' ?>">
                    <h3><?= $plugin->_('Live Streaming mit Opencast aus dem Hörsaal') ?></h3>
                    <br>
                    <p><?= $plugin->_('Während ihrer Vorlesung wird automatisch der Live-Stream für ihre Studierenden angezeigt.') ?></p>
                </div>
            <?endif;?>
        </div>
        <hr>
        <? if($mode == MODE_DEFAULT): ?>
            <?= $this->render_partial('player/_teacher_info') ?>
        <?endif;?>
        <? if($mode == MODE_OPENCAST): ?>
            <?= $this->render_partial('player/_teacher_oc_info') ?>
        <?endif;?>
    </div>
</section>
