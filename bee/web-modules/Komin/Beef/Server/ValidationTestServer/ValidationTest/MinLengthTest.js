oForm.setValidationTest('minLength', function (value, params) {
    var msgFmt = "Please type more than [min] chars ([currentLength] given)"; // @translator
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
});

