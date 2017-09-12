// Copyright 2013 Google Inc. All Rights Reserved.
// You may study, modify, and use this example for any purpose.
// Note that this example is provided "as is", WITHOUT WARRANTY
// of any kind either expressed or implied.

/**
 * Shows how to use the IMA SDK to request and display ads.
 */
var Ads = function(application, videoPlayer) {
    this.application_ = application;
    this.videoPlayer_ = videoPlayer;
    this.customClickDiv_ = document.getElementById('controlbar');
    this.contentCompleteCalled_ = false;
    google.ima.settings.setVpaidMode(google.ima.ImaSdkSettings.VpaidMode.ENABLED);
    // Call setLocale() to localize language text and downloaded swfs
    // google.ima.settings.setLocale('fr');
    this.adDisplayContainer_ =
        new google.ima.AdDisplayContainer(
              this.application_.adContainer,
              this.application_.contentPlayer,
              this.customClickDiv_);
    this.adsLoader_ = new google.ima.AdsLoader(this.adDisplayContainer_);
    this.adsManager_ = null;

    this.adsLoader_.addEventListener(
          google.ima.AdsManagerLoadedEvent.Type.ADS_MANAGER_LOADED,
          this.onAdsManagerLoaded_,
          false,
          this);
    this.adsLoader_.addEventListener(
          google.ima.AdErrorEvent.Type.AD_ERROR,
          this.onAdError_,
          false,
          this);
};

// On iOS and Android devices, video playback must begin in a user action.
// AdDisplayContainer provides a initialize() API to be called at appropriate
// time.
// This should be called when the user clicks or taps.
Ads.prototype.initialUserAction = function() {
    this.adDisplayContainer_.initialize();
    //this.application_.contentPlayer.load();
};

Ads.prototype.requestAds = function(adTagUrl) {
    var adsRequest = new google.ima.AdsRequest();
    adsRequest.adTagUrl = adTagUrl;
    adsRequest.linearAdSlotWidth = this.videoPlayer_.getVideoWidth();
    adsRequest.linearAdSlotHeight = this.videoPlayer_.getVideoHeight();
    adsRequest.nonLinearAdSlotWidth = this.videoPlayer_.getVideoWidth();
    adsRequest.nonLinearAdSlotHeight = this.videoPlayer_.getVideoHeight();
    this.adsLoader_.requestAds(adsRequest);
    stime = (new Date()).getTime();
};

Ads.prototype.pause = function() {
    if (this.adsManager_) {
        this.adsManager_.pause();
    }
};

Ads.prototype.resume = function() {
    if (this.adsManager_) {
        this.adsManager_.resume();
    }
};

Ads.prototype.resize = function(width, height) {
    if (this.adsManager_) {
        this.adsManager_.resize(width, height, google.ima.ViewMode.FULLSCREEN);
    }
};

Ads.prototype.contentEnded = function() {
    this.contentCompleteCalled_ = true;
    this.adsLoader_.contentComplete();
};

Ads.prototype.onAdsManagerLoaded_ = function(adsManagerLoadedEvent) {
    this.application_.log('Ads loaded.');
    var adsRenderingSettings = new google.ima.AdsRenderingSettings();
    adsRenderingSettings.restoreCustomPlaybackStateOnAdBreakComplete = true;
    adsRenderingSettings.loadVideoTimeout = 100000;
    this.adsManager_ = adsManagerLoadedEvent.getAdsManager(
      this.application_.contentPlayer, adsRenderingSettings);
    this.processAdsManager_(this.adsManager_);
};

