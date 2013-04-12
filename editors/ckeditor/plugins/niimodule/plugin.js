/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
/*
var my_niimodule = {};
(function($) {
        // duck-punching to make attr() return a map
        var _old = $.fn.attr;
        $.fn.attr = function() {
          var a, aLength, attributes,  map;
          if (this[0] && arguments.length === 0) {
                  map = {};
                  attributes = this[0].attributes;
                  aLength = attributes.length;
                  for (a = 0; a < aLength; a++) {
                          map[attributes[a].name.toLowerCase()] = attributes[a].value;
                  }
                  return map;
          } else {
                  return _old.apply(this, arguments);
          }
  }
}(jQuery));
(function($) {
	$.fn.list_dom = function(pat) {
		var out = [];
		var Nodes = function(n) {
			if (n.nodeType == 3) 
				return {type : "text", value : n.nodeValue} 			
			else {
				var node = {type : $(n).prop('tagName').toLowerCase(), attr : $(n).attr(), child : []}
				$.each(n.childNodes, function(a, b) {
					node.child.push(Nodes(b));
				});
				return node;
			}
		};
		this.each(function() {
			out.push(Nodes(this));
		});
		return out;
	};
})(jQuery);
function insert_in_editor(edt,arr) {
	for (var i in arr) {
		if (arr[i].type=="text")
			edt.add(new CKEDITOR.htmlParser.text(arr[i].value));
		else {
			var x = new CKEDITOR.htmlParser.element(arr[i].type,arr[i].attr);
			insert_in_editor(x,arr[i].child);
			edt.add(x);
		}
	}
}*/

CKEDITOR.editor.prototype.createFakeNiiElement = function( realElement, className, realElementType, isResizable )
{
	var cssStyle = CKEDITOR.htmlParser.cssStyle,
			cssLength = CKEDITOR.tools.cssLength;
	var lang = this.lang.fakeobjects,
		label = lang[ realElementType ] || lang.unknown;

	var attributes =
	{
		'class' : className,
		'data-cke-realelement' : encodeURIComponent( realElement.getOuterHtml() ),
		'data-cke-real-node-type' : realElement.type,
		title : label,
		align : realElement.getAttribute( 'align' ) || ''
	};

	if ( realElementType )
		attributes[ 'data-cke-real-element-type' ] = realElementType;

	if ( isResizable )
	{
		attributes[ 'data-cke-resizable' ] = isResizable;

		var fakeStyle = new cssStyle();

		var width = realElement.getAttribute( 'width' ),
			height = realElement.getAttribute( 'height' );

		width && ( fakeStyle.rules.width = cssLength( width ) );
		height && ( fakeStyle.rules.height = cssLength( height ) );
		fakeStyle.populate( attributes );
	}

	return this.document.createElement( 'div', { attributes : attributes } );
};

CKEDITOR.editor.prototype.createFakeNiiParserElement = function( realElement, className, realElementType, isResizable )
{
	var cssStyle = CKEDITOR.htmlParser.cssStyle,
			cssLength = CKEDITOR.tools.cssLength;
	var lang = this.lang.fakeobjects,
		label = lang[ realElementType ] || lang.unknown,
		html;

	var writer = new CKEDITOR.htmlParser.basicWriter();
	realElement.writeHtml( writer );
	html = writer.getHtml();

	var attributes =
	{
		'class' : className,
		'data-cke-realelement' : encodeURIComponent( html ),
		'data-cke-real-node-type' : realElement.type,
		title : label,
		align : realElement.attributes.align || ''
	};

	if ( realElementType )
		attributes[ 'data-cke-real-element-type' ] = realElementType;

	if ( isResizable )
	{
		attributes[ 'data-cke-resizable' ] = isResizable;
		var realAttrs = realElement.attributes,
			fakeStyle = new cssStyle();

		var width = realAttrs.width,
			height = realAttrs.height;

		width != undefined && ( fakeStyle.rules.width =  cssLength( width ) );
		height != undefined && ( fakeStyle.rules.height = cssLength ( height ) );
		fakeStyle.populate( attributes );
	}

	return new CKEDITOR.htmlParser.element( 'div', attributes );
};


