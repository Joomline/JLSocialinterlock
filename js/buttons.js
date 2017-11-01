var vkcountsys = 0;
var countvaluefb = 0;
var countvaluetw = 0;
var countvaluegp = 0;
var countvalueod = 0;
var countvaluemm = 0;
var timerId;
var isShareOpen = false;
var txtget = 0;

function countLength( collection ){
    if ( typeof collection.length === "number" ) {
        return collection.length;
    }
    var count = 0;
    for(var prs in collection)
    {
        count++;
    }
    return count;
}


function LockSetMailRuCount(data)
{
    var dataCount = countLength(data);
    if(dataCount == 0) return;

    var goal = jQuery('div.likes-lock a.lc-mllc');
    var linkUrl = goal.attr('data-href');
    var pageUrl = pathbs + window.location.pathname + window.location.search;
    var shareUrl = (linkUrl == '' || linkUrl == '#') ? pageUrl : linkUrl;
    var shares = data[shareUrl].shares;

    if(!isShareOpen)
    {
        if(shares > 0)
        {
            goal.addClass('likelc-not-empty');
            jQuery('.lc-countlc', goal).text(shares);
            countvaluemm = shares;
        }
    }
    else{
        var countvaluemm_ = data;
        if (shares > countvaluemm)
        {
            goal.addClass('likelc-not-empty');
            jQuery('.lc-countlc', goal).text(shares);
            countvaluemm = shares;
            openText(pathbs);
        }
    }
}



