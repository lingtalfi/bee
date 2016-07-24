(function () {

    if ('undefined' === typeof window.Bee) {
        var thisFile = '[web]/public/js/bee/lib/0.1/bee-0.1.js';

        /**
         * Bee.
         *
         * Some parts of this object depends on the jquery library.
         *
         *
         * This object does mainly the following things:
         *
         * - provide common javascript methods used by the bee framework.
         * - acts as a registry by which we can register tools
         *
         *
         * The goal being to provide consistency between the different scripts of a bee application,
         * and provide global access to the most useful tools (services).
         *
         *
         * Here is a brief overview of what this object can do:
         *
         * - centralize the notification system
         * - provides a notify() method
         * - provides a handleCAM() method
         * - provides a checkInterface() method
         * - provides an event dispatcher
         *
         *
         *
         *
         * Tools:
         * --------------------
         * Tools are like services in the php world.
         * Tools might register themselves. As a consequence, they might not use arguments in constructor.
         *
         *
         *
         *
         * checkInterface:
         * -------------------
         * This method allows to check for interface methods, in the interface emulation context.
         * @see notifier-interface-1.0.js
         *
         *
         *
         *
         * Registry:
         * ---------------------
         * Here is the list of following values used by the bee framework in the registry:
         * (Note: this section probably fits better elsewhere).
         *
         * - SSCrudOne.updateForm.values: contains the default values of an update form in a crud process
         * - SSCrudOne.formType: indicates the current form type: either insert or update.
         * - SSCrudOne.manager: the SSCrudOne manager instance in use.
         * @see /Bee/ATool/Collection/QuickList/SSCrudOne.php
         *
         * - bVerboseSensibleError: Whether or not to use the verbose sensible notification style.
         * @see Bee\Chemical\Exception\VerboseSensibleException
         *
         * - _beard: a reference to the beard miniTemplate
         * - _microParser: a reference to the arguments micro parser
         *
         */

        Bee = function () {


            var aTestErrors = [];

            var aoTools = {};
            var aoLocalEvents = {};
            var aoValues = {};
            var sCurrentLocalEventName = null;
            var oNotifier = null;
            var $this = this;


            //------------------------------------------------------------------------------/
            // TESTING
            //------------------------------------------------------------------------------/
            this.getTestErrors = function () {
                return aTestErrors;
            };
            this.resetTestErrors = function () {
                aTestErrors = [];
            };
            this.addTestError = function (msg) {
                aTestErrors.push(msg);
            };


            //------------------------------------------------------------------------------/
            // REGISTRY
            //------------------------------------------------------------------------------/
            this.setValue = function (sName, mValue) {
                aoValues[sName] = mValue;
            };

            this.getValue = function (sName, bTrueThrowEx, mDefaultValue) {
                if (Bee.arrayKeyExists(sName, aoValues)) {
                    return aoValues[sName];
                }
                if (false === bTrueThrowEx) {
                    return mDefaultValue;
                }
                fnError("This key was not set in the registry: %s".replace('%s', sName));
            };


            //------------------------------------------------------------------------------/
            // EVENT DISPATCHER
            //------------------------------------------------------------------------------/
            this.addListener = function (sName, fnCallback, bFalseReplace) {
                if (!Bee.arrayKeyExists(sName, aoLocalEvents)) {
                    aoLocalEvents[sName] = [];
                }
                if (true === bFalseReplace) {
                    aoLocalEvents[sName] = [fnCallback];
                }
                else {
                    aoLocalEvents[sName].push(fnCallback);
                }
            };

            this.dispatch = function (sName, mArgs) {
                if (Bee.arrayKeyExists(sName, aoLocalEvents)) {
                    sCurrentLocalEventName = sName;
                    for (var i in aoLocalEvents[sName]) {
                        aoLocalEvents[sName][i](mArgs);
                    }
                    sCurrentLocalEventName = null;
                }
            };

            this.getCurrentLocalEventName = function () {
                return sCurrentLocalEventName;
            };


            //------------------------------------------------------------------------------/
            // TOOLS
            //------------------------------------------------------------------------------/
            this.registerTool = function (sName, oTool, bFalseOverwrite) {
                if (false === Bee.arrayKeyExists(sName, aoTools) || true === bFalseOverwrite) {
                    aoTools[sName] = oTool;

                    // erase notifier in cache
                    if ('notifier' === sName) {
                        oNotifier = null;
                    }
                }
            };
            this.getTool = function (sName, bTrueError, mNullDefault) {
                if (true === Bee.arrayKeyExists(sName, aoTools)) {
                    return aoTools[sName];
                }
                if (false === bTrueError) {
                    if ('undefined' === typeof mNullDefault) {
                        mNullDefault = null;
                    }
                    return mNullDefault;
                }
                fnError("Undefined tool: %s".replace('%s', sName));
            };


            this.notify = function (mMessage, sType, bTrueUseSensibleTags) {
                if (false === bTrueUseSensibleTags) {

                }
                else {
                    mMessage = fnSensibilizeMessage(mMessage);
                }

                if ('s' === sType) {
                    sType = 'success';
                }
                else if ('e' === sType) {
                    sType = 'error';
                }
                else if ('w' === sType) {
                    sType = 'warning';
                }
                else if ('i' === sType) {
                    sType = 'info';
                }

                //
                if ('success' === sType) {
                    fnGetNotifier().displaySuccess(mMessage);
                }
                else if ('warning' === sType) {
                    fnGetNotifier().displayWarning(mMessage);
                }
                else if ('info' === sType) {
                    fnGetNotifier().displayInfo(mMessage);
                }
                else if ('error' === sType) {
                    fnGetNotifier().displayError(mMessage);
                }
                else {
                    fnError("Unknown notification type: %s".replace('%s', sType));
                }
            };

            //------------------------------------------------------------------------------/
            // UTILS
            //------------------------------------------------------------------------------/
            var fnGetNotifier = function () {
                if (null === oNotifier) {
                    oNotifier = $this.getTool('notifier', false);
                    if (!oNotifier) {
                        oNotifier = new Notifier();
                    }
                    else {
                        Bee.checkInterface(oNotifier, ['displayError', 'displaySuccess']);
                    }
                }
                return oNotifier;
            };

            var fnSensibilizeMessage = function (mMsg) {
                if (true === Bee.isArray(mMsg)) {
                    for (var i in mMsg) {
                        mMsg[i] = fnSensibilizeMessage(mMsg[i]);
                    }
                    return mMsg;
                }
                else {
                    mMsg = Bee.toString(mMsg);
                    if (true === Bee.getValue('bVerboseSensibleError', false, false)) {
                        mMsg = mMsg.replace(/\{\{(.*?)\}\}/g, function (match, $1, $2, offset, original) {
                            return $1;
                        });
                    }
                    else {
                        mMsg = mMsg.replace(/\{\{.*?\}\}/g, '');
                    }
                    return Bee.trim(mMsg);
                }
            };
            var fnError = function (msg) {
                Bee.error(msg);
            };
        };
        Bee.FLAG_TESTMODE = false;
        Bee._self = null;
        Bee.inst = function () {
            if (!Bee._self) {
                Bee._self = new Bee();
            }
            return Bee._self;
        };
        //------------------------------------------------------------------------------/
        // PROXIES
        //------------------------------------------------------------------------------/
        // tools
        Bee.registerTool = function (sName, oTool, bFalseOverwrite) {
            Bee.inst().registerTool(sName, oTool, bFalseOverwrite);
        };
        Bee.getTool = function (sName, bTrueError, mNullDefault) {
            return Bee.inst().getTool(sName, bTrueError, mNullDefault);
        };
        // notification
        Bee.notify = function (sMessage, sType, bTrueUseSensibleTags) {
            return Bee.inst().notify(sMessage, sType, bTrueUseSensibleTags);
        };
        // event dispatcher
        Bee.addListener = function (sName, fnCallback, bFalseReplace) {
            return Bee.inst().addListener(sName, fnCallback, bFalseReplace);
        };
        Bee.dispatch = function (sName, mArgs) {
            return Bee.inst().dispatch(sName, mArgs);
        };
        Bee.getCurrentLocalEventName = function () {
            return Bee.inst().getCurrentLocalEventName();
        };
        // registry
        Bee.setValue = function (sName, mValue) {
            return Bee.inst().setValue(sName, mValue);
        };
        Bee.getValue = function (sName, bTrueThrowEx, mDefaultValue) {
            return Bee.inst().getValue(sName, bTrueThrowEx, mDefaultValue);
        };
        // testing
        Bee.getTestErrors = function () {
            return Bee.inst().getTestErrors();
        };
        Bee.resetTestErrors = function () {
            return Bee.inst().resetTestErrors();
        };
        Bee.addTestError = function (msg) {
            return Bee.inst().addTestError(msg);
        };


        //------------------------------------------------------------------------------/
        // STATIC COMMON
        //------------------------------------------------------------------------------/
        Bee.adrScriptPreload = function (response, fnOnCodeExecutedAfter) {
            if (Bee.arrayKeyExists('script', response) &&
                Bee.isArrayOrObject(response.script) &&
                Bee.arrayKeyExists('code', response.script) &&
                Bee.arrayKeyExists('dependencies', response.script)
                ) {
                BeeAdr.injectAssets(response.script.dependencies, response.script.code, false, {
                    onCodeExecutedAfter: fnOnCodeExecutedAfter
                });
            }
        };

        Bee.appendArrayToFormData = function (aoArray, fd, parents) {
            if (!Bee.isDefined(parents)) {
                parents = [];
            }
            for (var i in aoArray) {
                var value = aoArray[i];
                if (Bee.isArrayOrObject(value)) {
                    parents.push(i);
                    Bee.appendArrayToFormData(value, fd, parents);
                    parents.pop();
                }
                else {
                    var zeName = i;
                    if (Bee.isDefined(parents) && parents.length > 0) {
                        zeName = parents[0];
                        var c = 0;
                        for (var j in parents) {
                            if (0 !== c) {
                                zeName += '[' + parents[j] + ']';
                            }
                            c++;
                        }
                        zeName += '[' + i + ']';
                    }
                    fd.append(zeName, value);
                }
            }
        };


        Bee.arrayCreateHierarchy = function (aoBase, aElements, mFinalValue) {
            if (aElements.length > 0) {
                var sKey = aElements.shift();
                if (Bee.arrayKeyExists(sKey, aoBase) && Bee.isArrayOrObject(aoBase[sKey])) {

                }
                else {
                    aoBase[sKey] = {};
                }
                if (aElements.length > 0) {
                    Bee.arrayCreateHierarchy(aoBase[sKey], aElements, mFinalValue);
                }
                else {
                    aoBase[sKey] = mFinalValue;
                }
            }
        };

        Bee.arrayGetFirstInfo = function (aoArray) {
            for (var i in aoArray) {
                return [i, aoArray[i]];
            }
        };

        Bee.arrayGetFirstValue = function (aoArray) {
            for (var i in aoArray) {
                return aoArray[i];
            }
        };

        Bee.arrayInsertAt = function (array, index) {
            var args = Array.prototype.slice.call(arguments, 2);
            var end = array.splice(index);
            for (var i in args) {
                array.push(args[i]);
            }
            for (var i in end) {
                array.push(end[i]);
            }
        };

        Bee.arrayKeyExists = function (key, aoArray, bTrueStrict) {
            for (var i in aoArray) {
                if (false === bTrueStrict) {
                    if (i == key) {
                        return true;
                    }
                }
                else {
                    if (i === key) {
                        return true;
                    }
                }
            }
            return false;
        };


        Bee.arrayKeyExistsOr = function (key, aoArray, mDefault) {
            for (var i in aoArray) {
                if (i === key) {
                    return aoArray[i];
                }
            }
            return mDefault;
        };


        Bee.arrayKeys = function (mInput) {
            var ret = [];
            for (var i in mInput) {
                ret.push(i);
            }
            return ret;
        };

        Bee.arrayMerge = function (aoArray1, aoArray2) {
            var aoRet = {};
            for (var i in aoArray1) {
                aoRet[i] = aoArray1[i];
            }
            for (var i in aoArray2) {
                aoRet[i] = aoArray2[i];
            }
            return aoRet;
        };


        /**
         * <default key>s are all attributes except class and style.
         *
         * If replace mode is on:
         *
         * - <default key>s existing in array 1 will be replaced by those of array 2
         * - style properties of array1 will be replaced by those of array 2
         *
         * If replace mode is off, that's the opposite:
         *
         * - <default key>s existing in array 1 will NOT be replaced by those of array 2
         * - style properties of array1 will NOT be replaced by those of array 2
         *
         * Related to style property, I assume that css declarations are semi-colon (;) separated (might conflict
         *      with some css3 props? or advanced/tricky css),
         *      and that for a given declaration, the key is separated from the declaration value by a colon char (:).
         *
         *      declaration which do not respect this syntax are considered <invalid>.
         *      An <invalid> declaration is ignored.
         *
         */
        Bee.arrayMergeCss = function (aoArray1, aoArray2, bTrueReplaceMode) {
            var aoRet = Bee.copyObject(aoArray1);
            bTrueReplaceMode = (false !== bTrueReplaceMode);
            for (var key in aoArray2) {
                if (Bee.arrayKeyExists(key, aoRet)) {
                    if ('style' === key) {
                        var aoStyles1 = Bee.cssStyleStringToArray(aoRet[key]);
                        var aoStyles2 = Bee.cssStyleStringToArray(aoArray2[key]);
                        var aoStyles = aoStyles1;
                        for (var zkey in aoStyles2) {
                            if (Bee.arrayKeyExists(zkey, aoStyles)) {
                                if (true === bTrueReplaceMode) {
                                    aoStyles[zkey] = aoStyles2[zkey];
                                }
                            }
                            else {
                                aoStyles[zkey] = aoStyles2[zkey];
                            }
                        }
                        var aDeclarations = [];
                        for (var i in aoStyles) {
                            aDeclarations.push(i + ': ' + aoStyles[i]);
                        }
                        aoRet[key] = aDeclarations.join('; ');
                    }
                    else if ('class' === key) {
                        var sClass1 = Bee.trim(aoRet[key]).replace(/\s+/g, ' ');
                        var sClass2 = Bee.trim(aoArray2[key]).replace(/\s+/g, ' ');
                        var parts1 = sClass1.split(' ');
                        var parts2 = sClass2.split(' ');
                        var classes = parts1;
                        for (var i in parts2) {
                            if (!Bee.inArray(parts2[i], classes)) {
                                classes.push(parts2[i]);
                            }
                        }
                        aoRet[key] = classes.join(' ');
                    }
                    else {
                        if (true === bTrueReplaceMode) {
                            aoRet[key] = aoArray2[key];
                        }
                    }
                }
                else {
                    aoRet[key] = aoArray2[key];
                }
            }
            return aoRet;
        };


        Bee.arrayObjectRemoveKeys = function (aoInput, mKey) {
            var aoRet = {};
            if (Bee.isString(mKey)) {
                mKey = [mKey];
            }
            for (var i in aoInput) {
                if (Bee.inArray(i, mKey)) {

                }
                else {
                    aoRet[i] = aoInput[i];
                }
            }
            return aoRet;
        };

        Bee.arrayObjectShift = function (aoArray) {
            for (var i in aoArray) {
                var m = aoArray[i];
                delete aoArray[i];
                return m;
            }
        };

        Bee.arrayRemoveElement = function (el, mArray) {
            for (var i in mArray) {
                if (el === mArray[i]) {
                    if (Bee.isArray(mArray)) {
                        Bee.unsetKey(i, mArray);
                    }
                    else {
                        delete mArray[i];
                    }
                }
            }
        };

        /**
         * aoNewArray's values replace aoBaseArray's values, but no new keys is created.
         * aDeep allows to repeat the process on nested elements.
         * aDeep uses dot syntax with no escape (the dot is always interpreted as a separator)
         */
        Bee.arrayReplace = function (aoBaseArray, aoNewArray, aPreserveArrayKeys, bFalseAllowNewKeys) {
            var $b = [];
            aPreserveArrayKeys = Bee.getOrDefault(aPreserveArrayKeys, []);
            bFalseAllowNewKeys = Bee.getOrDefault(bFalseAllowNewKeys, false);
            for (var i in aPreserveArrayKeys) {
                var value = aPreserveArrayKeys[i];
                Bee.setDotValue(value, [], $b);
            }
            return Bee._doArrayReplaceBase(aoBaseArray, aoNewArray, $b, bFalseAllowNewKeys);
        };

        Bee._doArrayReplaceBase = function ($baseArray, $newArray, $preserveKeys, $allowNewKeys) {
            for (var $k in $newArray) {
                var $v = $newArray[$k];
                if (Bee.arrayKeyExists($k, $baseArray)) {
                    if (Bee.arrayKeyExists($k, $preserveKeys) && Bee.isArrayOrObject($baseArray[$k])) {
                        var $preserveArray = $preserveKeys[$k];
                        if (Bee.isArrayOrObject($v)) {
                            $baseArray[$k] = Bee._doArrayReplaceBase($baseArray[$k], $v, $preserveArray, $allowNewKeys);
                        }
                    } else {
                        $baseArray[$k] = $v;
                    }
                } else if (true === $allowNewKeys) {
                    $baseArray[$k] = $v;
                }
            }
            return $baseArray;
        };


        Bee.arrayReplaceRecursive = function (arr) {
            // +   original by: Brett Zamir (http://brett-zamir.me)
            // *     example 1: array_replace_recursive({'citrus' : ["orange"], 'berries' : ["blackberry", "raspberry"]}, {'citrus' : ['pineapple'], 'berries' : ['blueberry']});
            // *     returns 1: {citrus : ['pineapple'], berries : ['blueberry', 'raspberry']}

            var retObj = {},
                i = 0,
                p = '',
                argl = arguments.length;

            if (argl < 2) {
                throw new Error('There should be at least 2 arguments passed to array_replace_recursive()');
            }

            // Although docs state that the arguments are passed in by reference, it seems they are not altered, but rather the copy that is returned (just guessing), so we make a copy here, instead of acting on arr itself
            for (p in arr) {
                retObj[p] = arr[p];
            }

            for (i = 1; i < argl; i++) {
                for (p in arguments[i]) {
                    if (retObj[p] && typeof retObj[p] === 'object') {
                        retObj[p] = Bee.arrayReplaceRecursive(retObj[p], arguments[i][p]);
                    } else {
                        retObj[p] = arguments[i][p];
                    }
                }
            }
            return retObj;
        };

        Bee.arrayToString = function (aoArray) {
            var s = '';
            var c = 0;
            for (var i in aoArray) {
                if (0 !== c) {
                    s += ', ';
                }
                if (Bee.isArrayOrObject(aoArray[i])) {
                    s += '{';
                    s += Bee.arrayToString(aoArray[i]);
                    s += '}';
                }
                else {
                    s += i + ': ' + aoArray[i];
                }
                c++;
            }
            return s;
        };

        Bee.arrayValues = function (mInput) {
            var ret = [];
            for (var i in mInput) {
                ret.push(mInput[i]);
            }
            return ret;
        };
        Bee.basename = function (path, suffix) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Ash Searle (http://hexmen.com/blog/)
            // +   improved by: Lincoln Ramsay
            // +   improved by: djmix
            // *     example 1: basename('/www/site/home.htm', '.htm');
            // *     returns 1: 'home'
            // *     example 2: basename('ecra.php?p=1');
            // *     returns 2: 'ecra.php?p=1'
            var b = path.replace(/^.*[\/\\]/g, '');

            if (typeof(suffix) == 'string' && b.substr(b.length - suffix.length) == suffix) {
                b = b.substr(0, b.length - suffix.length);
            }

            return b;
        };


        Bee.buildHtmlOptionsByJson = function (aoOptionsOrOptionsGroups) {
            var s = '';
            // options
            if (false === Bee.isArray(Bee.arrayGetFirstValue(aoOptionsOrOptionsGroups))) {
                for (var i in aoOptionsOrOptionsGroups) {
                    s += '<option value="' + i + '">' + aoOptionsOrOptionsGroups[i] + '</option>';
                }
            }
            // optionGroups
            else {
                for (var groupLabel in aoOptionsOrOptionsGroups) {
                    s += '<optgroup label="' + groupLabel + '">' + Bee.eol();
                    for (var i in aoOptionsOrOptionsGroups) {
                        s += '<option value="' + i + '">' + aoOptionsOrOptionsGroups[i] + '</option>' + Bee.eol();
                    }
                    s += '</optgroup>' + Bee.eol();
                }
            }
            return s;
        };

        Bee.checkArrayObject = function (aoArrayObject, aProps, sNullErrMsg) {
            var aMissing = [];
            for (var i in aProps) {
                if (false === Bee.arrayKeyExists(aProps[i], aoArrayObject)) {
                    aMissing.push(aProps[i]);
                }
            }
            if (aMissing.length > 0) {
                if (!Bee.isDefined(sNullErrMsg)) {
                    sNullErrMsg = "The following properties are missing: %s";
                }
                Bee.error(sNullErrMsg.replace("%s", aMissing.join(', ')));
            }
            return true;
        };


        Bee.checkInterface = function (oClass, aMethods, fnOnError) {
            var aMissing = [];
            for (var i in aMethods) {
                if (!oClass[aMethods[i]]) {
                    aMissing.push(aMethods[i]);
                }
            }
            if (aMissing.length > 0) {
                if (false === Bee.isDefined(fnOnError)) {
                    fnOnError = function ($aMissing) {
                        var s = '';
                        s += "The given class must implement the following methods: %s".replace('%s', $aMissing.join(', '));
                        Bee.notify(s, 'error');
                    };
                }
                return fnOnError(aMissing);
            }
            return true;
        };


        Bee.cloneObject = function (oldObject) {
            return jQuery.extend(true, {}, oldObject);
        };

        Bee.clvEncode = function (mValue) {
            if (Bee.isArrayOrObject(mValue)) {
                if (0 === Bee.countArrayObject(mValue)) {
                    return '*emptyArray';
                }
                else {
                    var ret = {};
                    for (i in mValue) {
                        ret[i] = Bee.clvEncode(mValue[i]);
                    }
                    return ret;
                }
            }
            if (true === mValue) {
                return '*true';
            }
            else if (false === mValue) {
                return '*false';
            }
            else if (null === mValue) {
                return '*null';
            }
            else if ('' === mValue) {
                return '*emptyString';
            }
            return mValue;
        };


        Bee.cookie = {
            /*!
             * Adapted from
             * jQuery Cookie Plugin v1.4.0
             * https://github.com/carhartl/jquery-cookie
             *
             * Copyright 2013 Klaus Hartl
             * Released under the MIT license
             */
            _pluses: /\+/g,
            getItem: function (key, type) {
                var result = key ? undefined : {};

                type = Bee.isDefinedOr(type, 'raw');

                // To prevent the for loop in the first place assign an empty array
                // in case there are no cookies at all. Also prevents odd result when
                // calling $.cookie().
                var cookies = document.cookie ? document.cookie.split('; ') : [];

                for (var i = 0, l = cookies.length; i < l; i++) {
                    var parts = cookies[i].split('=');
                    var name = Bee.cookie._decode(parts.shift(), type);
                    var cookie = parts.join('=');

                    if (key && key === name) {
                        result = Bee.cookie._read(cookie, type);
                        break;
                    }

                    // Prevent storing a cookie that we couldn't decode.
                    if (!key && (cookie = Bee.cookie._read(cookie, type)) !== undefined) {
                        result[name] = cookie;
                    }
                }
                return result;
            },
            setItem: function (key, value, options) {
                options = Bee.arrayMerge({
                    expires: 7
                }, options);


                var type = 'raw';
                if (Bee.arrayKeyExists("type", options)) {
                    type = options['type'];
                }

                if (typeof options.expires === 'number') {
                    var days = options.expires;
                    var t = options.expires = new Date();
                    t.setDate(t.getDate() + days);
                }
                return (document.cookie = [
                    Bee.cookie._encode(key, type), '=', Bee.cookie._stringifyCookieValue(value, type),
                    options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    options.path ? '; path=' + options.path : '',
                    options.domain ? '; domain=' + options.domain : '',
                    options.secure ? '; secure' : ''
                ].join(''));
            },
            removeItem: function (key, options) {
                if (Bee.cookie.getItem(key) === undefined) {
                    return false;
                }

                // Must not alter options, thus extending a fresh object...
                Bee.cookie.setItem(key, '', Bee.arrayReplace(options, { expires: -1 }));
                return !Bee.cookie.getItem(key);
            },
            _decode: function (s, type) {
                if ('json' === type) {
                    return s;
                }
                return decodeURIComponent(s);
            },
            _encode: function (s, type) {
                if ('json' === type) {
                    return s;
                }
                return encodeURIComponent(s);
            },
            _parseCookieValue: function (s, type) {
                if (s.indexOf('"') === 0) {
                    // This is a quoted cookie as according to RFC2068, unescape...
                    s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
                }


                if ("json" === type) {
                    try {
                        // If we can't parse the cookie, ignore it, it's unusable.
                        return JSON.parse(s);
                    } catch (e) {
                    }
                }
                else {
                    try {
                        // Replace server-side written pluses with spaces.
                        // If we can't decode the cookie, ignore it, it's unusable.
                        s = decodeURIComponent(s.replace(Bee.cookie._pluses, ' '));
                    } catch (e) {
                        return;
                    }
                }
            },
            _read: function (s, type) {
                var value = s;
                if ('json' === type) {
                    value = Bee.cookie._parseCookieValue(s, type);
                }
                return value;
            },
            _stringifyCookieValue: function (value, type) {
                if ('json' === type) {
                    value = JSON.stringify(value);
                }
                else {
                    value = String(value);
                }
                return Bee.cookie._encode(value, type);
            }
        };

        Bee.copyObject = function (mixed) {

            if (Bee.isArray(mixed)) {
                var out = [];
                var len = mixed.length;
                for (var i = 0; i < len; i++) {
                    out[i] = Bee.copyObject(mixed[i]);
                }
                return out;
            }
            else if (Bee.isArrayObject(mixed)) {
                var out = {};
                for (var i in mixed) {
                    out[i] = Bee.copyObject(mixed[i]);
                }
                return out;
            }
            return mixed;

            /**
             * Some time ago, I used JSON.stringify,
             *
             * until this
             * /plugin/komin-lee/js/editor/ted/1.1/ted.js which needs to copy an array with deleted entries.
             *
             * Be careful
             *
             * Quote from
             * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/stringify
             *
             * "If undefined, a function, or an XML value is encountered during conversion it is either
             * omitted (when it is found in an object) or censored to null (when it is found in an array)."
             *
             * So  that an array from which keys are deleted, and which might appear
             * as undefined in a log would be converted to null, which is absolutely not the same thing.
             *
             * If your intention is to copy an array which key can possibly be deleted, I recommend
             * not using the method below
             *
             */
            return JSON.parse(JSON.stringify(mixed));
        };


        Bee.countArrayObject = function (aoArray) {
            var c = 0;
            for (var i in aoArray) {
                c++;
            }
            return c;
        };

        /**
         * Returns a css length.
         * It uses a special syntax:
         *
         * - simple number are added the px suffix
         * - number beginning with %w are converted to the px equivalent of the window inner width
         * - number beginning with %h are converted to the px equivalent of the window inner height
         *
         *
         * @param mLength
         * @returns {*}
         */
        Bee.cssLength = function (mLength) {
            if (true === Bee.isNumerical(mLength)) {
                return mLength + 'px';
            }
            else if ('%w' === mLength.substr(0, 2)) {
                var percent = mLength.substr(2);
                mLength = Bee.getWindowInnerDimensions().width * percent / 100;
                return Bee.round(mLength, 2) + 'px';
            }
            else if ('%h' === mLength.substr(0, 2)) {
                var percent = mLength.substr(2);
                mLength = Bee.getWindowInnerDimensions().height * percent / 100;
                return Bee.round(mLength, 2) + 'px';
            }
            return mLength;
        };


        Bee.cssStyleStringToArray = function (sStyle) {
            var aoRet = {};
            var sStyle1 = Bee.trim(sStyle);
            if (';' === Bee.substr(sStyle1, -1)) {
                sStyle1 = Bee.substr(sStyle1, 0, -1);
            }
            sStyle1.split(';').map(function (val) {
                var els = val.split(':', 2);
                if (2 === els.length) {
                    var key = Bee.trim(els[0]);
                    var value = Bee.trim(els[1]);
                    aoRet[key] = value;
                }
            });
            return aoRet;
        };


        Bee.debug = function (msg, ciDebug) {
            if (!Bee.isDefined(ciDebug)) {
                ciDebug = 'debug';
            }
            var el = document.getElementById(ciDebug);
            el.innerHTML = msg;
        };

        Bee.eol = function () {
            return "\n";
        };


        Bee.error = function (msg) {
            if (false === Bee.FLAG_TESTMODE) {
                console.trace();
                var msg = thisFile + ": %s".replace('%s', msg);
                var e = new Error(msg);
                alert(thisFile + ":\n\n" + e.message + "\n\n" + e.stack);
            }
            else {

                Bee.addTestError(msg);
            }
        };


        Bee.explode = function (delimiter, string, limit) {

            if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') return null;
            if (delimiter === '' || delimiter === false || delimiter === null) return false;
            if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string === 'object') {
                return { 0: '' };
            }
            if (delimiter === true) delimiter = '1';

            // Here we go...
            delimiter += '';
            string += '';

            var s = string.split(delimiter);


            if (typeof limit === 'undefined') return s;

            // Support for limit
            if (limit === 0) limit = 1;

            // Positive limit
            if (limit > 0) {
                if (limit >= s.length) return s;
                return s.slice(0, limit - 1).concat([ s.slice(limit - 1).join(delimiter) ]);
            }

            // Negative limit
            if (-limit >= s.length) return [];

            s.splice(s.length + limit);
            return s;
        };


        Bee.getArgumentsMicroParser = function () {
            var parser = Bee.getValue('_microParser', false, null);
            if (null === parser) {
                parser = new ArgumentsMicroParser();
                Bee.setValue('_microParser', parser);
            }
            return parser;
        };


        Bee.getBeard = function () {
            var parser = Bee.getValue('_beard', false, null);
            if (null === parser) {
                parser = new Beard();
                Bee.setValue('_beard', parser);
            }
            return parser;
        };

        /**
         * @param {mixed} mTemplate Can be one of the following:
         *
         * - string
         * - jTemplate
         *
         * @see Bee\Bat\TemplateSystem\Beard\Beard
         */
        Bee.getBeardTemplate = function (mTemplate, aoTags) {

            if (Bee.isString(mTemplate)) {
                var sHtml = mTemplate;
            }
            else {
                var sHtml = mTemplate.clone().wrap('<p/>').parent().html();
            }
            sHtml = sHtml.replace(/\{\{&amp;/g, '{{&');
            var oMini = Bee.getBeard();
            sHtml = oMini.renderTemplate(sHtml, aoTags);
            return jQuery(sHtml);
        };

        // http://stackoverflow.com/questions/4998908/convert-data-uri-to-file-then-append-to-formdata
        Bee.getBlobFromDataURI = function (dataURI, aoExtra) {
            'use strict';
            var byteString,
                mimestring;

            if (dataURI.split(',')[0].indexOf('base64') !== -1) {
                byteString = atob(dataURI.split(',')[1]);
            } else {
                byteString = decodeURI(dataURI.split(',')[1]);
            }

            mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0];

            var content = new Array();
            for (var i = 0; i < byteString.length; i++) {
                content[i] = byteString.charCodeAt(i);
            }

            return new Blob([new Uint8Array(content)], {type: mimestring});
        };

        Bee.getBracketSquareValue = function (sPath, aoArray, mDefault) {
            while ('[]' === sPath.substr(-2)) {
                sPath = sPath.substr(0, sPath.length - 2);
            }

            sPath = Bee.strReplace('"', '\\"', sPath);
            sPath = Bee.strReplace(']', '"]', sPath);
            sPath = Bee.strReplace('[', '["', sPath);

            try {
                var a = aoArray;
                var code = 'var e = a.' + sPath;
                eval(code);
                if ('undefined' === typeof e) {
                    return mDefault;
                }
                return e;
            }
            catch (e) {
                return mDefault;
            }
        };


        // deprecated
        Bee.getCherryOverlay = function (dElement, $aoOptions) {
            var aoOptions = Bee.arrayReplace({
                zIndex: 10000,
                overlayWidth: "100%",
                overlayHeight: "100%",
                overlayMarginTop: "0",
                bgColor: 'rgba(0,0,0,0.2)',
                fnPrepareObject: function (dElement) {
                    // must be set to access width and height
                    dElement.style.display = 'block';
                    dElement.style.position = 'fixed';
                    dElement.style.top = '50%';
                    dElement.style.left = '50%';
                    dElement.style.marginTop = -(dElement.offsetHeight / 2) + 'px';
                    dElement.style.marginLeft = -(dElement.offsetWidth / 2) + 'px';

                }
            }, $aoOptions);
            var overlay = document.createElement("div");
            document.body.appendChild(overlay);
            overlay.style.position = 'fixed';
            overlay.style.width = aoOptions.overlayWidth;
            overlay.style.height = aoOptions.overlayHeight;
            overlay.style.marginTop = aoOptions.overlayMarginTop;
            overlay.style.backgroundColor = aoOptions.bgColor;
            overlay.style.zIndex = aoOptions.zIndex;


            var div = document.createElement("div");
            overlay.appendChild(div);
            var oldDisplay = dElement.style.display;
            dElement.style.display = "none";
            overlay.appendChild(dElement);
            overlay.replaceChild(dElement, div);
            aoOptions.fnPrepareObject(dElement);
            return overlay;
        };


        Bee.getData = function (jElement, sKey, mNullDefault) {
            var sId = Bee.uniqueId(jElement);
            var aoData = Bee.getValue("_data", false, {});
            if (Bee.arrayKeyExists(sId, aoData)) {
                if (Bee.arrayKeyExists(sKey, aoData[sId])) {
                    return aoData[sId][sKey];
                }
            }
            if (!Bee.isDefined(mNullDefault)) {
                mNullDefault = null;
            }
            return mNullDefault;
        };


        /**
         * sValuePath: uses dot notation, a regular dot should be escaped with double backslashes.
         */
        Bee.getDotValue = function (sValuePath, aoArray, mNullDefault) {
            var sUnguessable = '-_|beedot|_-';
            sValuePath = Bee.strReplace('\\.', sUnguessable, sValuePath);
            var aParts = sValuePath.split(".");
            var sFirst = aParts.shift();
            sFirst = Bee.strReplace(sUnguessable, '.', sFirst);
            if (Bee.arrayKeyExists(sFirst, aoArray)) {
                if (aParts.length > 0) {
                    var sNewPath = aParts.join(".");
                    return Bee.getDotValue(sNewPath, aoArray[sFirst], mNullDefault);
                }
                else {
                    return aoArray[sFirst];
                }
            }
            if (!Bee.isDefined(mNullDefault)) {
                mNullDefault = null;
            }
            return mNullDefault;
        };


        /**
         * signature: mValue[, aoContext]*
         *
         * mValue is a map.
         *
         * @see [bee:dsc]
         */
        Bee.getDscValue = function (mValue) {
            if (null !== mValue && Bee.isDefined(mValue)) {


                var aContexts = Array.prototype.slice.apply(arguments);
                aContexts.shift();
                var mode = 'normal';

                if (Bee.isArrayOrObject(mValue)) {
                    mValue = Bee.copyObject(mValue);
                    for (var i in mValue) {
                        mValue[i] = Bee.getDscValue.apply(null, [mValue[i]].concat(aContexts));
                    }
                }
                else {


                    for (var i in aContexts) {
                        if (Bee.isString(aContexts[i])) {
                            mode = aContexts[i];
                            delete aContexts[i];
                        }
                    }

                    var mTrueValue = null;
                    mValue = mValue.toString();
                    mValue = mValue.replace(/\$([^;\$]*);/gm, function ($raw, varName) {
                        var ret = $raw;
                        var originalVarName = varName;
                        var parts = Bee.explode(':', varName, 2);
                        var context = '0';
                        if (2 === parts.length) {
                            context = parts[0];
                            varName = parts[1];
                        }
                        var aoContext = null;
                        for (var i in aContexts) {
                            if (i == context) {
                                aoContext = aContexts[i];
                            }
                        }
                        if (null !== aoContext) {
                            if ('*' === varName) {
                                ret = aoContext;
                                mTrueValue = ret;
                            }
                            else {
                                if (true === Bee.hasDotValue(varName, aoContext)) {
                                    var value = Bee.getDotValue(varName, aoContext);
                                    ret = value;
                                    var bIsHost = ($raw.length === mValue.length);
                                    if (Bee.isArrayOrObject(value)) {
                                        if (true === bIsHost) {
                                            ret = value;
                                            mTrueValue = ret;
                                        }
                                        else {
                                            Bee.error("The variable tag (%s) must be scalar when injected as part of the host, array given".replace("%s", varName));
                                            ret = $raw;
                                        }
                                    }
                                    else {
                                        if (true === bIsHost) {
                                            mTrueValue = ret;
                                        }
                                    }
                                }
                                else {
                                    if ('normal' === mode) {
                                        ret = $raw.replace('$' + originalVarName + ';', '');
                                    }
                                }
                            }
                        }
                        else {
                            if ('strict' === mode) {
                                Bee.error("Context not found with identifier: %s".replace("%s", context));
                            }
                            else {
                                ret = $raw.replace('$' + originalVarName + ';', '');
                            }
                        }
                        return ret;
                    });

                    if (mTrueValue) {
                        mValue = mTrueValue;
                    }
                }
            }
            return mValue;
        };


        Bee.getFileExtension = function (sName) {
            if (Bee.isDefined(sName)) {
                var aParts = sName.split('.');
                if (aParts.length > 1) {
                    return aParts.pop();
                }
            }
            return '';
        };
        Bee.getFileName = function (string) {
            return Bee.basename(string);
        };


        /**
         * Was written to return "standard" control element types, which is the list below:
         *
         * - input (for input text)
         * - hidden
         * - radio
         * - checkbox
         * - select
         * - textarea
         *
         */
        Bee.getFormElementType = function (el) {
            if (el.jquery) {
                el = el[0];
            }
            var nodeName = el.nodeName.toLowerCase();
            if ('input' === nodeName) {
                var type = el.type.toLowerCase();
                if (Bee.inArray(type, ['radio', 'checkbox', 'hidden'])) {
                    nodeName = type;
                }
            }
            return nodeName;
        };


        Bee.getFormValues = function (form, bTrueFlatMode) {
            if (!Bee.isDefined(bTrueFlatMode)) {
                bTrueFlatMode = true;
            }
            if (true === bTrueFlatMode) {
                var ret = {};


                var els = [];
                if ('form' === form.nodeName.toLowerCase()) {
                    for (var i = 0; i < form.elements.length; i++) {
                        els.push(form.elements[i]);
                    }
                }
                // not a form
                else {
                    jQuery("[name]", jQuery(form)).each(function () {
                        els.push(jQuery(this)[0]);
                    });
                }


                for (var i in els) {
                    var e = els[i];
                    if (e.name) {
                        var value = e.value;
                        if ('checkbox' === e.type || 'radio' === e.type) {
                            if (null === e.getAttribute('value')) {
                                if (true === e.checked) {
                                    value = true;
                                }
                                else {
                                    value = false;
                                }
                            }
                            else {
                                if (false === e.checked) {
                                    value = null;
                                }
                            }
                        }
                        ret[e.name] = value;
                    }
                }

                return ret;
            }
            else {
                // should return array values as array
                Bee.error("Not implemented yet");
            }
        };


        Bee.getHexByCssString = function (sInput) {
            var ret = sInput;
            if (sInput.search("rgb") !== -1) {
                sInput = sInput.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                function hex(x) {
                    return ("0" + parseInt(x).toString(16)).slice(-2);
                }

                ret = hex(sInput[1]) + hex(sInput[2]) + hex(sInput[3]);
            }
            if ('#' === ret.substr(0, 1)) {
                ret = ret.substr(1);
            }
            return ret;
        };


        Bee.getHumanFileSize = function (iFileSizeInBytes) {
            var i = -1;
            var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
            do {
                iFileSizeInBytes = iFileSizeInBytes / 1024;
                i++;
            } while (iFileSizeInBytes > 1024);

            return Math.max(iFileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
        };

        Bee.getJqueryElement = function (element) {
            return jQuery(element);
        };
        Bee.getJqueryElementByInner = function (jInner, parentSelector, mode) {
            if (Bee.isDefined(mode)) {
                mode = 0;
            }
            var jEl = jInner.closest(parentSelector);
            if (jEl.length) {
                return jEl;
            }

            if (2 === mode) {
                return null;
            }
            var msg = "getJqueryElementByInner: Parent element not found with selector %s".replace("%s", parentSelector);
            if (0 === mode) {
                Bee.error(msg);
                return;
            }
            // mode===1
            throw new Error(msg);
        };


//        Bee.getMysqlDatetime = function(iNullTimestamp){
//            if(!Bee.isDefined(iNullTimestamp)){
//                iNullTimestamp = Bee.getTimestamp();
//            }
//            return Bee.date('Y-m-d H:i:s', iNullTimestamp);
//        };

        Bee.getNewCssId = function (cssId) {
            while (document.getElementById(cssId)) {
                cssId += '0';
            }
            return cssId;
        };

        Bee.getOrDefault = function (mVar, mDefault) {
            if (Bee.isDefined(mVar)) {
                return mVar;
            }
            return mDefault;
        };


        /**
         * Converts image URLs to dataURL schema using Javascript only.
         *
         * @param {String} url Location of the image file
         * @param {Function} success Callback function that will handle successful responses. This function should take one parameter
         *                            <code>dataURL</code> which will be a type of <code>String</code>.
         * @param {Function} error Error handler.
         *
         * @example
         * var onSuccess = function(e){
         * 	document.body.appendChild(e.image);
         * 	alert(e.data);
         * };
         *
         * var onError = function(e){
         * 	alert(e.message);
         * };
         *
         * getImageDataURL('myimage.png', onSuccess, onError);
         *
         */
        Bee.getImageDataURL = function (url, success, error) {
            var data, canvas, ctx;
            var img = new Image();
            img.onload = function () {
                // Create the canvas element.
                canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                // Get '2d' context and draw the image.
                ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0);
                // Get canvas data URL
                try {
                    data = canvas.toDataURL();
                    success({image: img, data: data});
                } catch (e) {
                    error(e);
                }
            };
            // Load image URL.
            try {
                img.src = url;
            } catch (e) {
                error(e);
            }
        };


//        Bee.getResolvedTemplateContent = function (sHtml, aoTags) {
//            for (var tag in aoTags) {
//                var value = aoTags[tag];
//                if (!Bee.isArrayOrObject(value)) {
//                    var tag2 = '{{&amp;' + tag + '}}';
//                    tag = '{{' + tag + '}}';
//                    sHtml = Bee.strReplace(tag2, Bee.htmlSpecialChars(value), sHtml);
//                    sHtml = Bee.strReplace(tag, value, sHtml);
//                }
//                else {
//                    var reg = new RegExp(
//                        "\\{\\{\\#" + tag + "\\}\\}((.|[\r\n])*?)\\{\\{/" + tag + "\\}\\}",
//                        "g");
//                    var result;
//                    while ((result = reg.exec(sHtml)) !== null) {
//                        var content = '';
//                        for (var j in value) {
//                            var contentTemplate = result[1];
//                            for (var k in value[j]) {
//                                var innerValue = value[j][k];
//                                if (!Bee.isArrayOrObject(innerValue)) {
//                                    var k2 = '{{&amp;' + k + '}}';
//                                    k = '{{' + k + '}}';
//                                    contentTemplate = Bee.strReplace(k2, Bee.htmlSpecialChars(innerValue), contentTemplate);
//                                    contentTemplate = Bee.strReplace(k, innerValue, contentTemplate);
//                                }
//                            }
//                            content += contentTemplate;
//                        }
//                        sHtml = Bee.strReplace(result[0], content, sHtml);
//                    }
//                }
//            }
//            return sHtml;
//        };


        Bee.getScriptTags = function (html) {
            var r = /(\<script(.*?)\>([\s\S.]*?)\<\/script\>)/gi;
            return html.match(r);
        };


//        Bee.getTimestamp = function () {
//            return new Date().getTime() * 1000;
//        };

        Bee.getUniqueCssId = function () {
            return 'uid-' + Bee.rand(1, 1000000000);
        };

        Bee.getVarName = function (string) {
            // blanks to underscore
            string = string.replace(/\s+/g, '_');
            // remove weird chars
            string = string.replace(/[^a-zA-Z0-9_]/gm, '');
            // converts multiple underscores to one single underscore
            string = string.replace(/_+/gm, '_', string);
            return string;
        };


        /**
         * Gets the dimensions of the inner window,
         * meaning the application window, without the firebug console.
         *
         * @returns {{width: number, height: number}}
         */
        Bee.getWindowInnerDimensions = function () {
            var winW = 630, winH = 460;
            if (document.body && document.body.offsetWidth) {
                winW = document.body.offsetWidth;
                winH = document.body.offsetHeight;
            }
            if (document.compatMode == 'CSS1Compat' &&
                document.documentElement &&
                document.documentElement.offsetWidth) {
                winW = document.documentElement.offsetWidth;
                winH = document.documentElement.offsetHeight;
            }
            if (window.innerWidth && window.innerHeight) {
                winW = window.innerWidth;
                winH = window.innerHeight;
            }
            return {
                width: winW,
                height: winH
            };
        };


        // http://stackoverflow.com/questions/2557247/easiest-way-to-retrieve-cross-browser-xmlhttprequest
        Bee.getXhr = function () {
            var XMLHttpFactories = [
                function () {
                    return new XMLHttpRequest();
                },
                function () {
                    return new ActiveXObject("Msxml2.XMLHTTP");
                },
                function () {
                    return new ActiveXObject("Msxml3.XMLHTTP");
                },
                function () {
                    return new ActiveXObject("Microsoft.XMLHTTP");
                }
            ];
            var xmlhttp = false;
            for (var i = 0; i < XMLHttpFactories.length; i++) {
                try {
                    xmlhttp = XMLHttpFactories[i]();
                }
                catch (e) {
                    continue;
                }
                break;
            }
            return xmlhttp;
        };


        Bee.handleCAM = function (url, mArgs, fnCallback, fnAlwaysCallback, aoOptions) {
            if (!Bee.isDefined(mArgs)) {
                mArgs = {};
            }
            aoOptions = Bee.arrayMerge({
                    useClv: true
                },
                aoOptions);
            if (true === aoOptions.useClv) {
                mArgs = Bee.clvEncode(mArgs);
            }

            jQuery.post(url, mArgs, function (CAM) {
                Bee.handleCAMMessage(CAM, fnCallback, aoOptions);
            }, 'json')
                .fail(function () {
                    Bee.error("Invalid url: %s. The url must return a CAM message.".replace('%s', url));
                })
                .always(function () {
                    if (Bee.isFunction(fnAlwaysCallback)) {
                        fnAlwaysCallback();
                    }
                });
        };


        Bee.handleCAMMessage = function (CAM, fnCallback, aoOptions) {
            if (CAM.type) {
                if ('undefined' !== CAM.message) {
                    if ('error' === CAM.type) {
                        var aMessage = Bee.toArray(CAM.message);
                        Bee.notify(aMessage.join("<br />"), 'error');
                    }
                    else {

                        if (!Bee.isDefined(aoOptions)) {
                            aoOptions = {};
                        }

                        var options = Bee.arrayReplace({
                            adrMode: 'post', // pre|post|false  see [asp]
                            onCodeExecutedAfterPost: null
                        }, aoOptions);


                        /**
                         * It's a little bit messy here,
                         * the fnSecondCallback is sort of an error: I wasn't aware that the
                         * adrScriptPreload will possibly load assets async, so it may be executed before
                         * the adrScriptPreload code is really loaded.
                         *
                         * The right method is options.onCodeExecutedAfterPost.
                         * fnSecondCallback is kept for compatibility.
                         *
                         */

                        if ('pre' === options.adrMode) {
                            Bee.adrScriptPreload(CAM.message);
                        }
                        if (Bee.isFunction(fnCallback)) {
                            fnCallback(CAM.message);
                        }
                        if ('post' === options.adrMode) {
                            Bee.adrScriptPreload(CAM.message, options.onCodeExecutedAfterPost);
                        }
                        if (aoOptions.fnSecondCallback && Bee.isFunction(aoOptions.fnSecondCallback)) {
                            aoOptions.fnSecondCallback(CAM.message);
                        }
                    }
                }
                else {
                    Bee.error("Message not found. This is not a valid CAM message");
                }
            }
            else {
                Bee.error("Type not found. This is not a valid CAM message");
            }
        };


        Bee.handleCAMSdo = function (url, mArgs, fnCallback, fnAlwaysCallback, aoOptions) {
            Bee.handleCAM(url, mArgs, function (response) {
                var html = '';
                if (Bee.arrayKeyExists('html', response)) {
                    html = response.html;
                }
                var aoExtraProperties = response; // should have strip html and script
                fnCallback(html, aoExtraProperties);
            }, fnAlwaysCallback, aoOptions);
        };

        Bee.hasDotValue = function (sPath, aoArray) {
            var sUnguessable = '-_|array-hasdot|_-';
            var ret = Bee.getDotValue(sPath, aoArray, sUnguessable);
            return (ret !== sUnguessable);
        };


        Bee.hasMethod = function (oObject, sMethod) {
            return ('function' === typeof oObject[sMethod]);
        };


        Bee.htmlSpecialChars = function (sString) {
            if (false === Bee.isDefined(sString) || null === sString) {
                return '';
            }
            return sString.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        };

        Bee.htmlSpecialCharsDecode = function (string, quote_style) {
            // http://kevin.vanzonneveld.net
            // +   original by: Mirek Slugen
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Mateusz "loonquawl" Zalega
            // +      input by: ReverseSyntax
            // +      input by: Slawomir Kaniecki
            // +      input by: Scott Cariss
            // +      input by: Francois
            // +   bugfixed by: Onno Marsman
            // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
            // +      input by: Ratheous
            // +      input by: Mailfaker (http://www.weedem.fr/)
            // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
            // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
            // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
            // *     returns 1: '<p>this -> &quot;</p>'
            // *     example 2: htmlspecialchars_decode("&amp;quot;");
            // *     returns 2: '&quot;'
            var optTemp = 0,
                i = 0,
                noquotes = false;
            if (typeof quote_style === 'undefined') {
                quote_style = 2;
            }
            string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            var OPTS = {
                'ENT_NOQUOTES': 0,
                'ENT_HTML_QUOTE_SINGLE': 1,
                'ENT_HTML_QUOTE_DOUBLE': 2,
                'ENT_COMPAT': 2,
                'ENT_QUOTES': 3,
                'ENT_IGNORE': 4
            };
            if (quote_style === 0) {
                noquotes = true;
            }
            if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
                quote_style = [].concat(quote_style);
                for (i = 0; i < quote_style.length; i++) {
                    // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
                    if (OPTS[quote_style[i]] === 0) {
                        noquotes = true;
                    } else if (OPTS[quote_style[i]]) {
                        optTemp = optTemp | OPTS[quote_style[i]];
                    }
                }
                quote_style = optTemp;
            }
            if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
                string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
                // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
            }
            if (!noquotes) {
                string = string.replace(/&quot;/g, '"');
            }
            // Put this in last place to avoid escape being double-decoded
            string = string.replace(/&amp;/g, '&');

            return string;
        };


        Bee.inArray = function (sValue, aoArray, bTrueStrict) {
            if (false === bTrueStrict) {
                for (var i in aoArray) {
                    if (aoArray[i] == sValue) {
                        return true;
                    }
                }
            }
            else {
                for (var i in aoArray) {
                    if (aoArray[i] === sValue) {
                        return true;
                    }
                }
            }
            return false;
        };

        // implements tiwp pattern
        // @see [pattern]/js/template/tiwp.txt
        Bee.injectTemplate = function (jTpl, jTarget) {
            var sParentSel = jTpl.attr('data-decorated-by');
            if (sParentSel) {
                var sHtml = jTpl.html();
                var parts = sHtml.split('<!---->');
                if (3 === parts.length) {
                    var tpl = Bee.trim(parts[1]);
                    var jRealTpl = jQuery(tpl);
                    var jParent = jTarget.find(sParentSel);
                    if (jParent && jParent.length > 0) {
                        jParent.append(jRealTpl);
                    }
                    else {
                        jTarget.append(sHtml);
                    }
                    return jRealTpl;
                }
                else {
                    Be.error("Invalid markup, see tiwp for more info");
                }
            }
            else {
                jTarget.append(jTpl);
            }
            return jTpl;
        };


        Bee.isArray = function (aArray) {
            if (Object.prototype.toString.call(aArray) === '[object Array]') {
                return true;
            }
            return false;
        };


        Bee.isArrayObject = function (aArray) {
            if (Bee.inArray(Object.prototype.toString.call(aArray), ['[object Object]'])) {
                return true;
            }
            return false;
        };

        Bee.isArrayOrObject = function (aArray) {
            if (Bee.inArray(Object.prototype.toString.call(aArray), ['[object Array]', '[object Object]'])) {
                return true;
            }
            return false;
        };

        Bee.isAnImage = function (sUrl) {
            var ext = Bee.getFileExtension(sUrl).toLowerCase();
            return Bee.inArray(ext, ['jpeg', 'jpg', 'png', 'gif', 'bmp']);
        };

        Bee.isDefined = function (mVar) {
            return typeof mVar !== 'undefined';
        };

        Bee.isDefinedOr = function (mVar, mDefault) {
            if (Bee.isDefined(mVar)) {
                return mVar;
            }
            return mDefault;
        };

        Bee.isFunction = function (mTest) {
            if (typeof mTest == 'function') {
                return true;
            }
            return false;
        };

        Bee.isJqueryElement = function (mixed) {
            if (mixed && mixed.jquery) {
                return true;
            }
            return false;
        };

        Bee.isNumerical = function (mTest) {
            return !isNaN(parseFloat(mTest)) && isFinite(mTest);
        };

        Bee.isObject = function (oObject) {
            if (Bee.inArray(Object.prototype.toString.call(oObject), ['[object Object]'])) {
                return true;
            }
            return false;
        };

        Bee.isSerialized = function (mValue) {
            if (false === Bee.isString(mValue) || Bee.isNumerical(mValue)) {
                return false;
            }
            try {
                var data = Bee.unserialize(mValue);
                if ('b:0;' === mValue || false !== data) {
                    return true;
                }
                return false;
            }
            catch (e) {
                return false;
            }

        };

        Bee.isString = function (mValue) {
            if ('string' === typeof mValue) {
                return true;
            }
            return false;
        };


        // @link http://api.jquery.com/category/selectors/
        Bee.jQuerySelectorEscape = function (sExpression) {
            return sExpression.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        };

        Bee.jQueryUIGetParentDialog = function (jInner) {
            var jDialog = jInner.closest(".ui-dialog");
            if (jDialog.length > 0) {
                return jDialog;
            }
            return null;
        };


        Bee.jsdanResolve = function (mInput, aKeys) {
            if (Bee.isArrayOrObject(mInput)) {
                if (aKeys.length) {
                    for (var i in aKeys) {
                        if (Bee.arrayKeyExists(aKeys[i], mInput)) {
                            mInput[aKeys[i]] = Bee.jsdanResolve(mInput[aKeys[i]], aKeys);
                        }
                    }
                }
                else {
                    for (var i in mInput) {
                        mInput[i] = Bee.jsdanResolve(mInput[i], aKeys);
                    }
                }
            }
            else {
                if (Bee.isString(mInput) && /^_screen(Width|Height)\.[0-9]+$/.test(mInput)) {
                    var parts = mInput.split('.');
                    var screenDim = 0;
                    if ('_screenWidth' === parts[0]) {
                        screenDim = window.innerWidth;
                    }
                    else {
                        screenDim = window.innerHeight;
                    }
                    return parseInt(parts[1]) * screenDim / 100;
                }
                return mInput;
            }
        };


        Bee.ltrim = function (str, charlist) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: Erkekjetter
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Onno Marsman
            // *     example 1: ltrim('    Kevin van Zonneveld    ');
            // *     returns 1: 'Kevin van Zonneveld    '
            charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
            var re = new RegExp('^[' + charlist + ']+', 'g');
            return (str + '').replace(re, '');
        };

        Bee.matchClumsy = function (sValue, sBase) {
            return (sValue.substr(0, sBase.length) === sBase);
        };

        Bee.notis = function (aoNotisNode) {
            if (Bee.arrayKeyExists("msg", aoNotisNode)) {
                if (Bee.arrayKeyExists("type", aoNotisNode)) {
                    Bee.notify(aoNotisNode.msg, aoNotisNode.type);
                }
                else {
                    Bee.error("Invalid notis node format: type property is missing");
                }
            }
            else {
                Bee.error("Invalid notis node format: msg property is missing");
            }
        };

        Bee.notNull = function (mVar) {
            if (Bee.isDefined(mVar)) {
                if (null !== mVar) {
                    return true;
                }
            }
            return false;
        };


        Bee.parseJSON = function (sString) {
            return JSON.parse(sString);
        };


        Bee.parseStr = function (str, array) {
            // http://kevin.vanzonneveld.net
            // +   original by: Cagri Ekin
            // +   improved by: Michael White (http://getsprink.com)
            // +    tweaked by: Jack
            // +   bugfixed by: Onno Marsman
            // +   reimplemented by: stag019
            // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: stag019
            // +   input by: Dreamer
            // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
            // +   input by: Zaide (http://zaidesthings.com/)
            // +   input by: David Pesta (http://davidpesta.com/)
            // +   input by: jeicquest
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // %        note 1: When no argument is specified, will put variables in global scope.
            // %        note 1: When a particular argument has been passed, and the returned value is different parse_str of PHP. For example, a=b=c&d====c
            // *     example 1: var arr = {};
            // *     example 1: parse_str('first=foo&second=bar', arr);
            // *     results 1: arr == { first: 'foo', second: 'bar' }
            // *     example 2: var arr = {};
            // *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
            // *     results 2: arr == { str_a: "Jack and Jill didn't see the well." }
            // *     example 3: var abc = {3:'a'};
            // *     example 3: parse_str('abc[a][b]["c"]=def&abc[q]=t+5');
            // *     results 3: JSON.stringify(abc) === '{"3":"a","a":{"b":{"c":"def"}},"q":"t 5"}';


            var strArr = String(str).replace(/^&/, '').replace(/&$/, '').split('&'),
                sal = strArr.length,
                i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
                postLeftBracketPos, keys, keysLen,
                fixStr = function (str) {
                    return decodeURIComponent(str.replace(/\+/g, '%20'));
                };

            if (!array) {
                array = this.window;
            }

            for (i = 0; i < sal; i++) {
                tmp = strArr[i].split('=');
                key = fixStr(tmp[0]);
                value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

                while (key.charAt(0) === ' ') {
                    key = key.slice(1);
                }
                if (key.indexOf('\x00') > -1) {
                    key = key.slice(0, key.indexOf('\x00'));
                }
                if (key && key.charAt(0) !== '[') {
                    keys = [];
                    postLeftBracketPos = 0;
                    for (j = 0; j < key.length; j++) {
                        if (key.charAt(j) === '[' && !postLeftBracketPos) {
                            postLeftBracketPos = j + 1;
                        }
                        else if (key.charAt(j) === ']') {
                            if (postLeftBracketPos) {
                                if (!keys.length) {
                                    keys.push(key.slice(0, postLeftBracketPos - 1));
                                }
                                keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
                                postLeftBracketPos = 0;
                                if (key.charAt(j + 1) !== '[') {
                                    break;
                                }
                            }
                        }
                    }
                    if (!keys.length) {
                        keys = [key];
                    }
                    for (j = 0; j < keys[0].length; j++) {
                        chr = keys[0].charAt(j);
                        if (chr === ' ' || chr === '.' || chr === '[') {
                            keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
                        }
                        if (chr === '[') {
                            break;
                        }
                    }

                    obj = array;
                    for (j = 0, keysLen = keys.length; j < keysLen; j++) {
                        key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
                        lastIter = j !== keys.length - 1;
                        lastObj = obj;
                        if ((key !== '' && key !== ' ') || j === 0) {
                            if (obj[key] === undef) {
                                obj[key] = {};
                            }
                            obj = obj[key];
                        }
                        else { // To insert new dimension
                            ct = -1;
                            for (p in obj) {
                                if (obj.hasOwnProperty(p)) {
                                    if (+p > ct && p.match(/^\d+$/g)) {
                                        ct = +p;
                                    }
                                }
                            }
                            key = ct + 1;
                        }
                    }
                    lastObj[key] = value;
                }
            }
        };


        Bee.popKey = function (key, aoArray) {
            var ret = null;
            if (true === Bee.isArray(aoArray)) {
                for (var i in aoArray) {
                    if (i == key) {
                        ret = aoArray[key];
                        aoArray.splice(key, 1);
                    }
                }
            }
            else {
                if (Bee.arrayKeyExists(key, aoArray)) {
                    ret = aoArray[key];
                    delete aoArray[key];
                }
            }

            return ret;
        };


        Bee.pregQuote = function (str, delimiter) {
            // http://kevin.vanzonneveld.net
            // +   original by: booeyOH
            // +   improved by: Ates Goral (http://magnetiq.com)
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Onno Marsman
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // *     example 1: preg_quote("$40");
            // *     returns 1: '\$40'
            // *     example 2: preg_quote("*RRRING* Hello?");
            // *     returns 2: '\*RRRING\* Hello\?'
            // *     example 3: preg_quote("\\.+*?[^]$(){}=!<>|:");
            // *     returns 3: '\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:'
            return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
        };


        Bee.prepareJqueryObject = function (jObj, sSelector, jContext, bTrueEx) {
            jObj = jQuery(sSelector, jContext);
            if (jObj.length) {
                return jObj;
            }
            else {
                if (false !== bTrueEx) {
                    throw new Error("Object not found with selector %s".replace('%s', sSelector));
                }
                return null;
            }
        };


        Bee.rand = function (min, max) {
            // http://kevin.vanzonneveld.net
            // +   original by: Leslie Hoare
            // +   bugfixed by: Onno Marsman
            // %          note 1: See the commented out code below for a version which will work with our experimental (though probably unnecessary) srand() function)
            // *     example 1: rand(1, 1);
            // *     returns 1: 1
            var argc = arguments.length;
            if (argc === 0) {
                min = 0;
                max = 2147483647;
            } else if (argc === 1) {
                fnError('Warning: rand() expects exactly 2 parameters, 1 given');
            }
            return Math.floor(Math.random() * (max - min + 1)) + min;
        };


        Bee.round = function (value, precision, mode) {
            // http://kevin.vanzonneveld.net
            // +   original by: Philip Peterson
            // +    revised by: Onno Marsman
            // +      input by: Greenseed
            // +    revised by: T.Wild
            // +      input by: meo
            // +      input by: William
            // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
            // +      input by: Josep Sanz (http://www.ws3.es/)
            // +    revised by: Rafa? Kukawski (http://blog.kukawski.pl/)
            // %        note 1: Great work. Ideas for improvement:
            // %        note 1:  - code more compliant with developer guidelines
            // %        note 1:  - for implementing PHP constant arguments look at
            // %        note 1:  the pathinfo() function, it offers the greatest
            // %        note 1:  flexibility & compatibility possible
            // *     example 1: round(1241757, -3);
            // *     returns 1: 1242000
            // *     example 2: round(3.6);
            // *     returns 2: 4
            // *     example 3: round(2.835, 2);
            // *     returns 3: 2.84
            // *     example 4: round(1.1749999999999, 2);
            // *     returns 4: 1.17
            // *     example 5: round(58551.799999999996, 2);
            // *     returns 5: 58551.8
            var m, f, isHalf, sgn; // helper variables
            precision |= 0; // making sure precision is integer
            m = Math.pow(10, precision);
            value *= m;
            sgn = (value > 0) | -(value < 0); // sign of the number
            isHalf = value % 1 === 0.5 * sgn;
            f = Math.floor(value);

            if (isHalf) {
                switch (mode) {
                    case 'PHP_ROUND_HALF_DOWN':
                        value = f + (sgn < 0); // rounds .5 toward zero
                        break;
                    case 'PHP_ROUND_HALF_EVEN':
                        value = f + (f % 2 * sgn); // rouds .5 towards the next even integer
                        break;
                    case 'PHP_ROUND_HALF_ODD':
                        value = f + !(f % 2); // rounds .5 towards the next odd integer
                        break;
                    default:
                        value = f + (sgn > 0); // rounds .5 away from zero
                }
            }

            return (isHalf ? value : Math.round(value)) / m;
        };


        Bee.serialize = function (mixed_value) {
            // http://kevin.vanzonneveld.net
            // +   original by: Arpad Ray (mailto:arpad@php.net)
            // +   improved by: Dino
            // +   bugfixed by: Andrej Pavlovic
            // +   bugfixed by: Garagoth
            // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
            // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
            // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
            // +      input by: Martin (http://www.erlenwiese.de/)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
            // +   improved by: Le Torbi (http://www.letorbi.de/)
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
            // +   bugfixed by: Ben (http://benblume.co.uk/)
            // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
            // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
            // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
            // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
            // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
            // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
            var val, key, okey,
                ktype = '', vals = '', count = 0,
                _utf8Size = function (str) {
                    var size = 0,
                        i = 0,
                        l = str.length,
                        code = '';
                    for (i = 0; i < l; i++) {
                        code = str.charCodeAt(i);
                        if (code < 0x0080) {
                            size += 1;
                        }
                        else if (code < 0x0800) {
                            size += 2;
                        }
                        else {
                            size += 3;
                        }
                    }
                    return size;
                },
                _getType = function (inp) {
                    var match, key, cons, types, type = typeof inp;

                    if (type === 'object' && !inp) {
                        return 'null';
                    }
                    if (type === 'object') {
                        if (!inp.constructor) {
                            return 'object';
                        }
                        cons = inp.constructor.toString();
                        match = cons.match(/(\w+)\(/);
                        if (match) {
                            cons = match[1].toLowerCase();
                        }
                        types = ['boolean', 'number', 'string', 'array'];
                        for (key in types) {
                            if (cons == types[key]) {
                                type = types[key];
                                break;
                            }
                        }
                    }
                    return type;
                },
                type = _getType(mixed_value)
                ;

            switch (type) {
                case 'function':
                    val = '';
                    break;
                case 'boolean':
                    val = 'b:' + (mixed_value ? '1' : '0');
                    break;
                case 'number':
                    val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;
                    break;
                case 'string':
                    val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';
                    break;
                case 'array':
                case 'object':
                    val = 'a';
                    /*
                     if (type === 'object') {
                     var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
                     if (objname == undefined) {
                     return;
                     }
                     objname[1] = this.serialize(objname[1]);
                     val = 'O' + objname[1].substring(1, objname[1].length - 1);
                     }
                     */

                    for (key in mixed_value) {
                        if (mixed_value.hasOwnProperty(key)) {
                            ktype = _getType(mixed_value[key]);
                            if (ktype === 'function') {
                                continue;
                            }

                            okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                            vals += Bee.serialize(okey) + Bee.serialize(mixed_value[key]);
                            count++;
                        }
                    }
                    val += ':' + count + ':{' + vals + '}';
                    break;
                case 'undefined':
                // Fall-through
                default:
                    // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
                    val = 'N';
                    break;
            }
            if (type !== 'object' && type !== 'array') {
                val += ';';
            }
            return val;
        };


        Bee.setBracketSquareValue = function (sPath, aoArray, mValue, bTrueCreateNewKeys) {
            while ('[]' === sPath.substr(-2)) {
                sPath = sPath.substr(0, sPath.length - 2);
            }

            if (Bee.isDefined(bTrueCreateNewKeys) && false === bTrueCreateNewKeys) {
                sPath = Bee.strReplace('"', '\\"', sPath);
                sPath = Bee.strReplace(']', '"]', sPath);
                sPath = Bee.strReplace('[', '["', sPath);

                try {
                    var a = aoArray;
                    var code = 'a.' + sPath + ' = mValue';
                    eval(code);
                }
                catch (e) {
                    return false;
                }
            }

            sPath = Bee.strReplace('"', '\\"', sPath);
            sPath = Bee.strReplace(']', '', sPath);
            sPath = Bee.strReplace('[', '...', sPath);

            var aParts = sPath.split('...');
            Bee.arrayCreateHierarchy(aoArray, aParts, mValue);
        };

        Bee.setDotValue = function (sPath, mReplacement, aoArray) {
            var $beeDot = '__-BEE_DOT-__';
            sPath = sPath.replace("\\.", $beeDot);
            var $parts = sPath.split(".");
            if ($parts.length > 1) {
                var firstEl = $parts.shift();
                if (firstEl) {
                    var $key = firstEl.replace($beeDot, '.');
                    if (!Bee.arrayKeyExists($key, aoArray) || (Bee.arrayKeyExists($key, aoArray) && !Bee.isArrayOrObject(aoArray[$key]))) {
                        aoArray[$key] = {};
                    }
                }
                Bee.setDotValue($parts.join('.'), mReplacement, aoArray[$key]);
            } else {
                var firstEl = $parts.shift();
                $key = firstEl.replace($beeDot, '.');
                aoArray[$key] = mReplacement;
            }
        };


        Bee.setData = function (jElement, sKey, mData) {
            var sId = Bee.uniqueId(jElement);
            var aoData = Bee.getValue("_data", false, {});
            if (!Bee.arrayKeyExists(sId, aoData)) {
                aoData[sId] = {};
            }
            aoData[sId][sKey] = mData;
            Bee.setValue("_data", aoData);
        };

        Bee.setZIndexFocus = function (jEl) {
            var index_highest = 0;
            jQuery('div').each(function () {
                var index_current = parseInt(jQuery(this).css("z-index"), 10);
                if (index_current > index_highest) {
                    index_highest = index_current;
                }
            });
            jEl.css("z-index", index_highest + 1);
        };

        Bee.sprintf = function () {
            // http://kevin.vanzonneveld.net
            // +   original by: Ash Searle (http://hexmen.com/blog/)
            // + namespaced by: Michael White (http://getsprink.com)
            // +    tweaked by: Jack
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: Paulo Freitas
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Dj
            // +   improved by: Allidylls
            // *     example 1: sprintf("%01.2f", 123.1);
            // *     returns 1: 123.10
            // *     example 2: sprintf("[%10s]", 'monkey');
            // *     returns 2: '[    monkey]'
            // *     example 3: sprintf("[%'#10s]", 'monkey');
            // *     returns 3: '[####monkey]'
            // *     example 4: sprintf("%d", 123456789012345);
            // *     returns 4: '123456789012345'
            var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
            var a = arguments,
                i = 0,
                format = a[i++];

            // pad()
            var pad = function (str, len, chr, leftJustify) {
                if (!chr) {
                    chr = ' ';
                }
                var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
                return leftJustify ? str + padding : padding + str;
            };

            // justify()
            var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
                var diff = minWidth - value.length;
                if (diff > 0) {
                    if (leftJustify || !zeroPad) {
                        value = pad(value, minWidth, customPadChar, leftJustify);
                    } else {
                        value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
                    }
                }
                return value;
            };

            // formatBaseX()
            var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
                // Note: casts negative numbers to positive ones
                var number = value >>> 0;
                prefix = prefix && number && {
                    '2': '0b',
                    '8': '0',
                    '16': '0x'
                }[base] || '';
                value = prefix + pad(number.toString(base), precision || 0, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            };

            // formatString()
            var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
                if (precision != null) {
                    value = value.slice(0, precision);
                }
                return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
            };

            // doFormat()
            var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
                var number;
                var prefix;
                var method;
                var textTransform;
                var value;

                if (substring === '%%') {
                    return '%';
                }

                // parse flags
                var leftJustify = false,
                    positivePrefix = '',
                    zeroPad = false,
                    prefixBaseX = false,
                    customPadChar = ' ';
                var flagsl = flags.length;
                for (var j = 0; flags && j < flagsl; j++) {
                    switch (flags.charAt(j)) {
                        case ' ':
                            positivePrefix = ' ';
                            break;
                        case '+':
                            positivePrefix = '+';
                            break;
                        case '-':
                            leftJustify = true;
                            break;
                        case "'":
                            customPadChar = flags.charAt(j + 1);
                            break;
                        case '0':
                            zeroPad = true;
                            break;
                        case '#':
                            prefixBaseX = true;
                            break;
                    }
                }

                // parameters may be null, undefined, empty-string or real valued
                // we want to ignore null, undefined and empty-string values
                if (!minWidth) {
                    minWidth = 0;
                } else if (minWidth === '*') {
                    minWidth = +a[i++];
                } else if (minWidth.charAt(0) == '*') {
                    minWidth = +a[minWidth.slice(1, -1)];
                } else {
                    minWidth = +minWidth;
                }

                // Note: undocumented perl feature:
                if (minWidth < 0) {
                    minWidth = -minWidth;
                    leftJustify = true;
                }

                if (!isFinite(minWidth)) {
                    throw new Error('sprintf: (minimum-)width must be finite');
                }

                if (!precision) {
                    precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type === 'd') ? 0 : undefined;
                } else if (precision === '*') {
                    precision = +a[i++];
                } else if (precision.charAt(0) == '*') {
                    precision = +a[precision.slice(1, -1)];
                } else {
                    precision = +precision;
                }

                // grab value using valueIndex if required?
                value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

                switch (type) {
                    case 's':
                        return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
                    case 'c':
                        return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
                    case 'b':
                        return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'o':
                        return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'x':
                        return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'X':
                        return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
                    case 'u':
                        return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'i':
                    case 'd':
                        number = +value || 0;
                        number = Math.round(number - number % 1); // Plain Math.round doesn't just truncate
                        prefix = number < 0 ? '-' : positivePrefix;
                        value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                        return justify(value, prefix, leftJustify, minWidth, zeroPad);
                    case 'e':
                    case 'E':
                    case 'f': // Should handle locales (as per setlocale)
                    case 'F':
                    case 'g':
                    case 'G':
                        number = +value;
                        prefix = number < 0 ? '-' : positivePrefix;
                        method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                        textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                        value = prefix + Math.abs(number)[method](precision);
                        return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
                    default:
                        return substring;
                }
            };

            return format.replace(regex, doFormat);
        };

        Bee.stripScriptTags = function (html) {
            var r = /(\<script(.*?)\>([\s\S.]*?)\<\/script\>)/gi;
            return html.replace(r, "");
        };

        Bee.strpos = function (haystack, needle, offset) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Onno Marsman
            // +   bugfixed by: Daniel Esteban
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // *     example 1: strpos('Kevin van Zonneveld', 'e', 5);
            // *     returns 1: 14
            var i = (haystack + '').indexOf(needle, (offset || 0));
            return i === -1 ? false : i;
        };

        Bee.strRepeat = function (input, multiplier) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
            // +   improved by: Ian Carter (http://euona.com/)
            // *     example 1: str_repeat('-=', 10);
            // *     returns 1: '-=-=-=-=-=-=-=-=-=-='

            var y = '';
            while (true) {
                if (multiplier & 1) {
                    y += input;
                }
                multiplier >>= 1;
                if (multiplier) {
                    input += input;
                }
                else {
                    break;
                }
            }
            return y;
        };


        Bee.strReplace = function (search, replace, subject, count) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Gabriel Paderni
            // +   improved by: Philip Peterson
            // +   improved by: Simon Willison (http://simonwillison.net)
            // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
            // +   bugfixed by: Anton Ongson
            // +      input by: Onno Marsman
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +    tweaked by: Onno Marsman
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   input by: Oleg Eremeev
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Oleg Eremeev
            // %          note 1: The count parameter must be passed as a string in order
            // %          note 1:  to find a global variable in which the result will be given
            // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
            // *     returns 1: 'Kevin.van.Zonneveld'
            // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
            // *     returns 2: 'hemmo, mars'
            var i = 0,
                j = 0,
                temp = '',
                repl = '',
                sl = 0,
                fl = 0,
                f = [].concat(search),
                r = [].concat(replace),
                s = subject,
                ra = Object.prototype.toString.call(r) === '[object Array]',
                sa = Object.prototype.toString.call(s) === '[object Array]';
            s = [].concat(s);
            if (count) {
                this.window[count] = 0;
            }

            for (i = 0, sl = s.length; i < sl; i++) {
                if (s[i] === '') {
                    continue;
                }
                for (j = 0, fl = f.length; j < fl; j++) {
                    temp = s[i] + '';
                    repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                    s[i] = (temp).split(f[j]).join(repl);
                    if (count && s[i] !== temp) {
                        this.window[count] += (temp.length - s[i].length) / f[j].length;
                    }
                }
            }
            return sa ? s : s[0];
        };


        Bee.substr = function (str, start, len) {
            // Returns part of a string
            //
            // version: 909.322
            // discuss at: http://phpjs.org/functions/substr
            // +     original by: Martijn Wieringa
            // +     bugfixed by: T.Wild
            // +      tweaked by: Onno Marsman
            // +      revised by: Theriault
            // +      improved by: Brett Zamir (http://brett-zamir.me)
            // %    note 1: Handles rare Unicode characters if 'unicode.semantics' ini (PHP6) is set to 'on'
            // *       example 1: substr('abcdef', 0, -1);
            // *       returns 1: 'abcde'
            // *       example 2: substr(2, 0, -6);
            // *       returns 2: false
            // *       example 3: ini_set('unicode.semantics',  'on');
            // *       example 3: substr('a\uD801\uDC00', 0, -1);
            // *       returns 3: 'a'
            // *       example 4: ini_set('unicode.semantics',  'on');
            // *       example 4: substr('a\uD801\uDC00', 0, 2);
            // *       returns 4: 'a\uD801\uDC00'
            // *       example 5: ini_set('unicode.semantics',  'on');
            // *       example 5: substr('a\uD801\uDC00', -1, 1);
            // *       returns 5: '\uD801\uDC00'
            // *       example 6: ini_set('unicode.semantics',  'on');
            // *       example 6: substr('a\uD801\uDC00z\uD801\uDC00', -3, 2);
            // *       returns 6: '\uD801\uDC00z'
            // *       example 7: ini_set('unicode.semantics',  'on');
            // *       example 7: substr('a\uD801\uDC00z\uD801\uDC00', -3, -1)
            // *       returns 7: '\uD801\uDC00z'
            // Add: (?) Use unicode.runtime_encoding (e.g., with string wrapped in "binary" or "Binary" class) to
            // allow access of binary (see file_get_contents()) by: charCodeAt(x) & 0xFF (see https://developer.mozilla.org/En/Using_XMLHttpRequest ) or require conversion first?
            var i = 0,
                allBMP = true,
                es = 0,
                el = 0,
                se = 0,
                ret = '';
            str += '';
            var end = str.length;

            // BEGIN REDUNDANT
            this.php_js = this.php_js || {};
            this.php_js.ini = this.php_js.ini || {};
            // END REDUNDANT
            switch ((this.php_js.ini['unicode.semantics'] && this.php_js.ini['unicode.semantics'].local_value.toLowerCase())) {
                case 'on':
                    // Full-blown Unicode including non-Basic-Multilingual-Plane characters
                    // strlen()
                    for (i = 0; i < str.length; i++) {
                        if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
                            allBMP = false;
                            break;
                        }
                    }

                    if (!allBMP) {
                        if (start < 0) {
                            for (i = end - 1, es = (start += end); i >= es; i--) {
                                if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
                                    start--;
                                    es--;
                                }
                            }
                        } else {
                            var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
                            while ((surrogatePairs.exec(str)) != null) {
                                var li = surrogatePairs.lastIndex;
                                if (li - 2 < start) {
                                    start++;
                                } else {
                                    break;
                                }
                            }
                        }

                        if (start >= end || start < 0) {
                            return false;
                        }
                        if (len < 0) {
                            for (i = end - 1, el = (end += len); i >= el; i--) {
                                if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
                                    end--;
                                    el--;
                                }
                            }
                            if (start > end) {
                                return false;
                            }
                            return str.slice(start, end);
                        } else {
                            se = start + len;
                            for (i = start; i < se; i++) {
                                ret += str.charAt(i);
                                if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
                                    se++; // Go one further, since one of the "characters" is part of a surrogate pair
                                }
                            }
                            return ret;
                        }
                        break;
                    }
                // Fall-through
                case 'off':
                // assumes there are no non-BMP characters;
                //    if there may be such characters, then it is best to turn it on (critical in true XHTML/XML)
                default:
                    if (start < 0) {
                        start += end;
                    }
                    end = typeof len === 'undefined' ? end : (len < 0 ? len + end : len + start);
                    // PHP returns false if start does not fall within the string.
                    // PHP returns false if the calculated end comes before the calculated start.
                    // PHP returns an empty string if start and end are the same.
                    // Otherwise, PHP returns the portion of the string from start to end.
                    return start >= str.length || start < 0 || start > end ? !1 : str.slice(start, end);
            }
            return undefined; // Please Netbeans
        };


        Bee.toArray = function (mixed) {
            if (!Bee.isArray(mixed)) {
                if (Bee.isDefined(mixed)) {
                    mixed = [mixed];
                }
                else {
                    mixed = [];
                }
            }
            return mixed;
        };


        Bee.toBool = function (mValue) {
            if (Bee.isString(mValue)) {
                switch (mValue.toLowerCase()) {
                    case "true":
                    case "yes":
                    case "1":
                        return true;
                    case "false":
                    case "no":
                    case "0":
                    case "":
                    case null:
                        return false;
                    default:
                        return Boolean(mValue);
                }
            }
            return Boolean(mValue);
        };


        Bee.toHtmlAttributesString = function ($attributes) {
            var $s = '';
            for (var $k in $attributes) {
                var $v = $attributes[$k];
                $s += ' ';
                if (Bee.isNumerical($k)) {
                    $s += Bee.htmlSpecialChars($v);
                } else {
                    $s += Bee.htmlSpecialChars($k) + '="' + Bee.htmlSpecialChars($v) + '"';
                }
            }
            return $s;
        };


        Bee.toJSON = function (mVal) {
            return JSON.stringify(mVal);
        };

        Bee.toString = function (mVal) {
            if (mVal) {
                return mVal.toString();
            }
            return '';
        };


        Bee.toUl = function ($array, $parentCallback, $childCallback, $wrapOutCallback) {
            if (!$parentCallback) {
                $parentCallback = function ($key, $list) {
                    return Bee.sprintf('<li><span>%s</span><ul>%s</ul></li>', Bee.htmlSpecialChars($key), $list);
                };
            }
            if (!$childCallback) {
                $childCallback = function ($key, $value) {
                    return Bee.sprintf('<li><span>%s: %s</span></li>', Bee.htmlSpecialChars($key), Bee.htmlSpecialChars($value));
                };
            }
            if (!$wrapOutCallback) {
                $wrapOutCallback = function ($out) {
                    return Bee.sprintf('<ul>%s</ul>', $out);
                };
            }
            var $out = Bee.toUlDo($array, $parentCallback, $childCallback);
            $out = $wrapOutCallback($out);
            return $out;
        };

        Bee.toUlDo = function ($array, $parentCallback, $childCallback) {
            var $out = "";
            for (var $key in $array) {
                var $elem = $array[$key];
                if (!Bee.isArrayOrObject($elem)) {
                    $out += $childCallback($key, $elem);
                } else {
                    $out += $parentCallback($key, Bee.toUlDo($elem, $parentCallback, $childCallback));
                }
            }
            return $out;
        };


        Bee.trim = function (sString) {
            return sString.replace(/^\s+/g, '').replace(/\s+$/g, '')
        };

        Bee.ucfirst = function (str) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Onno Marsman
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // *     example 1: ucfirst('kevin van zonneveld');
            // *     returns 1: 'Kevin van zonneveld'
            str += '';
            var f = str.charAt(0).toUpperCase();
            return f + str.substr(1);
        };


        Bee.uniqueId = function (jItem) {
            var uid = jItem.attr("id");
            if (uid) {
                return uid;
            }
            var uid = Bee.getUniqueCssId();
            jItem.attr("id", uid);
            return uid;
        };


        Bee.unserialize = function (data) {
            // http://kevin.vanzonneveld.net
            // +     original by: Arpad Ray (mailto:arpad@php.net)
            // +     improved by: Pedro Tainha (http://www.pedrotainha.com)
            // +     bugfixed by: dptr1988
            // +      revised by: d3x
            // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +        input by: Brett Zamir (http://brett-zamir.me)
            // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +     improved by: Chris
            // +     improved by: James
            // +        input by: Martin (http://www.erlenwiese.de/)
            // +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +     improved by: Le Torbi
            // +     input by: kilops
            // +     bugfixed by: Brett Zamir (http://brett-zamir.me)
            // +      input by: Jaroslaw Czarniak
            // +     improved by: Eli Skeggs
            // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
            // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays
            // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
            // *       returns 1: ['Kevin', 'van', 'Zonneveld']
            // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
            // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
            var that = this,
                utf8Overhead = function (chr) {
                    // http://phpjs.org/functions/unserialize:571#comment_95906
                    var code = chr.charCodeAt(0);
                    if (code < 0x0080) {
                        return 0;
                    }
                    if (code < 0x0800) {
                        return 1;
                    }
                    return 2;
                },
                error = function (type, msg, filename, line) {
                    throw new Error(msg, filename, line);
                },
                read_until = function (data, offset, stopchr) {
                    var i = 2, buf = [], chr = data.slice(offset, offset + 1);

                    while (chr != stopchr) {
                        if ((i + offset) > data.length) {
                            error('Error', 'Invalid');
                        }
                        buf.push(chr);
                        chr = data.slice(offset + (i - 1), offset + i);
                        i += 1;
                    }
                    return [buf.length, buf.join('')];
                },
                read_chrs = function (data, offset, length) {
                    var i, chr, buf;

                    buf = [];
                    for (i = 0; i < length; i++) {
                        chr = data.slice(offset + (i - 1), offset + i);
                        buf.push(chr);
                        length -= utf8Overhead(chr);
                    }
                    return [buf.length, buf.join('')];
                },
                _unserialize = function (data, offset) {
                    var dtype, dataoffset, keyandchrs, keys, contig,
                        length, array, readdata, readData, ccount,
                        stringlength, i, key, kprops, kchrs, vprops,
                        vchrs, value, chrs = 0,
                        typeconvert = function (x) {
                            return x;
                        };

                    if (!offset) {
                        offset = 0;
                    }
                    dtype = (data.slice(offset, offset + 1)).toLowerCase();

                    dataoffset = offset + 2;

                    switch (dtype) {
                        case 'i':
                            typeconvert = function (x) {
                                return parseInt(x, 10);
                            };
                            readData = read_until(data, dataoffset, ';');
                            chrs = readData[0];
                            readdata = readData[1];
                            dataoffset += chrs + 1;
                            break;
                        case 'b':
                            typeconvert = function (x) {
                                return parseInt(x, 10) !== 0;
                            };
                            readData = read_until(data, dataoffset, ';');
                            chrs = readData[0];
                            readdata = readData[1];
                            dataoffset += chrs + 1;
                            break;
                        case 'd':
                            typeconvert = function (x) {
                                return parseFloat(x);
                            };
                            readData = read_until(data, dataoffset, ';');
                            chrs = readData[0];
                            readdata = readData[1];
                            dataoffset += chrs + 1;
                            break;
                        case 'n':
                            readdata = null;
                            break;
                        case 's':
                            ccount = read_until(data, dataoffset, ':');
                            chrs = ccount[0];
                            stringlength = ccount[1];
                            dataoffset += chrs + 2;

                            readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
                            chrs = readData[0];
                            readdata = readData[1];
                            dataoffset += chrs + 2;
                            if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
                                error('SyntaxError', 'String length mismatch');
                            }
                            break;
                        case 'a':
                            readdata = {};

                            keyandchrs = read_until(data, dataoffset, ':');
                            chrs = keyandchrs[0];
                            keys = keyandchrs[1];
                            dataoffset += chrs + 2;

                            length = parseInt(keys, 10);
                            contig = true;

                            for (i = 0; i < length; i++) {
                                kprops = _unserialize(data, dataoffset);
                                kchrs = kprops[1];
                                key = kprops[2];
                                dataoffset += kchrs;

                                vprops = _unserialize(data, dataoffset);
                                vchrs = vprops[1];
                                value = vprops[2];
                                dataoffset += vchrs;

                                if (key !== i)
                                    contig = false;

                                readdata[key] = value;
                            }

                            if (contig) {
                                array = new Array(length);
                                for (i = 0; i < length; i++)
                                    array[i] = readdata[i];
                                readdata = array;
                            }

                            dataoffset += 1;
                            break;
                        default:
                            error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
                            break;
                    }
                    return [dtype, dataoffset - offset, typeconvert(readdata)];
                }
                ;

            return _unserialize((data + ''), 0)[2];
        };


        Bee.unsetKey = function (sKey, aArray) {
            aArray.splice(sKey, 1);
        };

        Bee.urlencode = function (str) {
            // http://kevin.vanzonneveld.net
            // +   original by: Philip Peterson
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: AJ
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: travc
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Lars Fischer
            // +      input by: Ratheous
            // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Joris
            // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
            // %          note 1: This reflects PHP 5.3/6.0+ behavior
            // %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
            // %        note 2: pages served as UTF-8
            // *     example 1: urlencode('Kevin van Zonneveld!');
            // *     returns 1: 'Kevin+van+Zonneveld%21'
            // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
            // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
            // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
            // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
            str = (str + '').toString();

            // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
            // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
            return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
                replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
        };


        Bee.vsprintf = function (format, args) {
            // http://kevin.vanzonneveld.net
            // +   original by: ejsanders
            // -    depends on: sprintf
            // *     example 1: vsprintf('%04d-%02d-%02d', [1988, 8, 1]);
            // *     returns 1: '1988-08-01'
            return Bee.sprintf.apply(this, [format].concat(args));
        }


        Bee.wallFilter = function (aoArray, aWallFilter) {
            var ret = {};
            for (var i in aoArray) {
                if (!Bee.inArray(i, aWallFilter)) {
                    ret[i] = aoArray[i];
                }
            }
            return ret;
        };


        Bee.xTrigger = function (jTarget, fnCallback, ccXTrigger, ccXMain, fnError) {
            if (!Bee.isDefined(ccXTrigger) || null === ccXTrigger) {
                ccXTrigger = 'x-trig';
            }
            if (!Bee.isDefined(ccXMain) || null === ccXMain) {
                ccXMain = 'x-main';
            }
            if (jTarget.hasClass(ccXTrigger) || jTarget.hasClass(ccXMain)) {
                var jTarget = jTarget.closest("." + ccXMain);
                if (jTarget.length) {
                    return fnCallback(jTarget);
                }
                else {
                    var msg = "Invalid html markup: orphan x-trigger";
                    if (!Bee.isFunction(fnError)) {
                        fnError = function (msg) {
                            Bee.error(msg);
                        }
                    }
                    fnError(msg);
                }
            }
        };

        //------------------------------------------------------------------------------/
        // DEFAULT INTERFACE IMPLEMENTATIONS
        //------------------------------------------------------------------------------/
        var Notifier = function () {
            this.displaySuccess = function (message) {
                fnDisplayMessage(message, 'Success');
            };
            this.displayError = function (message) {
                fnDisplayMessage(message, 'Error');
            };
            this.displayInfo = function (message) {
                fnDisplayMessage(message, 'Info');
            };
            this.displayWarning = function (message) {
                fnDisplayMessage(message, 'Warning');
            };
            var fnDisplayMessage = function (m, t) {
                alert('BeeDefaultNotifier: ' + t + ': ' + m);
            }
        };


        //------------------------------------------------------------------------------/
        // MINI MUSTACHE
        //------------------------------------------------------------------------------/
        /**
         * port of class: Bee\Bat\TemplateSystem\Beard\Beard
         */
        var Beard = function () {

            var argsParser = Bee.getArgumentsMicroParser();
            argsParser.setProtectionChar("'");
            var logger = null;// todo: use a standard logger?
            var context = "static";
            var contextCpt = 0;


            this.setContext = function ($context) {
                context = $context;
            };

            this.renderTemplate = function (sTemplate, aoTags) {
                var aoVarname2Template = {};


                var aoSource2Template = {};

                // unprotect the context tags which match with the current context
                var regex = new RegExp("\\{\\{(\\*" + context + ")\\}\\}([\\s\\S]*?)\\{\\{/\\1\\}\\}", "gm");
                sTemplate = sTemplate.replace(regex, function ($match, $one, $two) {
                    return $two;
                });

                var $aContextTags = {};
                var $otherContext = ('static' === context) ? 'dynamic' : 'static';
                // protect the context tags which do not match with the current context
                var regex = new RegExp("\\{\\{(\\*" + $otherContext + ")\\}\\}([\\s\\S]*?)\\{\\{/\\1\\}\\}", "gm");
                sTemplate = sTemplate.replace(regex, function ($match) {
                    $aContextTags[contextCpt] = $match;
                    var $ret = '-_|*' + contextCpt + '*|_-';
                    contextCpt++;
                    return $ret;
                });


                // extract the source tags once
                sTemplate = sTemplate.replace(/\{\{:([a-zA-Z0-9-\._]*)\}\}([\s\S]*?)\{\{\/:\1\}\}/gm, function ($raw, $varname, $inner) {
                    // source tag
                    aoSource2Template[$varname] = $inner;
                    return '';
                });
                sTemplate = fnDoRenderTemplate(sTemplate, aoTags, aoSource2Template, aoVarname2Template);

                // recreate protected context tags
                sTemplate = sTemplate.replace(/-_\|\*([0-9]+)\*\|_-/gm, function ($match, $one) {

                    if (Bee.arrayKeyExists($one, $aContextTags)) {
                        return $aContextTags[$one];
                    }
                    fnLog("Cannot found context tag with key: %d".replace('%d', $one));
                    return $match;
                });


                return sTemplate;
            };

            var fnParseTemplate = function (sTemplate, aoTags) {
                sTemplate = sTemplate.replace(/\{\{([a-zA-Z0-9\|"'&\\\._=/\s-]*)\}\}/gm, function ($raw, $match) {

                    var $parts = $match.split("=", 2);
                    var $defaultValue = null;
                    var $escape = false;
                    if (2 === $parts.length) {
                        $defaultValue = $parts[1].toString();
                    }


                    if ('&' === $parts[0].substr(0, 1)) {
                        $parts[0] = $parts[0].substr(1);
                        $escape = true;
                    }

                    var $values = $parts[0].split('|');
                    for (var i in $values) {
                        var $value = $values[i];
                        if (Bee.hasDotValue($value, aoTags)) {
                            if (true === $escape) {
                                return Bee.htmlSpecialChars(Bee.getDotValue($value, aoTags));
                            }
                            return Bee.getDotValue($value, aoTags);
                        }
                    }
                    if (null !== $defaultValue) {
                        return $defaultValue;
                    }
                    return $raw;
                });
                return sTemplate;
            };


            var fnGetArgs = function (string) {
                return argsParser.parse(string);
            };

            var fnDoRenderTemplate = function (sTemplate, aoTags, aoSource2Template, aoVarname2Template) {



                // then extract the tag list
                sTemplate = sTemplate.replace(/\{\{((\#+)[a-zA-Z0-9-\\\._]*)\}\}([\s\S]*?)\{\{\/\1\}\}/gm, function ($raw, $varname, $sharps, $inner) {
                    $varname = Bee.ltrim($varname, '#');

                    var $sharpsLength = $sharps.length;
                    // tag list
                    aoVarname2Template[$varname] = [$inner, $raw, $sharpsLength];
                    return '{{' + Bee.strRepeat("+", $sharpsLength) + $varname + '}}';
                });

                // then safely resolve the tags
                sTemplate = fnParseTemplate(sTemplate, aoTags);

                // then resolve conditional tag container
                sTemplate = sTemplate.replace(/\{\{([a-zA-Z0-9-\._]*)\?([a-zA-Z0-9:\._-]*)?\}\}([\s\S]*?)\{\{\/\1\}\}/gm, function ($raw, $varName, $value, $inner) {


                    var $negativeInner = null;
                    var $parts = $inner.split('{{' + $varName + ':}}');
                    if ($parts.length > 1) {
                        $inner = $parts[0];
                        $negativeInner = $parts[1];
                    }

                    // existence
                    if (!Bee.isDefined($value) || 0 === $value.length) {
                        if (true === Bee.hasDotValue($varName, aoTags)) {
                            return fnDoRenderTemplate($inner, aoTags, aoSource2Template, aoVarname2Template);
                        }
                        if ($negativeInner) {
                            return fnDoRenderTemplate($negativeInner, aoTags, aoSource2Template, aoVarname2Template);
                        }
                        return '';
                    } // existence and value
                    else {
                        if (true === Bee.hasDotValue($varName, aoTags)) {

                            switch ($value) {
                                case 'null':
                                    $value = null;
                                    break;
                                case 'true':
                                    $value = true;
                                    break;
                                case 'false':
                                    $value = false;
                                    break;
                                case ':null':
                                    $value = 'null';
                                    break;
                                case ':true':
                                    $value = 'true';
                                    break;
                                case ':false':
                                    $value = 'false';
                                    break;
                                case ':empty':
                                    $value = 'empty';
                                    break;
                                case ':notempty':
                                    $value = 'notempty';
                                    break;
                                default:
                                    break;
                            }
                            var $dotValue = Bee.getDotValue($varName, aoTags);
                            if (Bee.isNumerical($value)) {
                                $value = $value.toString();
                            }
                            if (Bee.isNumerical($dotValue)) {
                                $dotValue = $dotValue.toString();
                            }

                            var $match = (
                                ('empty' === $value && ('0' === $dotValue || '' === $dotValue)) ||
                                    ('notempty' === $value && ('0' !== $dotValue && '' !== $dotValue)) ||
                                    ($value === $dotValue)
                                );

                            if ($match) {
                                return fnDoRenderTemplate($inner, aoTags, aoSource2Template, aoVarname2Template);
                            }
                            if ($negativeInner) {
                                return fnDoRenderTemplate($negativeInner, aoTags, aoSource2Template, aoVarname2Template);
                            }
                        }
                        return '';
                    }
                });


                // then resolve the reference tags
                sTemplate = sTemplate.replace(/\{\{@([a-zA-Z0-9-\.,\s_\[\]\|\$;=]*)\}\}/gm, function ($raw, $varname) {

                    var parts = Bee.explode('=', $varname, 2);
                    $varname = parts[0];
                    var $default = null;
                    var $adaptorFallback = null;
                    var adaptorArgs = {};

                    if (2 === parts.length) {
                        $default = Bee.trim(parts[1]);
                        if ('[' === $default.substr(0, 1)) {
                            var $subs = Bee.explode('|', $default, 2);
                            var $adaptor = Bee.trim($subs[0]);
                            if (2 === $subs.length) {
                                var $ssubs = Bee.explode('|', $subs[1], 2);
                                $adaptorFallback = Bee.trim($ssubs[0]);
                                if (0 === $adaptorFallback.length) {
                                    $adaptorFallback = null;
                                }
                                if (2 === $ssubs.length) {
                                    $default = Bee.trim($ssubs[1]);
                                }
                            }
                            $adaptor = fnGetArgs($adaptor);
                            adaptorArgs = $adaptor[0];
                        }
                    }

                    var $variableExists = true;
                    var resolvedVarname = $varname.replace(/\$([a-zA-Z0-9-\._]*);/g, function ($raww, $var) {
                        if (Bee.hasDotValue($var, aoTags)) {
                            return Bee.getDotValue($var, aoTags);
                        }
                        $variableExists = false;
                        return $raww;
                    });


                    if (true === $variableExists) {
                        if (Bee.arrayKeyExists(resolvedVarname, adaptorArgs)) {
                            resolvedVarname = adaptorArgs[resolvedVarname];
                            if (Bee.arrayKeyExists(resolvedVarname, aoSource2Template)) {
                                return fnDoRenderTemplate(aoSource2Template[resolvedVarname], aoTags, aoSource2Template, aoVarname2Template);
                            }
                        }
                        if (null !== $adaptorFallback) {
                            if (Bee.arrayKeyExists($adaptorFallback, aoSource2Template)) {
                                return fnDoRenderTemplate(aoSource2Template[$adaptorFallback], aoTags, aoSource2Template, aoVarname2Template);
                            }
                            return $raw;
                        }
                        if (Bee.arrayKeyExists(resolvedVarname, aoSource2Template)) {
                            return fnDoRenderTemplate(aoSource2Template[resolvedVarname], aoTags, aoSource2Template, aoVarname2Template);
                        }
                    }


                    if (null !== $default && Bee.arrayKeyExists($default, aoSource2Template)) {
                        return fnDoRenderTemplate(aoSource2Template[$default], aoTags, aoSource2Template, aoVarname2Template);
                    }
                    return $raw;
                });

                // then resolve recursively the tag list
                for (var $name in aoVarname2Template) {
                    var $info = aoVarname2Template[$name];
                    var $tpl = $info[0];
                    var $raw = $info[1];
                    var $sharpsLength = $info[2];

                    var $s = '';
                    var $aSubTags = Bee.getDotValue($name, aoTags);
                    if (null !== $aSubTags) {
                        if (Bee.isArrayOrObject($aSubTags)) {
                            for (var i in $aSubTags) {
                                var $row = $aSubTags[i];
                                if (Bee.isArrayOrObject($row)) {
                                    $s += fnDoRenderTemplate($tpl, $row, aoSource2Template, aoVarname2Template);
                                }
                            }
                        }
                        else {
                            $s = $raw;
                        }
                    } else {
                        $s = $raw;
                    }
                    sTemplate = sTemplate.replace('{{' + Bee.strRepeat('+', $sharpsLength) + $name + '}}', $s);
                }

                // finally resolve the <method tags>
                sTemplate = sTemplate.replace(/data-beard-method\s*=\s*"\s*([a-zA-Z0-9_]*)\s*\(([^"]*)\)"/gm, function ($raw, $method, $args) {
                    $args = fnGetArgs($args);
                    switch ($method) {
                        case 'toAttributes':
                            var $var = $args.shift();
                            if (null !== $var) {
                                var $value = {};
                                if (true === Bee.hasDotValue($var, aoTags)) {
                                    $value = Bee.getDotValue($var, aoTags);
                                } else {
                                    fnLog(Bee.sprintf("method toAttributes: variable %s not found in the given array", $var));
                                }

                                if (false === Bee.isArrayOrObject($value)) {
                                    $value = {};
                                    fnLog(Bee.sprintf("method toAttributes: variable %s must be an array", $var));
                                }
                                var $baseArray = $args.shift();
                                if ($baseArray) {
                                    if (true === Bee.isArrayOrObject($baseArray)) {
                                        for (var $k in $baseArray) {
                                            var $v = $baseArray[$k];
                                            if (Bee.arrayKeyExists($k, $value)) {
                                                if (Bee.inArray($k, ['style', 'class'])) {
                                                    var sep = ';';
                                                    if ('class' === $k) {
                                                        sep = ' ';
                                                        if ($v === $value[$k]) {
                                                            continue;
                                                        }
                                                    }
                                                    if (sep !== $v.substr(-1)) {
                                                        $v += sep;
                                                    }
                                                    $value[$k] = $v + $value[$k];
                                                }
                                            } else {
                                                $value[$k] = $v;
                                            }
                                        }
                                    }
                                }
                                var $wallFilter = $args.shift();
                                if ($wallFilter) {
                                    $value = Bee.wallFilter($value, $wallFilter);
                                }
                                return Bee.toHtmlAttributesString($value);


                            } else {
                                fnLog("method toAttributes expect at least one argument: 0 given");
                            }
                            break;
                        case 'ifExists':
                            var $var = $args.shift();
                            if (null !== $var) {
                                var ret = '';
                                var $value = $args.shift();
                                if (true === Bee.hasDotValue($var, aoTags)) {
                                    return $value;
                                }
                                return ret;
                            } else {
                                fnLog("method toAttributes expect at least one argument: 0 given");
                            }
                            break;
                        default:
                            fnLog(Bee.sprintf("unknown method: %s", $method));
                            break;
                    }


                    return $raw;
                });

                return sTemplate;
            };


        };

        var fnLog = function (message) {
            console.log(message);
        };

        //------------------------------------------------------------------------------/
        // ARGUMENTS MICRO PARSER (FOR MINI MUSTACHE)
        //------------------------------------------------------------------------------/
        /**
         * Port of Bee\Component\Parser\ArgumentsMicroParser\ArgumentsMicroParser.php
         */

        var ArgumentsMicroParser = function () {


            var protectionChar = '"';
            var escapingChar = '\\';
            var argSepChar = ',';
            var kvSepChar = '=';
            var arrayBeginChar = '[';
            var arrayEndChar = ']';
            var pos = 0;


            this.setProtectionChar = function (char) {
                protectionChar = char;
            };

            this.parse = function (string) {
                pos = 0;
                var ret = [];
                var string = Bee.trim(string);
                var length = string.length;

                while (pos < length) {
                    var char = string[pos];
                    if (' ' === char || "\t" === char || argSepChar === char) {
                        pos++;
                        continue;
                    }
                    if (arrayBeginChar === char) {
                        ret.push(fnParseArray(string));
                    } else {
                        ret.push(fnParseString(string));
                    }
                }
                return ret;
            };


            var fnParseArrayItem = function (string, oScope) {
                var length = string.length;
                var ret = '';
                var protectedValue = null;
                var hasKey = false;
                var isProtected = false;

                if (arrayBeginChar === string[pos]) {
                    oScope.key = null;
                    return fnParseArray(string);
                }

                if (protectionChar === string[pos]) {
                    isProtected = true;
                    pos++;
                }
                while (pos < length) {
                    var char = string[pos];

                    if (false === isProtected && (' ' === char || "\t" === char)) {
                        pos++;
                        continue;
                    }

                    if (true === hasKey) {
                        if (arrayBeginChar === string[pos]) {
                            return fnParseArray(string);
                        }
                        return fnParseString(string, [argSepChar, arrayEndChar]);
                    }

                    if (false === isProtected && kvSepChar === char) {
                        hasKey = true;
                        if (null === protectedValue) {
                            oScope.key = Bee.trim(ret);
                        } else {
                            ret = ret.substr(0, (ret.length - 1));
                            ret = ret.replace('\\"', '"');
                            oScope.key = ret;
                        }
                        ret = '';
                        protectedValue = null;
                        pos++; // skip <key value sep char>
                        continue;
                    }

                    if (true === isProtected && protectionChar === char && escapingChar !== string[pos - 1]) {
                        isProtected = false;
                        protectedValue = ret.replace('\\"', '"');
                    }

                    // end
                    if (false === isProtected && (argSepChar === char || arrayEndChar === char)) {
                        if (false === hasKey) {
                            oScope.key = null;
                        }
                        if (null === protectedValue) {
                            return fnGetEvaluatedValue(ret);
                        }
                        return protectedValue;
                    }
                    ret += char;
                    pos++;
                }
                return fnGetEvaluatedValue(ret);
            }


            var fnParseArray = function (string) {
                var length = string.length;
                pos++; // skip <array begin char>
                var ret = {};
                while (pos < length) {
                    var char = string[pos];
                    if (' ' === char || "\t" === char || argSepChar === char) {
                        pos++;
                        continue;
                    }
                    if (arrayEndChar === char) {
                        pos++;
                        return ret;
                    }
                    var key = null;
                    var oScope = {key: key};
                    var value = fnParseArrayItem(string, oScope);
                    char = string[pos];
                    if (null === oScope.key) {
                        var max = 0;
                        if (Bee.arrayKeyExists(max, ret, false)) {
                            for (var i in ret) {
                                if (i > max) {
                                    max = i;
                                }
                            }
                            max++;
                        }
                        ret[max] = value;
                    } else {
                        ret[oScope.key] = value;
                    }

                    if (argSepChar === char || arrayEndChar === char) {
                        if (arrayEndChar === char) {
                            pos++;
                            return ret;
                        }
                    }
                    pos++;
                }
                return ret;
            };


            var fnParseString = function (string, stopChars) {
                if (!Bee.isDefined(stopChars)) {
                    stopChars = [argSepChar];
                }
                var length = string.length;
                var isProtected = false;
                if (protectionChar === string[pos]) {
                    isProtected = true;
                    pos++; // skip opening <protecting char>
                }
                var ret = '';
                while (pos < length) {
                    var char = string[pos];
                    if (true === isProtected && protectionChar === char && escapingChar !== string[pos - 1]) {
                        pos++; // skip closing <protecting char>
                        return ret.replace('\\"', '"');
                    }
                    if (false === isProtected && (Bee.inArray(char, stopChars))) {
                        return fnGetEvaluatedValue(ret);
                    }
                    ret += char;
                    pos++;
                }
                return fnGetEvaluatedValue(ret);
            };

            var fnGetEvaluatedValue = function ($string) {
                var $val = Bee.trim($string);
                if ('true' === $val) {
                    return true;
                } else if ('false' === $val) {
                    return false;
                } else if ('null' === $val) {
                    return null;
                }
                if (Bee.isNumerical($val)) {
                    if (-1 === $val.indexOf('.')) {
                        $val = parseInt($val);
                    } else {
                        $val = parseFloat($val);
                    }
                }
                return $val;
            };


        }


    }
})
    ();