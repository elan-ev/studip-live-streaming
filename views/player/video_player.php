<section class="livestream-video">
    <video 
        id="stream_video" 
        class="video-js vjs-default-skin vjs-big-play-centered" 
        controls 
        preload="auto" 
        width="640px" 
        height="auto" 
        data-setup='{}'
    >
        <source src="<?= $player_url . '/' . Context::getId() . '/' . $filename ?>" type='application/x-mpegurl' />
        <source src="<?= $player_url . '/' . Context::getId() . '/' . $filename ?>" type='application/dash+xml' />
    </video>
</section>

<script>
var player = videojs('stream_video');
player.play();
</script>
