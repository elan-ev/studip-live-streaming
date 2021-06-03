<form class="default">
    <fieldset>
        <legend><?= htmlReady($plugin->_('LiveStreaming Player'))?></legend>
        <label>
            <span>
                <strong><?= $plugin->_('Stream-URL (Sender)') ?>:</strong>
                <br>
                <span><?= htmlReady($sender_url) ?></span>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Stream-URL (Empfänger)') ?>:</strong>
                <br>
                <span><?= htmlReady($player_url) ?></span>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Benutzername') ?>:</strong>
                <br>
                <span><?= htmlReady($player_username) ?></span>
            </span>
        </label>
        <label>
            <span>
                <strong><?= $plugin->_('Passwort') ?>:</strong>
                <br>
                <span><?= htmlReady($player_password) ?></span>
            </span>
        </label>
    </fieldset>
</form>
