/**
 * Depends on:
 * - jquery
 * - ajaxTim
 * - uii.dialogg
 * - assetLoader
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


        /**
         * By default, a test should validate.
         * Methods return either true (if the validation test passes),
         * or a string (the formatted error message if the test doesn't validate).
         * They throw an error for developer errors,
         * for instance if a mandatory param is missing.
         */
        var ruleMethods = {
            minLength: function (msgFmt, value, params) {
                if ('min' in params) {
                    var len = 0;
                    if ('string' === typeof value) {
                        len = value.length;
                    }
                    if (len < params.min) {
                        var m = msgFmt.replace('[min]', params.min);
                        m = m.replace('[currentLength]', len);
                        return m;
                    }
                }
                else {
                    throw new Error("undefined param min");
                }
                return true;
            }
        };

        function addControlErrors(controlErrors, controlName, msg) {
            if (false === (controlName in controlErrors)) {
                controlErrors[controlName] = [];
            }
            controlErrors[controlName].push(msg);
        }


        window.beef = {
            util: {
                getFormInstance: function (jForm) {
                    return jForm.data('beefFormInstance');
                },
                dynamize: function (obj) {
                    obj.prototype.value = null;

                    obj.prototype.setValue = function (value) {
                        this.value = value;
                    };
                    obj.prototype.getValue = function () {
                        return this.value;
                    };
                    obj.prototype.build = function () {

                    };
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
                            width: 400,
                            height: 300,
                            buttons: [
                                {
                                    'text': "Send",
                                    'click': function (e) {
                                        var nestedForm = window.beef.util.getFormInstance(jContent);
                                        var values = nestedForm.post();
                                        if (false !== values) {
                                            callback(values);
                                            jContent.dialogg("destroy");
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
                    // ruleName: ruleParams
                    ruleParams: {},
                    ruleMessages: {
                        minLength: "Please type more than [min] chars"
                    },
                    errorClass: 'error',
                    submitClass: 'submit'
                }, options);

                this.dynamicElements = options.dynamicElements;
                this.jForm = options.jForm;
                this.values = options.values;
                this.ruleParams = options.ruleParams;
                this.ruleMessages = options.ruleMessages;
                this.allElements = {};
                /**
                 * Default class used when a dynamic validation error occurs.
                 */
                this.errorClass = options.errorClass;
                this.submitClass = options.submitClass;


                this.start = function (submit) {
                    var zis = this;
                    for (var i in this.dynamicElements) {

                        (function (name) {
                            // assigning the getControlName method to every dynamic element,
                            // so that they can access their name (useful for callForm2Control)
                            zis.dynamicElements[i].getControlName = function () {
                                return name;
                            };
                            zis.dynamicElements[i].build();
                        })(i);
                    }
                    this.setValues(this.values);
                    this.jForm.data('beefFormInstance', this);


                    if (false !== submit) {
                        var zis = this;
                        var jSubmit = this.jForm.find(".submit:first");
                        jSubmit
                            .off('click.beefSubmit')
                            .on('click.beefSubmit', function () {
                                var values = zis.post();
                                if (false !== values) {
                                    if ('function' === typeof submit) {
                                        submit(values);
                                    }
                                    else {
                                        zis.sendViaHttp(values);
                                    }
                                }
                                return false;
                            });
                    }
                };


                this.setValues = function (values) {
                    this.values = values;
                    for (name in values) {
                        var v = values[name];
                        this.values[name] = v;
                        if (name in this.dynamicElements) {
                            this.dynamicElements[name].setValue(v);
                        }
                        else {
                            var jEl = this.jForm.find("[name=" + selectorEscape(name) + "]");
                            if (jEl.length) {
                                setControlValue(jEl, v);
                            }
                            else {
                                devError("Cannot set the value: element not found for " + name);
                            }
                        }

                    }
                };


                // does it work with files controls?
                this.sendViaHttp = function (values) {
                    console.log("Sending following values through http", values);

                    var form = $('<form>');
                    form.attr('action', this.jForm.attr('action'));
                    form.attr('method', this.jForm.attr("method"));
                    form.attr('enctype', this.jForm.attr("enctype"));


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
                };

                this.addErrorMessage = function (name, message) {
                    if (name in this.allElements) {

                        var obj;
                        if (name in this.dynamicElements) {
                            obj = this.dynamicElements[name].getControl();
                        }
                        else {
                            obj = this.jForm.find('[name=' + selectorEscape(name) + ']');
                        }


                        var errControl = null;
                        var useSibling = true;
                        // first let's see if the relative mode has been set
                        if (obj.attr("data-beef-error")) {
                            var errorClass = obj.attr("data-beef-error");
                            var jEl = obj.parent().find("." + errorClass + ':first');
                            if (jEl.length) {
                                useSibling = false;
                                jEl.html(message);
                                jEl.show();
                                errControl = jEl;
                            }
                        }

                        // sibling mode
                        if (true === useSibling) {
                            // first search an existing sibling
                            var jSibling = obj.next("." + this.errorClass);
                            if (jSibling.length) {
                                jSibling.html(message);
                                jSibling.show();
                            }
                            // or create it
                            else {
                                jSibling = $('<span class="' + this.errorClass + '">' + message + '</span>');
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
                        devError("Element " + name + ' not found, use the collectElements method first');
                    }
                };

                this.collectElements = function () {

                    var zis = this;
                    // find static elements first
                    var vvv = this.jForm
                        .find('input, textarea, select')
                        .not('[type=submit]')
                        .not('[data-beef-ignore=1] *')
                        .serializeArray();


                    for (var i in vvv) {
                        this.allElements[vvv[i].name] = true;
                        this.values[vvv[i].name] = vvv[i].value;
                    }


                    // now collect dynamic elements
                    for (var name in this.dynamicElements) {
                        zis.allElements[name] = this.dynamicElements[name];
                        zis.values[name] = this.dynamicElements[name].getValue();
                    }

                };


                // return the posted values, or false if an error occurred
                this.post = function () {
                    this.allElements = {};
                    var controlErrors = {};


                    this.collectElements();


                    // apply validation rules
                    // we could catch the errors for this block,
                    // but we rather prefer the user to figure it out
                    for (var controlName in this.ruleParams) {
                        var ruleParams = this.ruleParams[controlName];
                        for (var ruleName in ruleParams) {
                            var params = ruleParams[ruleName];
                            if (ruleName in ruleMethods) {
                                if (ruleName in this.ruleMessages) {

                                    // initialize value with null for checkboxes?
                                    var value = null;
                                    if (controlName in this.values) {
                                        value = this.values[controlName];
                                    }
                                    var msgFmt = this.ruleMessages[ruleName];
                                    var f = ruleMethods[ruleName];
                                    var msg = f(msgFmt, value, params);

                                    if (true !== msg) {
                                        addControlErrors(controlErrors, controlName, msg);
                                    }
                                }
                                else {
                                    devError("Unknown rule message for rule " + ruleName);
                                }
                            }
                            else {
                                devError("Rule " + ruleName + " was not defined");
                            }
                        }
                    }


                    // now let's display the validation error messages
                    var ret = this.values;
                    for (var name in controlErrors) {
                        this.addErrorMessage(name, controlErrors[name]);
                        ret = false;
                    }
                    return ret;
                };
            }
        };
    })(jQuery);
}