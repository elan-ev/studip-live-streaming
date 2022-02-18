const opOptions = {};

// Defining Player stretching mode.
let mode = {
    responsive: 'responsive', // Default.
    fit: 'fit', // To obtain black bars.
    fill: 'fill' // Crop image.
};
opOptions.mode = mode.responsive;

// Detach menu.
opOptions.detachMenus = false;

// Native capabilities.
opOptions.forceNative = false;

// duration to hide playbtn after video is played. (MS)
opOptions.hidePlayBtnTimer = 350;

// Rewind/Forward time range. (MS).
opOptions.step = 0;

// Initial volume.
opOptions.startVolume = 1;

// Initial volume.
opOptions.startVolume = 1;

// Initial play time of media. (S)
opOptions.startTime = 0;

// Display loader when video is loading.
opOptions.showLoaderOnInit = true;

// Setting default level id, if level config is added.
opOptions.defaultLevel = null;

// Callback for error.
// opOptions.onError = (e) => console.error('Failed to load stream!');

// Player width. (string with unit ("100%", "350px") or number of pixels)
opOptions.width = 0;

// Player height. (string with unit ("100%", "350px") or number of pixels)
opOptions.height = 0;

// Allow multiple player instances to play at the same time.
opOptions.pauseOthers = true;

// The player's controls.
// Available controls are: ['play', 'time', 'volume', 'progress', 'captions', 'settings', 'fullscreen']
let controls = {
    alwaysVisible: false, // Permanently show the controls.
    layers: { // The position of each control to be displayed in.
        left: ['play', 'volume'],
        middle: [],
        right: ['settings', 'fullscreen'],
    }
};
opOptions.controls = controls;

// live stream configs to display in the controls.
let live = {
    showLabel: true, // To show "Live Broadcast" label.
    showProgress: false // To show progress bar in live streamings.
};
opOptions.live = live;

// Progress bar configs.
let progress = {
    duration: 0, // The default duration in seconds to show while loading the media.
    showCurrentTimeOnly: false // show both time and duration (true)/current time (false)
}
opOptions.progress = progress;

// FLV configs if any. (https://github.com/bilibili/flv.js/blob/master/docs/api.md#mediadatasource)
let flv = {

};
opOptions.flv = flv;

// HLS configs if any. (https://github.com/video-dev/hls.js/blob/master/docs/API.md#fine-tuning)
let hls = {
    // startLevel: -1
};
opOptions.hls = hls;

let dash = {
    // Possible values are SW_SECURE_CRYPTO, SW_SECURE_DECODE, HW_SECURE_CRYPTO, HW_SECURE_CRYPTO,
    // HW_SECURE_DECODE, HW_SECURE_ALL
    robustnessLevel: null,
    // object containing property names corresponding to key system name strings (e.g. "org.w3.clearkey") and
    // associated values being instances of ProtectionData
    // (http://vm2.dashif.org/dash.js/docs/jsdocs/MediaPlayer.vo.protection.ProtectionData.html)
    drm: null,
};
opOptions.dash = dash;

export default opOptions;