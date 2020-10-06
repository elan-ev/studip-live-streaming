<h1><?= $title ?></h1>

<?= $this->render_partial('player/video_player') ?>

<section class="contentbox">
    <header><h1><?= _('LiveStream Daten') ?></h1></header>
    <dl style="margin: 0;">
    <dt><?= _('Stream-URL') ?></dt>
    <dd><?= htmlReady($sender_url . '/' . Context::getId()) ?></dd>
    <dt><?= _('Benutzername') ?></dt>
    <dd><?= $player_username ?></dd>
    <dt><?= _('Passwort') ?></dt>
    <dd><span class="player-password"><?= $player_password ?></span></dd>
</section>
