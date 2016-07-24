/**
 * References:
 *
 * - http://unixpapa.com/js/dyna.html
 *
 * What's new:
 * 
 * 2015-01-29: loadDependencies accepts any argument type for dependencies
 * 
 * - 1.0 
 */
if ('undefined' === typeof window.assetLoader) {

    var _assetLoader = {
        head: document.getElementsByTagName('head')[0],
        devError: function (msg) {
            alert("AssetLoader: " + msg);
        },
        arrayUnique: function (array) {
            var ret = [];
            for (var i in array) {
                if (-1 === ret.indexOf(array[i])) {
                    ret.push(array[i]);
                }
            }
            return ret;
        },
        isArray: function (mixed) {
            if (Object.prototype.toString.call(mixed) === '[object Array]') {
                return true;
            }
            return false;
        }
    };

    window.assetLoader = {
        loadDependencies: function (dependencies, callback) {
            if (true === _assetLoader.isArray(dependencies)) {
                var js = [];
                var css = [];
                for (var i in dependencies) {
                    var ext = window.assetLoader.getFileExtension(dependencies[i]);
                    if ('js' === ext) {
                        js.push(dependencies[i]);
                    }
                    else if ('css' === ext) {
                        css.push(dependencies[i]);
                    }
                }

                css = _assetLoader.arrayUnique(css);
                js = _assetLoader.arrayUnique(js);
                for (var i in css) {
                    window.assetLoader.loadCss(css[i]);
                }

                window.assetLoader.loadJsScripts(js, callback);
            }
            else{
                callback();
            }
        },
        loadJsScripts: function (js, callback) {
            if (js.length) {
                var lastJs = js.pop();
                window.assetLoader.loadJs(lastJs, function () {
                    window.assetLoader.loadJsScripts(js, callback);
                });
            }
            else {
                callback();
            }
        },
        loadJs: function (url, callback) {
            if (false === window.assetLoader.isJsLoaded(url)) {
                var script = document.createElement("script");
                script.type = "text/javascript";

                if (script.readyState) {  //IE
                    script.onreadystatechange = function () {
                        if (script.readyState == "loaded" ||
                            script.readyState == "complete") {
                            script.onreadystatechange = null;
                            callback();
                        }
                    };
                } else {  //Others
                    script.onload = function () {
                        callback();
                    };
                }
                script.src = url;
                _assetLoader.head.appendChild(script);
            }
            else {
                callback();
            }
        },
        loadCss: function (url) {
            if (false === window.assetLoader.isCssLoaded(url)) {
                var fileref = document.createElement("link");
                fileref.setAttribute("rel", "stylesheet");
                fileref.setAttribute("type", "text/css");
                fileref.setAttribute("href", url);
                _assetLoader.head.appendChild(fileref);
            }
        },
        isJsLoaded: function (url) {
            var scripts = document.getElementsByTagName('script');
            for (var i = scripts.length; i--;) {
                if (scripts[i].src == url) {
                    return true;
                }
            }
            return false;
        },
        isCssLoaded: function (url) {
            var links = document.getElementsByTagName('link');
            for (var i = links.length; i--;) {
                if (links[i].src == url) {
                    return true;
                }
            }
            return false;
        },
        getFileExtension: function (filename) {
            return filename.split('.').pop().toLowerCase();
        }
    };
}