Ads.prototype.processAdsManager_ = function(adsManager) {
    console.log(adsManager);
    if (adsManager.isCustomClickTrackingUsed()) {
    }
    // Attach the pause/resume events.
    adsManager.addEventListener(
        google.ima.AdEvent.Type.CONTENT_PAUSE_REQUESTED,
        this.onContentPauseRequested_,
        false,
        this);
    adsManager.addEventListener(
        google.ima.AdEvent.Type.CONTENT_RESUME_REQUESTED,
        this.onContentResumeRequested_,
        false,
        this);
    // Handle errors.
    adsManager.addEventListener(
        google.ima.AdErrorEvent.Type.AD_ERROR,
        this.onAdError_,
        false,
        this);
    var events = [google.ima.AdEvent.Type.ALL_ADS_COMPLETED,
                google.ima.AdEvent.Type.CLICK,
                google.ima.AdEvent.Type.COMPLETE,
                google.ima.AdEvent.Type.FIRST_QUARTILE,
                google.ima.AdEvent.Type.LOADED,
                google.ima.AdEvent.Type.MIDPOINT,
                google.ima.AdEvent.Type.PAUSED,
                google.ima.AdEvent.Type.STARTED,
                google.ima.AdEvent.Type.THIRD_QUARTILE];
    for (var index in events) {
        adsManager.addEventListener(
            events[index],
            this.onAdEvent_,
            false,
            this);
    }
    var initWidth, initHeight;
    if (this.application_.fullscreen) {
        initWidth = this.application_.fullscreenWidth;
        initHeight = this.application_.fullscreenHeight;
    } else {
        initWidth = this.videoPlayer_.getVideoWidth();
        initHeight = this.videoPlayer_.getVideoHeight();
    }
    adsManager.init(
    initWidth,
    initHeight,
    google.ima.ViewMode.NORMAL);

    adsManager.start();
};

Ads.prototype.onContentPauseRequested_ = function() {
    this.application_.pauseForAd();
    this.application_.setVideoEndedCallbackEnabled(false);
};

Ads.prototype.onContentResumeRequested_ = function() {
    this.application_.setVideoEndedCallbackEnabled(true);
    // Without this check the video starts over from the beginning on a
    // post-roll's CONTENT_RESUME_REQUESTED
    if (!this.contentCompleteCalled_) {
        this.application_.resumeAfterAd();
    }
};

Ads.prototype.onAdEvent_ = function(adEvent) {
    this.application_.log('Ad event: ' + adEvent.type);

    if (adEvent.type == google.ima.AdEvent.Type.CLICK) {
        this.application_.adClicked();
    } else if (adEvent.type == google.ima.AdEvent.Type.LOADED) {
        var ad = adEvent.getAd();
        if (!ad.isLinear())
        {
          this.onContentResumeRequested_();
        }
    }
};

Ads.prototype.onAdError_ = function(adErrorEvent) {
    this.application_.log('Ad error: ' + adErrorEvent.getError().toString());
    if (this.adsManager_) {
        this.adsManager_.destroy();
    }
    this.application_.resumeAfterAd();
};

var stime;
/* coz ads*/
var neoAds = function() {
    this.videoPlayerContainer = document.getElementById('videoContainter');
    this.videoPlayerContainer.innerHTML = this.videoPlayerContainer.innerHTML + '<div id="adsContainter" ></div><style>#videoContainter>*{z-index: 10;}</style>';
    this.contentPlayer = document.getElementById('media-video');
    this.adContainer = document.getElementById('adsContainter');
    
    this.width = 640;
    this.height = 360;

    this.fullscreenWidth = null;
    this.fullscreenHeight = null;

    var fullScreenEvents = [
        'fullscreenchange',
        'mozfullscreenchange',
        'webkitfullscreenchange'];
    for (key in fullScreenEvents) {
        document.addEventListener(
        fullScreenEvents[key],
        this.bind_(this, this.onFullscreenChange_),
        false);
    }

    this.playing_ = false;
    this.adsActive_ = false;
    this.adsDone_ = false;
    this.fullscreen = false;

    this.videoPlayer_ = VideoVNE;
    this.ads_ = new Ads(this, this.videoPlayer_);
    this.adTagUrl_ = '';
    this.videoEndedCallback_ = this.bind_(this, this.onContentEnded_);
    this.setVideoEndedCallbackEnabled(true);
    window.addEventListener('resize', this.bind_(this, this.resize), true);

};
neoAds.prototype.isMobilePlatform = function() {
  return this.contentPlayer.paused &&
      (navigator.userAgent.match(/(iPod|iPhone|iPad)/) ||
       navigator.userAgent.toLowerCase().indexOf('android') > -1);
};
neoAds.prototype.SAMPLE_AD_TAG_ = 'https://pubads.g.doubleclick.net/' +
    'gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&' +
    'ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&' +
    'unviewed_position_start=1&' +
    'cust_params=deployment%3Ddevsite%26sample_ct%3Dlinear&correlator=';

