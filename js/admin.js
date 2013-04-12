var ytt='',ch_t='',cls='',new_t,men_t,mod_t='',to_trash='';
var ctrlPressed = false;
var cur_open=[];
var backinstall=true;
var addons;
var lang_in_use = {};
//Astrazione gestore estensioni
(function($) {
	niiElement = function() {
		var
			do_nothing = function() {return true},
			defaults = {
				toMenu : false,
				toLink : false,
				mob : false,
				conf : false,
				deactive : false,
				zones : false,
				langs : false,
				theme : false,
				using : false,
				id : 'none',
				title : '',
				inf : '',
				img : '',
				auth : '',
				site : '',
				desc : '',
				onSelect : do_nothing
			},
			config = function() {
				exts_config($(this).closest('ol').data('type'),$(this).closest('li').data('nome'));
			},
			editor_ch_lang = function() {
				if (!$(this).hasClass('imgok')) {
					e = $(this).closest('li').data('nome');
					l = $(this).data('l');
					$.ajax({
						url : 'admin_editors.html',
						data : {con : e, lan : l},
						dataType : 'json',
						success : function(d) {
							if (d.r=='y') {
								old = $(lang_in_use[l]).html(__use_this).parent().find('imm').removeClass('imgok').closest('li');							
								if (old.find('.imgok').length == 0)
									old.removeClass('nodelete');
								$(this).html('').parent().find('imm').addClass('imgok').closest('li').addClass('nodelete');
								lang_in_use[l] = this;
								on_select_editors();
							}
						}
					});
				}
				editor
				$(this).data('l');
			},
			resize_elem = function() {
				elem = $(this).closest('li');
				cur_open = elem.closest('ol').data('cur_open');
				if (cur_open != null) {
					$(cur_open).animate({width: '18em',height: '17em'},600);
					$(cur_open).find('.extra').animate({height: '0'},600);      
				}
				if ($(cur_open).attr('id') == elem.attr('id'))
					elem.closest('ol').data('cur_open',null);
				else {
					elem.closest('ol').data('cur_open',elem.get(0));
					elem.animate({width: elem.outerWidth()*2-(elem.outerWidth()-elem.width()),height: elem.outerHeight()*2-(elem.outerHeight()-elem.height())},600);
					elem.find('.extra').animate({height: '18em'},600);
				}
			},
			use_theme = function() {
				inf = $(this).data('info');
				elem = this;
				if (inf.mob) 
					$.ajax({
						url:'admin_template.html',
						data:{tem:inf.nome,mob:0},
						dataType:'json',
						success:function(d){
							if(d.r == 'y') {
								$(tem_mob_in_use).removeClass('nodelete').find('.bar a').html(__u_this);
								$(elem).closest('li').addClass('nodelete').find('.bar a').html($('<span></span>').addClass('template_in_use').text(__in_u_this));
								tem_mob_in_use=$(elem).closest('li').get(0);
							}
						}
					});
				else 
					$.ajax({
						url:'admin_template.html',
						data:{tem:inf.nome},
						dataType:'json',
						success:function(d){
							if(d.r == 'y') {
								console.log(elem);
								$(tem_pc_in_use).removeClass('nodelete').find('.bar a').html(__u_this);
								$(elem).closest('li').addClass('nodelete').find('.bar a').html($('<span></span>').addClass('template_in_use').text(__in_u_this));
								tem_pc_in_use=$(elem).closest('li').get(0);
							}
						}
					});
			}
		return {
			init : function(opt) {
				opt = $.extend({}, defaults, opt||{});
				if ($('#'+opt.id).length > 0)
					$('#'+opt.id).remove();
				element = $('<li></li>').data('nome',opt.title).addClass('element').attr({id : opt.id,title : opt.title}).click(opt.onSelect).addClass('drop_on_trash');
				if (opt.toMenu)
					element.addClass('drop_on_menu');
				if (opt.toLink) {
					if (opt.mob)
						element.addClass('drop_on_link_m is_mobile');
					else
						element.addClass('drop_on_link');
				}
				bar = $('<b></b>').addClass('bar');
				if (opt.conf)
					bar.append($('<a></a>').text(__config).attr('href','#').click(config));
				if (opt.theme) {
					u_t = $('<a></a>').text(__u_this).attr('href','#').click(use_theme).data('info',{mob:opt.mob,nome:opt.title,id:opt.id});
					if (opt.using) {
						u_t.html($('<span></span>').addClass('template_in_use').text(__in_u_this));
						if (opt.mob)
							tem_mob_in_use = element.get(0);
						else
							tem_pc_in_use = element.get(0);
					}
					bar.append(u_t);
				}
				extra = $('<div></div>').addClass('extra')
					.text(__by)
					.append($('<b></b>').text(opt.auth))
					.append('<br/><br/>')
					.append($('<a></a>').attr({target : '_blank',href : opt.site}).text(opt.site))
					.append('<br/>');
				if (opt.zones||opt.langs) {
					left = $('<span></span>').addClass('left');
					extra.append($('<span></span>').addClass('right').html(opt.desc)).append(left);
					if (opt.zones) {
						dci = $('<span></span>').addClass('deactiv');
						bar.append(dci);
						if (opt.zones.length==0) {
							opt.deactive=true;
							left.html(__lib);
						} else {
							if (opt.deactive)
								dci.html(__deactive);
							left.html(__col+' : <br/><br/>');
							for(i in opt.zones)
								left.append('&nbsp;&nbsp;'+opt.zones[i]+'<br/>');
						}
					} else {
						for (i in opt.langs) {
							cont = $('<a></a>').addClass('link').data('l',opt.langs[i].l).attr('href','#').click(editor_ch_lang);
							precont = $('<a></a>').addClass('imm');
							if (opt.langs[i].u=='y') {
								precont.addClass('imgok');
								element.addClass('nodelete');
								lang_in_use[opt.langs[i].l] = cont.get(0);
							} else
								cont.html(__use_this);
							left.append('&nbsp;&nbsp;').append($('<span></span>').addClass(opt.langs[i].l).append(precont).append(' '+opt.langs[i].l+' ').append(cont)).append('<br/>');
						}
					}
				} else
					extra.append(opt.desc);
				if (opt.deactive) 
					element.addClass('deactive');
				element
					.append($('<b></b>').addClass('text').text(opt.inf))
					.append(opt.img)
					.append($('<a></a>').addClass('resize').click(resize_elem))
					.append(extra)
					.append(bar);
				return element;
			}
		}
	}();
	$.fn.extend({
		niiExtElement : niiElement.init
	});
})(jQuery);


