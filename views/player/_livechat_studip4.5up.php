<? if (Navigation::hasItem("/community/blubber") && $mode == MODE_DEFAULT && $thread): ?>
    <div class="chatbox-container hide-chat">
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
    </div>
<? endif ?>
