<section>
    <div class="upcoming-livestream">
        <div class="countdown">
            <h4>
                <p><?= sprintf($plugin->_('Der nächste Live-Stream beginnt am heute um %s Uhr:'), date("H:i", $upcoming_termin->date)) ?></p>
                <span class="upcoming-countdown" data-start="<?= $upcoming_termin->date?>">
                    00:00:00
                </span>
            </h4>
        </div>
    </div>
</section>
