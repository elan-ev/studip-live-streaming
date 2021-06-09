<section>
    <div class="dozent-view">
        <? if($select_mode): ?>
            <p class="mode-selection-question">
                <?= $plugin->_('Welchen Livestream-Modus möchten Sie verwenden?') ?>
            </p>
            <form action="<?= PluginEngine::getLink('LiveStreaming/player/select_mode') ?>" method="post">
                <?= CSRFProtection::tokenTag() ?>    
                <input type="hidden" name="livestream-mode" value="<?=$mode?>">
            </form>
        <?endif;?>
        <div class="livestream-mode-selection">
            <div data-mode="default" class="<?= ($select_mode) ? 'selectable' : '' ?> <?= ( $mode == MODE_DEFAULT ) ? 'active' : '' ?>">
                <h3><?= $plugin->_('Live Streaming von Zuhause') ?></h3>
                <br>
                <p><?= $plugin->_('Sie können mit OBS Studio oder anderer Software selber einen Live-Stream für ihre Studierenden senden.') ?></p>
            </div>
            <? if($select_mode): ?>
                <div data-mode="opencast" class="<?= ($select_mode) ? 'selectable' : '' ?> <?= ( $mode == MODE_OPENCAST ) ? 'active' : '' ?>">
                    <h3><?= $plugin->_('Live Streaming mit Opencast aus dem Hörsaal') ?></h3>
                    <br>
                    <p><?= $plugin->_('Während ihrer Vorlesung wird automatisch der Live-Stream für ihre Studierenden angezeigt.') ?></p>
                </div>
            <?endif;?>
        </div>
        <? if($mode == MODE_DEFAULT ): ?>
            <form class="default" action="<?= PluginEngine::getLink('LiveStreaming/player/toggle_countdown') ?>" method="post">
                <?= CSRFProtection::tokenTag() ?>
                <fieldset>
                    <legend><?= htmlReady(_('Termin und Countdown'))?></legend>
                    <label>
                        <input type="checkbox"
                            name="countdown_active"
                            id="countdown_active"
                            <? if ($countdown_activated == 1) echo 'checked'; ?>>
                            <?= _('Termin hinzufügen und Countdown zum nächsten LiveStream anzeigen');?>
                            <?= tooltipIcon(_('Vereinbaren Sie den nächsten Termin des LiveStreams und der Countdown wird angezeigt.')) ?>
                    </label>
                    <div id="livestream_next" <? if ($countdown_activated != 1) echo 'style="display: none;"' ?>>
                        <label>
                            <input type="radio" name="manuell" value="0" <? if ($countdown_manuell == 0) echo 'checked'; ?>>
                            <?= _('Nächster/Aktueller Termin in der Sitzung') . ($sem_next_session ? ': ' . $sem_next_session : ' (' . _('Es gibt keinen neuen Termin verfügbar') . ')') ?>
                        </label>
                        <label>
                            <input type="radio" name="manuell" value="1" <? if ($countdown_manuell == 1) echo 'checked'; ?>>
                            <?= _('Manuelle Termineingabe') . ':' ?>
                        </label>
                        <label class="col-2">
                            <?= _('Datum') ?>
                            <input class="has-date-picker size-s" type="text" name="next_livestream_date"
                                value="<?= htmlReady(($next_livestream ? date('d.m.Y', $next_livestream) : '')) ?>" <?= ($countdown_manuell == 1 ? 'requiered' : 'disabled' )?>>
                        </label>
                        <label class="col-2">
                            <?= _('Startzeit') ?>
                            <input class="studip-timepicker size-s" type="text" name="next_livestream_starttime" <?= ($countdown_manuell == 1 ? 'requiered' : 'disabled' )?>
                                value="<?= htmlReady(($next_livestream ? date('H:i', $next_livestream) : '')) ?>" placeholder="HH:mm">
                        </label>
                        <label class="col-2">
                            <?= _('Endzeit') ?>
                            <input class="studip-timepicker size-s" type="text" name="next_livestream_endtime" <?= ($countdown_manuell == 1 ? 'requiered' : 'disabled' )?>
                                value="<?= htmlReady(($next_livestream_end ? date('H:i', $next_livestream_end) : '')) ?>" placeholder="HH:mm">
                        </label>
                        <label>
                            <input type="checkbox"
                                name="terminate_session"
                                id="terminate_session"
                                <? if ($terminate_session == 1) echo 'checked'; ?>>
                                <?= _('Livestream nach Ablauf der Zeit beenden');?>
                                <?= tooltipIcon(_('Wenn der Termin beendet ist, wird der LiveStream-Player nicht mehr angezeigt.')) ?>
                        </label>
                    </div>
                </fieldset>
                <footer>
                    <?= Studip\Button::create(_('Speichern'))?>
                </footer>
            </form>
            <hr>
            <form class="default" action="<?= PluginEngine::getLink('LiveStreaming/player/toggle_chat') ?>" method="post">
                <?= CSRFProtection::tokenTag() ?>
                <fieldset>
                    <legend><?= _('Live-Chat') ?></legend>
                    <label>
                        <input type="checkbox" name="chat_active" id="chat_active" value="1" 
                            <? if ($chat_active == 1) echo 'checked'; ?>>
                            <?= _('Live-Chat aktivieren') ?>
                            <?= tooltipIcon(_('Soll ein Live-Chat während des Streams unter dem Video verfügbar sein?')) ?>
                    </label>
                </fieldset>
                <footer>
                    <?= Studip\Button::create(_('Speichern'))?>
                </footer>
            </form>
        <?endif;?>
        <hr>
        <? if($mode == MODE_DEFAULT): ?>
            <?= $this->render_partial('player/_teacher_info') ?>
        <?endif;?>
        <? if($mode == MODE_OPENCAST): ?>
            <?= $this->render_partial('player/_teacher_oc_info') ?>
        <?endif;?>
    </div>
</section>
