<section>
    <? foreach ($live_termine as $live_termin): ?>
        <div class="live-livestream">
            <div class="countdown">
                <h4><?= htmlReady($live_termin['room_name']) ?></h4>
                <h4>
                    <p>
                        <?= sprintf($plugin->_('Der aktuelle Live-Stream endet um %s Uhr:'),
                            date("H:i", $live_termin['termin']->end_time)) ?>
                    </p>
                    <span class="live-countdown" data-end="<?= intval($live_termin['termin']->end_time)?>">
                        00:00:00
                    </span>
                </h4>
            </div>
        </div>
    <? endforeach; ?>
</section>