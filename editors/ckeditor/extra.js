function ckok(a) {
	ckedbut.getInputElement().$.offsetParent.offsetParent.firstChild.firstChild.firstChild.firstChild.lastChild.firstChild.firstChild.value = a[0].n.substr(2);
}
CKEDITOR.on( 'dialogDefinition', function( ev )
	{
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;
		if ( dialogName == 'image' )
		{
			var infoTab = dialogDefinition.getContents( 'info' );
			infoTab.add( {
					type : 'button',
					label : 'Upload',
					id : 'customField',
					onClick : function() {
						ckedbut = this;
						media_manager({uid : media_uid, onSelected : ckok, dir : './media/'});						
					}
				},'browse');
	}
});