function niimodule (edit, r, parser) {
	if (parser) {
		name = r.attributes.name;
		s = 'cke_niimodule_'+name;
		e = edit.createFakeNiiParserElement(r,s,'niimodule',true);
		st = (e.attributes["style"] == undefined)?'':e.attributes["style"];
		e.attributes["style"] = st+"min-width:100px;min-height:100px;position:relative;display:inline-block;border:1px dotted";
		e.attributes["contentEditable"] = "false";
	} else {
		name = r.$.attributes.name.nodeValue;
		s = 'cke_niimodule_'+name;
		e = edit.createFakeNiiElement(r,s,'niimodule',true);
		e.setStyles({
    'min-width':'100px','min-height':'100px','position':'relative','display':'inline-block','border':'1px dotted' });
		e.setAttributes({contentEditable: 'false'});
	}
	if (name != undefined) {
			$.ajax({
				url : 'zone_get_module.html',
				data : {mod : name},
				success : function (d) {
					$('.'+edit.id+' .cke_niimodule_'+name).html('<div class="cke_niimodule" title="'+name+'" style="position:absolute;left:0;top:0;width:100%;height:100%;"></div>'+d);
				}
			});
	}
	
	
	
    return e;
}
(function () {
    var a = /\[\[[^\]]+\]\]/g;
	CKEDITOR.plugins.add( 'niimodule',
	{
		requires: ['dialog'],
		lang: ['en', 'it'],
		init: function( b )
		{
			var c = b.lang.niimodule;
			b.addCommand('insertmodule', new CKEDITOR.dialogCommand('insertmodule'));
            b.addCommand('getmodule', new CKEDITOR.dialogCommand('getmodule'));
			b.ui.addButton('CreateModule', {
				label: c.toolbar,
                command: 'insertmodule',
                icon: this.path + 'niimodule.gif'
            });
			if (b.addMenuItems) {
                b.addMenuGroup('niimodule');
                b.addMenuItems({
                    editmodule: {
						label: c.edit,
                        group: 'niimodule',
						command: 'getmodule',
                        icon: this.path + 'niimodule.gif'
                    }
                });
                if (b.contextMenu) b.contextMenu.addListener(function (d, e) {
                    if (!d || (d.$.className != 'cke_niimodule')) return null;
                    return {						
                        editmodule: CKEDITOR.TRISTATE_OFF
                    };
                });
            }
            b.on('doubleclick', function (d) {
                if (CKEDITOR.plugins.niimodule.getmodule(b)) d.data.dialog = 'getmodule';
            });
			CKEDITOR.dialog.add('insertmodule', this.path + 'dialogs/niimodule.js');
            CKEDITOR.dialog.add('getmodule', this.path + 'dialogs/niimodule.js');
		},
		afterInit: function(editor){
            var dataProcessor = editor.dataProcessor,
                dataFilter = dataProcessor && dataProcessor.dataFilter;

            if ( dataFilter )
            {
                dataFilter.addRules(
                    {
                        elements :
                        {
                            niimodule : function( element )
                            {
								my_mod=element;
								return niimodule(editor, element, true);
                               
                            }
                        }
                    }, 3); 
            }
        }
	} );
})();
CKEDITOR.plugins.niimodule = {
    insertmodule: function (a, b, c, d) {
        var e = new CKEDITOR.dom.element('niimodule', a.document);
        e.setAttributes({
            name:c.name,
			width:c.width,
			height:c.height
        });
        if (d) return e.getOuterHtml();
		e = niimodule(a, e, false);
        if (b) {
			//Ottengo il vero modulo
			//Su cerchiamo di modificalo
			b = b.getParent();
			realElement = a.restoreRealElement( b );
			realElement.setAttribute('Name',c.name);
			realElement.setAttribute('Width',c.width);
			realElement.setAttribute('Height',c.height);
            if (CKEDITOR.env.ie) {
                e.insertAfter(b);
                setTimeout(function () {
                    b.remove();
                    e.focus();
                }, 10);
            } else e.replace(b);
        } else a.insertElement(e);
		
        return null;
    },
    getmodule: function (a) {
        var b = a.getSelection().getRanges()[0];
        b.shrink(CKEDITOR.SHRINK_TEXT);
        var c = b.startContainer;
        while (c && !(c.type == CKEDITOR.NODE_ELEMENT && (c.$.className == 'cke_niimodule'))) c = c.getParent();
        return c;
    }
};