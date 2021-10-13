<form class="default livestreaming-player-info">
    <div class="messagebox"></div>
    <fieldset>
        <legend><?= htmlReady($plugin->_('LiveStreaming Player'))?></legend>
        <label>
            <span>
                <strong><?= $plugin->_('Stream-URL (Sender)') ?>:</strong>
                <?= Icon::create(
                    'edit+export',
                    Icon::ROLE_CLICKABLE,
                    ['title' => $plugin->_('In Zwischenablage kopieren')]
                )->asInput([
                    'style' => 'vertical-align: top;',
                    'data-target-id' => 'sender_url',
                    'data-target-title' => $plugin->_('Stream-URL (Sender)'),
                    'class' => 'clipboard-btn'
                ]) ?>
                <br>
                <span id="sender_url"><?= htmlReady($sender_url) ?></span>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Stream-URL (Empfänger)') ?>:</strong>
                <?= Icon::create(
                    'edit+export',
                    Icon::ROLE_CLICKABLE,
                    ['title' => $plugin->_('In Zwischenablage kopieren')]
                )->asInput([
                    'style' => 'vertical-align: top;',
                    'data-target-id' => 'player_url',
                    'data-target-title' => $plugin->_('Stream-URL (Empfänger)'),
                    'class' => 'clipboard-btn'
                ]) ?>
                <br>
                <span id="player_url"><?= htmlReady($player_url) ?></span>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Benutzername') ?>:</strong>
                <?= Icon::create(
                    'edit+export',
                    Icon::ROLE_CLICKABLE,
                    ['title' => $plugin->_('In Zwischenablage kopieren')]
                )->asInput([
                    'style' => 'vertical-align: top;',
                    'data-target-id' => 'player_username',
                    'data-target-title' => $plugin->_('Benutzername'),
                    'class' => 'clipboard-btn'
                ]) ?>
                <br>
                <span id="player_username"><?= htmlReady($player_username) ?></span>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Passwort') ?>:</strong>
                <?= Icon::create(
                    'edit+export',
                    Icon::ROLE_CLICKABLE,
                    ['title' => $plugin->_('In Zwischenablage kopieren')]
                )->asInput([
                    'style' => 'vertical-align: top;',
                    'data-target-id' => 'player_password',
                    'data-target-title' => $plugin->_('Passwort'),
                    'class' => 'clipboard-btn'
                ]) ?>
                <br>
                <span id="player_password"><?= htmlReady($player_password) ?></span>
            </span>
        </label>
    </fieldset>
</form>
