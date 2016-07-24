/**
 *
 * LingTalfi - 2015-01-20 --> 2015-01-28
 *
 * Depends on:
 * - jquery
 * - ajaxTim
 * - uii.dialogg
 * - assetLoader
 * - bdot
 * - bjs
 * - pea
 *
 * Translations must be included BEFORE calling this file.
 *
 *
 * What's new?
 *
 * Adding another validation mode, where error messages are displayed through a js popup rather than inline.
 * The process is encapsulated in a method, so that other systems could be implemented.
 * Also, the default errorControl system has been reimplemented.
 * Added an onStartAfter and addOnStartAfter methods to ease stripping of an ajax form.
 * Added a stripForm utility method.
 *
 * - 0.08: callForm now uses [sick™] pattern to ease server implementations.
 * added setValue, setValidationRule, setValidationTest and setDynamicElement methods to ease BeefServer implementation.
 * - 0.07: Revisiting the whole api (value injection mostly)
 * - 0.06: Handling of html brackets syntax (for instance sports[fight], sports[][fight], ...)
 * - 0.05: Fix returned static values when collected.
 * - 0.04: This object now handles the array notation for static classic html controls: for instance for checkboxes, the html name can be sports[]
 * but we can use the name sports to reference it.
 *
 */
if ('undefined' == typeof window.beef) {
    (function ($) {

        var cpt = 0;
        var errorControlClass = 'beef-error-control';

        window.beef = {
            util: {
                getFormInstance: function (jForm) {
                    return jForm.data('beefFormInstance');
                },
                collectStaticValues: function (jForm, values) {

                    // find static elements first
                    var jStaticEls = jForm
                        .find('input, textarea, select')
                        .not('[type=file]')
                        .not('[type=submit]')
                        .not('[data-beef-ignore=1] *');

                    var statics = jStaticEls.serializeArray();
                    for (var i in statics) {
                        insertValueByHtmlPath(statics[i].name, statics[i].value, values);
                    }
                    // let's make unchecked values defaults to null
                    jStaticEls.each(function () {
                        var name = $(this).attr('name');
                        var controlName = getControlNameByHtmlName(name);
                        if (false === (controlName in values)) {
                            insertValueByHtmlPath(name, null, values);
                        }
                    });
                },
                dynamize: function (obj) {
                    var prototype = Object.getPrototypeOf(obj);

                    if ('undefined' === typeof prototype.value) {
                        prototype.value = null;
                    }

                    if ('undefined' === typeof prototype.setValue) {
                        prototype.setValue = function (value) {
                            this.value = value;
                        };
                    }

                    if ('undefined' === typeof prototype.getValue) {
                        prototype.getValue = function () {
                            return this.value;
                        };
                    }

                    if ('undefined' === typeof prototype.build) {
                        prototype.build = function () {

                        };
                    }
                },
                /**
                 * Calling ajax form using [sick™] pattern (oForm).
                 * This means the form is generated server side, and the initializing js code too, using sick(oForm).
                 */
                callForm: function (data, url, callback, options) {
                    options = $.extend({
                        title: null,
                        modal: true,
                        /**
                         * Intention of this is to handle an ajaxloader
                         */
                        createAfter: function (jForm) {
                        },
                        beefOptions: {}
                    }, options);

                    ajaxTim.sendMessage(url, data, function (m) {
                        var jContent = $(m.html);
                        var sJs = m.js;
                        var dependencies = m.dependencies;
                        var varName = 'beefNestedForm' + cpt++;
                        var beefOptions = $.extend(options.beefOptions, {
                            jForm: jContent
                        });
                        window[varName] = new window.beef.form(beefOptions);

                        jContent.dialogg({
                            title: options.title,
                            modal: options.modal,
                            class: 'beefform',
                            buttons: [
                                {
                                    'text': "Send",
                                    'click': function (e) {

                                        var values = window[varName].post();
                                        if (false !== values) {
                                            var ret = callback(values, window[varName], jContent);
                                            if (false !== ret) {
                                                jContent.dialogg("destroy");
                                            }
                                        }
                                    }
                                }
                            ],
                            create: function (jDialog, $options) {

                                window.assetLoader.loadDependencies(dependencies, function () {
                                    var code = '<script>';
                                    code += '(function(oForm){';
                                    code += "\n";
                                    code += sJs;
                                    code += "\n\n";
                                    code += 'oForm.start();';
                                    code += "\n";
                                    code += '})(window["' + varName + '"])';
                                    code += '</sc' + 'ript>';
                                    jQuery('head').append(code);

                                    if (pea.isFunction(options.createAfter)) {
                                        options.createAfter(jContent);
                                    }

                                });
                            },
                            buildButton: function (text) {
                                return '<button class="submit button">' + text + '</button>';
                            }
                        });
                    });
                },
                stripForm: function (jForm, $options) {
                    $options = $.extend({
                        oddClass: 'odd',
                        evenClass: 'even'
                    }, $options);

                    var cpt = 1;
                    jForm.find('tr').each(function () {
                        if (false === $(this).hasClass('beef-error-control')) {
                            if (0 === cpt % 2) {
                                $(this).addClass($options.evenClass);
                            }
                            else {
                                $(this).addClass($options.oddClass);
                            }
                            cpt++;
                        }
                    });
                }


            },
            form: function (options) {

                options = $.extend({
                    dynamicElements: {},
                    jForm: null,
                    values: {},
                    /**
                     * - $controlName: $rules
                     *
                     * With:
                     * - $rules:
                     * ----- $ruleName: $params
                     *
                     *
                     */
                    rules: {},
                    /**
                     * null|array,
                     * If null, it will use a default validationTest library (see code below) if found.
                     * If not null, it's an array that we pass manually
                     */
                    validationTests: null,
                    /**
                     * Callback responsible to display validation error messages.
                     * If null, the primary beef system will be used: which is displaying messages inline.
                     *
                     *          void callback ( array controlsErrors, oBeef )
                     *                      controlsErrors: array of controlName => array of errorMessages
                     *
                     *
                     */
                    validationHandler: null,
                    /**
                     * @param getErrorControl: null|callback
                     *
                     *      jErrorControl   callback ( jReferentControl )
                     *              Note: jReferentControl can be an empty set.
                     */
                    getErrorControl: null,
                    getErrorMessageHolder: function (jErrorControl) {
                        return jErrorControl;
                    },
                    submitClass: 'submit'
                }, options);


                var zis = this;
                var validationTests = options.validationTests;
                if (null === validationTests) {
                    if ('undefined' !== typeof window.beefValidationTests) {
                        validationTests = window.beefValidationTests;
                    }
                    else {
                        validationTests = {};
                    }
                }
                var dynamicElements = options.dynamicElements;
                if (null === options.validationHandler) {
                    options.validationHandler = function (controlErrors, oBeef) {
                        for (var controlName in controlErrors) {
                            oBeef.addErrorMessage(controlName, controlErrors[controlName]);
                        }
                    };
                }
                var jForm = options.jForm;
                var values = options.values;
                var rules = options.rules;
                var onStartAfterCallbacks = [];

                // cache for all elements 
                var allElements = {};


                var submitClass = options.submitClass;


                /**
                 * onStartAfter ( jForm )
                 */
                this.addOnStartAfter = function (callback) {
                    onStartAfterCallbacks.push(callback);
                };

                this.setDynamicElements = function ($dynamicElements) {
                    for (var controlName in $dynamicElements) {
                        zis.setDynamicElement(controlName, $dynamicElements[controlName]);
                    }
                };
                this.setDynamicElement = function (controlName, dynamicElement) {
                    dynamicElements[controlName] = dynamicElement;

                    // autocompleting missing methods in dynamic elements
                    window.beef.util.dynamize(dynamicElement);
                    if ('undefined' === typeof dynamicElement.getControl) {
                        devError("Invalid dynamic element: " + controlName + " must contain the getControl method");
                    }
                };
                this.setDynamicElements(dynamicElements);


                this.getFormElement = function () {
                    return jForm;
                };

                this.setValidationTests = function ($validationTests) {
                    validationTests = $validationTests;
                };

                this.setValidationTest = function (testName, validationTest) {
                    validationTests[testName] = validationTest;
                };
                /**
                 * array of:
                 *      $controlName => [
                 *          $ruleName => $params
                 *      ]
                 */
                this.setValidationRules = function ($rules) {
                    rules = $rules;
                };

                this.setValidationRule = function (controlName, $rules) {
                    rules[controlName] = $rules;
                };

                this.setValues = function ($values) {
                    values = $values;
                };

                this.setValue = function (controlName, value) {
                    values[controlName] = value;
                };


                this.start = function (submit) {

                    jForm.data('beefFormInstance', this);

                    for (var i in dynamicElements) {
                        (function (name) {
                            // assigning the getControlName method to every dynamic element,
                            // so that they can access their name (useful for callForm2Control)
                            dynamicElements[name].getControlName = function () {
                                return name;
                            };
                            dynamicElements[name].build();
                        })(i);
                    }
                    this.injectValues(values);


                    if (false !== submit) {
                        var zis = this;
                        var jSubmit = jForm.find("." + submitClass + ":first");
                        jSubmit
                            .off('click.beefSubmit')
                            .on('click.beefSubmit', function () {
                                var zevalues = zis.post();
                                if (false !== zevalues) {
                                    if ('function' === typeof submit) {
                                        submit(zevalues);
                                    }
                                    else {
                                        sendViaHttp(zevalues, jForm);
                                    }
                                }
                                return false;
                            });
                    }


                    if (onStartAfterCallbacks.length) {
                        for (var i in onStartAfterCallbacks) {
                            onStartAfterCallbacks[i](jForm);
                        }
                    }

                };


                this.injectValues = function ($values) {
                    values = $values;
                    for (name in values) {
                        var v = values[name];
                        if (name in dynamicElements) {
                            dynamicElements[name].setValue(v);
                        }
                        else {
                            injectValueInStaticControl(name, v, jForm);
                        }

                    }
                };


                this.addErrorMessage = function (name, message) {
                    if (name in allElements) {

                        var jReferentControl;
                        if (name in dynamicElements) {
                            // accessing the dynamic referent control
                            jReferentControl = dynamicElements[name].getControl();
                        }
                        else {
                            jReferentControl = getStaticReferentControl(name, jForm);
                        }


                        var jErrorControl = null;

                        if (pea.isFunction(options.getErrorControl)) {
                            jErrorControl = options.getErrorControl(jReferentControl);
                        }
                        else {
                            /**
                             * Using native code to find jErrorControl
                             */
                            // first let's see if the relative mode has been set
                            if (jReferentControl.attr("data-beef-error")) {
                                var zeErrorClass = jReferentControl.attr("data-beef-error");
                                var jEl = jReferentControl.parent().find("." + zeErrorClass + ':first');
                                if (jEl.length) {
                                    jErrorControl = jEl;
                                }
                            }
                            // sibling mode
                            if (null === jErrorControl) {
                                // first search an existing sibling
                                var jSibling = jReferentControl.next('.' + errorControlClass);
                                if (jSibling.length) {
                                    jErrorControl = jSibling;
                                }
                                // or create it
                                else {
                                    jErrorControl = $('<span class="' + errorControlClass + '"></span>');
                                    jReferentControl.after(jErrorControl);
                                }
                            }
                        }


                        if (null !== jErrorControl && jErrorControl.length) {

                            var jErrorMessageHolder = options.getErrorMessageHolder(jErrorControl);
                            jErrorMessageHolder.html(message);
                            jErrorControl.show();

                            /**
                             * dismiss the error
                             */
                            jReferentControl
                                .off('click.dismissError')
                                .on('click.dismissError', function () {
                                    jErrorControl.hide();
                                });
                        }
                        else {
                            // not sure if it's a dev error or not?
                            devError("Cannot find the errorControl element for element with name " + name);
                        }
                    }
                    else {
                        devError("Element " + name + ' not found, use the collectElementsAndValues method first');
                    }
                };


                // return the posted values, or false if an error occurred
                this.post = function () {


                    var controlErrors = {};


                    // hiding all errorControls
                    jForm.find('.' + errorControlClass).hide();


                    collectElementsAndValues();
                    var ret = values;

                    // apply validation rules

                    // I decided to catch the (validation tests) exception, because
                    // the code will not have the opportunity to prevent 
                    // the browser's default behaviour, so the error message
                    // will only appear a few milli seconds and the form would be
                    // posted and the page reloaded, which is not handful.
                    try {
                        for (var controlName in rules) {
                            var zerules = rules[controlName];
                            for (var ruleName in zerules) {
                                var params = zerules[ruleName];
                                if (ruleName in validationTests) {

                                    // initialize value with null for checkboxes?
                                    var value = null;
                                    if (controlName in values) {
                                        value = values[controlName];
                                    }
                                    var f = validationTests[ruleName];
                                    var msg = f(value, params);


                                    if (true !== msg) {
                                        addControlErrors(controlErrors, controlName, msg);
                                    }
                                }
                                else {
                                    devError("Rule " + ruleName + " was not defined");
                                }
                            }
                        }
                    }
                    catch (e) {
                        devError(e.message);
                        ret = false;
                    }


                    // now let's display the validation error messages
                    if (pea.count(controlErrors) > 0) {
                        options.validationHandler(controlErrors, zis);
                        ret = false;
                    }
                    return ret;
                };

                function collectElementsAndValues() {


                    allElements = {};
                    values = {};

                    // find static elements first
                    window.beef.util.collectStaticValues(jForm, values);
                    for (var controlName in values) {
                        allElements[controlName] = true;
                    }

                    // now collect dynamic elements
                    for (var name in dynamicElements) {
                        allElements[name] = dynamicElements[name];
                        values[name] = dynamicElements[name].getValue();
                    }
                }
            }
        };


        //------------------------------------------------------------------------------/
        // PRIVATE METHODS
        //------------------------------------------------------------------------------/

        function devError(msg) {
            alert('beef: ' + msg);
        }

        function isArrayOrObject(array) {
            if (
                '[object Array]' === Object.prototype.toString.call(array) ||
                '[object Object]' === Object.prototype.toString.call(array)
            ) {
                return true;
            }
            return false;
        }

        function selectorEscape(sExpression) {
            return sExpression.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        }


        // does it work with files controls?
        function sendViaHttp(values, jForm) {

            var form = $('<form>');
            form.attr('action', jForm.attr('action'));
            form.attr('method', jForm.attr("method"));
            form.attr('enctype', jForm.attr("enctype"));


            var toInput = function (key, value) {
                if (isArrayOrObject(value)) {
                    for (var i in value) {
                        var zeKey = key + '[' + i + ']';
                        toInput(zeKey, value[i]);
                    }
                }
                else {
                    var input = $('<input type="hidden">');
                    input.attr({
                        name: key,
                        value: value
                    });
                    form.append(input);
                }
            };

            for (var i in values) {
                toInput(i, values[i]);
            }

            // Submit the form, then remove it from the page
            form.appendTo(document.body);
            form.submit();
            form.remove();
        }


        function addControlErrors(controlErrors, controlName, msg) {
            if (false === (controlName in controlErrors)) {
                controlErrors[controlName] = [];
            }
            controlErrors[controlName].push(msg);
        }

        function getStaticReferentControl(name, jForm) {
            var jEl = jForm.find("[name=" + selectorEscape(name) + "]:first");
            if (jEl.length) {
                return jEl;
            }
            return jForm.find("[name^=" + selectorEscape(name + '[') + "]:first");
        }


        function injectValueInStaticControl(controlName, value, jForm) {
            // complex control
            if (null === value || isArrayOrObject(value)) {
                var jEls = getComplexControl(controlName, jForm);
                if (jEls.length) {
                    setComplexControlValue(jEls, value, controlName);
                }
                else {
                    devError("Cannot set the value: complex control with name: '" + controlName + "' not found in the html code");
                }
            }
            // simple control
            else {
                var jEl = getSimpleControl(controlName, jForm);
                if (jEl.length) {
                    setSimpleControlValue(jEl, value);
                }
                else {
                    devError("Cannot set the value: control with name: '" + controlName + "' not found in the html code");
                }
            }
        }


        function getSimpleControl(name, jForm) {
            return jForm.find("[name=" + selectorEscape(name) + "]");
        }

        function setSimpleControlValue(jControl, value) {
            var tagName = jControl.prop('tagName');
            if ('INPUT' === tagName) {
                var type = jControl.attr('type').toLowerCase();
                if ('radio' === type || 'checkbox' === type) {
                    jControl.filter('[value=' + selectorEscape(value) + ']').prop('checked', true);
                }
                else {
                    jControl.val(value);
                }
            }
            else {
                jControl.val(value);
            }
        }

        function getComplexControl(name, jForm) {
            return jForm.find("[name^=" + selectorEscape(name + '[') + "]");
        }


        function setComplexControlValue(jControlEls, value, controlName) {
            if (null === value) {
                jControlEls.each(function () {
                    var tagName = $(this).prop('tagName');
                    if ('INPUT' === tagName) {
                        var type = $(this).attr('type').toLowerCase();
                        if ('checkbox' === type || 'radio' === type) {
                            $(this).prop('checked', false);
                        }
                        else {
                            // input.text
                            $(this).val('');
                        }
                    }
                    else if ('SELECT' === tagName) {
                        $(this).find('option').each(function () {
                            $(this).prop('selected', false);
                        });
                    }
                    else if ('TEXTAREA' === tagName) {
                        $(this).val('');
                    }

                });
            }
            else {
                var patterns = {};
                injectPathPattern2Value(value, controlName, patterns);

                for (var pattern in patterns) {
                    var val = patterns[pattern];

                    var found = false;
                    var spareControls = {};


                    jControlEls.filter(function (i, el) {
                        var name = $(this).attr('name');
                        var reg = new RegExp(pattern);
                        return reg.test(name);
                    }).each(function () {
                        if (true === found) {
                            return;
                        }
                        var tagName = $(this).prop('tagName');
                        if ('INPUT' === tagName) {
                            var type = $(this).attr('type').toLowerCase();
                            if (val === $(this).val()) {
                                if ('checkbox' === type || 'radio' === type) {
                                    $(this).prop('checked', true);
                                    found = true;
                                }
                            }
                            else {
                                if ('text' === type) {
                                    if ($.isEmptyObject(spareControls)) {
                                        spareControls[pattern] = $(this);
                                    }
                                }
                            }
                        }
                        else if ('SELECT' === tagName) {
                            $(this).find('option').each(function () {
                                if ($(this).attr('value') === val) {
                                    $(this).prop('selected', true);
                                    found = true;
                                    delete spareControls[pattern];
                                }
                            });
                        }
                        else if ('TEXTAREA' === tagName) {
                            /**
                             * Textarea is the last chance, like the input.text.
                             */
                            if ($.isEmptyObject(spareControls)) {
                                spareControls[pattern] = $(this);
                            }
                        }

                    });

                    /**
                     * In case of mixed elements types (for instance checkboxes and input.texts) sharing the same controlName,
                     * if a checkbox is not checked, then the value should necessary? (that's what the code below is about)
                     * be the input value (which can be anything).
                     */
                    if (false === $.isEmptyObject(spareControls)) {
                        var jInputOrArea = spareControls[pattern];
                        jInputOrArea.val(val);
                        delete spareControls[pattern];
                    }
                }
            }
        }

        /**
         * @para html, it is assumed that it does not end with []
         */
        function insertValueByHtmlPath(htmlKey, value, $values) {
            var p = htmlKey.split('[]');
            var path = htmlPathToBDot(p, $values);
            window.bdot.setDotValue(path, value, $values);
        }

        function injectPathPattern2Value(value, name, ret) {
            for (var i in value) {
                var val = value[i];
                var ind = i;
                if (pea.isNumeric(i)) {
                    ind += '?';
                }
                var key = name + '\\[' + ind + '\\]';
                if (isArrayOrObject(val)) {
                    injectPathPattern2Value(val, key, ret);
                }
                else {
                    ret[key] = val;
                }
            }
        }

        function htmlPathToBDot(p, $values, prevPath) {
            var seg = p.shift();
            var segment = convertToDot(seg);


            var path;
            if ('undefined' === typeof prevPath) {
                path = segment;
            }
            else {
                if ('' === segment) {
                    path = prevPath;
                }
                else {
                    path = prevPath + '.' + segment;
                }
            }

            if (p.length) { // has other elements
                var nextArray = window.bdot.getDotValue(segment, $values);
                var nextKey = 0;
                if (isArrayOrObject(nextArray)) {
                    nextKey = window.bJsTool.getNextNaturalKey(nextArray);
                }
                return htmlPathToBDot(p, $values, path + '.' + nextKey);
            }
            else { // last element
                return path;
            }
        }

        function convertToDot(segment) {
            if ('[' === segment.substr(0, 1)) {
                segment = segment.substr(1);
            }
            segment = segment.replace(/\./g, '\\.');
            segment = segment.replace(/\[/g, '.');
            segment = segment.replace(/\]/g, '');
            return segment;
        }

        /**
         * A control name must not contain any bracket
         */
        function getControlNameByHtmlName(htmlName) {
            var controlName = htmlName;
            var pos = htmlName.indexOf('[');
            if (-1 !== pos) {
                controlName = controlName.substr(0, pos);
            }
            return controlName;
        }

        //------------------------------------------------------------------------------/
        // DEPRECATED
        //------------------------------------------------------------------------------/
        function noEndingBrackets(html) {
            if ('[]' === html.substr(-2)) {
                return html.substr(0, html.length - 2);
            }
            return html;
        }


    })(jQuery);
}