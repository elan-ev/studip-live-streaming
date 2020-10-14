<form class="default" action="<?= PluginEngine::getLink('LiveStreaming/admin/admin_change_playerdata') ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= htmlReady($plugin->_('LiveStreaming URL und Zugangsdaten'))?></legend>
        <label>
            <small><?= htmlReady(sprintf($plugin->_('URL Placeholder: %s'), $url_placeholder)) ?></small>
        </label>
        <?if($opencast_installed):?>
            <label>
                <input id="use_opencast" type="checkbox" name="use_opencast" <?= $use_opencast ? 'checked' : '' ?>>
                <?= $plugin->_('Opencast verwenden') ?>
            </label>
            <label style="display: <?= $use_opencast ? 'block' : 'none' ?>">
                <span class=" <?= $use_opencast ? 'required' : '' ?>"><?= htmlReady($plugin->_('Opencast Stream-URL (Empfänger)')) ?></span>
                <input id="oc_player_url" type="text" name="oc_player_url" value="<?= $oc_player_url ? htmlReady($oc_player_url) : '' ?>"  <?= $use_opencast ? 'required' : '' ?>/>
            </label>
        <?endif;?>
        <label>
            <span class="required"><?= htmlReady($plugin->_('Stream-URL (Sender)')) ?></span>
            <input type="text" name="sender_url" value="<?= $sender_url ? htmlReady($sender_url) : '' ?>" autofocus required/>
        </label>
        <label>
            <span class="required"><?= htmlReady($plugin->_('Stream-URL (Empfänger)')) ?></span>
            <input type="text" name="player_url" value="<?= $player_url ? htmlReady($player_url) : '' ?>" required/>
        </label>
        <label class="col-3">
            <span class="required"><?= htmlReady($plugin->_('Benutzername')) ?></span>
            <input type="text" id="loginname" name="loginname" required value="<?= $loginname ? htmlReady($loginname) : '' ?>" required/>
        </label>
        <label class="col-3">
            <span class="required"><?= htmlReady($plugin->_('Passwort')) ?></span>
            <input type="text" name="player_password" value="<?= $player_password ? htmlReady($player_password) : '' ?>" required/>
        </label>
    </fieldset>
    <footer>
        <?= Studip\Button::createAccept(htmlReady($plugin->_('Daten speichern')), 'livestream_save', 
                ['class' => 'livestream-save-button']) ?>
    </footer>
</form>
<script>
    $(document).ready(function(){
        if($('#use_opencast').is(':visible')) {
            displayOpencastField($('#use_opencast').prop('checked'));
            $('#use_opencast').on('change', function(e) {
                e.preventDefault();
                displayOpencastField($(this).prop('checked'));
            })
        }
    });
    function displayOpencastField(state) {
        $('#oc_player_url').prop('required', state);
        $('#oc_player_url').attr('aria-required', state);
        $('#oc_player_url').prev().toggleClass('required', state);
        $('#oc_player_url').parent().css('display', ((state) ? 'block' : 'none'));
    }
</script>

    
