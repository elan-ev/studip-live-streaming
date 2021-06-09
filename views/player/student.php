<? if($show_countdown): ?>
    <section>
        <?= $this->render_partial('player/_countdown') ?>
    </section>
<? endif; ?>

<? if($show_player): ?>
    <section>
        <?= $this->render_partial('player/_video_player') ?>
    </section>
<? endif; ?>
