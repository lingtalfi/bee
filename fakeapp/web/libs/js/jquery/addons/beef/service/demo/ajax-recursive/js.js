var values = $.extend({
    name: "p",
    job: "i"
}, $values$);

oForm.setValues(values);

oForm.setValidationRules({
    name: {
        minLength: {
            min: 2
        }
    },
    job: {
        minLength: {
            min: 2
        }
    }
});


var jForm = oForm.getFormElement();


//------------------------------------------------------------------------------/
// DYNAMIC ELEMENTS
//------------------------------------------------------------------------------/
var jThing = jForm.find('.idealthing');
var oIdealThing = new window.IdealThing({
    element: jThing
});

var jUl = jForm.find('.conf ul:first');
var oArray = new window.beefSimpleArrayControl({
    container: jUl,
    isDeletable: true,
    isClosed: function (realPath, key, level) {
        return (level > 1);
    },
    onStructureUpdatedAfter: function (v) {
        $('#zelog').html(JSON.stringify(v));
        $('#zelog').append(window.array2UlTool.render(v));

    }
});
oArray.setValue(values);


oForm.setDynamicElements({
    idealThing: oIdealThing,
    conf: oArray
});