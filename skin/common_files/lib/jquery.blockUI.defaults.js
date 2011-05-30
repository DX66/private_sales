// override these in your code to change the default behavior and style 
$.blockUI.defaults = { 
    // message displayed when blocking (use null for no message) 
    message:  '<span class="waiting">' + lbl_blockui_default_message + '</span>', 
 
    // styles for the message when blocking; if you wish to disable 
    // these and use an external stylesheet then do this in your code: 
    // $.blockUI.defaults.css = {}; 
    css: { 
        padding:        0, 
        margin:         0, 
        width:          '200px', 
        top:            '', 
        left:           '',
        textAlign:      'center', 
        color:          '#000', 
        border:         '3px solid #aaa', 
        backgroundColor:'#fff', 
        cursor:         'wait' 
    }, 
 
    // styles for the overlay 
    overlayCSS:  { 
        backgroundColor: '#212121', 
        opacity:         0.3
    }, 
 
    // styles applied when using $.growlUI 
    growlCSS: { 
        width:    '350px', 
        top:      '10px', 
        left:     '0px', 
        right:    '0px', 
        border:   'solid 1px #aaa', 
        padding:  '5px', 
        opacity:   0.9, 
        cursor:    null, 
        color:    '#000', 
        backgroundColor: '#fff',
        '-webkit-border-radius': '3px',
        '-moz-border-radius':    '3px'
    }, 
     
    // IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w 
    // (hat tip to Jorge H. N. de Vasconcelos) 
    iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank', 
 
    // force usage of iframe in non-IE browsers (handy for blocking applets) 
    forceIframe: false, 
 
    // z-index for the blocking overlay 
    baseZ: 1000, 
 
    // set these to true to have the message automatically centered 
    centerX: true, // <-- only effects element blocking (page block controlled via css above) 
    centerY: true, 
 
    // allow body element to be stetched in ie6; this makes blocking look better 
    // on "short" pages.  disable if you wish to prevent changes to the body height 
    allowBodyStretch: true, 
 
    // enable if you want key and mouse events to be disabled for content that is blocked 
    bindEvents: true, 
 
    // be default blockUI will supress tab navigation from leaving blocking content 
    // (if bindEvents is true) 
    constrainTabKey: true, 
 
    // fadeIn time in millis; set to 0 to disable fadeIn on block 
    fadeIn:  0, 
 
    // fadeOut time in millis; set to 0 to disable fadeOut on unblock 
    fadeOut:  0, 
 
    // time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock 
    timeout: 0, 
 
    // disable if you don't want to show the overlay 
    showOverlay: true, 
 
    // if true, focus will be placed in the first available input field when 
    // page blocking 
    focusInput: true, 
 
    // suppresses the use of overlay styles on FF/Linux (due to performance issues with opacity) 
    applyPlatformOpacityRules: false, 
 
    // callback method invoked when unblocking has completed; the callback is 
    // passed the element that has been unblocked (which is the window object for page 
    // blocks) and the options that were passed to the unblock call: 
    //     onUnblock(element, options) 
    onUnblock: null, 
 
    // don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493 
    quirksmodeOffsetHack: 4 
}; 
