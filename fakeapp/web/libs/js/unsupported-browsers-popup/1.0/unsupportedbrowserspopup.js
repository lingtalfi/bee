/**
 * This script has no dependencies to other libraries.
 */
(function () {
    if ('undefined' === typeof window.UnsupportedBrowsersPopup) {

        function isArray(o) {
            return Object.prototype.toString.call(o) === '[object Array]';
        }

        function isFunction(mixed) {
            if (typeof mixed == 'function') {
                return true;
            }
            return false;
        }

        function createElement(parent, type, html, cssClass) {
            var x = document.createElement(type);
            if (cssClass) {
                x.className = cssClass;
            }
            if (html) {
                x.innerHTML = html;
            }
            parent.appendChild(x);
            return x;
        }

        function getListHtml(aList) {
            var s = '';
            for (var i in aList) {
                var info = aList[i];
                s += '<li>' +
                    '<span><a target="_blank" href="' + info.link + '">' + info.text + '</a></span>' +
                    '<div class="browser ' + info.icon + '"></div>' +
                    '</li>';
            }
            return s;
        }


        function setCookie(cname, cvalue, exdays) {
            if (null === exdays) {
                document.cookie = cname + "=" + cvalue;
            }
            else {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + "; " + expires;
            }
        }

        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1);
                if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
            }
            return "";
        }


        window.UnsupportedBrowsersPopup = function ($options) {
            var o = {
                ifTest: null,
                remember: true,
                rememberDuration: null,
                displayOptions: {
                    message: ":strong",
                    overlayBgColor: "#333",
                    browsers: "latest",
                    showFooter: true,
                    width: 400,
                    height: 300,
                    lang: "eng"
                }
            };
            if ('undefined' !== typeof $options) {
                for (var i in $options) {
                    o[i] = $options[i];
                }
            }

            var overlayClassName = 'unsupportedbrowserspopup-overlay';
            var msgContainerClassName = 'unsupportedbrowserspopup-container';


            this.displayPopup = function ($options) {
                var options = {
                    message: ":strong",
                    overlayBgColor: "#333",
                    browsers: "latest",
                    showFooter: true,
                    width: 400,
                    height: 300,
                    lang: "eng"
                };
                if ('undefined' !== typeof $options) {
                    for (var i in $options) {
                        options[i] = $options[i];
                    }
                }


                // create overlay
                var dOverlay = createElement(document.body, 'div', null, overlayClassName);
                dOverlay.style.backgroundColor = options.overlayBgColor;

                // now create container
                var s = '<div id="unsupportedbrowserspopup-message" class="message"></div>' +
                    '<div class="browsers">' +
                    '<ul id="unsupportedbrowserspopup-browserslist"></ul>' +
                    '</div>' +
                    '<div id="unsupportedbrowserspopup-footer" class="footer">' +
                    '<button id="unsupportedbrowserspopup-close"></button>' +
                    '</div>';
                var dContainer = createElement(document.body, 'div', s, msgContainerClassName);
                var dMessage = document.getElementById("unsupportedbrowserspopup-message");
                var dBrowserList = document.getElementById("unsupportedbrowserspopup-browserslist");
                var dFooter = document.getElementById("unsupportedbrowserspopup-footer");
                var dCloseButton = document.getElementById("unsupportedbrowserspopup-close");
                dCloseButton.onclick = function () {
                    dOverlay.parentNode.removeChild(dOverlay);
                    dContainer.parentNode.removeChild(dContainer);
                };

                dContainer.style.width = options.width + 'px';
                dContainer.style.height = options.height + 'px';
                dContainer.style.marginLeft = -1 * parseInt(options.width / 2) + 'px';
                dContainer.style.marginTop = -1 * parseInt(options.height / 2) + 'px';


                // inject message
                var message = '';
                if (':' === options.message.substr(0, 1)) {
                    message = UnsupportedBrowsersPopup.locale[options.lang].messages[options.message.substr(1)];
                }
                else {
                    message = options.message;
                }
                dMessage.innerHTML = message;

                // inject browser list
                var aList = [];
                if (isArray(options.browsers)) {
                    aList = options.browsers;
                }
                else {
                    aList = UnsupportedBrowsersPopup.locale[options.lang].browsers[options.browsers];
                }
                dBrowserList.innerHTML = getListHtml(aList);


                // inject close button text
                dCloseButton.innerHTML = UnsupportedBrowsersPopup.locale[options.lang].ok;


                // do we show the footer?
                if (false === options.showFooter) {
                    dFooter.className = "footer hidden";
                }
                else {
                    dFooter.className = "footer";
                }
            };


            // detect if the popup needs to be triggered
            var trigger = false;
            //
            if (true === o.remember && "1" === getCookie("unsupportedbrowserspopupflag")) {
                // the user already passed the test, and the remember flag is on,
                // so we can simply bypass the test
            }
            else if (true === isFunction(o.ifTest)) {
                trigger = o.ifTest();
            }


            if (true === trigger) {
                this.displayPopup(o.displayOptions);

                // shall we remember the test result?
                if (true === o.remember) {
                    setCookie("unsupportedbrowserspopupflag", "1", o.rememberDuration);
                }
            }

        };


        UnsupportedBrowsersPopup.locale = {};
    }
})();