/**
 * @depends pea
 */
if ('undefined' === typeof window.debugTool) {
    window.debugTool = function () {
    };

    window.debugTool.dump = function (object) {
        // todo: improve if necessary
        return JSON.stringify(object);
    }

    window.debugTool.miniDump = function ($var, $varName) {
        if ('undefined' === typeof $varName) {
            $varName = '';
        }

        if (pea.isString($var)) {
            if ($var.length > 0) {
                $ret = 'string (' + $var + ')';
            }
            else {
                $ret = 'empty string';
            }
        }
        else if (true === $var) {
            $ret = 'true';
        }
        else if (false === $var) {
            $ret = 'false';
        }
        else if (null === $var) {
            $ret = 'null';
        }
        else if (pea.isArray($var)) {
            $ret = 'array(' + $var.length + ')';
        }
        else if (pea.isArrayObject($var)) {
            $ret = 'arrayObject(' + pea.count($var) + ')';
        }
        else if (pea.isNumeric($var)) {
            $ret = 'number(' + $var + ')';
        }
        else if (pea.isFunction($var)) {
            $ret = 'function';
            if ('name' in $var) {
                $ret += ' ' + $var['name'];
            }
        }
        else {
            var type = typeof $var;
            if ('undefined' === type) {
                $ret = 'undefined';
            }
            else {
                $ret = '(unknown value type)';
            }
        }

        if ($varName) {
            $ret = '$' + $varName + ':' + $ret;
        }
        return $ret;
    };
}