function openText(domen){
    var contayners = jQuery(".jlLikeLockAlltxt");
    if (txtindex == 1) {
        contayners.css("display", "block");
    }
    else {

        jQuery.each(contayners, function() {
            var $this = jQuery(this);
            var txthidden = $this.html();
            jQuery.post(domen + '/plugins/system/jllikelock/models/ajax.php', {encoded_text: txthidden, encoded: '1', txtpost: '1'},
                function (data) {
                    if (data) {
                        $this.html(data);
                        $this.css("display", "block");
                    }
                }
            );
        });
    }
    jQuery("#ftxt").remove();
    jQuery('#waitimg').hide();
    jQuery.cookie('jllikelockon', '1', {expires: 70, path: "/"});
    jQuery.cookie('jltindex_' + jltid, jltid, {expires: 70, path: "/"});
    clearInterval(timerId);
}
jQuery.noConflict();
(function ($, w, d, undefined) {



    function getParam(key) {
        if (key) {
            var pairs = top.location.search.replace(/^\?/, '').split('&');

            for (var i in pairs) {
                var current = pairs[i];
                var match = current.match(/([^=]*)=(\w*)/);
                if (match[1] === key) {
                    return decodeURIComponent(match[2]);
                }
            }
        }
        return false;
    }


    var ButtonConfigurationLc = function (params) {
        if (params) {
            return $.extend(true, ButtonConfigurationLc.defaults, params)
        }
        return ButtonConfigurationLc.defaults;
    }

    ButtonConfigurationLc.defaults = {
        selectors: {
            facebookButtonLc: '.lc-fblc',
            twitterButtonLc: '.lc-twlc',
            vkontakteButtonLc: '.lc-vklc',
            gplusButtonLc: '.lc-gplc',
            mailButtonLc: '.lc-mllc',
            odnoklasButtonLc: '.lc-oklc',
            count: '.lc-countlc',
            ico: '.lc-icolc',
            shareTitle: 'h2:eq(0)',
            shareSumary: 'p:eq(0)',
            shareImages: 'img[src]'
        },

        buttonDepth: 3,
        alternativeImage: '',
        alternativeSummary: '',
        alternativeTitle: '',
        forceAlternativeImage: false,
        forceAlternativeSummary: false,
        forceAlternativeTitle: false,

        classes: {
            countVisibleClass: 'likelc-not-empty'
        },

        keys: {
            shareLinkParam: 'data-href'
        },

        popupWindowOptions: [
            'left=0',
            'top=0',
            'width=500',
            'height=250',
            'personalbar=0',
            'toolbar=0',
            'scrollbars=1',
            'resizable=1'
        ]
    };

    var Button = function () {
    };
    Button.lastIndex = 0;

    Button.prototype = {
        /*@methods*/
        init: function ($context, conf, index) {
            this.config = conf;
            this.index = index;
            this.id = $($context).attr('id');
            this.$context = $context;
            this.$count = $(this.config.selectors.count, this.$context);
            this.$ico = $(this.config.selectors.ico, this.$context);

            this.collectShareInfo();
            this.bindEvents();
            this.ajaxRequest = this.countLikes();
        },

        bindEvents: function () {
            this
                .$context
                .bind('click', Button.returnFalse);

            this
                .$ico
                .bind('click', this, this.openShareWindow);

        },

        setCountValue: function (count) {
            this
                .$context
                .addClass(this.config.classes.countVisibleClass);

            this
                .$count
                .text(count);
        },

        getCountLink: function (url) {
            return this.countServiceUrl + encodeURIComponent(url);
        },

        collectShareInfo: function () {
            var
                $parent = this.$context,
                button = this;

            for (var i = 0; i < this.config.buttonDepth; i++) {
                $parent = $parent.parent();
            }

            var
                href = this.$context.attr(this.config.keys.shareLinkParam),
                origin = pathbs;

            this.domenhreflc = w.location.protocol + "//" + w.location.host;

            this.linkhref = pathbs + w.location.pathname + w.location.search;

            this.linkToShare = href;
            if (!href || href == '#') {
                href = w.location.origin + w.location.pathname;
            } else if (href.indexOf('http://') == -1 && href.indexOf('https://') == -1) {
                this.linkToShare
                    = (href[0] == '/' ? origin + href : w.location.origin + w.location.pathname + href);
            }
            if(!this.linkToShare || this.linkToShare == '#'){
                this.linkToShare = this.linkhref;
            }

            var
                $tmpParent,
                $title = $(this.config.selectors.shareTitle, $parent),
                $summary = $(this.config.selectors.shareSumary, $parent),
                $images = $(this.config.selectors.shareImages, $parent).not('#waitimg');

            $tmpParent = $parent;
			if(!$images.length){
				var $i = 0;
                while($images.length == 0 && $i < 20){
					$i++;
                    $tmpParent = $tmpParent.parent();
                    $images = $(this.config.selectors.shareImages, $tmpParent).not('#waitimg');
                }
            }

            if(!$title.length){
                $title = $(this.config.selectors.shareTitle, $tmpParent);
            }

            if(!$summary.length){
                $summary = $(this.config.selectors.shareSumary, $tmpParent);
            }

            this.title = $title.text();
            if (this.config.forceAlternativeTitle) {
                this.title = this.config.alternativeTitle;
            } else if ($title.length == 0 && this.config.alternativeTitle) {
                this.title = this.config.alternativeTitle;
            } else {
                this.title = d.title;
            }

            if ($summary.length > 0 & !this.config.forceAlternativeSummary) {
                this.summary = $summary.text();
            } else {
                this.summary = this.config.alternativeSummary ? this.config.alternativeSummary : '';
            }

            this.summary = (this.summary.length > 200) ? this.summary.substring(0,200) + '...' : this.summary;

            this.images = [];
            if ($images.length > 0 & !this.config.forceAlternativeImage) {
                $images.each(function (index, element) {
                    button.images[index] = element.src;
                });
            } else {
                this.images[0] = this.config.alternativeImage ? this.config.alternativeImage : undefined;
            }
        },

        getPopupOptions: function () {
            return this.config.popupWindowOptions.join(',');
        },

        openShareWindow: function (e) {
            var
                button = e.data,
                shareUri = button.getShareLink(),
                windowOptions = button.getPopupOptions();

            var
                newWindow = w.open(shareUri, '', windowOptions);

            if (w.focus) {
                newWindow.focus();
            }

//---------Действия после клика на иконку провайдера (открытие окна шары, проверка прохождения шары)--------
            function getalltext()
            {
                switch (button.type)
                {
                    case "twitter":
                        clearInterval(timerId);
                        $.ajax({
                            url: button.countServiceUrl+'&method=get_twitter_followers&id=1',
                            dataType: 'json',
                            success: function (data, status, jqXHR)
                            {
                                if (status == 'success' && data > 0)
                                {
                                    openText(button.twhref);
                                }
                                $('#waitimg').hide();
                            }
                        });
                        break;

                    case "facebook":
                        FB.api(
                            "/",
                            {
                                "id": button.lnkShare
                            },
                            function (response) {
                                if (response && !response.error) {
                                    if (response.share.share_count > 0) {
                                        if (response.share.share_count > countvaluefb)
                                        {
                                            button.sysFB.setCountValue(response.share.share_count);
                                            countvaluefb = response.share.share_count;
                                            openText(button.fbhref);
                                        }
                                    }
                                }
                            }
                        );
                        break;

                    case "gplusButtonLc":
                        var serviceURI = button.lnkShare;

						$.ajax({
                    		type: 'POST',
                    		url: 'https://clients6.google.com/rpc',
                    		processData: true,
                    		contentType: 'application/json',
                    		data: JSON.stringify({
                        		'method': 'pos.plusones.get',
                        		'id': serviceURI,
                        		'params': {
                            		'nolog': true,
                            		'id': serviceURI,
                            		'source': 'widget',
                            		'userId': '@viewer',
                            		'groupId': '@self'
                        		},
                        		'jsonrpc': '2.0',
                        		'key': 'p',
                        		'apiVersion': 'v1'
                    		}),
                    		success: function(response) {
								if (response.result.metadata.globalCounts.count > 0)
                            	{
                                	var countvaluegp_ = response.result.metadata.globalCounts.count;
                                	if (countvaluegp_ > countvaluegp)
                                	{
                                    	button.sysGP.setCountValue(response.result.metadata.globalCounts.count);
                                    	openText(button.sysGPdomen);
                                	}
                            	}
                    		}
                		});
                        break;

                    case "mailButtonLc":
                        var serviceURI = button.getCountLink(button.lnkShare);
                        isShareOpen = true;
                        $.ajax({
                            url: serviceURI,
                            dataType: 'jsonp'
                        });
                        break;

                    case "odnoklasButtonLc":
                        var origODCount = w.ODKL.updateCount;
                        w.ODKL.updateCount = function (elementId, count)
                            {
                            if (count > countvalueod)
                                {
                                    openText(button.sysODdomen);
                                origODCount(elementId, count);
                                }
                        };

                        var serviceURI = button.getCountLink(button.lnkShare);
                        $.ajax({
                            url: serviceURI,
                            dataType: 'jsonp'
                        });
                        break;

                    case "vkontakte":
                        var origVkCount = w.VK.Share.count;
                         w.VK = {
                             Share: {
                                 count: function (index, count) {
                                     if (count > vkcountsys)
                                {
                                         button.sysVK.setCountValue(count);
                                    openText(button.vkhref);
                                         w.VK.Share.count = origVkCount;
                                }
                            }
                             }
                         };

                        return $.ajax({
                            url: button.serviceURIVK,
                            dataType: 'jsonp'
                        });
                        break;
                }
                $i++;
                if ($i == 60) {
                    clearInterval(timerId);
                    $('#waitimg').hide();
                }
            }

            $('#waitimg').show();
            var $i = 0;

            setTimeout(function () {
                if (newWindow.closed){
                    timerId = setInterval(getalltext, 2000);
                }
                else{
                    setTimeout(arguments.callee, 1);
                }
            }, 1);

//---------КОНЕЦ Действия после клика на иконку провайдера (открытие окна шары, проверка прохождения шары)--------

        },

        /*@properties*/
        linkToShare: null,
        title: d.title,
        summary: null,
        images: [],

        countServiceUrl: null,
        $context: null,
        $count: null,
        $ico: null
    };

    Button = $.extend(Button, {
        /*@methods*/
        returnFalse: function (e) {
            return false;
        }
        /*@properties*/
    });

    var FacebookButtonLc = function ($context, conf, index) {
        this.init($context, conf, index);
        this.type = 'facebook';
        this.lnkShare = this.linkToShare;
        this.sysFB = this;
        this.fbhref = this.domenhreflc;
    };
    FacebookButtonLc.prototype = new Button;
    FacebookButtonLc.prototype
        = $.extend(FacebookButtonLc.prototype,
        {
            /*@methods*/
            countLikes: function () {
                var
                    linkToShare = this.linkToShare,
                    execContext = this;

                FB.api(
                    "/",
                    {
                        "id": linkToShare
                    },
                    function (response) {
                        if (response && !response.error && response.share) {
                            if (response.share.share_count > 0) {
                                execContext.setCountValue(response.share.share_count);
                                countvaluefb = response.share.share_count;
                            }
                        }
                    }
                );
            },
            getCountLink: function (url) {
                return this.countServiceUrl + encodeURIComponent(url);
            },
            getShareLink: function () {
                var images = '';

                for (var i in this.images) {
                    images += ('&p[images][' + i + ']=' + encodeURIComponent(this.images[i]));
                }
                images = this.images;
                return 'https://www.facebook.com/sharer/sharer.php?app_id=1866833980259274&sdk=joey&u='
                    + '?src=sp&u=' + encodeURIComponent(this.linkToShare)
                    + '&p[title]=' + encodeURIComponent(this.title);
            },
            /*@properties*/
            countServiceUrl: 'https://graph.facebook.com/'
        });

    var TwitterButtonLc = function ($context, conf, index) {
        this.init($context, conf, index);
        this.type = 'twitter';
        this.lnkShare = this.linkToShare;
        this.sysTW = this;
        this.titleTW = this.title;
        this.twhref = this.domenhreflc;
        this.serviceURITW = this.countServiceUrl;
    };
    TwitterButtonLc.prototype = new Button;
    TwitterButtonLc.prototype
        = $.extend(TwitterButtonLc.prototype,
        {
            /*@methods*/
            countLikes: function () {
                var
                    serviceURI = this.countServiceUrl+'&method=get_twitter_followers',
                    execContext = this;
                return $.ajax({
                    url: serviceURI,
                    dataType: 'json',
                    success: function (data, status, jqXHR) {
                        if (status == 'success' & data > 0) {
                            execContext.setCountValue(data);
                            countvaluetw = data.count;
                        }
                    }
                });
            },
            getShareLink: function () {
                return this.countServiceUrl+'&method=get_twirrer_url';
            },
            /*@properties*/

            countServiceUrl: window.location.protocol + '//' + window.location.hostname + '/index.php?plg_system_jllikelock=1'
        });

    var VkontakteButtonLc = function ($context, conf, index) {
        this.init($context, conf, index);
        this.type = 'vkontakte';
        this.serviceURIVK = this.getCountLink(this.linkToShare) + '&index=' + this.index;

        this.indexVK = this.index;
        this.sysVK = this;
        this.vkhref = this.domenhreflc;
    };
    VkontakteButtonLc.prototype = new Button;
    VkontakteButtonLc.prototype
        = $.extend(VkontakteButtonLc.prototype,
        {
            /*@methods*/
            countLikes: function () {
                var serviceURI = this.getCountLink(this.linkToShare) + '&index=' + this.index;
                w.socialButtonCountObjectsLc = {};

                function vkShareLc(index, count) {
                    var buttonlc = w.socialButtonCountObjectsLc[index];
                    if (count > 0) {
                        buttonlc.setCountValue(count);
                        vkcountsys = count;
                    }
                }

                if (!w.VK) {
                    w.VK = {
                        Share: {
                            count: function (index, count) {
                                vkShareLc(index, count);
                            }
                        }
                    }
                } else {
                    if (!w.VK || !w.VK.Share || !w.VK.Share.count){
                        w.VK.Share = {
                            count: function (index, count) {
                                vkShareLc(index, count);
                            }
                        };
                    }
                    var originalVkCountLc = w.VK.Share.count;
                    w.VK.Share.count = function (index, count) {
                        vkShareLc(index, count);
                        originalVkCountLc.call(w.VK.Share, index, count);
                    };
                }
                w.socialButtonCountObjectsLc[this.index] = this;
                return $.ajax({
                    url: serviceURI,
                    dataType: 'jsonp'
                });
            },
            getShareLink: function () {
                return 'https://vk.com/share.php?' + 'url=' + encodeURIComponent(this.linkToShare);
            },
            /*@properties*/
            countServiceUrl: 'https://vk.com/share.php?act=count&url='
        });

    /***GOOGLE ***/////
    var gplusButtonLc = function ($context, conf, index) {
        this.init($context, conf, index);
        this.type = 'gplusButtonLc';
        this.lnkShare = this.linkToShare;
        this.sysGP = this;
        this.sysGPdomen = this.domenhreflc;
    };
    gplusButtonLc.prototype = new Button;
    gplusButtonLc.prototype
        = $.extend(gplusButtonLc.prototype,
        {
            /*@methods*/
            countLikes: function () {
                var execGPLc = this;
				return $.ajax({
                    type: 'POST',
                    url: 'https://clients6.google.com/rpc',
                    processData: true,
                    contentType: 'application/json',
                    data: JSON.stringify({
                        'method': 'pos.plusones.get',
                        'id': this.linkToShare,
                        'params': {
                            'nolog': true,
                            'id': this.linkToShare,
                            'source': 'widget',
                            'userId': '@viewer',
                            'groupId': '@self'
                        },
                        'jsonrpc': '2.0',
                        'key': 'p',
                        'apiVersion': 'v1'
                    }),
                    success: function(response) {
						if (response.result.metadata.globalCounts.count > 0) {
                        	execGPLc.setCountValue(response.result.metadata.globalCounts.count);
                        	countvaluegp = response.result.metadata.globalCounts.count;
                    	} else {
                        	countvaluegp = 0;
                    	}
                    }
                });
            },
            getShareLink: function () {
                return 'https://plus.google.com/share?url=' + encodeURIComponent(this.linkToShare);
            },
            /*@properties*/
            countServiceUrl: 'https://plusone.google.com/_/+1/fastbutton?url='
        });

    var odnoklasButtonLc = function ($context, conf, index) {
        this.init($context, conf, index);
        this.type = 'odnoklasButtonLc';
        this.lnkShare = this.linkToShare;
        this.sysOD = this;
        this.sysODdomen = this.domenhreflc;
    };
    odnoklasButtonLc.prototype = new Button;
    odnoklasButtonLc.prototype
        = $.extend(odnoklasButtonLc.prototype,
        {
            /*@methods*/
            countLikes: function ()
            {
                function odklShare(elementId, count)
                {
                    if (count > 0)
                    {
                        countvalueod = count;
                        var elem = $('#'+elementId);
                        elem.addClass('likelc-not-empty');
                        $('span.lc-countlc', elem).text(count);
                    }
                    }

                if (!w.ODKL)
                {
                    w.ODKL = {
                        updateCount: function(elementId, count){
                            odklShare(elementId, count);
                        }
                    }
                }
                else
                {
                    var originalOdCount = ODKL.updateCount;

                    ODKL.updateCount = function (elementId, count)
                    {
                        odklShare(elementId, count);
                        originalOdCount(elementId, count);
                    };
                }
                var url = this.getCountLink();
                return $.ajax({
                    url: this.getCountLink(),
                    dataType: 'jsonp'
                });
            },
            getCountLink: function (url) {
                return 'https://www.odnoklassniki.ru/dk?st.cmd=extLike&uid=' + this.id + '&ref=' + encodeURIComponent(this.linkToShare);
            },
            getShareLink: function () {
                return 'https://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl='
                    + this.linkToShare
                    +'&st.comments=' + encodeURIComponent(this.summary);
            },
            /*@properties*/
            countServiceUrl: 'https://www.odnoklassniki.ru/dk?st.cmd=extLike&uid=' + this.id + '&ref=' + encodeURIComponent(this.linkToShare)
        });

    var mailButtonLc = function ($context, conf, index) {
        this.init($context, conf, index);
        this.type = 'mailButtonLc';
        this.lnkShare = this.linkToShare;
        this.sysMM = this;
        this.sysMMdomen = this.domenhreflc;
    };
    mailButtonLc.prototype = new Button;
    mailButtonLc.prototype
        = $.extend(mailButtonLc.prototype,
        {
            /*@methods*/
            countLikes: function () {
                var
                    serviceURI = this.getCountLink(this.linkToShare),
                    execContext = this;
                function a(data){
                    execContext.setCountValue(data);
                    countvaluemm = data;
                }
                return $.ajax({
                    url: serviceURI,
                    dataType: 'jsonp'
                });
            },
            getCountLink: function (url) {
                return this.countServiceUrl + encodeURIComponent(url)+ '&func=LockSetMailRuCount';
            },
            getShareLink: function () {
                return 'https://connect.mail.ru/share?url=' + encodeURIComponent(this.linkToShare);
            },
            /*@properties*/
            countServiceUrl: 'https://connect.mail.ru/share_count?callback=1&url_list='
        });

//+++++++++    


    $.fn.socialButtonLock = function (config) {

        this.each(function (index, element) {
            setTimeout(function () {
                var
                    $element = $(element),
                    conf = new ButtonConfigurationLc(config),
                    b = false;

                Button.lastIndex++;

                if ($element.is(conf.selectors.facebookButtonLc)) {
                    b = new FacebookButtonLc($element, conf, Button.lastIndex);
                } else if ($element.is(conf.selectors.twitterButtonLc)) {
                    b = new TwitterButtonLc($element, conf, Button.lastIndex);
                } else if ($element.is(conf.selectors.vkontakteButtonLc)) {
                    b = new VkontakteButtonLc($element, conf, Button.lastIndex);
                } else if ($element.is(conf.selectors.gplusButtonLc)) {
                    b = new gplusButtonLc($element, conf, Button.lastIndex);
                } else if ($element.is(conf.selectors.mailButtonLc)) {
                    b = new mailButtonLc($element, conf, Button.lastIndex);
                } else if ($element.is(conf.selectors.odnoklasButtonLc)) {
                    b = new odnoklasButtonLc($element, conf, Button.lastIndex);
                }

                $
                    .when(b.ajaxRequest)
                    .then(
                    function () {
                        $element.trigger('socialButtonLock.done', [b.type]);
                    }
                    , function () {
                        $element.trigger('socialButtonLock.done', [b.type]);
                    }
                );
            }, 0);
        });

        return this;
    };

    $.scrollToButton = function (hashParam, duration) {
        if (!w.location.hash) {
            if (w.location.search) {
                var currentHash = getParam(hashParam);
                if (currentHash) {
                    var $to = $('#' + currentHash);
                    if ($to.length > 0) {
                        $('html,body')
                            .animate({
                                scrollTop: $to.offset().top,
                                scrollLeft: $to.offset().left
                            }, duration || 1000);
                    }
                }
            }
        }

        return this;
    };

})(jQuery, window, document);

jQuery(document).ready(function ($) {
    $('.likelc').socialButtonLock();
});
