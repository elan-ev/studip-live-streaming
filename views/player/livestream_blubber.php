<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */
?>
<head>
    <? if ($_SESSION['_language'] !== 'de_DE'): ?>
        <link rel="localization" hreflang="<?= htmlReady(strtr($_SESSION['_language'], '_', '-')) ?>"
              href="<?= URLHelper::getScriptLink('dispatch.php/localizations/' . $_SESSION['_language']) ?>" type="application/vnd.oftn.l10n+json">
    <? endif ?>

    <script>
    window.STUDIP = {
        ABSOLUTE_URI_STUDIP: "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>",
        ASSETS_URL: "<?= $GLOBALS['ASSETS_URL'] ?>",
        CSRF_TOKEN: {
            name: '<?=CSRFProtection::TOKEN?>',
            value: '<? try {echo CSRFProtection::token();} catch (SessionRequiredException $e){}?>'
        },
        STUDIP_SHORT_NAME: "<?= htmlReady(Config::get()->STUDIP_SHORT_NAME) ?>",
        URLHelper: {
            base_url: "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>",
            parameters: <?= json_encode(URLHelper::getLinkParams(), JSON_FORCE_OBJECT) ?>
        },
        jsupdate_enable: <?= json_encode(
                         is_object($GLOBALS['perm']) &&
                         $GLOBALS['perm']->have_perm('autor') &&
                         PersonalNotifications::isActivated()) ?>,
        wysiwyg_enabled: <?= json_encode((bool) Config::get()->WYSIWYG) ?>
    }
    </script>

    <?= PageLayout::getHeadElements() ?>
    
    <style>
        
        #blubber_threads .posting > .writer {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 98%;
            padding: 5px;
        }
        
    </style>
    
    <script>
    window.STUDIP.editor_enabled = <?= json_encode((bool) Studip\Markup::editorEnabled()) ?> && CKEDITOR.env.isCompatible;
    </script>
</head>

<body id="<?= $body_id ?: PageLayout::getBodyElementId() ?>" <? if (SkipLinks::isEnabled()) echo 'class="enable-skiplinks"'; ?>>
   
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
</body>

