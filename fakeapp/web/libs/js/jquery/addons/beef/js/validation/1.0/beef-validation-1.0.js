/**
 *
 * This is a validation library for beef.
 * LingTalfi - 2015-01-18
 *
 *
 * 1.0
 *
 *
 * You might want to use this for dynamic validation on the CLIENT side.
 * You also need to include the corresponding translation library for the error messages to display properly.
 *
 *
 *
 * A test returns either true (if the test is successful),
 * or a string (the formatted and translated error message if the test doesn't validate).
 * We throw an error for developer errors, for instance if a mandatory param is missing.
 */

if ('undefined' == typeof window.beefValidationTests) {
    window.beefValidationTests = {
        minLength: function (value, params) {
            var msgFmt = window.beefTranslations.minLength;
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
        },
        minCount: function (value, params) {
            var msgFmt = window.beefTranslations.minCount;
            if ('min' in params) {
                var len = 0;
                for(var i in value){
                    len++;
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
}

    
