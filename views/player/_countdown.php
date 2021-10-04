<?
$today_date = new \DateTime();
$upcoming_termin_date = new \DateTime(date("d.m.Y H:i", $upcoming_termin));

$today_date->setTime( 0, 0, 0 );
$upcoming_termin_date->setTime( 0, 0, 0 );

$diff = $today_date->diff( $upcoming_termin_date );
$diff_days = (integer)$diff->format( "%R%a" );
$diff_days_str = 'heute';
switch( $diff_days ) {
    case 0:
        $diff_days_str = _('heute');
        break;
    case +1:
        $diff_days_str = _('morgen');
        break;
    default:
        $diff_days_str = "am " . date("d.m.Y", $upcoming_termin);
}
?>
<section>
    <div class="upcoming-livestream">
        <div class="countdown">
            <h4>
                <p><?= sprintf($plugin->_('Der nächste Live-Stream beginnt %s um %s Uhr'), $diff_days_str, date("H:i", $upcoming_termin)) ?>
                </p>
                <? if($diff_days == 0): ?>
                    <span class="upcoming-countdown" data-start="<?= $upcoming_termin?>">
                        00:00:00
                    </span>
                <? endif; ?>
            </h4>
        </div>
    </div>
</section>