//Desktop
function save_desktop() {
	//Prelevo i dati
	bk = $('.brow_window .content').css('background');
	tx_col = $('.brow_window .content a').css('color');
	x = $('.brow_window .content').removeClass('content');
	bk_mode = x.attr('class');
	x.addClass('content');
	$.ajax({
		url : 'admin_global.html',
		data : {desktop_bk : bk, tx : tx_col, text_siz : $('#text_size_sel').val()},
		dataType : 'json',
		success : function(d) {
			if (d.r == 'y') {	
				//Aggiorno il desktop
				$('body').attr('class','sfondo').css({'background':bk});
				$('.cmsicon').css('color',tx_col);
			}					
		}
	});
}
function choose_img(a) {
	b = a[0].n.substr(2);
	$('.brow_window .content').css('background-image','url('+b+')');
}
function change_back_mode(v) {
	x = $('.brow_window .content').attr('class', 'content').css({'background-size':'','background-repeat' : '','background-position' : '','moz-background-size' : ''});
	switch(v) {
		case '1' : x.addClass('riempi'); break;
		case '2' : x.addClass('esteso'); break;
		case '3' : x.addClass('affiancato'); break;
		case '4' : x.addClass('affiancato centrato'); break;
		case '5' : x.addClass('centrato'); break;
	}
}
//Creazione dinamica estensioni
function add_mod(mod) {	
	$.ajax({
		url : 'admin_module.html',
		data : {info : mod.n},
		dataType : 'json',
		success: function(data) {
			$().niiExtElement({title : data.n,toTrash : true,toMenu : true,id : data.id,inf : data.inf,img : data.img,auth : data.auth,site : data.site,desc : data.desc,conf : data.conf,onSelect : select_mod}).appendTo('#mods_content').show('fold',1000);
			make_dragable_m();
		}
	})
	
}
function load_all_modules() {
	$.ajax({
		url : 'admin_module.html',
		data : {getList : ''},
		dataType : 'json',
		success: function(data) {
			$('#mods_content').html('').data('cur_open',null).data('type','module')
			.selectable({ filter: "li" ,  cancel: ".no_selectable,a" ,stop: on_select_mods });
			for (i in data)
				$().niiExtElement({title : data[i].n,toTrash : true,toMenu : true,id : data[i].id,inf : data[i].inf,img : data[i].img,auth : data[i].auth,site : data[i].site,desc : data[i].desc,conf : data[i].conf,onSelect : select_mod}).appendTo('#mods_content');
			make_dragable_m();
		}
	});
}
function add_com(com) {	
	$.ajax({
		url : 'admin_component.html',
		data : {info : com.n},
		dataType : 'json',
		success: function(data) {
			if (data.m||data.om)
				$().niiExtElement({title : data.n,toTrash : true,toLink : true,mob:true,id : data.id,inf : data.inf,img : data.img,auth : data.auth,site : data.site,desc : data.desc,conf : data.conf,onSelect : select_com}).insertAfter('#coms_content .pre_mob').show('fold',1000);
			if (!data.om)
				$().niiExtElement({title : data.n,toTrash : true,toLink : true,id : data.id,inf : data.inf,img : data.img,auth : data.auth,site : data.site,desc : data.desc,conf : data.conf,onSelect : select_com}).insertAfter('#coms_content .pre_pc').show('fold',1000);
			make_dragable_c();
		}
	});	
}
function load_all_components() {
	$.ajax({
		url : 'admin_component.html',
		data : {getList : ''},
		dataType : 'json',
		success: function(data) {
			$('#coms_content').data('cur_open',null).data('type','component')
			.selectable({ filter: "li" ,  cancel: ".no_selectable,a" ,stop: on_select_coms });
			for (i in data) {
				if (data[i].m||data[i].om)
					$().niiExtElement({title : data[i].n,toTrash : true,toLink : true,mob:true,id : data[i].id,inf : data[i].inf,img : data[i].img,auth : data[i].auth,site : data[i].site,desc : data[i].desc,conf : data[i].conf,onSelect : select_com}).insertAfter('#coms_content .pre_mob');
				if (!data[i].om)
					$().niiExtElement({title : data[i].n,toTrash : true,toLink : true,id : data[i].id,inf : data[i].inf,img : data[i].img,auth : data[i].auth,site : data[i].site,desc : data[i].desc,conf : data[i].conf,onSelect : select_com}).insertAfter('#coms_content .pre_pc');
			}
			make_dragable_c();
		}
	});
}
function add_plugin(plg,deac) {
	$.ajax({
		url : 'admin_plugin.html',
		data : {info : plg.n},
		dataType : 'json',
		success: function(data) {
			$().niiExtElement({title : data.n,toTrash : true,deactive : deac,id : data.id,inf : data.inf,img : data.img,auth : data.auth,site : data.site,desc : data.desc,conf : data.conf, zones : data.zones,onSelect : select_plg}).appendTo('#plugins_content').show('fold',1000);
			make_dragable_p();
		}
	});
}
function load_all_plugins() {
	$.ajax({
		url : 'admin_plugin.html',
		data : {getList : ''},
		dataType : 'json',
		success: function(data) {
			$('#plugins_content').html('').data('cur_open',null).data('type','plugin')
			.selectable({ filter: "li" ,  cancel: ".no_selectable,a" ,stop: on_select_plugins });
			for (i in data)
				$().niiExtElement({title : data[i].n,toTrash : true,deactive : data[i].deac,id : data[i].id,inf : data[i].inf,img : data[i].img,auth : data[i].auth,site : data[i].site,desc : data[i].desc,conf : data[i].conf, zones : data[i].zones,onSelect : select_plg}).appendTo('#plugins_content');
			make_dragable_p();
		}
	});
}
function add_editor(edt) {
	$.ajax({
		url : 'admin_editors.html',
		data : {info : edt.n},
		dataType : 'json',
		success: function(data) {
			$().niiExtElement({title : data.n,toTrash : true,id : data.id,inf : data.inf,img : data.img,auth : data.auth,site : data.site,desc : data.desc, langs : data.langs,onSelect : select_edt}).appendTo('#editors_content').show('fold',1000);
			make_dragable_e();
		}
	});
}
function load_all_editors() {
	$.ajax({
		url : 'admin_editors.html',
		data : {getList : ''},
		dataType : 'json',
		success: function(data) {
			$('#plugins_content').html('').data('cur_open',null)
			.selectable({ filter: "li" ,  cancel: ".no_selectable" ,stop: on_select_editors });
			for (i in data)
				$().niiExtElement({title : data[i].n,toTrash : true,id : data[i].id,inf : data[i].inf,img : data[i].img,auth : data[i].auth,site : data[i].site,desc : data[i].desc, langs : data[i].langs,onSelect : select_edt}).appendTo('#editors_content');
			make_dragable_e();
		}
	});
}
function add_theme(mob,tem) {
	$.ajax({
		url : 'admin_template.html',
		data : {info : tem.n, "mob" : mob},
		dataType : 'json',
		success: function(data) {
			theme = $().niiExtElement({title : data.n,theme : true,mob : data.mob,id : data.id,inf : data.inf,img : data.img,auth : data.auth,site : data.site,desc : data.desc,using : data.using,onSelect : select_tem});
			if (data.mob)
				theme.insertAfter('#templates_content .pre_mob');
			else
				theme.insertAfter('#templates_content .pre_pc');
			theme.show('fold',1000);
			make_dragable_t();
		}
	})
}
function load_all_themes() {
	$.ajax({
		url : 'admin_template.html',
		data : {getList : ''},
		dataType : 'json',
		success: function(data) {
			$('#templates_content').data('cur_open',null)
			.selectable({ filter: "li" ,  cancel: ".no_selectable,a" ,stop: on_select_templates });
			for (i in data) {
				theme = $().niiExtElement({title : data[i].n,theme : true,mob : data[i].mob,id : data[i].id,inf : data[i].inf,img : data[i].img,auth : data[i].auth,site : data[i].site,desc : data[i].desc,using : data[i].using,onSelect : select_tem});
				if (data[i].mob)
					theme.insertAfter('#templates_content .pre_mob');
				else
					theme.insertAfter('#templates_content .pre_pc');			
			}
			make_dragable_t();
		}
	});
}
//Spostamento dinamico estensioni nel cestino
////
////
function mod_del() {
	//Finestra per la richiesta di cestinazione
	if (mods_selected.length>0) {
		if (mods_selected.length>1)
			$('.trash_icon').html(__mttm_multi.format_f(mods_selected.length));
		else
			$('.trash_icon').html(__mttm_single.format_f(mods_selected[0].n));
		$('#move_to_trash').dialog( "option", "title", __move_to_trash ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_module.html',
				data : { del : mods_selected},
				dataType : 'json',
				success : function(d) {
					$('#move_to_trash').dialog('close');
					if(d.r=='y') {								
						$('.cmstrash').addClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							$('#'+d.dels[i].id).hide('clip',{},1000,function(){$(this).remove()});
							$('<li class="file" dir="m" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon extension_drag cmsmodule"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .pre_pc').show('fold',1000);						
						}
						mods_selected=[];
						$('#mods_content li').removeClass('ui-selected');
						$("#mod_del").find("span").addClass("imgdelbig_in");
					}
				}
			});
			
		}
		/**/
	}
}
function com_del() {
	//Finestra per la richiesta di cestinazione
	if (coms_selected.length>0) {
		if (coms_selected.length>1)
			$('.trash_icon').html(__mttc_multi.format_f(coms_selected.length));
		else
			$('.trash_icon').html(__mttc_single.format_f(coms_selected[0].n));
		$('#move_to_trash').dialog( "option", "title", __move_to_trash ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_component.html',
				data : { del : coms_selected},
				dataType : 'json',
				success : function(d) {
					$('#move_to_trash').dialog('close');
					if(d.r=='y') {								
						$('.cmstrash').addClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							x = $('#'+d.dels[i].id)
							tot = x.length;
							x.hide('clip',{},1000,function(){$(this).remove()});
							//conteggio
							if ((tot>2)||(!d.dels[i].m))
								$('<li class="file" dir="c" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon extension_drag cmscomponent"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .pre_pc').show('fold',1000);
							else if ((tot>2)||(d.dels[i].m))
								$('<li class="file" dir="c" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon extension_drag cmscomponent"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .pre_mob').show('fold',1000);
						}
						coms_selected=[];
						$('#coms_content li').removeClass('ui-selected');
						$("#com_del").find("span").addClass("imgdelbig_in");						
					}
				}
			});
			
		}
		/**/
	}
}
function plugin_del() {
	//Finestra per la richiesta di cestinazione
	if (plugins_selected.length>0) {
		if (plugins_selected.length>1)
			$('.trash_icon').html(__mttp_multi.format_f(plugins_selected.length));
		else
			$('.trash_icon').html(__mttp_single.format_f(plugins_selected[0].n));
		$('#move_to_trash').dialog( "option", "title", __move_to_trash ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_plugin.html',
				data : { del : plugins_selected},
				dataType : 'json',
				success : function(d) {
					$('#move_to_trash').dialog('close');
					if(d.r=='y') {								
						$('.cmstrash').addClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							$('#'+d.dels[i].id).hide('clip',{},1000,function(){$(this).remove()});
							$('<li class="file" dir="p" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon extension_drag cmsplugin"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .pre_pc').show('fold',1000);						
						}
						plugins_selected_d=[];
						plugins_selected=[];
						$('#plugins_content li').removeClass('ui-selected');
						$("#plugin_del").find("span").addClass("imgdelbig_in");
						$("#plugin_off").find("span").addClass("imgoff_in");
					}
				}
			});
			
		}
		/**/
	}
}
function editor_del() {
	//Finestra per la richiesta di cestinazione
	if (editors_selected_d.length>0) {
		if (editors_selected_d.length>1)
			$('.trash_icon').html(__mtte_multi.format_f(editors_selected_d.length));
		else
			$('.trash_icon').html(__mtte_single.format_f(editors_selected_d[0].n));
		$('#move_to_trash').dialog( "option", "title", __move_to_trash ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_editors.html',
				data : { del : editors_selected_d},
				dataType : 'json',
				success : function(d) {
					$('#move_to_trash').dialog('close');
					if(d.r=='y') {								
						$('.cmstrash').addClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							$('#'+d.dels[i].id).hide('clip',{},1000,function(){$(this).remove()});
							$('<li class="file" dir="e" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon extension_drag cmseditors"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .pre_pc').show('fold',1000);						
						}
						editors_selected_d=[];
						editors_selected=[];
						$("#editor_del").find("span").addClass("imgdelbig_in");
					}
				}
			});
			
		}
		/**/
	}
}
function template_del() {
	//Finestra per la richiesta di cestinazione
	if (templates_selected_d.length>0) {
		if (templates_selected_d.length>1)
			$('.trash_icon').html(__mttt_multi.format_f(templates_selected_d.length));
		else
			$('.trash_icon').html(__mttt_single.format_f(templates_selected_d[0].n));
		$('#move_to_trash').dialog( "option", "title", __move_to_trash ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_template.html',
				data : { del : templates_selected_d},
				dataType : 'json',
				success : function(d) {
					$('#move_to_trash').dialog('close');
					if(d.r=='y') {								
						$('.cmstrash').addClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							$('#'+d.dels[i].id).hide('clip',{},1000,function(){$(this).remove()});
							$('<li class="file" dir="'+d.dels[i].t+'" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon extension_drag cmstemplate"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .'+((d.dels[i].t == 't')?'pre_pc':'pre_mob')).show('fold',1000);						
						}
						templates_selected_d=[];
						templates_selected=[];
						$("#template_del").find("span").addClass("imgdelbig_in");
					}
				}
			});
			
		}
		/**/
	}
}
//Selezione
//e draggable
//
function select_mod(a) {
	if (!ctrlPressed)
		$('#mods_content li').removeClass('ui-selected');
	$(a).addClass('ui-selected');
	on_select_mods();
}
function on_select_mods() {
	mods_selected = [];
	$( "#mods_content .ui-selected" ).each(function(i,elem) {
		aa = {n : $(elem).attr("title"), id : $(elem).attr("id")};
		mods_selected.push(aa);
	});
	if (mods_selected.length > 0) {
		$("#mod_del").find("span").removeClass("imgdelbig_in");
	} else {
		$("#mod_del").find("span").addClass("imgdelbig_in");
	}
}
function make_dragable_m() {
	$( "#mods_content li" ).draggable({
		cursorAt: { top: -12, left: -20 },
		drag: function (ev) {
			if (mods_selected.length < 2) 
				return !($(this).is(".nodelete"))
			else return true;
		},
		helper: function( event ) {
			to_trash='mod';
			xx = $("<ol></ol>").css({ width : 0 , height : 0}).appendTo("body");
			if (mods_selected.length < 2) {
				open_window("menu");
				aa = {n : $(this).attr("title"), id : $(this).attr("id")};
				mods_selected = [aa];
				item_for_menu = $(this).attr("title");
				setTimeout(function(){$( "#menu_tab" ).tabs( "option", "selected", 0 );},420);
				return $( "<div class='extension_drag cmsmodule'><span class='text'>"+mods_selected[0].n+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
			} else
				return $( "<div class='extension_drag cmsmodule'><span class='number'>"+mods_selected.length+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
		},
		stop: function(ev, ui){
			$(xx).remove();
		}
	});
}
function select_com() {
	if (!ctrlPressed)
		$('#coms_content li').removeClass('ui-selected');
	$(this).addClass('ui-selected');
	on_select_coms();
}
function on_select_coms() {
	coms_selected = [];
	$( "#coms_content .ui-selected" ).each(function(i,elem) {		
		if (!search_n(coms_selected,$(elem).attr("title"))) {
			aa = {n : $(elem).attr("title"), id : $(elem).attr("id"), m : $(elem).hasClass("is_mobile")};
			coms_selected.push(aa);
		}
	});
	if (coms_selected.length > 0) {
		$("#com_del").find("span").removeClass("imgdelbig_in");
	} else {
		$("#com_del").find("span").addClass("imgdelbig_in");
	}
}
function make_dragable_c() {
	$( "#coms_content li" ).draggable({
		cursorAt: { top: -12, left: -20 },
		helper: function( event ) {
			to_trash='com';
			xx = $("<ol></ol>").css({ width : 0 , height : 0}).appendTo("body");
			if (coms_selected.length < 2) {
				open_window("menu");
				is_this_mobile = $(this).hasClass("is_mobile");
				setTimeout(function(){if (is_this_mobile) $( "#menu_tab" ).tabs( "option", "selected", 1 );else $( "#menu_tab" ).tabs( "option", "selected", 0 );},420);
				aa = {n : $(this).attr("title"), id : $(this).attr("id"), m : $(this).hasClass("is_mobile")};
				coms_selected = [aa];
				item_for_menu = $(this).attr("title");
				item_for_menu_l = 'com_'+$(this).attr("title")+'.html';
				return $( "<div class='extension_drag cmscomponent'><span class='text'>"+coms_selected[0].n+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
			} else
				return $( "<div class='extension_drag cmscomponent'><span class='number'>"+coms_selected.length+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
		},
		stop: function(ev, ui){
			$(xx).remove();
		}
	});
}
function select_plg(a) {
	if (!ctrlPressed)
		$('#plugins_content li').removeClass('ui-selected');
	$(this).addClass('ui-selected');
	on_select_plugins();
}
function on_select_plugins() {
	plugins_selected = [];
	plugins_selected_d = [];
	$( "#plugins_content .ui-selected" ).each(function(i,elem) {
		aa = {n : $(elem).attr("title"), id : $(elem).attr("id"), deact : $(elem).is(".deactive")};
		if (!$(elem).is(".nodeactive"))
			plugins_selected_d.push(aa);
		plugins_selected.push(aa);
	});
	if (plugins_selected.length > 0) {
		$("#plugin_del").find("span").removeClass("imgdelbig_in");
	} else {
		$("#plugin_del").find("span").addClass("imgdelbig_in");
	}
	if (plugins_selected_d.length == 1) {
		$("#plugin_off").find("span").removeClass("imgoff_in");
	} else {
		$("#plugin_off").find("span").addClass("imgoff_in");
	}	
}
function make_dragable_p() {
	$( "#plugins_content li" ).draggable({
		cursorAt: { top: -12, left: -20 },
		drag: function (ev) {
			if (plugins_selected.length < 2) 
				return !($(this).is(".nodelete"))
			else return true;
		},
		helper: function( event ) {
			to_trash='plugin';
			xx = $("<ol></ol>").css({ width : 0 , height : 0}).appendTo("body");
			if (plugins_selected.length < 2) {
				aa = {n : $(this).attr("title"), id : $(this).attr("id")};
				plugins_selected = [aa];
				if ($(this).is(".nodeactive"))
					plugins_selected_d = [];
				else
					plugins_selected_d = [aa];
				return $( "<div class='extension_drag cmsplugin'><span class='text'>"+plugins_selected[0].n+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
			} else
				return $( "<div class='extension_drag cmsplugin'><span class='number'>"+plugins_selected.length+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
		},
		stop: function(ev, ui){
			$(xx).remove();
		}
	});
}
function select_edt() {
	if (!ctrlPressed)
		$('#editors_content li').removeClass('ui-selected');
	$(this).addClass('ui-selected');
	on_select_editors();
}
function on_select_editors() {
	editors_selected = [];
	editors_selected_d = [];
	$( "#editors_content .ui-selected" ).each(function(i,elem) {
		aa = {n : $(elem).attr("title"), id : $(elem).attr("id")};
		if (!$(elem).is(".nodelete"))
			editors_selected_d.push(aa);
		editors_selected.push(aa);
	});
	if (editors_selected_d.length > 0) {
		$("#editor_del").find("span").removeClass("imgdelbig_in");
	} else {
		$("#editor_del").find("span").addClass("imgdelbig_in");
	}			
}
function make_dragable_e() {
	$( "#editors_content li" ).draggable({
		cursorAt: { top: -12, left: -20 },
		drag: function (ev) {
			if (editors_selected.length < 2) 
				return !($(this).is(".nodelete"))
			else return true;
		},
		helper: function( event ) {
			to_trash='editor';
			xx = $("<ol></ol>").css({ width : 0 , height : 0}).appendTo("body");
			if (editors_selected.length < 2) {
				aa = {n : $(this).attr("title"), id : $(this).attr("id")};
				editors_selected = [aa];
				if ($(this).is(".nodelete"))
					editors_selected_d = [];
				else
					editors_selected_d = [aa];
				return $( "<div class='extension_drag cmseditors'><span class='text'>"+editors_selected_d[0].n+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
			} else
				return $( "<div class='extension_drag cmseditors'><span class='number'>"+editors_selected_d.length+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
		},
		stop: function(ev, ui){
			$(xx).remove();
		}
	});
}
function select_tem() {
	if (!ctrlPressed)
		$('#templates_content li').removeClass('ui-selected');
	$(this).addClass('ui-selected');
	on_select_templates();
}

function on_select_templates() {
	templates_selected = [];
	templates_selected_d = [];
	$( "#templates_content .ui-selected" ).each(function(i,elem) {
		aa = {n : $(elem).attr("title"), m : $(elem).hasClass('is_mobile'), id : $(elem).attr("id")};
		if (!$(elem).is(".nodelete"))
			templates_selected_d.push(aa);
		templates_selected.push(aa);
	});
	if (templates_selected_d.length > 0) {
		$("#template_del").find("span").removeClass("imgdelbig_in");
	} else {
		$("#template_del").find("span").addClass("imgdelbig_in");
	}
	if (templates_selected.length > 0) {
		$("#template_down").find("span").removeClass("imgdownbig_in");
	} else {
		$("#template_down").find("span").addClass("imgdownbig_in");
	}	
}
function make_dragable_t() {
	$( "#templates_content li" ).draggable({
		cursorAt: { top: -12, left: -20 },
		drag: function (ev) {
			if (templates_selected.length < 2) 
				return !($(this).is(".nodelete"))
			else return true;
		},
		helper: function( event ) {
			to_trash='template';
			xx = $("<ol></ol>").css({ width : 0 , height : 0}).appendTo("body");
			if (templates_selected.length < 2) {
				is_this_mobile = $(this).hasClass("is_mobile");
				aa = {n : $(this).attr("title"), m : $(this).hasClass("is_mobile"), id : $(this).attr("id")};
				templates_selected = [aa];
				item_for_menu = $(this).attr("title");
				if ($(this).is(".nodelete"))
					templates_selected_d = [];
				else
					templates_selected_d = [aa];
				return $( "<div class='extension_drag cmstemplate'><span class='text'>"+templates_selected_d[0].n+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
			} else
				return $( "<div class='extension_drag cmstemplate'><span class='number'>"+templates_selected_d.length+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
		},
		stop: function(ev, ui){
			$(xx).remove();
		}
	});
}
//Gestione cestino
//
//
function trash_drop( event, ui ) {
	//Scopri chi è che esegue l'azione
	if (to_trash=='page')
		page_del();
	if (to_trash=='template')
		template_del();
	if (to_trash=='editor')
		editor_del();
	if (to_trash=='plugin')
		plugin_del();
	if (to_trash=='com')
		com_del();
	if (to_trash=='mod')
		mod_del();
};
function trash_del() {
	if (trash_selected.length>0) {
		if (trash_selected.length>1)
			$('.trash_icon').html(__dp_multi.format_f(trash_selected.length));
		else
			$('.trash_icon').html(__dp_single.format_f(trash_selected[0].n,convers[trash_selected[0].t]));
		$('#move_to_trash').dialog( "option", "title", __delete_permanent).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_trash.html',
				data : {del : trash_selected},
				dataType : 'json',
				success : function(d) {
					if(d.r=='y') {
						$('#move_to_trash').dialog('close');
						if (d.empty)
							$('.cmstrash').removeClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							$('#trash_content li[dir="'+d.dels[i].t+'"][title="'+d.dels[i].n+'"]').hide('clip',{},1000,function(){$(this).remove()});
						}
						trash_selected=[];
						$('#trash_content li').removeClass('ui-selected');
						$("#trash_del").find("span").addClass("imgdelbig_in");
						$("#trash_restore").find("span").addClass("imgrestore_in");
					}
				}
			});					
		}
	}			
}
function trash_restore() {
	if (trash_selected.length>0) {
		if (trash_selected.length>1)
			$('.trash_icon').html(__rest_multi.format_f(trash_selected.length));
		else
			$('.trash_icon').html(__rest_single.format_f(trash_selected[0].n,convers[trash_selected[0].t]));
		$('#move_to_trash').dialog( "option", "title", __restore ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_trash.html',
				data : {restore : trash_selected},
				dataType : 'json',
				success : function(d) {
					if(d.r=='y') {
						$('#move_to_trash').dialog('close');
						if (d.empty)
							$('.cmstrash').removeClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.rests) {
							$('#trash_content li[dir="'+d.rests[i].t+'"][title="'+d.rests[i].n+'"]').hide('clip',{},1000,function(){$(this).remove()});
							//Ricreazione degli elementi se la scheda è aperta
							if (d.rests[i].t == 'pg') {
								$('<li class="drop_on_link drop_on_trash is_pc file '+d.rests[i].b+'" title="'+d.rests[i].n+'" style="cursor:pointer" onclick="select_page(\''+d.rests[i].n+'\')" ondblclick="open_page(\''+d.rests[i].n+'\')"><a class="pageicon page'+d.rests[i].a+'"><a class="fname">'+d.rests[i].n+'</a></li>').insertAfter('#pages_content .pre_pc').show('fold',1000);
								make_dragable();								
							}
							if (d.rests[i].t == 'pm') {									
								$('<li class="drop_on_link_m drop_on_trash is_mobile file '+d.rests[i].b+'" title="'+d.rests[i].n+'" style="cursor:pointer" onclick="select_page(\''+d.rests[i].n+'\',true)" ondblclick="open_page(\''+d.rests[i].n+'\',\'&mob=\',true)"><a class="pageicon page'+d.rests[i].a+'"><a class="fname">'+d.rests[i].n+'</a></li>').insertAfter('#pages_content .pre_mob').show('fold',1000);
								make_dragable();								
							}
							if (d.rests[i].t == 't') 
								add_theme(false,d.rests[i]);							
							if (d.rests[i].t == 'tm') 
								add_theme(true,d.rests[i]);
							if (d.rests[i].t == 'e')
								add_editor(d.rests[i]);
							if (d.rests[i].t == 'p')	
								add_plugin(d.rests[i],true);
							if (d.rests[i].t == 'c')
								add_com(d.rests[i]);
							if (d.rests[i].t == 'm')
								add_mod(d.rests[i]);
						}
						trash_selected=[];
						$('#trash_content li').removeClass('ui-selected');
						$("#trash_del").find("span").addClass("imgdelbig_in");
						$("#trash_restore").find("span").addClass("imgrestore_in");
						
					}
				}
			});					
		}
	}
}
//Installazione Estensioni
//
//
function search_n(arr,val) {
for (i in arr) if (arr[i].n == val) return true; return false;
}
function exts_config(ext,name) {
	pg = (ext+name).replace(/\\/g,'_').replace(/\//g,'__');
	p = $('#ext__'+pg);
	if (p.length == 0) {
		$('body').append("<div id='ext__"+pg+"' title='"+name+"'><div class='window_sub' id='sub_ext__"+pg+"'><iframe width='100%' height='100%' src='' id='sub_ifm_ext__"+pg+"' frameborder='0' border='0' ></iframe></div></div>");
		$('#ext__'+pg).niiwin({width:700,height:500,set:ext,icon:'icon'+ext});			
	}
	$("#sub_ifm_ext__"+pg).attr('src',"admin_"+ext+'.html?conf='+name);
	$('#ext__'+pg).niiwin("open");		
}
function dep_bginstall(a,b,c) {
	$.ajax({
	  url: "admin_nii.html",
	  data: "aj=0&inst="+a+"&ty="+b+"&req="+c,
	  cache: false,
	  dataType: "json",
	  success: function(d) { 
		if (d.res == 'ok') {
			inst=false;
			elab_coda();
		} else 
			new_install(d.to,true);		
	}});
}
function dep_barra() {
  x = ((320/tot)*proc)+"px";
  $("#depinstbarf").animate({width : x},300);
}
function elab_coda() {
	if ((typeof nii_dep == "undefined")||(!nii_dep)) {
		aggiungi(tipo[current.a]+" <b>"+current.c+"</b> &egrave; stato installato<br>");
		if (current.a == 'u') 
			location.href = 'admin_nii.html';
		else {
			$(current.d).html("<a class='installed' href='#'>Installato</a>");
			if (coda.length > 0) {
				next = coda.shift();
				install(next.a,next.b,next.c,next.d);
				proc++;
				barra();							
			} else {
				proc++;
				barra();
				tot=1;
				proc=0;
				$("#kit").css("display","none");
				setTimeout("barra()",3000);
			}
		}
	} else if (coda.length > 0) {
		next = coda.shift();
		$("#depwhat").html(__inst+next.c);
		dep_bginstall(next.a,next.b,next.c);
		proc++;
		dep_barra();
	} else {
		proc++;
		dep_barra();
		$("#depwhat").html(" ");
		$("#depnext").hide();
		$('#instend').show();
	}
}
function startinst() {
	document.getElementById("depprec").style.display="none";
	document.getElementById("depnext").style.display="block";
	nii_dep=true;
	elab_coda();
}
function success_installed(a,c) {
	if (a == 't') 
		add_theme(false,{n : c});							
	if (a == 'tm') 
		add_theme(true,{n : c});
	if (a == 'e')
		add_editor({n : c});
	if (a == 'p')	
		add_plugin({n : c},false);
	if (a == 'c')
		add_com({n : c});
	if (a == 'm')
		add_mod({n : c});
	elab_coda();
	cur = cur_open.splice(-1,1);
	return $('#'+cur).dialog('close');
}
function setCookie(sNome, sValore, iGiorni) {
	var dtOggi = new Date()
	var dtExpires = new Date()
	dtExpires.setTime
	(dtOggi.getTime() + 24 * iGiorni * 3600000)
	document.cookie = sNome + "=" + escape(sValore) + "; expires=" + dtExpires.toGMTString();
}
function installed(t) {
	$.ajax({
	  url: "admin_nii.html",
	  data: "list="+t,
	  cache: false,
	  dataType: "json",
	  success: function(d) {
		for (i in d.data) {
			$("#"+d.data[i].md).html("<a class='installed' href='#'>"+__installed+"</a>");
		}
	}});
}
function is_installed() {
	$.ajax({
	  url: "admin_nii.html",
	  data: "is_inst=0",
	  cache: false,
	  dataType: "json",
	  success: function(d) {
		addons = d.data;
	}});
}
function site_auth(k,a) {
	ajax_loadContent('niidiv',"admin_nii.html?ax=0&key="+k+"&api="+a);
}
function nii_to(a) {
	ajax_loadContent('niidiv',"admin_nii.html?aj=0&to="+escape(a));
}
function bginstall(a,b,c) {
	$.ajax({
	  url: "admin_nii.html",
	  data: "aj=0&inst="+a+"&ty="+b+"&req="+c,
	  cache: false,
	  dataType: "json",
	  success: function(d) { 
		if (d.res == 'ok') {
			inst=false;
			//Componente installato
			if (a == 't') 
				add_theme(false,{n : c});							
			if (a == 'tm') 
				add_theme(true,{n : c});
			if (a == 'e')
				add_editor({n : c});
			if (a == 'p')	
				add_plugin({n : c},false);
			if (a == 'c')
				add_com({n : c});
			if (a == 'm')
				add_mod({n : c});
			elab_coda();
		} else 
			new_install(d.to,true);		
	}});
}

$(window).keydown(function(evt) {
  if (evt.which == 17) { // ctrl
	ctrlPressed = true;
  }
}).keyup(function(evt) {
  if (evt.which == 17) { // ctrl
	ctrlPressed = false;
  }
});
function processExts(files) {
   if(files && typeof FileReader !== "undefined") {
      for(var i=0; i<files.length; i++) {
         readExt(files[0]);
      }
   }
   else 
	  console.log('Error in the upload!');
}
function new_install(name,mode) {
	mode = (typeof mode == 'undefined')? false : mode;
	if (mode)
		pg = (name.z+name.n).replace(/\(/g,'_').replace(/\)/g,'__').replace(/\./g,'___');
	else
		pg = name.replace(/\(/g,'_').replace(/\)/g,'__').replace(/\./g,'___');
	p = $('#inst__'+pg);
	cur_open.push('inst__'+pg);
	if (p.length != 0) 
	p.remove();
	$('body').append("<div id='inst__"+pg+"' dir='"+name+"' title='"+__installation+"'><div class='window_sub' id='sub_inst__"+pg+"'></div></div>");
	$('#inst__'+pg).dialog({"zindex" : 4000,"width" : 800,"height" : 600,autoOpen: false,close:function(){$.ajax({url:'admin_ext.html',data:{act:'no_inst',file:$(this).attr('dir')}});$(this).remove();}});
	if (mode) {
		if (name.z == 'update')
			$('#sub_inst__'+pg).html('<iframe width="100%" height="100%" src="update.php" frameborder="0" border="0" ></iframe>');
		else
			$('#sub_inst__'+pg).html('<iframe width="100%" height="100%" src="admin_'+name.z+'.html?install='+name.n+'" frameborder="0" border="0" ></iframe>')
	} else
		ajax_loadContent("sub_inst__"+pg,'admin_ext.html?act=info&file='+name);
	$('#inst__'+pg).dialog("open");		
}
var readExt = function(file) {
	if (file) {
		//Creazione del file fitizio nel gruppo	
		var fileSize = 0;
		if (file.size > 1024 * 1024)
			fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
		else
			fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
		$('.upload_exts .progress-bar').show();
		$('.upload_exts .progress-bar span').css('width',0);
		var xhr = new XMLHttpRequest();
		xhr.file = file; // not necessary if you create scopes like this
		xhr.upload.onprogress = function(e) {
			var done = e.position || e.loaded, total = e.totalSize || e.total;
			$('.upload_exts .progress-bar span').css('width',(Math.floor(done/total*1000)/10) + '%');
		};		
		xhr.onreadystatechange = function(e) {
			if ( 4 == this.readyState ) {
				response = eval("(" + xhr.responseText + ")");
				if (response.success) {
					//Controllo del tipo
					$('.upload_exts .progress-bar').hide();
					//Creazione e apertura di una nuova finestra
					new_install(response.filename);
				} else 
					alert(response.error);				
			}
		};
		url = 'admin_ext.html?myfile='+file.name;
		xhr.open("POST", url, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(file.name));
        xhr.setRequestHeader("Content-Type", "application/octet-stream");
        xhr.send(file);	
	}}
//Funzioni supplementari
//gestione estensioni
//
var cur_r={t : '', m : '', p : '', c : '', e : ''};
function template_down() {
	if (templates_selected.length>0) {
		for (i in templates_selected)
			window.open('admin_template.html?down='+templates_selected[i].n+(templates_selected[i].m?'&mob=0':''));
	}
}
function insert_mod(md,men) {
	$.ajax({
		url : 'admin_menu.html',
		data : {add : men, tip : 'b', mod : md},
		dataType : 'json',
		success : function(d) {
			if (d.o == 'y') {
				//Inserimento del menu
				js_make_menu(men,false,d.c,false,d.b,md)
				//Modalità modifica
				inline_edit_menu(men+"_"+d.c,men,d.b,false);
			}
		}
	});
}
function plugin_off() {
	if (plugins_selected_d.length == 1) {
		if (plugins_selected_d[0].deact) {
			$.ajax({
				url : 'admin_plugin.html',
				data : { act : plugins_selected_d[0]},
				dataType : 'json',
				success : function (d) {
					$('#'+d.id).removeClass('deactive').find('.deactiv').html('');
					plugins_selected_d=[];
					plugins_selected=[];
					$('#plugins_content li').removeClass('ui-selected');
					$("#plugin_del").find("span").addClass("imgdelbig_in");
					$("#plugin_off").find("span").addClass("imgoff_in");				
				}
			});
		} else {
			$.ajax({
				url : 'admin_plugin.html',
				data : { deac : plugins_selected_d[0]},
				dataType : 'json',
				success : function (d) {
					$('#'+d.id).addClass('deactive').find('.deactiv').html(__deactive);
					plugins_selected_d=[];
					plugins_selected=[];
					$('#plugins_content li').removeClass('ui-selected');
					$("#plugin_del").find("span").addClass("imgdelbig_in");
					$("#plugin_off").find("span").addClass("imgoff_in");
				}
			});
		}
	}
}
function change_template(a,b,c) {
				
}
function make_dragable() {
	$( "#pages_content li.file" ).draggable({
		cursorAt: { top: -12, left: -20 },
		helper: function( event ) {
			to_trash='page';
			xx = $("<ol class='pages'></ol>").css({ width : 0 , height : 0}).appendTo("body");
			if (pages_selected.length < 2) {
				open_window("menu");
				is_this_mobile = $(this).hasClass("is_mobile");
				setTimeout(function(){if (is_this_mobile) $( "#menu_tab" ).tabs( "option", "selected", 1 );else $( "#menu_tab" ).tabs( "option", "selected", 0 );},420);
				aa = {n : $(this).attr("title"), m : $(this).hasClass("is_mobile")};
				pages_selected = [aa];
				item_for_menu = $(this).attr("title");
				item_for_menu_l = $(this).attr("title")+'.htm';
				if ($(this).is(".nodelete"))
					pages_selected_d = [];
				else
					pages_selected_d = [aa];
				return $(this).clone().css({ opacity : 0.7 , 'z-index' : 20000}).appendTo(xx);
			} else
				return $( "<div class='extension_drag cmspages'><span class='number'>"+pages_selected.length+"</span></div>" ).css({ opacity : 0.7, 'z-index' : 20000}).appendTo(xx);
		},
		stop: function(ev, ui){
			$(xx).remove();
		}
	});
}
function elem_resize(tid,tp){
    if (cur_r[tp] != '') {
      $('#'+cur_r[tp]).animate({width: '18em',height: '17em'},600);
      $('#'+cur_r[tp]+' .extra').animate({height: '0'},600);      
    }
    if (cur_r[tp] == tid)
      cur_r[tp] = '';
    else {
      cur_r[tp] = tid;
      $('#'+tid).animate({width: $('#'+tid).outerWidth()*2-($('#'+tid).outerWidth()-$('#'+tid).width()),height: $('#'+tid).outerHeight()*2-($('#'+tid).outerHeight()-$('#'+tid).height())},600);
      $('#'+tid+' .extra').animate({height: '18em'},600);
    }
}
function menu_choose_mod(a) {
	if (mod_t != '')
		$('#live_mod_'+mod_t).removeClass('live-elem-choose');
	$('#live_mod_'+a).addClass('live-elem-choose');
	mod_t = a;
	$('#chn_mod').html(mod_t);
}
function open_mods() {
	$('.chn_mod').show();
	//Apri finestra per la scelta del modulo
	$( "#new-mod" ).dialog( "open" );
	$("#live-mods-list").html("<img src='css/images/live/loader.gif' width='32px' height='32px' class='live-loader'/>");
	//Ottengo la lista dei moduli
	$.ajax({
		url : 'admin_live.html',
		data : {ajax : 'get_mods'},
		cache: false,
		dataType : "json",
		success : function(d)  {
			$("#live-mods-list").html("");				
			for (i in d.data) {
				if (d.data[i].i == 'noimg') {
					st='';
					cl='cmsmdnoimg ';
				} else {
					st=" style='background:url(\""+d.data[i].i+"\") no-repeat'";
					cl='';
				}						 
				$("#live-mods-list").append("<div class='live-elem live-hint' title='"+quotes(d.data[i].d)+"' onclick='menu_choose_mod(\""+d.data[i].r+"\")' id='live_mod_"+d.data[i].r+"'><a class='"+cl+"live-elem-img' "+st+"></a><a class='live-elem-title'>"+d.data[i].n+"</a></div>");
			}
			$("#live-mods-list").append("<div class='live-elem ghost'></div>");
			make_live_tooltip();
		}
	});
}
function close_mods() {
	$('.chn_mod').hide();
}
function js_make_menu(t,mob,x,type,n,mod) {
	pre = (mob) ? 'mob_' : '';
	val = (mob) ? 'true' : 'false';
	console.log('val :'+val);
	xd = (type)? "&nbsp;<a href='#' onclick='inline_add(\""+pre+t+'_'+x+"\",\""+t+"\",\""+n+"\","+val+")' title='"+__add_link+"' class='imgadd hint'></a>" : '';
	uid = 'uid_'+Math.abs((pre+n).hashCode());
	to_append = "<div class='sort_menu "+uid+"' id='"+pre+n+"'><h3 id='"+pre+t+'_'+x+"h'><a href='#' class='tlink hint' id='"+pre+t+'_'+x+"a' onclick='dopen(\""+pre+t+'_'+x+"\")' title='"+__d_o_c+"'>+</a><a class='mname' id='10'>"+n+"</a> "+xd+" <a href='#' onclick='inline_del_menu(\""+t+'_'+x+"\",\""+t+"\",\""+n+"\","+val+")' title='"+__d_del_menu+"' class='imgdel hint'></a> <a href='#' onclick='inline_edit_menu(\""+pre+t+'_'+x+"\",\""+t+"\",\""+n+"\","+val+")' title='"+__d_mod_menu+"' class='imgedit hint'></a></h3><div style='display:none;padding-left:30px' id='"+pre+t+'_'+x+"'>";
	if (type) {
		to_append += "<table class='sortmenu hint' id='"+pre+t+'_'+x+"tb' title='"+__m_order+"'><thead><tr><td><center><a id='"+pre+t+'_'+x+"_save' title='"+__s_order+"' href='#' style='display:none' onclick='savem(\""+pre+t+'_'+x+"\",\""+t+"\",\""+n+"\","+val+")' class='imgsave hint'></a></center><td>"+__nom+"<td>"+__url+"<td>"+__lev+"<td>"+__lng+"<tr><td>&nbsp;<td>&nbsp;</tr></thead><tbody></tbody></table>";				
	}
	else {
		to_append += "mod : "+mod;
	}
	to_append += '</div></div>';
	$('#men_'+pre+t).append(to_append);
	if (type) {
		tv ='$("#'+pre+t+'_'+x+'tb tbody").sortable({placeholder: "menu-highlight", update : function() { $("#'+pre+t+'_'+x+'_save").show() }}).disableSelection();$( ".'+uid+'" ).droppable({accept: ".drop_on_link'+(mob?'_m':'')+'", activeClass: "ui-state-hover",hoverClass: "ui-state-active",tolerance: "pointer",drop: function( event, ui ) {		inline_add("'+pre+t+'_'+x+'","'+t+'","'+n+'",'+val+');$("#inline_n").val(item_for_menu); $("#inline_h").val(item_for_menu_l);}});';
		console.log(tv);
		eval(tv);
	}
}		
function make_menu() {
	if ($.trim($('#new_menu_nome').val()) != '') {
		tt = radio_value(new_menu_f.tip);
		if(tt!=undefined) {
			if (tt=='a') {
				//Creazione di un menu normale
				$.ajax({
					url : 'admin_menu.html',
					data : {add : men_t, tip : 'a', nome : $('#new_menu_nome').val()},
					dataType : 'json',
					success : function(d) {
						if (d.r == 'y') {
							//Inserimento del menu
							js_make_menu(men_t,men_m,d.c,true,$('#new_menu_nome').val(),'')
							$('#new_menu').dialog('close');
						}
					}
				});
			} else if(mod_t != '') {
				//Creazione di un menu modulo
				$.ajax({
					url : 'admin_menu.html',
					data : {add : men_t, tip : 'b', nome : $('#new_menu_nome').val(), mod : mod_t},
					dataType : 'json',
					success : function(d) {
						if (d.o == 'y') {
							//Inserimento del menu
							js_make_menu(men_t,men_m,d.c,false,$('#new_menu_nome').val(),mod_t)
							$('#new_menu').dialog('close');
						}
					}
				});
			} else notify_error(__no_empty_m,'newmenu_error');
		} else notify_error(__no_empty_r,'newmenu_error');
	} else notify_error(__no_empty,'newmenu_error');
}
function add_menu(a,b) {
	men_t=a;
	men_m=b;
	$('#new_menu').dialog('open');
}
function notify_error(t,x) {
	url_error = $( "#"+x );
	url_error.html( t ).addClass( "ui-state-highlight" );
	setTimeout(function() {
		url_error.removeClass( "ui-state-highlight", 1500 );
	}, 500 );
}
function page_make_new(diag) {
	name = $('#live_np_n').val();
	if ($.trim(name) == '') 
		notify_error(__no_empty,'newpagen_error');
	else {
		$.getJSON('admin_live.html?ajax=make_page&n='+name+'&t='+ch_type+((new_t)?'&mob=0':''), function(d) {
			if(d.r == 'y') {
				//Creazione pagina				
				$( diag ).dialog( "close" );
				//Creazione icona della nuova pagina
				if (typeof make_dragable == 'function') {
					if (new_t) {
						t = 'is_mobile';
						t2 = ',true';
						t3 = ',\'&mob=\',true';
						t4 = 'pre_mob';
						t5 = '_m';
					} else {
						t = 'is_pc';
						t5 = t3 = t2 = '';
						t4 = 'pre_pc';
					}
					$('<li class="drop_on_link'+t5+' drop_on_trash file '+t+'" title="'+name+'" style="cursor:pointer" onclick="select_page(\''+name+'\''+t2+')" ondblclick="open_page(\''+name+'\''+t3+')"><a class="pageicon page'+ch_type+'"><a class="fname">'+name+'</a></li>').insertAfter('#pages_content .'+t4).show('fold',1000);
					make_dragable();
				}
				//Apertura
				open_page(name,((new_t)?'&mob=':''),new_t);
			} else
			notify_error(d.r,'newpagen_error');
		});
	}
}
function make_page(diag) {
	if (ch_type != '') {
		$( diag ).dialog( "close" );
		$('#new_page_n').dialog("open");
	}
}
function new_page(a){
	new_t=a;
	$('#new_page').dialog('open');
}
function change_type(a) {
	if (ch_type != '') {
		$.getJSON('admin_pages.html?pchn='+pages_selected[0].n+'&type='+ch_type+((pages_selected[0].m)?'&mob=':''), function(d) {
			if(d.r == 'y') {
				$( a ).dialog( "close" );
				//Modifica icon elemento
				$('#pages_content .'+((pages_selected[0].m)?'is_mobile':'is_pc')+'[title="'+pages_selected[0].n+'"]').find('.pageicon').removeClass('page'+cls).addClass('page'+ch_type);
				//Se è aperta la scheda di modifica della pagina la chiudo
				pg = pages_selected[0].n.replace(/\\/g,'_').replace(/\//g,'__');
				if(pages_selected[0].m) pg += '_mb';
				$('#page__'+pg).dialog('close');
			} else	alert(d.r);
		});
	}
}
function choose__type(a,b) {
	ch_type=a;
	if (ch_t != '')
		$(ch_t).removeClass('live-elem-choose');
	b = $('.live_npage_t_'+a);
	$(b).addClass('live-elem-choose');
	ch_t = b;
}
function page_change() {
	if (pages_selected.length == 1) {
		cc = ((pages_selected[0].m)?'is_mobile':'is_pc');
		xx = $('#pages_content .'+cc+'[title="'+pages_selected[0].n+'"]').find('.pageicon').removeClass('pageicon');
		cls = xx.attr('class');
		xx.addClass('pageicon');
		cls = cls.substr(4);
		if ((cls=='php')||(cls=='link'))
			alert(__lost_if_proced);
		if ((cls=='mhtml')||(cls=='msimple'))
			alert(__slost_if_proced);
		$('#live-choose-type .live-elem').show();
		$('#live-choose-type #live_npage_t_'+cls).hide();
		$('#choose_type').dialog( "open" );
	}
}
function load_backup(a) {
	$('#show_backups').dialog("close");
	open_page(pages_selected[0].n,'&rbak='+a);
}
function page_old(){
	if (pages_selected.length == 1) {
		$.ajax({
			url : 'admin_live.html',
			data : {ajax : 'old_baks', sbak : pages_selected[0].n , mob : pages_selected[0].m},
			success : function(d){
				$('#old_versions').html(d);
				$('#show_backups').dialog("open");
			}
		})
	
	}			
}
function select_page(a,b) {
	if (b)
		this_elem = $('#pages_content .is_mobile[title="'+a+'"]');
	else
		this_elem = $('#pages_content .is_pc[title="'+a+'"]');
	if (!this_elem.hasClass('inactive')) {
		if (!ctrlPressed)
			$('#pages_content li').removeClass('ui-selected');
		this_elem.addClass('ui-selected');
		on_select_pages();
	}
}
function on_select_pages() {
	pages_selected = [];
	pages_selected_d = [];
	pages_selected_b = [];
	pages_selected_c = [];
	$( "#pages_content .ui-selected" ).each(function(i,elem) {
		aa = {n : $(elem).attr("title"), m : $(elem).hasClass('is_mobile')};
		if (!$(elem).is(".nodelete"))
			pages_selected_d.push(aa);
		if ($(elem).is(".has_bak"))
			pages_selected_b.push(aa);
		if (!aa.m)
			pages_selected_c.push(aa);
		pages_selected.push(aa);
	});
	if (pages_selected_b.length > 0) {
		$("#page_draft").find("span").removeClass("imgdraft_in");
	} else {
		$("#page_draft").find("span").addClass("imgdraft_in");
	}
	if (pages_selected_d.length > 0) {
		$("#page_del").find("span").removeClass("imgdelbig_in");
	} else {
		$("#page_del").find("span").addClass("imgdelbig_in");
	}
	if (pages_selected.length == 1) {		
		$("#page_old").find("span").removeClass("imgold_in");
		$("#page_change").find("span").removeClass("imgchn_in");
	} else {
		$("#page_old").find("span").addClass("imgold_in");
		$("#page_change").find("span").addClass("imgchn_in");
	}
	if (pages_selected_c.length > 0) 			
		$("#page_prev").find("span").removeClass("imgpreviewbig_in");
	else 					
		$("#page_prev").find("span").addClass("imgpreviewbig_in");
}
function page_draft() {
	for (i in pages_selected_b) {
		open_page(pages_selected_b[i].n,'&bak=0'+((pages_selected_b[i].m)?'&mob=0':''));
	}
}
function yes_to_trash() {
	ytt();
}
function page_prev() {
	if (pages_selected_c.length>0) {
		for (i in pages_selected_c)
			window.open(pages_selected_c[i].n+'.htm');
	}
}
function page_saved(pid,a,b) {
	$('#saved_'+pid).show(600);
	$('#pages_content .'+((b)?'is_mobile':'is_pc')+'[title="'+a+'"]').removeClass('has_bak');
}
function empty_win(e) {
	$(e).find('.window_sub').html('');
	return true;
}
function make_window(id,i) {
	$("#"+id).niiwin({width:700,height:500,set:i,icon:'icon'+i,onClose:empty_win});
}
function open_page(page,a,b) {
	aa = (typeof a == 'undefined')?'':a;
	bb = (typeof b == 'undefined')?false:b;
	pg = page.replace(/\\/g,'_').replace(/\//g,'__');
	c = '';
	if(b) { pg += '_mb'; c+= '(mobile)'}
	p = $('#page__'+pg);
	if (p.length == 0) {
		$('body').append("<div id='page__"+pg+"' title='"+page.split('/').pop()+c+"'><div class='window_sub' id='sub_page__"+pg+"'></div></div>");
		$('#page__'+pg).niiwin({width:700,height:500,set:'pages',icon:'iconpages'});			
	}
	ajax_loadContent("sub_page__"+pg,'admin_pages.html?edit='+page+aa);
	$('#page__'+pg).niiwin("open");		
}
function open_window(id) {
	if ($("#"+id).is(":visible")) {
		$("#"+id).niiwin("open");
	} else {
		if (id == 'live') {
			document.getElementById("live_frame").src = 'admin_live.html';
			$("#"+id).niiwin("open");
		} else
			$.ajax({
				url : 'admin_'+id+'.html?show_first=',
				success : function (d) {
					$("#"+id).niiwin("open");
					$("#sub_"+id).html(d);						
				}
			});
		
	}
}

function page_del() {			
	if (pages_selected_d.length>0) {
		if (pages_selected_d.length>1)
			$('.trash_icon').html(__mtt_multi.format_f(pages_selected_d.length));
		else
			$('.trash_icon').html(__mtt_single.format_f(pages_selected_d[0].n));
		$('#move_to_trash').dialog( "option", "title", __move_to_trash ).dialog('open');
		ytt = function () {
			$.ajax({
				url : 'admin_pages.html',
				data : { del : pages_selected_d},
				dataType : 'json',
				success : function(d) {
					$('#move_to_trash').dialog('close');
					if(d.r=='y') {						
						$('.cmstrash').addClass('cmstrash_n');
						//Eliminazione degli elementi trascinati
						for (i in d.dels) {
							cc = ((d.dels[i].t == 'pg')?'is_pc':'is_mobile');
							xx = $('#pages_content .'+cc+'[title="'+d.dels[i].n+'"]').find('.pageicon').removeClass('pageicon');
							clas = xx.attr('class');
							xx.addClass('pageicon');
							$('#pages_content .'+cc+'[title="'+d.dels[i].n+'"]').hide('clip',{},1000,function(){$(this).remove()});
							//Se è aperto il cestino farli apparire nel cestino
							$('<li class="drop_on_link drop_on_trash file" dir="'+d.dels[i].t+'" title="'+d.dels[i].n+'" style="cursor:pointer;display:none"><a class="pageicon '+clas+'"><a class="fname">'+d.dels[i].n+'</a></li>').insertAfter('#trash_content .'+((d.dels[i].t == 'pg')?'pre_pc':'pre_mob')).show('fold',1000);
						}
						pages_selected_d=[];
						pages_selected=[];
						$('#pages_content li').removeClass('ui-selected');
						$("#page_prev").find("span").addClass("imgpreviewbig_in");
						$("#page_del").find("span").addClass("imgdelbig_in");
						$("#page_change").find("span").addClass("imgchn_in");
						$("#page_old").find("span").addClass("imgold_in");
						$("#page_draft").find("span").addClass("imgdraft_in");
					}
				}
			});
			
		}
		/**/
	}
}
function show_dir(a) {
    if (a == "") dirx = cdir;
    else {
        dirx = a;
        cdir = a
    }
    $.ajax({
        url: "zone_media_man.html",
        data: "act=list&d=" + dirx,
        cache: false,
        dataType: "json",
        success: function (a) {
            filecon = "";
            $.each(a.data, function (a, b) {
                if (b) {
                    if (b.t == "f") {
                        e = ext(b.n);
                        if (e == "png" || e == "jpg" || e == "bmp" || e == "gif") cla = "imm";
                        else if (e == "js") cla = "js";
                        else if (e == "xml") cla = "xml";
                        else if (e == "php") cla = "php";
                        else if (e == "css") cla = "css";
                        else if (e == "htm" || e == "html" || e == "phtml") cla = "html";
                        else if (e == "zip" || e == "rar" || e == "7z" || e == "tar" || e == "gz" || e == "iso") cla = "zip";
                        else cla = "file";
						if (mode == "prev") {
							if (cla == "imm") {
								add = "<img src='" + dirx + "/" + b.n + "' class='media_man_imm' />";
								cla = "load imgimmI";
							} else 
								cla += "big";							
						} else add = "";
                        filecon += "<li onmouseover='fileover(\"" + dirx + "/" + b.n + '","f","' + cla + "\")' title='" + b.n + "' dir='f' class='file'><a rev='" + b.n + "' class='img" + cla + "' >" + add + "</a><a class='fname' href='#' ondblclick='twoclick( function(a) {rename(a)},this)' onclick='oneclick(function() {sh_file(\"" + dirx + "/" + b.n + "\")})'>" + b.n + "</a><a class='fperm'>" + b.p + "</a><a class='fsize'>" + b.s + "</a></li>"
                    }
					cla = "dir";
					if (mode == "prev")
						cla += "big";
                    if (b.t == "d") filecon += "<li onmouseover='fileover(\"" + dirx + "/" + b.n + '","d")\' title=\'' + b.n + "' dir='d' class='file'><a class='img"+cla+"'></a><a class='fname' href='#' ondblclick='twoclick( function(a) {rename(a);},this)' onclick='oneclick(function() {show_dir(\"" + dirx + "/" + b.n + "\");})'>" + b.n + "</a><a class='fperm'>" + b.p + "</a></li>";
                    if (b.t == "s") if (b.n != filesum) {
                        filesum = b.n;
                        $("#view").html(filecon);						
						$("#view").selectable({ filter: 'li',  cancel: "a"  ,stop: function() {
							var result = "";
							selected = new Array;
							$( ".ui-selected", this ).each(function() {
								a = {
									n: cdir + "/" + this.title,
									t: this.dir
								};
								selected.push(a);
							});
						}});
						$('.media_man_imm').load(function(){
							//Controllo dimensioni
							if ($(this).height() > $(this).width()) {
								if ($(this).height() > 100)
									$(this).height(100);
							} else {
								if ($(this).width() > 100)
									$(this).width(100);
							}
							$(this).show();	
							$(this).parent().removeClass('imgload');
						});
                        rnm = true
                    }
                }
            }
			
			)
        }
    });
    if (a == "") setTimeout('show_dir("");', 1e4);
}
function fileover(a, b, c) {
    curfile = a;
    tfile = b;
    tyfile = c
}
function rename(a) {
    if (rnm) {
        a.innerHTML = '<input id="rnminput" onkeydown="if (event.keyCode == 13) frename(this,this.value,\'' + a.innerHTML + '\')" type="text" value=' + a.innerHTML + ">";
        document.getElementById("rnminput").focus();
        rnm = false
    }
}
function frename(a, b, c) {
    if (b == c) {
        a.parentElement.innerHTML = c;
        rnm = true
    } else $.ajax({
        url: "admin_explorer.html?act=rnm&f=" + escape(cdir + "/" + c) + "&n=" + b,
        cache: false,
        dataType: "json",
        success: function (d) {
            if (d.s == "y") {
                a.parentElement.innerHTML = b;
                show_dir(cdir)
            } else a.parentElement.innerHTML = c;
            rnm = true
        }
    })
}
function sh_file(a) {
    newWindow(a, "popup", 800, 600, 1, 1, 0, 0, 0, 1, 0)
}
function newWindow(a, b, c, d, e, f, g, h, i, j, k) {
    var l = (screen.width - c) / 2;
    var m = (screen.height - d) / 2;
    var n = "height=" + d + ",width=" + c + ",top=" + m + ",left=" + l + ",scrollbars=" + e + ",resizable=" + f + ",menubar=" + g + ",toolbar=" + h + ",location=" + i + ",statusbar=" + j + ",fullscreen=" + k + "";
    var o = window.open(a, b, n);
    if (parseInt(navigator.appVersion) >= 4) {
        o.window.focus()
    }
}
function dir_up() {
    if (cdir != ".") show_dir(cdir.substring(0, cdir.lastIndexOf("/")))
}
function ext(b) {
    a = b.substring(b.lastIndexOf(".") + 1, b.length);
    return a.toLowerCase()
}
function cmod(a) {
    $("#view").removeClass();
    $("#view").addClass(a);
    showcmod();
    if (a == "prev") {
		$(".imgjs").removeClass("imgjs").addClass("imgjsbig");
		$(".imgxml").removeClass("imgxml").addClass("imgxmlbig");
		$(".imgphp").removeClass("imgphp").addClass("imgphpbig");
		$(".imgcss").removeClass("imgcss").addClass("imgcssbig");
		$(".imghtml").removeClass("imghtml").addClass("imghtmlbig");
		$(".imgzip").removeClass("imgzip").addClass("imgzipbig");
		$(".imgfile").removeClass("imgfile").addClass("imgfilebig");
		$(".imgdir").removeClass("imgdir").addClass("imgdirbig");
        $(".imgimm").attr("style", function () {
            return 'background : none;';
        }).html(function () {
            return "<img src='" + cdir + "/" +this.rev + "' class='media_man_imm' />";
        }).removeClass("imgimm").addClass("imgload imgimmI");
		$('.media_man_imm').load(function(){
			if ($(this).height() > $(this).width()) {
				if ($(this).height() > 100)
					$(this).height(100);
			} else {
				if ($(this).width() > 100)
					$(this).width(100);
			}
			$(this).show();	
			$(this).parent().removeClass('imgload');
		});
		
		
    } else if (mode == "prev") {
		$(".imgjs").addClass("imgjs").removeClass("imgjsbig");
		$(".imgxml").addClass("imgxml").removeClass("imgxmlbig");
		$(".imgphp").addClass("imgphp").removeClass("imgphpbig");
		$(".imgcss").addClass("imgcss").removeClass("imgcssbig");
		$(".imghtml").addClass("imghtml").removeClass("imghtmlbig");
		$(".imgzip").addClass("imgzip").removeClass("imgzipbig");
		$(".imgfile").addClass("imgfile").removeClass("imgfilebig");
		$(".imgdir").addClass("imgdir").removeClass("imgdirbig");
		$(".imgimmI").attr("style", "").html('').addClass('imgimm').removeClass('imgimmI').removeClass('imgload');
	}
    mode = a
}
function showcmod() {
    $("#icmod").toggleClass("show")
}
function twoclick(a, b) {
    clearTimeout(timer);
    a(b)
}
function oneclick(a) {
    if (rnm) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(function () {
            a()
        }, 250)
    }
}
function PopupMenu(a) {
    function j(b) {
        menu = document.getElementById(a);
        menu.style.display = b;
		pmenu_onopen();
    }
    var b = true;
    var c = document.all;
    var d = document.getElementById && !document.all;
    var e = false;
    var f = null;
    var g = false;
    var h = function () {
            return false
        };
    var i = function () {
            return true
        };
    this.overpopupmenu = function (a) {
        g = a
    };
    this.SetShow = function (a) {
        b = a
    };
    this.SetOnOpenPopup = function (a) {
        h = a
    };
    this.SetOnClosePopup = function (a) {
        i = a
    };
    this.CloseMenu = function () {
        menu.style.display = "none"
    };
    this.OpenMenu = function (a) {
        j(a)
    };
    this.mouseSelect = function (b) {
        menu = document.getElementById(a);
        var c = d ? b.target.parentNode : event.srcElement.parentElement;
        if (e) {
            if (g == false) {
                e = false;
                g = false;
                menu.style.display = "none";
                i();
                return false
            }
            return true
        }
        return false
    };
    this.ItemSelMenu = function (a) {
        if (!b) return true;
        j("block");
        var c = d ? a.target.parentNode : event.srcElement.parentElement;
        f = c;
        if (d) {
            menu.style.left = a.clientX;
            menu.style.top = a.clientY
        } else {
            menu.style.pixelLeft = event.clientX;
            menu.style.pixelTop = event.clientY
        }
        e = true;
        return false
    };
    this.Displayed = function () {
        return document.getElementById(a).style.display != "none"
    };
    document.getElementById("view").oncontextmenu = this.ItemSelMenu
}
function del_file() {
    pmenu.CloseMenu();
	//Richesta eliminazione
	if (confirm(__del_file)) {
		if ((selected != undefined)&&(selected.length > 0)) {
			elabq = "";
			for (i = 0; i < selected.length; i++) elabq += "&f[]=" + selected[i]["n"] + "&d[]=" + selected[i]["t"];
			$.ajax({
				url: "zone_media_man.html?act=del" + elabq,
				success: function () {
					show_dir(cdir)
				}
			})
		} else $.ajax({
			url: "zone_media_man.html?act=del&f[]=" + escape(popupfile) + "&d[]=" + popupfilet,
			success: function () {
				show_dir(cdir)
			}
		});
	}
}
function zip() {
    pmenu.CloseMenu();
    zipname = prompt(l__zipn, ".zip");
    if (zipname != null) {
        if (selected.length > 0) {
            elabq = "";
            for (i = 0; i < selected.length; i++) {
                if (selected[i]["t"] == "d") a = "/";
                else a = "";
                elabq += "&f[]=" + escape(selected[i]["n"] + a)
            }
            $.ajax({
                url: "admin_explorer.html?zn=" + zipname + "&act=zip" + elabq,
                success: function () {
                    show_dir(cdir)
                }
            })
        } else {
            if (popupfilet == "d") a = "/";
            else a = "";
            $.ajax({
                url: "admin_explorer.html?zn=" + zipname + "&act=zip&f[]=" + escape(popupfile + a),
                success: function () {
                    show_dir(cdir)
                }
            })
        }
    }
}
function open_editor() {
    pmenu.CloseMenu();
    if (popupfilet != "d") {
        document.getElementById("winiframe").src = "admin_explorer.html?act=mods&f=" + escape(popupfile);
        do_open()
    }
}
function open_editorm() {
    pmenu.CloseMenu();
    if (popupfilet != "d") {
        document.getElementById("winiframe").src = "admin_explorer.html?act=mod&t=" + popupfilety + "&f=" + escape(popupfile);
        do_open()
    }
}
function unzip() {
    pmenu.CloseMenu();
    a = prompt(l__dir, cdir + "/");
    if (a != null) $.ajax({
        url: "admin_explorer.html?act=uzip&f=" + escape(popupfile) + "&d=" + a,
        cache: false,
        dataType: "json",
        success: function (a) {
            if (a.s == "y") {
                show_dir(cdir)
            }
        }
    })
}
function upload() {
    document.getElementById("winiframe").src = "zone_develop.html?act=upl&d=" + cdir;
    do_open()
}
function new_dir() {
    a = prompt(l__name);
    if (a != null) $.ajax({
        url: "zone_media_man.html?act=newd&f=" + escape(cdir + "/" + a),
        cache: false,
        dataType: "json",
        success: function (a) {
            if (a.s == "y") {
                show_dir(cdir)
            }
        }
    })
}
function new_file() {
    a = prompt(l__name);
    if (a != null) $.ajax({
        url: "admin_explorer.html?act=newf&f=" + escape(cdir + "/" + a),
        cache: false,
        dataType: "json",
        success: function (a) {
            if (a.s == "y") {
                show_dir(cdir)
            }
        }
    })
}
function pmenu_onopen() {
    var a = false;
	if (selected != undefined)
    for (j = 0; j < selected.length; j++) {
        if (selected[j]["n"] == curfile && selected[j]["t"] == tfile) {
            a = true;
            break
        }
    }
    document.getElementById("popedit").style.display = "none";
    document.getElementById("popeditc").style.display = "none";
    document.getElementById("popdown").style.display = "none";
    document.getElementById("popezip").style.display = "none";
    if (!a) {
        selected = new Array;
        popupfile = curfile;
        popupfilet = tfile;
        popupfilety = tyfile;
        if (tfile == "f") {
            document.getElementById("popdown").style.display = "block";
            if (tyfile == "css" || tyfile == "js" || tyfile == "php" || tyfile == "html" || tyfile == "xml") {
                document.getElementById("popedit").style.display = "block";
                document.getElementById("popeditc").style.display = "block"
            } else if (tyfile == "file") {
                document.getElementById("popedit").style.display = "block"
            } else if (tyfile == "zip") {
                document.getElementById("popezip").style.display = "block"
            }
        }
    }
}
function mouse_down(a) {
    if (!pmenu.mouseSelect(a)) {
        sx = ex;
        sy = ey;
        oxx = osx;
        oyy = osy;
    }
}
function dragmove(b) {
    if (document.getElementById && !document.all) {
        osx = b.clientX;
        osy = b.clientY;
        ex = document.body.scrollLeft + osx;
        ey = document.body.scrollTop + osy
    } else {
        ex = event.clientX + document.body.scrollLeft;
        ey = event.clientY + document.body.scrollTop
    }
}
function mvcpf(a) {
    pmenu.CloseMenu();
    if (a) explorer_filelist("movef_end");
    else explorer_filelist("copyf_end");
    if (selected.length > 0) {
        to_elab = "";
        for (i = 0; i < selected.length; i++) {
            if (selected[i]["t"] == "d") a = "/";
            else a = "";
            to_elab += "&f[]=" + escape(selected[i]["n"] + a)
        }
    } else {
        if (popupfilet == "d") a = "/";
        else a = "";
        to_elab = "&f[]=" + escape(popupfile + a)
    }
}
function chmod() {
    pmenu.CloseMenu();
    if (selected.length > 0) {
        elab = "";
        for (i = 0; i < selected.length; i++) {
            if (selected[i]["t"] == "d") a = "/";
            else a = "";
            elab += "&f[]=" + escape(selected[i]["n"] + a)
        }
    } else {
        if (popupfilet == "d") a = "/";
        else a = "";
        elab = "&f[]=" + escape(popupfile + a)
    }
    document.getElementById("winiframe").src = "admin_explorer.html?act=chmod" + elab;
    do_open()
}
function explorer_filelist(a) {
    if (a != null) {
        document.getElementById("winiframe").src = "zone_develop.html?act=dir&d=.&call=" + a;
        do_open()
    } else alert("argument invalid")
}
function shell() {
    document.getElementById("winiframe").src = "admin_explorer.html?act=shell";
    do_open()
}
function copyf_end(a) {
    $.ajax({
        url: "admin_explorer.html?to=" + escape(a) + "&d=" + cdir + "&act=copy" + to_elab,
        success: function () {
            show_dir(cdir)
        }
    })
}
function movef_end(a) {
    $.ajax({
        url: "admin_explorer.html?to=" + escape(a) + "&d=" + cdir + "&act=move" + to_elab,
        success: function () {
            show_dir(cdir)
        }
    })
}
function download() {
    pmenu.CloseMenu();
    window.open("admin_explorer.html?act=down&f=" + popupfile, "Download")
}
function do_open() {
    $("#mywindow").addClass("show")
}
function close_win() {
    $("#mywindow").removeClass("show")
}