neoAds.prototype.resize = function() {
    try {
        this.width = this.videoPlayer_.getVideoWidth();
        this.height = this.videoPlayer_.getVideoHeight();
        if( typeof this.ads_ != 'undefined'){
            this.ads_.resize(
                this.width,
                this.height);
        }
    } catch (b) {
        console.log(b);
    }
};
neoAds.prototype.adsDone = function() {
    return this.adsDone_;
};
neoAds.prototype.setAdTagUrl = function( tagUrl ) {
    this.adTagUrl_ = tagUrl;
};
neoAds.prototype.setVideoEndedCallbackEnabled = function(enable) {
    if (enable) {
        this.contentPlayer.addEventListener('ended', this.videoEndedCallback_, false);
    } else {
        this.contentPlayer.removeEventListener('ended', this.videoEndedCallback_, false);
    }
};

neoAds.prototype.log = function(message) {
    console.log(message);
    logEl = document.getElementById('log');
    try{
        logEl.innerHTML += '<b>'+message+'</b> at '+((new Date()).getTime()-stime)+'<br />';
    }catch(e){
        logEl.innerHTML += '<b>'+message+'</b><br />';
    }
};

neoAds.prototype.resumeAfterAd = function() {
    this.log('resumeAfterAd');
    this.videoPlayer_.resumVideo();
    this.adsActive_ = false;
    this.updateChrome_();
};

neoAds.prototype.pauseForAd = function() {
    this.log('pauseForAd');
    this.adsActive_ = true;
    this.playing_ = true;
    this.videoPlayer_.pauseVideo();
    this.updateChrome_();
};

neoAds.prototype.adClicked = function() {
    this.updateChrome_();
};

neoAds.prototype.bind_ = function(thisObj, fn) {
    return function() {
        fn.apply(thisObj, arguments);
    };
};

neoAds.prototype.onSampleAdTagClick_ = function() {
    this.adTagBox_.value = this.SAMPLE_AD_TAG_;
};

neoAds.prototype.loadAds = function() {
    if ( !this.adsDone_ ) {
        if ( this.adTagUrl_.length <=0 ) {
            this.log('Error: please fill in an ad tag');
            this.playing_ = true;
            this.adsDone_ = true;
            this.resumeAfterAd();
            this.videoPlayer_.resumVideo();
            this.updateChrome_();
            return;
        }
        
        //this.adTagUrl_ = 'https://googleads.g.doubleclick.net/pagead/ads?client=ca-video-pub-5157263156975427&slotname=3395691785&ad_type=video_text_image&description_url=http%3A%2F%2Fvnexpress.net&videoad_start_delay=0';
        // The user clicked/tapped - inform the ads controller that this code
        // is being run in a user action thread.
        this.ads_.initialUserAction();
        // At the same time, initialize the content player as well.
        // When content is loaded, we'll issue the ad request to prevent it
        // from interfering with the initialization. See
        // https://developers.google.com/interactive-media-ads/docs/sdks/html5/v3/ads#iosvideo
        // for more information.
        //this.videoPlayer_.preloadContent(this.bind_(this, this.loadAds_));
        this.loadAds_();
        this.adsDone_ = true;
        return;
    }

    if (this.adsActive_) {
        if (this.playing_) {
            this.ads_.pause();
        } else {
            this.ads_.resume();
        }
    } else {
        if (this.playing_) {
            this.videoPlayer_.pauseVideo();
        } else {
            this.videoPlayer_.resumVideo();
        }
    }
    this.playing_ = !this.playing_;
    this.updateChrome_();
};

