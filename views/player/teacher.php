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
         <form class="default" action="<?= PluginEngine::getLink('LiveStreaming/player/toggle_countdown') ?>" method="post">
            <?= CSRFProtection::tokenTag() ?>
            <fieldset>
                <section class="col-3">
                    <span class="label-text"><?= _('Soll ein Countdown zum nächsten LiveStream angezeigt werden?') ?></span>
                    <div class="hgroup" id="countdown_active">
                        <label>
                            <input name="countdown_active" type="radio" value="1" <? if ($countdown_activated == 1) echo 'checked'; ?>>
                            <?= _('ja')?>
                        </label>
                        <label>
                            <input name="countdown_active" type="radio" value="0" <? if ($countdown_activated === 0) echo 'checked'; ?>>
                            <?= _('nein')?>
                        </label>
                    </div>
                </section>
                <br>
                <section class="col-2" id="livestream_next" <? if ($countdown_activated != 1) echo 'style="display: none;"' ?>>
                    <span class="label-text"><?= _('Termin des nächsten LiveStreams') ?></span>
                    <div class="hgroup">
                        <label>
                            <input type="radio" name="manuell" value="0" <? if ($countdown_manuell === 0) echo 'checked'; ?>>
                            <?= _('Nächster Termin in der Sitzung:' . $sem_next_session) ?>
                        </label>
                        <label>
                            <input type="radio" name="manuell" value="1" <? if ($countdown_manuell == 1) echo 'checked'; ?>>
                            <?= _('Manuelle Termineingabe') ?>
                        </label>
                    </div>
                </section>
                <div id="livestream_next_date" <? if ($countdown_manuell != 1) echo 'style="display: none;"' ?>>
                    <section class="col-2">
                        <span class="label-text"><?= _('Termin des nächsten LiveStreams') ?></span>
                        <input type="text" name="next_livestream_date" class="has-date-picker size-s" 
                            value="<? if ($next_livestream) echo date('d.m.Y', strtotime($next_livestream)) ?>">
                    </section>
                    <section class="col-2">
                        <span class="label-text"><?= _('Uhrzeit (24 Stunden-Format)') ?></span>
                        <input type="text" name="next_livestream_time" class="has-time-picker size-s" 
                            value="<? if ($next_livestream) echo date('H:i', strtotime($next_livestream)) ?>">
                    </section>
                </div>
                <br><?= Studip\Button::create(_('Speichern'))?>
            </fieldset>
            </form>
        <hr>
        <? if($mode == MODE_DEFAULT): ?>
            <?= $this->render_partial('player/_teacher_info') ?>
        <?endif;?>
        <? if($mode == MODE_OPENCAST): ?>
            <?= $this->render_partial('player/_teacher_oc_info') ?>
        <?endif;?>
    </div>
</section>
