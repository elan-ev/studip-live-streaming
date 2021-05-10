<? if (Navigation::hasItem("/community/blubber") && $mode == MODE_DEFAULT && $thread): ?>
        <div class="chatbox-container hide-chat">
            <input type="hidden" id="base_url" value="plugins.php/blubber/streams/">
			<input type="hidden" id="context_id" value="<?= htmlReady($thread->getId()) ?>">
			<input type="hidden" id="stream" value="thread">
			<input type="hidden" id="user_id" value="<?= htmlReady($GLOBALS['user']->id) ?>">
			<input type="hidden" id="stream_time" value="<?= time() ?>">
			<input type="hidden" id="browser_start_time" value="">
			<input type="hidden" id="orderby" value="mkdate">
			<div id="editing_question" style="display: none;"><?= _("Wollen Sie den Beitrag wirklich bearbeiten?") ?></div>
			
			<ul id="blubber_threads" class="coursestream singlethread" aria-live="polite" aria-relevant="additions">
				<?= $this->render_partial("player/_blubber.php", compact("thread")) ?>
			</ul>
		</div>
<? endif ?>
