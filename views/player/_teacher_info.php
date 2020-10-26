<form class="default">
    <fieldset>
        <legend><?= htmlReady($plugin->_('LiveStreaming Player'))?></legend>
        <label>
            <span>
                <strong><?= $plugin->_('Stream-URL (Sender)') ?>:</strong>
                <br>
                <?= htmlReady($sender_url) ?></span></label>
        <label>
            <span>
                <strong><?= $plugin->_('Stream-URL (Empfänger)') ?>:</strong>
                <br>
                <?= htmlReady($player_url) ?></span></label>
        <label>
            <span>
                <strong><?= $plugin->_('Benutzername') ?>:</strong>
                <br>
                <?= htmlReady($player_username) ?>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Passwort') ?>:</strong>
                <br>
                <?= htmlReady($player_password) ?>
            </span>
        </label>
    </fieldset>
</form>
