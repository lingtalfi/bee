/**
 * LingTalfi - 2015-01-26
 * Port from Bat.HtmlTool, see doc there.
 * 
 * @depends pea
 */
if('undefined' === typeof window.htmlTool){
    window.htmlTool = {
        toAttributesString: function(attributes){
            var s ='';
            for(var i in attributes){
                var v = attributes[i];
                if(pea.isNumeric(i)){
                    s += ' ';
                    s += pea.htmlSpecialChars(v, 'ENT_QUOTES');
                }
                else{
                    if(null !== v){
                        s += ' ';
                        s += pea.htmlSpecialChars(i, 'ENT_QUOTES');
                        s += '="'+ pea.htmlSpecialChars(v, 'ENT_QUOTES') +'"';
                    }
                }
            }
            return s;
        }
    };
}

