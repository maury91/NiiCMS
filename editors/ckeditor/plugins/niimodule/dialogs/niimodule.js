(function () {
    function a(b, c) {
        var d = b.lang.niimodule,
            e = b.lang.common.generalTab;
        return {
            title: d.title,
            minWidth: 300,
            minHeight: 80,
            contents: [{
                id: 'info',
                label: e,
                title: e,
                elements: [{
                    id: 'text',
                    type: 'text',
                    style: 'width: 100%;',
                    label: d.text,
                    'default': '',
                    required: true,
                    validate: CKEDITOR.dialog.validate.notEmpty(d.textMissing),
                    setup: function (f) {
                        if (c) this.setValue(f.$.title);
                    },
                    commit: function (f) {
                        f.name = this.getValue();
                        
                    }
                },{
                    id: 'height',
                    type: 'text',
                    style: 'width: 100%;',
                    label: d.height,
                    'default': '100px',
                    required: true,
                    validate: CKEDITOR.dialog.validate.notEmpty(d.textMissing),
                    setup: function (f) {
						if (c) {
							my_f=f;
							w = f.getParent().getStyle('height');
							this.setValue((w=='')?'100px':w);
						}
                    },
                    commit: function (f) {
                         f.height = this.getValue();
                       
                    }
                },{
                    id: 'width',
                    type: 'text',
                    style: 'width: 100%;',
                    label: d.width,
                    'default': '100px',
                    required: true,
                    validate: CKEDITOR.dialog.validate.notEmpty(d.textMissing),
                    setup: function (f) {
                        if (c) {
							w = f.getParent().getStyle('width');
							this.setValue((w=='')?'100px':w);
						}
                    },
                    commit: function (f) {
                        f.width = this.getValue();                        
                    }
                }]
            }],
            onShow: function () {
                if (c) this._element = CKEDITOR.plugins.niimodule.getmodule(b);
                this.setupContent(this._element);
            },
            onOk: function () {
				data = [];
                this.commitContent(data);
				CKEDITOR.plugins.niimodule.insertmodule(b, this._element, data);
                delete this._element;
            }
        };
    };
    CKEDITOR.dialog.add('insertmodule', function (b) {
        return a(b);
    });
    CKEDITOR.dialog.add('getmodule', function (b) {
        return a(b, 1);
    });
})();