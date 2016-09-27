
/**
 * Product:     Custom Product Preview
 * Package:     Aitoc_Aitcg_3.0.1_1.0.0_520274
 * Purchase ID: n/a
 * Generated:   2013-03-05 20:52:02
 * File path:   js/aitoc/aitcg/prototype/rewrite.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
if(Prototype.Version<'1.6.1')
{
    if (Prototype.Browser.IE) 
    {
        Object.extend(Selector.handlers, {
            // IE improperly serializes _countedByPrototype in (inner|outer)HTML.
            unmark: (function()
            {
                var PROPERTIES_ATTRIBUTES_MAP = (function(){
                    var el = document.createElement('div'),
                        isBuggy = false,
                        propName = '_countedByPrototype',
                        value = 'x'
                    el[propName] = value;
                    isBuggy = (el.getAttribute(propName) === value);
                    el = null;
                    return isBuggy;
                })();

                return PROPERTIES_ATTRIBUTES_MAP ?
                    function(nodes) {
                        for (var i = 0, node; node = nodes[i]; i++)
                            node.removeAttribute('_countedByPrototype');
                        return nodes;
                    } :
                    function(nodes) {
                        for (var i = 0, node; node = nodes[i]; i++)
                            node._countedByPrototype = void 0;
                        return nodes;
                    }
            })()
        });
    }
}