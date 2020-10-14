<section>
    <div class="live-livestream">
        <div class="countdown">
            <h4>
                <p><?= sprintf($plugin->_('Der aktuelle Live-Stream endet um %s Uhr:'), date("H:i", $live_termin->end_time)) ?></p>
                <span class="live-countdown" data-end="<?= $live_termin->end_time?>">
                    00:00:00
                </span>
            </h4>
        </div>
    </div>
</section>