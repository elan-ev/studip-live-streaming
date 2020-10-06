<h1><?= $title ?></h1>

<form class="default" action="<?= PluginEngine::getLink('LiveStreaming/player/admin_change_playerdata') ?>" method="post">
<?= CSRFProtection::tokenTag() ?>

<fieldset>
    <legend><?= _('LiveStreaming URL und Zugangsdaten') ?></legend>
    <section class="col-5">
    <div>
        <label class="label_text"><?= _('Stream-URL (Sender)') ?><br>
        <input type="text" name="sender_url" value="<?= $sender_url ? htmlReady($sender_url) : '' ?>" />
        </label>
    </div>
    </section>
    <section class="col-3">
    <div>
        <label class="label_text"><?= _('Stream-URL (Empfänger)') ?><br>
        <input type="text" name="player_url" value="<?= $player_url ? htmlReady($player_url) : '' ?>" />
        </label>
    </div>
    </section>
    
    <section class="col-2">
    <div>
        <label class="label_text"><?= _('Dateiname') ?><br>
        <input type="text" name="filename" value="<?= $filename ? htmlReady($filename) : '' ?>" />
        </label>
    </div>
    </section>
    
    <section class="col-2">
    <div>
        <label class="label_text"><?= _('Benutzername') ?><br>
        <input type="text" name="loginname" value="<?= $loginname ? htmlReady($loginname) : '' ?>" />
        </label>
    </div>
    </section>
    
    <section class="col-2">
    <div>
        <label class="label_text"><?= _('Passwort') ?><br>
        <input type="text" name="player_password" value="<?= $player_password ? htmlReady($player_password) : '' ?>" />
    </div>
    </section>
    
    <p></p>
    <?= Studip\Button::create(_('Daten speichern'), 'livestream_save', 
                ['class' => 'livestream-save-button']) ?>
</fieldset>
</form>

    
