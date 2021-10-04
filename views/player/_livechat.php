<? if (StudipVersion::olderThan('4.5')): ?>
	<div class="chatbox-container">
		<input type="hidden" id="current_posts_count" value="0">
		<input type="hidden" id="last_check" value="<?= time() ?>">
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
<? else: ?>
	<div id="blubber-index">
		<div class="blubber_panel"
				data-active_thread="<?= htmlReady($thread->getId()) ?>"
				data-thread_data="<?= htmlReady(json_encode($thread->getJSONData() ?: ['thread_posting' => []])) ?>"
				data-threads_more_down=""
				:class="waiting ? 'waiting' : ''">

			<div id="blubber_stream_container">
				<blubber-thread :thread_data="thread_data"></blubber-thread>
			</div>
		</div>
	</div>
<? endif ?>