neoAds.prototype.onFullscreenClick_ = function() {
    if (this.fullscreen) {
        // The video is currently in fullscreen mode
        var cancelFullscreen = document.exitFullscreen ||
        document.exitFullScreen ||
        document.webkitCancelFullScreen ||
        document.mozCancelFullScreen;
        if (cancelFullscreen) {
            cancelFullscreen.call(document);
        } else {
            this.onFullscreenChange_();
        }
    } else {
        // Try to enter fullscreen mode in the browser
        var requestFullscreen = document.documentElement.requestFullscreen ||
            document.documentElement.webkitRequestFullscreen ||
            document.documentElement.mozRequestFullscreen ||
            document.documentElement.requestFullScreen ||
            document.documentElement.webkitRequestFullScreen ||
            document.documentElement.mozRequestFullScreen;
        if (requestFullscreen) {
            this.fullscreenWidth = window.screen.width;
            this.fullscreenHeight = window.screen.height;
            requestFullscreen.call(document.documentElement);
        } else {
            this.fullscreenWidth = window.innerWidth;
            this.fullscreenHeight = window.innerHeight;
            this.onFullscreenChange_();
        }
    }
    requestFullscreen.call(document.documentElement);
};

neoAds.prototype.updateChrome_ = function() {
    if( this.adsActive_ ){
        this.adContainer.style.position = 'absolute';
        this.adContainer.style.top = '0px';
        this.adContainer.style.left = '0px';
        this.adContainer.style.width = '100%';
        this.adContainer.style.height = '100%';
        this.adContainer.style.display = 'block';
        this.adContainer.style.zIndex = '9999999999';
        this.contentPlayer.style.position = 'relative';
        this.contentPlayer.style.zIndex = 0;
    }else{
        this.adContainer.style.position = 'absolute';
        this.adContainer.style.top = '0px';
        this.adContainer.style.left = '0px';
        this.adContainer.style.width = '100%';
        this.adContainer.style.height = '100%';
        this.adContainer.style.display = 'block';
        this.adContainer.style.zIndex = '1';
        this.contentPlayer.style.position = 'relative';
        this.contentPlayer.style.zIndex = 0;
    }
};

neoAds.prototype.loadAds_ = function() {
    //this.videoPlayer_.removePreloadListener();
    this.ads_.requestAds(this.adTagUrl_);
};

neoAds.prototype.onFullscreenChange_ = function() {
    if ( this.fullscreen ) {
        // The user just exited fullscreen
        // Resize the ad container
        this.ads_.resize(
            this.videoPlayer_.width,
            this.videoPlayer_.height);
        // Return the video to its original size and position
        this.videoPlayer_.resize(
            'relative',
            '',
            '',
            this.videoPlayer_.width,
            this.videoPlayer_.height);
        this.fullscreen = false;
    } else {
        // The fullscreen button was just clicked
        // Resize the ad container
        var width = this.fullscreenWidth;
        var height = this.fullscreenHeight;
        this.makeAdsFullscreen_();
        // Make the video take up the entire screen
        this.videoPlayer_.resize('absolute', 0, 0, width, height);
        this.fullscreen = true;
    }
};

neoAds.prototype.makeAdsFullscreen_ = function() {
    this.ads_.resize(
        this.fullscreenWidth,
        this.fullscreenHeight);
};

neoAds.prototype.onContentEnded_ = function() {
    this.ads_.contentEnded();
};


