<? if($show_countdown): ?>
    <section>
        <?= $this->render_partial('player/_countdown') ?>
    </section>
<? endif; ?>

<? if($show_player): ?>
    <? if($mode == LiveStreamLib::MODE_DEFAULT ): ?>
        <?= $this->render_partial('player/_video_player') ?>
    <? elseif($mode == LiveStreamLib::MODE_OPENCAST && !empty($oc_players)): ?>
        <? foreach ($oc_players as $player_index => $player): ?>
            <section class="oc-player-section">
                <h3><?= htmlReady($player['room_name']) ?></h3>
                <?
                    // Dynamically instantiating the player_url and plyer_index variables.
                    $this->player_url = $player['url'];
                    $this->player_index = $player_index;
                ?>
                <?= $this->render_partial('player/_video_player') ?>
            </section>
        <? endforeach; ?>
    <?endif;?>
<? endif; ?>

<? if (StudipVersion::olderThan('5.5') && \Navigation::hasItem("/community/blubber") && $thread && $chat_active && $show_player): ?>
    <section class="blubber-section">
        <div class="blubber-container">
            <?= $this->render_partial("player/_livechat.php") ?>
        </div>
    </section>
<? endif ?>
