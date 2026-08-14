<? if($info_message ?? false): ?>
    <?= $info_message ?>
<? endif; ?>
<? if($show_live_countdown ?? false): ?>
    <?= $this->render_partial('player/_live_countdown') ?>
<? endif; ?>
<? if($show_countdown ?? false): ?>
    <?= $this->render_partial('player/_countdown') ?>
<? endif; ?>
