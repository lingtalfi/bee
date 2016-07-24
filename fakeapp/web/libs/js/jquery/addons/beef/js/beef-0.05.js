/**
 *
 * LingTalfi - 2015-01-20
 *
 * Depends on:
 * - jquery
 * - ajaxTim
 * - uii.dialogg
 * - assetLoader
 *
 * Translations must be included BEFORE calling this file.
 *
 *
 * What's new?
 * 
 * Fix returned static values when collected.
 * 
 * 
 * - 1.04: This object now handles the array notation for static classic html controls: for instance for checkboxes, the html name can be sports[]
 * but we can use the name sports to reference it.
 *
 */
if ('undefined' == typeof window.beef) {
    (function ($) {


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


        function setControlValue(jControl, value) {
            if ('radio' === jControl.attr('type')) {
                jControl.filter('[value=' + selectorEscape(value) + ']').prop('checked', true);
            }
            else {
                jControl.val(value);
            }
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


        function getStaticElementByName(name, jForm) {
            var jEl = jForm.find("[name=" + selectorEscape(name) + "]");
            if (jEl.length) {
                return jEl;
            }
            return jForm.find("[name=" + selectorEscape(name + '[]') + "]");
        }


        window.beef = {
            util: {
                getFormInstance: function (jForm) {
                    return jForm.data('beefFormInstance');
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
                callForm: function (data, url, callback, options) {
                    options = $.extend({
                        title: null,
                        modal: true
                    }, options);

                    ajaxTim.sendMessage(url, data, function (m) {
                        var jContent = $(m.form);
                        var sJs = m.js;
                        var dependencies = m.dependencies;

                        jContent.dialogg({
                            title: options.title,
                            modal: options.modal,
                            class: 'beefform',
                            buttons: [
                                {
                                    'text': "Send",
                                    'click': function (e) {
                                        var nestedForm = window.beef.util.getFormInstance(jContent);
                                        var values = nestedForm.post();
                                        if (false !== values) {
                                            var ret = callback(values, nestedForm, jContent);
                                            if (false !== ret) {
                                                jContent.dialogg("destroy");
                                            }
                                        }
                                    }
                                }
                            ],
                            create: function (jDialog, options) {
                                if (false === $.isArray(dependencies)) {
                                    dependencies = [];
                                }
                                window.assetLoader.loadDependencies(dependencies, function () {
                                    var code = '<script>' + sJs + '</sc' + 'ript>';
                                    jQuery('head').append(code);
                                });
                            },
                            buildButton: function (text) {
                                return '<button class="submit button">' + text + '</button>';
                            }
                        });
                    });
                }


            },
            form: function (options) {

                options = $.extend({
                    dynamicElements: {},
                    jForm: null,
                    values: {},
                    rules: {},
                    /**
                     * null|array,
                     * If null, it will use a default validationTest library (see code below) if found.
                     * If not null, it's an array that we pass manually
                     */
                    validationTests: null,
                    errorClass: 'beef-error',
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
                var jForm = options.jForm;
                var values = options.values;
                var rules = options.rules;

                // cache for all elements 
                var allElements = {};


                /**
                 * Default class used when a dynamic validation error occurs.
                 */
                var errorClass = options.errorClass;
                var submitClass = options.submitClass;


                // autocompleting missing methods in dynamic elements
                for (var i in dynamicElements) {
                    window.beef.util.dynamize(dynamicElements[i]);
                    if ('undefined' === typeof dynamicElements[i].getControl) {
                        devError("Invalid dynamic element: " + i + " must contain the getControl method");
                    }
                }


                this.setValidationTests = function ($validationTests) {
                    validationTests = $validationTests;
                };


                this.start = function (submit) {
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
                    jForm.data('beefFormInstance', this);


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
                };


                this.injectValues = function ($values) {
                    values = $values;
                    for (name in values) {
                        var v = values[name];
                        if (name in dynamicElements) {
                            dynamicElements[name].setValue(v);
                        }
                        else {
                            var jEl = getStaticElementByName(name, jForm);
                            if (jEl.length) {
                                setControlValue(jEl, v);
                            }
                            else {
                                devError("Cannot set the value: element not found for " + name);
                            }
                        }

                    }
                };


                this.addErrorMessage = function (name, message) {
                    if (name in allElements) {

                        var obj;
                        if (name in dynamicElements) {
                            obj = dynamicElements[name].getControl();
                        }
                        else {
                            obj = getStaticElementByName(name, jForm);
                        }

                        var errControl = null;
                        var useSibling = true;
                        // first let's see if the relative mode has been set
                        if (obj.attr("data-beef-error")) {
                            var zeErrorClass = obj.attr("data-beef-error");
                            var jEl = obj.parent().find("." + zeErrorClass + ':first');
                            if (jEl.length) {
                                useSibling = false;
                                jEl.html(message);
                                jEl.show();
                                errControl = jEl;
                            }
                        }

                        // sibling mode
                        if (null === errControl && true === useSibling) {
                            // first search an existing sibling
                            var jSibling = obj.next("." + errorClass);
                            if (jSibling.length) {
                                jSibling.html(message);
                                jSibling.show();
                            }
                            // or create it
                            else {
                                jSibling = $('<span class="' + errorClass + '">' + message + '</span>');
                                jSibling.show();
                                obj.after(jSibling);
                            }
                            errControl = jSibling;
                        }


                        if (null !== errControl) {
                            obj
                                .off('click.dismissError')
                                .on('click.dismissError', function () {
                                    errControl.hide();
                                });
                        }


                    }
                    else {
                        devError("Element " + name + ' not found, use the collectElementsAndValues method first');
                    }
                };


                // return the posted values, or false if an error occurred
                this.post = function () {


                    var controlErrors = {};


                    // removing default beef error messages
                    jForm.find('.' + errorClass).remove();


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
                    for (var name in controlErrors) {
                        this.addErrorMessage(name, controlErrors[name]);
                        ret = false;
                    }
                    return ret;
                };

                function collectElementsAndValues() {
                    // find static elements first
                    var vvv = jForm
                        .find('input, textarea, select')
                        .not('[type=submit]')
                        .not('[data-beef-ignore=1] *')
                        .serializeArray();


                    allElements = {};
                    values = {};
                    
                    for (var i in vvv) {
                        var staticName = vvv[i].name.replace('[]', '');
                        allElements[staticName] = true;

                        if (staticName === vvv[i].name) { // scalar
                            values[staticName] = vvv[i].value;
                        }
                        else { // array
                            if (true === (staticName in values)) {
                                values[staticName].push(vvv[i].value);
                            }
                            else {
                                values[staticName] = [vvv[i].value];
                            }
                        }
                    }


                    // now collect dynamic elements
                    for (var name in dynamicElements) {
                        allElements[name] = dynamicElements[name];
                        values[name] = dynamicElements[name].getValue();
                    }
                }
            }
        };
    })(jQuery);
}