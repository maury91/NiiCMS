/*
	Live Edit
	Ultima modifica 11/07/12 (v 0.5)
*/

var link_info = new Array();
var menu_info = new Array();
var lmenu,mmenu,nmmenu,curr_link=-1,curr_menu=-1,new_menu=-1,curr_link_jq,opener='';
var link_rename=false,link_prop=false,menu_conf=false,ch_mod='',ch_page='',ch_com='',pax='a',new_link,nlink_url='',page_attr,ch_t='',ch_type;
var menu_disp = new Array();


function more_urls(mob) {
	$("#smallwindow").show();
	if (mob) extra = '&mob=0'; else extra = '';
	ajax_loadContent('smallsub','admin_menu_extra.html?aj=0'+extra)
}
function PopupMenu(a) {
	function j(b) {
		menu = document.getElementById(a);
		menu.style.display = b;
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
		menu_open();
		menu.style.left = a.left;
		menu.style.top = a.top;
		e = true;
		return false
	};
	this.Displayed = function () {
		return document.getElementById(a).style.display != "none"
	};
}
function live_menu_choose_mod(a) {
	if (ch_mod != '')
		$('#live_mod_'+ch_mod).removeClass('live-elem-choose');
	$('#live_mod_'+a).addClass('live-elem-choose');
	ch_mod = a;
}
function live_elab_coda() {
	old_elab_coda();
	if (coda.length < 1) {
		$('#niis').hide(200);
		if (opener == 'm')
			live_menu_new_mod();
		if (opener == 'c')
			live_link_com();
	}
}
function open_nii_service(a) {			
	ajax_loadContent('live-nii-service',"admin_nii.html?aj=0&live="+a);
	opener=a;
}
function live_menu_make_new_mod(diag) {
	if (ch_mod != '') {
		if (pax == 'a') {
			$("#new-mod-sub").html('<p class="validateTips" id="newmenumod_error"></p><form><fieldset><label for="live_nmd_n">'+__men_name+'</label><input type="text" name="live_nmd_n" id="live_nmd_n" class="text ui-widget-content ui-corner-all" /></fieldset></form>');
			pax = 'b';
		} else {
			//Controllo errori					
			name = $('#live_nmd_n').val();
			if ($.trim(name) == '') 
				notify_error(__no_empty,'newmenumod_error');
			else {
				$.getJSON('admin_live.html?ajax=valid_menu&f1='+new_menu+'&nom='+name, function(d) {
				if(d.r == 'y') {
					//Creazione menu
					$.getJSON('admin_live.html?ajax=make_menu&mod='+ch_mod+'&f1='+new_menu+'&nom='+name, function(d) {
					if (d.r == 'y')
						$.ajax({
							url : 'admin_live.html',
							data : { ajax : 'load_menu', f1 : new_menu, f2 : name},
							cache: false,
							dataType : "html",
							success : function(d) {
								$('.live_new_menu_'+new_menu).before(d);				
								$(".live_menu_r").each(function(i){
									curr_menu=menu_info.length;
									eval('arr = '+$(this).attr('dir'));
									$(this).prepend('<a title="'+__menudesc+'" class="live_link_edit live_menu_edit imgedit hint" href="#" onclick="live_menu_popup('+curr_menu+','+arr.t+')" id="live_menu_'+curr_menu+'"></a>');
									var live_link = $('#live_menu_'+curr_menu);	
									menu_info.push(arr);
									$(this).removeClass('live_link').mouseover(function(){
										live_link.css({visibility:"visible"});
									}).mouseout(function(){live_link.css({visibility:"hidden"});});	
								});
								tool();
							}
						});
					});
					$( diag ).dialog( "close" );
				} else
					notify_error(d.r,'newmenumod_error');
			});
			}
		}
	}
}
function live_menu_new_mod() {
	nmmenu.CloseMenu();
	ch_mod = '';
	pax = 'a';
	$("#new-mod-sub").html('<h2>'+__ch_mod+'</h2><br><div id="live-mods-list" class="live-elems-list"></div>');
	$("#live-mods-list").mousewheel(function(event, delta) {
      this.scrollLeft -= (delta * 250);
	  this.scrollLeft = Math.floor(this.scrollLeft/250)*250;
      event.preventDefault();
   });
	$( "#new-mod" ).dialog( "open" );
	$("#live-mods-list").html("<img src='css/images/live/loader.gif' width='32px' height='32px' class='live-loader'/>");
	//Ottengo la lista dei moduli
	$.ajax({
		url : 'admin_live.html',
		data : {ajax : 'get_mods'},
		cache: false,
		dataType : "json",
		success : function(d)  {
			$("#live-mods-list").html("<div class='live-elem live-hint' title='"+__new_mod_nii+"' onclick='open_nii_service(\"m\")'><a class='cmslivenewelem live-elem-img'></a><a class='live-elem-title'>"+__new_mod_ni+"</a></div>");				
			for (i in d.data) {
				if (d.data[i].i == 'noimg') {
					st='';
					cl='cmsmdnoimg ';
				} else {
					st=" style='background:url(\""+d.data[i].i+"\") no-repeat'";
					cl='';
				}						 
				$("#live-mods-list").append("<div class='live-elem live-hint' title='"+quotes(d.data[i].d)+"' onclick='live_menu_choose_mod(\""+d.data[i].r+"\")' id='live_mod_"+d.data[i].r+"'><a class='"+cl+"live-elem-img' "+st+"></a><a class='live-elem-title'>"+d.data[i].n+"</a></div>");
			}
			$("#live-mods-list").append("<div class='live-elem ghost'></div>");
			make_live_tooltip();
		}
	});
}
function live_link_make(diag) {
	if ($.trim(nlink_url)=='') {
		if (nlink_tip=='p')
			notify_error(__no_user,'nproplink_error');
		else
			notify_error(__no_url,'nproplink_error');
	} else if ($.trim($('#live_nml_n').val()) == '')
		notify_error(__no_empty,'nproplink_error');
	else {
		llng = $("#live_nml_g").val();
		$.ajax({
			url : 'admin_menu.html',
			data : { add : new_link.m, sub : new_link.s, nome : $("#live_nml_n").val(), url : nlink_url, level : $("#live_nml_l").val(), lng : $("#live_nml_g").val() },
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.o == 'y') {
					$(diag).dialog("close");
					//Trovo il menu
					for (i in menu_info)
						if ((menu_info[i].f1 == new_link.m)&&(menu_info[i].f2 == new_link.s)) {
							cmenu = i;
							break;
						}
					//Rilettura menu
					$.ajax({
						url : 'admin_live.html',
						data : { ajax : 'load_menu', f1 : new_link.m, f2 : new_link.s},
						cache: false,
						dataType : "html",
						success : function(d) {
							$('#live_menu_'+cmenu).parent().replaceWith(d);
							var len = $(".live_link_r").length;
							//Controllo che il link inserito sia nella stessa lingua in cui viene mostrata la pagina
							if ((llng != __lang)&&(llng != 'all'))
								len++;
							$(".live_link_r").each(function(i){
								$(this).append('<a title="'+__linkdesc+'" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+i+')" id="live_link_'+i+'"></a>');
								var live_link = $('#live_link_'+i);
								//Modifica dell'array aggiungendo il nuovo link
								eval('arr = '+$(this).attr('dir'));
								if (i == len-1) 								
									link_info.push(arr)								
								$(this).attr('id',arr.i);
								$(this).addClass('live_link_edited').removeClass('live_link_r').mouseover(function(){
									live_link.css({visibility:"visible"});
								}).mouseout(function(){live_link.css({visibility:"hidden"});});	
							});
							$(".live_menu_r").each(function(i){
								eval('arr = '+$(this).attr('dir'));
								$(this).prepend('<a title="'+__menudesc+'" class="live_link_edit live_menu_edit imgedit hint" href="#" onclick="live_menu_popup('+cmenu+','+arr.t+')" id="live_menu_'+cmenu+'"></a>');
								var live_link = $('#live_menu_'+cmenu);
								$(this).sortable({
									items: ".live_link_edited",
									update : function() { live_sorted_links(cmenu,this); }
								}).disableSelection();
								menu_disp[cmenu] = $(this).sortable('toArray');								
								$(this).removeClass('live_menu_r').mouseover(function(){
									live_link.css({visibility:"visible"});
								}).mouseout(function(){live_link.css({visibility:"hidden"});});	
							});
							tool();
						}
					});				
				}
			}
		});
	}
}
function live_link_page(diag) {
	if (ch_page != '') {
		$( diag ).dialog( "close" );
		$( "#new-link-prop" ).dialog( "open" );
		nlink_url=ch_page+'.htm';
		$("#live_nml_g").val('all');
		$("#live_nml_l").val(10);
		$("#live_nml_n").val('');
	}
}
function live_choose_page(a) {
	if (ch_page != '')
		$('#live_npage_'+ch_page).removeClass('live-elem-choose');
	$('#live_npage_'+a).addClass('live-elem-choose');
	ch_page = a;
}
function live_link_url() {
	nlmenu.CloseMenu();
	nlink_url='';
	nlink_tip='u';
	//Scrivi roba QUA
	$('#nlp_extra').html(__l_url);
	$("#new-link-prop").dialog( "open" );
	$("#live_nml_g").val('all');
	$("#live_nml_l").val(10);
	$("#live_nml_n").val('');
}
function live_link_admin() {
	nlmenu.CloseMenu();
	nlink_url='';
	nlink_tip='p';
	//Scrivi roba QUA
	$('#nlp_extra').html(__l_adm);
	$("#new-link-prop").dialog( "open" );
	$("#live_nml_g").val('all');
	$("#live_nml_l").val(10);
	$("#live_nml_n").val('');
}
function live_link_user() {
	nlmenu.CloseMenu();
	nlink_url='';
	nlink_tip='p';
	//Scrivi roba QUA
	$('#nlp_extra').html(__l_page);
	$("#new-link-prop").dialog( "open" );
	$("#live_nml_g").val('all');
	$("#live_nml_l").val(10);
	$("#live_nml_n").val('');
}
function live_link_ext_page() {
	nlmenu.CloseMenu();
	ch_page = '';
	pax = 'a';
	$('#nlp_extra').html('');
	$( "#choose-page" ).dialog( "open" );
	$("#live-page-list").html("<img src='css/images/live/loader.gif' width='32px' height='32px' class='live-loader'/>");
	//Ottengo la lista delle pagine
	$.ajax({
		url : 'admin_live.html',
		data : {ajax : 'get_pages'},
		cache: false,
		dataType : "json",
		success : function(d)  {
			$("#live-page-list").html("<div class='live-elem live-hint' title='"+__new_page_d+"' onclick='live_new_page()'><a class='cmslivenewelem live-elem-img'></a><a class='live-elem-title'>"+__new_page+"</a></div>");
			for (i in d.data) {
				$("#live-page-list").append("<div class='live-elem' onclick='live_choose_page(\""+d.data[i].n+"\")' id='live_npage_"+d.data[i].n+"'><a class='cmslive"+d.data[i].t+" live-elem-img'></a><a class='live-elem-title'>"+d.data[i].n+"</a></div>");
			}
			$("#live-page-list").append("<div class='live-elem ghost'></div>");
			make_live_tooltip();
		}
	});
}
function live_link_com_s(diag) {
	if (ch_com != '') {
		$( diag ).dialog( "close" );
		$( "#new-link-prop" ).dialog( "open" );
		nlink_url='com_'+ch_com+'.html';
		$("#live_nml_g").val('all');
		$("#live_nml_l").val(10);
		$("#live_nml_n").val('');
	}
}
function live_choose_com(a) {
	if (ch_com != '')
		$('#live_com_'+ch_com).removeClass('live-elem-choose');
	$('#live_com_'+a).addClass('live-elem-choose');
	ch_com = a;
}
function live_link_com() {
	nlmenu.CloseMenu();
	ch_com = '';
	$('#nlp_extra').html('');
	$( "#choose-com" ).dialog( "open" );
	//Immagine caricamento
	$("#live-com-list").html("<img src='css/images/live/loader.gif' width='32px' height='32px' class='live-loader'/>");
	//Ottengo la lista dei moduli
	$.ajax({
		url : 'admin_live.html',
		data : {ajax : 'get_coms'},
		cache: false,
		dataType : "json",
		success : function(d)  {
			$("#live-com-list").html("<div class='live-elem live-hint' title='"+__new_com_nii+"' onclick='open_nii_service(\"c\")'><a class='cmslivenewelem live-elem-img'></a><a class='live-elem-title'>"+__new_com_ni+"</a></div>");				
			for (i in d.data) {
				if (d.data[i].i == 'noimg') {
					st='';
					cl='cmsmdnoimg ';
				} else {
					st=" style='background-image:url(\""+d.data[i].i+"\")'";
					cl='';
				}						 
				$("#live-com-list").append("<div class='live-elem live-hint' title='"+quotes(d.data[i].d)+"' onclick='live_choose_com(\""+d.data[i].r+"\")' id='live_com_"+d.data[i].r+"'><a class='"+cl+"live-elem-img' "+st+"></a><a class='live-elem-title'>"+d.data[i].n+"</a></div>");
			}
			$("#live-com-list").append("<div class='live-elem ghost'></div>");
			make_live_tooltip();
		}
	});
}
function live_window_close() {
	$('#livewindow').hide()
	if (menu_conf) {
		menu_conf = false;
		$.ajax({
			url : 'admin_live.html',
			data : { ajax : 'load_menu', f1 : menu_info[curr_menu].f1, f2 : menu_info[curr_menu].f2},
			cache: false,
			dataType : "html",
			success : function(d) {
				$('#live_menu_'+curr_menu).parent().replaceWith(d);						
				$(".live_menu_r").each(function(i){
					eval('arr = '+$(this).attr('dir'));
					$(this).prepend('<a title="'+__menudesc+'" class="live_link_edit live_menu_edit imgedit hint" href="#" onclick="live_menu_popup('+curr_menu+','+arr.t+')" id="live_menu_'+curr_menu+'"></a>');
					var live_link = $('#live_menu_'+curr_menu);
					$(this).sortable({
						items: ".live_link_edited",
						update : function() { live_sorted_links(curr_menu,this); }
					}).disableSelection();
					menu_disp[curr_menu] = $(this).sortable('toArray');
					$(this).removeClass('live_menu_r').mouseover(function(){
						live_link.css({visibility:"visible"});
					}).mouseout(function(){live_link.css({visibility:"hidden"});});	
				});
				tool();
			}
		});
	}			
}
function menu_open() {
	if (link_rename) {
		curr_link_jq.html(link_info[curr_link].nome+'<a title="'+__linkdesc+'" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+curr_link+')" id="live_link_'+curr_link+'"></a>');
		var live_link = $('#live_link_'+curr_link);
		curr_link_jq.mouseover(function(){live_link.css({visibility:"visible"});}).mouseout(function(){live_link.css({visibility:"hidden"});});					
		tool();
		link_rename = false;
	}
	if (link_prop) {
		$('#smallwindow').hide();
		live_window_close();
		link_prop = false;
	}
}
function live_menu() {
	$(".live_link").each(function(i){
		$(this).append('<a title="'+__linkdesc+'" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+i+')" id="live_link_'+i+'"></a>');
		var live_link = $('#live_link_'+i);
		eval('arr = '+$(this).attr('dir'));
		$(this).attr('id',arr.i);
		link_info.push(arr);
		$(this).addClass('live_link_edited').removeClass('live_link').mouseover(function(){
			live_link.css({visibility:"visible"});
		}).mouseout(function(){live_link.css({visibility:"hidden"});});	
	});
	$(".live_menu").each(function(i){
		eval('arr = '+$(this).attr('dir'));
		$(this).prepend('<a title="'+__menudesc+'" class="live_link_edit live_menu_edit imgedit hint" href="#" onclick="live_menu_popup('+i+','+arr.t+')" id="live_menu_'+i+'"></a>');
		var live_link = $('#live_menu_'+i);				
		menu_info.push(arr);
		$(this).sortable({
			items: ".live_link_edited",
			update : function() { live_sorted_links(i,this); }
		}).disableSelection();
		menu_disp.push($(this).sortable('toArray'));
		$(this).removeClass('live_menu').mouseover(function(){
			live_link.css({visibility:"visible"});
		}).mouseout(function(){live_link.css({visibility:"hidden"});});	
	});	
	tool();
}
function live_link_save_prop(diag) {
	if ($.trim($('#live_ml_n').val()) == '')
		notify_error(__no_empty,'proplink_error');
	else
		$.ajax({
			url : 'admin_menu.html',
			data : { edit : link_info[curr_link].f1, sub : link_info[curr_link].f2, nom : link_info[curr_link].i, nome : $("#live_ml_n")[0].value, url : $("#live_ml_h")[0].value, level : $("#live_ml_l")[0].value, lng : $("#live_ml_g")[0].value, class : $("#live_ml_c")[0].value, image : $("#live_ml_i")[0].value },
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.o == 'y') {
					$(diag).dialog("close");
					$('#smallwindow').hide();
					live_window_close();
					link_prop = false;
					$('#live_link_'+curr_link).parent().html($("#live_ml_n")[0].value+'<a title="'+__linkdesc+'" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+curr_link+')" id="live_link_'+curr_link+'"></a>');
					link_info[curr_link].nome = $("#live_ml_n")[0].value;
					var live_link = $('#live_link_'+curr_link);
					$('#live_link_'+curr_link).parent().mouseover(function(){live_link.css({visibility:"visible"});}).mouseout(function(){live_link.css({visibility:"hidden"});});					
					tool();
				}
			}
		});
}
function live_menu_save_prop(diag) {
	newf2 = $("#live_mm_n")[0].value;
	if ($.trim(newf2) == '') 
		notify_error(__no_empty,'propmenu_error');
	else {
		$.getJSON('admin_live.html?ajax=valid_menu&f1='+menu_info[curr_menu].f1+'&nom='+newf2, function(d) {
			if(d.r == 'y') {
				$.ajax({
					url : 'admin_menu.html',
					data : { edit : menu_info[curr_menu].f1, nom : menu_info[curr_menu].f2, nome : newf2, level : $("#live_mm_l")[0].value },
					cache: false,
					dataType: "json",
					success: function(d) {
						if (d.o == 'y') {
							$( diag ).dialog( "close" );
							$('#smallwindow').hide();
							live_window_close();
							link_prop = false;
							//Modifica di tutti gli array con i nuovi valori
							for (i in link_info) {
								if ((link_info[i].f1 == menu_info[curr_menu].f1)&&(link_info[i].f2 == menu_info[curr_menu].f2))
									link_info[i].f2 = newf2;
							}
							menu_info[curr_menu].f2 = newf2;
							//Deve ottenere il nuovo codice
							$.ajax({
								url : 'admin_live.html',
								data : { ajax : 'load_menu', f1 : menu_info[curr_menu].f1, f2 : menu_info[curr_menu].f2},
								cache: false,
								dataType : "html",
								success : function(d) {
									$('#live_menu_'+curr_menu).parent().replaceWith(d);						
									$(".live_link_r").each(function(i){
										$(this).append('<a title="'+__linkdesc+'" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+i+')" id="live_link_'+i+'"></a>');
										var live_link = $('#live_link_'+i);
										eval('arr = '+$(this).attr('dir'));
										$(this).attr('id',arr.i);
										$(this).addClass('live_link_edited').removeClass('live_link_r').mouseover(function(){
										live_link.css({visibility:"visible"});
										}).mouseout(function(){live_link.css({visibility:"hidden"});});	
									});
									$(".live_menu_r").each(function(i){
										eval('arr = '+$(this).attr('dir'));
										$(this).prepend('<a title="'+__menudesc+'" class="live_link_edit live_menu_edit imgedit hint" href="#" onclick="live_menu_popup('+curr_menu+','+arr.t+')" id="live_menu_'+curr_menu+'"></a>');
										var live_link = $('#live_menu_'+curr_menu);	
										$(this).sortable({
											items: ".live_link_edited",
											update : function() { live_sorted_links(curr_menu,this); }
										}).disableSelection();
										menu_disp[curr_menu] = $(this).sortable('toArray');
										$(this).removeClass('live_menu_r').mouseover(function(){
											live_link.css({visibility:"visible"});
										}).mouseout(function(){live_link.css({visibility:"hidden"});});	
									});
									tool();
								}
							});
							/*$("#live_ml_n")[0].value+'<a title="$__linkdesc" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+curr_link+')" id="live_link_'+curr_link+'"></a>');
							link_info[curr_link].nome = $("#live_ml_n")[0].value;
							var live_link = $('#live_link_'+curr_link);
							$('#live_link_'+curr_link).parent().mouseover(function(){live_link.css({visibility:"visible"});}).mouseout(function(){live_link.css({visibility:"hidden"});});		*/			
						}
					}
				});
			} else 
				notify_error(d.r,'propmenu_error');
		});
	}
}		
function live_menu_config() {
	link_prop = true;
	menu_conf = true;
	ajax_loadContent('livesub','admin_live.html?ajax=menu_config&f1='+menu_info[curr_menu].f1+'&f2='+menu_info[curr_menu].f2);
	$("#livewindow").show();
	mmenu.CloseMenu();
}
function live_menu_prop() {
	$.ajax({
		url : 'admin_live.html?ajax=menu_prop',
		data : { f1 : menu_info[curr_menu].f1, f2 : menu_info[curr_menu].f2},
		cache: false,
		dataType: "json",
		success: function(d) {
			$('#live_mm_n').val(d.n);
			$('#live_mm_l').val(d.l);
			$( "#menu-prop" ).dialog( "open" );
			mmenu.CloseMenu();
		}
	});	
}
function live_link_prop() {
	$.ajax({
		url : 'admin_live.html?ajax=link_prop',
		data : { f1 : link_info[curr_link].f1, f2 : link_info[curr_link].f2, i : link_info[curr_link].i },
		cache: false,
		dataType: "json",
		success: function(d) {
			$('#live_ml_n').val(d.n);
			$('#live_ml_h').val(d.h);
			$('#live_ml_l').val(d.l);
			$('#live_ml_g').val(d.g);
			$('#live_ml_c').val(d.c);
			$('#live_ml_i').val(d.i);
			$( "#link-prop" ).dialog( "open" );
			lmenu.CloseMenu();
		}
	});		
}
function live_link_rename_key(e) {
	if (e.keyCode == 13) {
		new_value = $('#live_link_input')[0].value;
		if ($.trim(new_value) == '')
			alert(__no_empty);
		else
			$.ajax({
				url : 'admin_live.html?ajax=link_rnm',
				data : { f1 : link_info[curr_link].f1, f2 : link_info[curr_link].f2, i : link_info[curr_link].i, nome : new_value },
				cache: false,
				dataType: "json",
				success: function(d) {
					if (d.r == 'y') {
						curr_link_jq.html(new_value+'<a title="$'+__linkdesc+'" class="live_link_edit imgedit hint" href="#" onclick="live_link_popup('+curr_link+')" id="live_link_'+curr_link+'"></a>');
						link_info[curr_link].nome = new_value;
						var live_link = $('#live_link_'+curr_link);
						curr_link_jq.mouseover(function(){
							live_link.css({visibility:"visible"});
						}).mouseout(function(){live_link.css({visibility:"hidden"});});					
						tool();
					}
				}
			});				
			return false;
	}
}
function live_link_rename() {
	link_rename = true;
	curr_link_jq = $('#live_link_'+curr_link).parent();
	curr_link_jq.html('<a href="#"><input type="text" id="live_link_input" class="textbox" value="'+link_info[curr_link].nome+'" onkeypress="return live_link_rename_key(event)"></a>');
	lmenu.CloseMenu();
}
function live_menu_popup(a,b) {
	if (b)
		$('.mod_opz').hide();
	else if(menu_info[a].c == 1)
		$('.mod_opz').show();
	else
		$('.mod_opz').hide();
	mmenu.ItemSelMenu($('#live_menu_'+a).offset());
	curr_menu = a;			
}
function live_page_popup() {
	pmenu.ItemSelMenu($('#live_page_e').offset());
	if (page_attr.ty=='p') {
		$('.live_com').hide();
		$('.live_page').show();
		if (page_attr.n == 'home')
			$('.live_page_del').hide();
		if ((page_attr.t!='php')&&(page_attr.t!='link'))
			$('.page_edit').hide();			
		else
			$('.live_conf').hide();
		if (page_attr.b != 1)
			$('.pag_boz').hide();
	} else {
		if (page_attr.b == 1)
			$('.live_com').show();
		else
			$('.live_com').hide();
		$('.live_page').hide();
	}	
}
function live_link_popup(a) {
	lmenu.ItemSelMenu($('#live_link_'+a).offset());
	curr_link = a;
}
function live_new_menu(a) {
	nmmenu.ItemSelMenu($('#live_new_men_'+a).offset());
	new_menu = a;			
}
function live_new_link(a,b) {
	nlmenu.ItemSelMenu($('.live_new_link_'+a+'_'+b).offset());
	new_link = { m : a, s : b };
}
function mouse_down(a) {
	if ((!lmenu.mouseSelect(a))&&(!nlmenu.mouseSelect(a))&&(!mmenu.mouseSelect(a))&&(!nmmenu.mouseSelect(a))&&(!pmenu.mouseSelect(a))) {
	}
} 
function notify_error(t,x) {
	url_error = $( "#"+x );
	url_error.html( t ).addClass( "ui-state-highlight" );
	setTimeout(function() {
		url_error.removeClass( "ui-state-highlight", 1500 );
	}, 500 );
}
function live_menu_make_new_url(diag) {
	name = $('#live_nm_n').val();
	if ($.trim(name) == '') 
		notify_error(__no_empty,'newmenuurl_error');
	else {
		$.getJSON('admin_live.html?ajax=valid_menu&f1='+new_menu+'&nom='+name, function(d) {
			if(d.r == 'y') {
			//Creazione menu
				$.getJSON('admin_live.html?ajax=make_menu&f1='+new_menu+'&nom='+name, function(d) {
					if (d.r == 'y')
						$.ajax({
							url : 'admin_live.html',
							data : { ajax : 'load_menu', f1 : new_menu, f2 : name},
							cache: false,
							dataType : "html",
							success : function(d) {
								$('.live_new_menu_'+new_menu).before(d);				
								$(".live_menu_r").each(function(i){
									curr_menu=menu_info.length;
									eval('arr = '+$(this).attr('dir'));
									$(this).prepend('<a title="'+__menudesc+'" class="live_link_edit live_menu_edit imgedit hint" href="#" onclick="live_menu_popup('+curr_menu+','+arr.t+')" id="live_menu_'+curr_menu+'"></a>');
									var live_link = $('#live_menu_'+curr_menu);	
									menu_info.push(arr);
									$(this).removeClass('live_menu_r').mouseover(function(){
										live_link.css({visibility:"visible"});
									}).mouseout(function(){live_link.css({visibility:"hidden"});});	
								});
								tool();
							}
						});
				});
				$( diag ).dialog( "close" );
			} else
			notify_error(d.r,'newmenuurl_error');
		});
	}
}
function live_start() {
	live_menu();
	lmenu = new PopupMenu("linkmenu");
	nlmenu = new PopupMenu("nlinkmenu");
	mmenu = new PopupMenu("menumenu");
	nmmenu = new PopupMenu("nmenumenu");
	pmenu = new PopupMenu("pagemenu");
	document.onmousedown = mouse_down;
	$(".live-elems-list").mousewheel(function(event, delta) {
      this.scrollLeft -= (delta * 250);
	  this.scrollLeft = Math.floor(this.scrollLeft/250)*250;
      event.preventDefault();
   });
	make_dialogs();
}
function live_page() {
	if (typeof page_attr.tit != 'undefined') {	
		$('#live_pg_t').val(page_attr.tit);
		$('#live_pg_l').val(page_attr.lev);
		$('#live_pg_mtd').val(page_attr.mdesc);
		$('#live_pg_mtt').val(page_attr.mtag);
	}
	$("#live_page").each(function(i){
		$(this).prepend('<a title="'+__pagedesc+'" class="live_link_edit live_page_edit imgedit hint" href="#" onclick="live_page_popup()" id="live_page_e"></a>');
		var live_link = $('#live_page_e');
		$(this).removeClass('live_link').mouseover(function(){
			live_link.css({visibility:"visible"});
		}).mouseout(function(){live_link.css({visibility:"hidden"});});	
	});
	tool();
}
function live_get_page(a) {
	ajax_loadContent('live_page','admin_live.html?page='+a);
}
function live_menu_new_url(a) {
	$( "#new-menu" ).dialog( "open" );
	nmmenu.CloseMenu();
}
function live_page_config() {
	ajax_loadContent('live_page','admin_live_com.html?conf='+page_attr.n);
	pmenu.CloseMenu();
}
function return_to_com() {
	ajax_loadContent('live_page','admin_live.html?page=com_'+page_attr.n+'.html');
}
function live_page_edit() {
	ajax_loadContent('live_page','admin_live.html?edit='+page_attr.n);
	pmenu.CloseMenu();
}
function return_to_page() {
	ajax_loadContent('live_page','admin_live.html?page='+page_attr.n+'.htm');
}
function live_page_boz() {
	ajax_loadContent('live_page','admin_live.html?bak=0&edit='+page_attr.n);
	pmenu.CloseMenu();
}
function del_i_link(x) {
	//Questo è da migliorare
	$('#live_link_'+x).parent().remove();
	//Correzione di tutti gli altri link
	for (i in link_info) {
		if ((i != x)&&(link_info[i].f1 == link_info[x].f1)&&(link_info[i].f2 == link_info[x].f2)&&(link_info[i].i > link_info[x].i))
			link_info[i].i--;
	}
	link_info.splice(x,1);
}
function del_correlate_link(a) {
	for (i in link_info) {
		if (link_info[i].h == a) {
			$.ajax({
				url : 'admin_menu.html',
				data : { del : link_info[i].f1, sub : link_info[i].f2, nom : link_info[i].i},
				cache: false,
				dataType: "json"
			});
			del_i_link(i);
			i = 0;
		}
	}
}
function live_new_page() {
	$( "#new-page" ).dialog( "open" );
}
function live_make_page(diag) {
	if (ch_type != '') {
		$( diag ).dialog( "close" );
		$('#new-page-n').dialog("open");
	}
}
function live_page_make_new(diag) {
	name = $('#live_np_n').val();
	if ($.trim(name) == '') 
		notify_error(__no_empty,'newpagen_error');
	else {
		$.getJSON('admin_live.html?ajax=make_page&n='+name+'&t='+ch_type, function(d) {
			if(d.r == 'y') {
				//Creazione pagina				
				$( diag ).dialog( "close" );
				$('#choose-page').dialog( "close" );
				nlink_url=name+'.htm';
				$( "#new-link-prop" ).dialog( "open" );
				$("#live_nml_g").val('all');
				$("#live_nml_l").val(10);
				$("#live_nml_n").val('');
				ajax_loadContent('live_page','admin_live.html?edit='+name);
				page_attr = {b: 0,n: name,t: ch_type,ty: "p"};
			} else
			notify_error(d.r,'newpagen_error');
		});
	}
	
}
function choose_type(a,b) {
	ch_type=a;
	if (ch_t != '')
		$(ch_t).removeClass('live-elem-choose');
	$(b).addClass('live-elem-choose');
	ch_t = b;
}
function live_change_type(diag) {
	$.getJSON('admin_pages.html?live=0&pchn='+page_attr.n+'&type='+ch_type, function(d) {
		if(d.r == 'y') {
			$( diag ).dialog( "close" );
			//Ricarica pagina
			return_to_page();
		} else	alert(d.r);
	});
}
function live_page_type() {
	pmenu.CloseMenu();
	if ((page_attr.t=='php')||(page_attr.t=='link'))
		alert(__lost_if_proced);
	if ((page_attr.t=='mhtml')||(page_attr.t=='msimple'))
		alert(__slost_if_proced);
	$('#live-choose-type .live-elem').show();
	$('#live-choose-type #live_npage_t_'+page_attr.t).hide();
	$('#choose-type').dialog( "open" );
}
function live_page_backup() {
	ajax_loadContent('old-versions','admin_live.html?ajax=old_baks&sbak='+page_attr.n);
	pmenu.CloseMenu();
	$('#show-backups').dialog( "open" );	
}
function load_backup(a) {
	ajax_loadContent('live_page','admin_live.html?rbak='+a+'&edit='+page_attr.n);
	$('#show-backups').dialog( "close" );
}
function live_sorted_links(k,a) {
	arr = $(a).sortable('toArray');
	v = new Array();
	for (i in arr) {	
		cont = true;
		for (j=0;j<v.length;j++)  {
			if (arr[i] == v[j]) {
				cont = false;
				break;
			}
		}
		if (cont)
		if (arr[i] != menu_disp[k][i]) {
			v.push(arr[i]);
			v.push(menu_disp[k][i]);
		}
	}
	$.ajax({
		url : 'admin_live.html',
		data : { ajax : 'order_links',f1 : menu_info[k].f1,f2 : menu_info[k].f2,order : v.join(',')},
		cache:false,
		dataType:'json'
	});
	for (j=0;j<v.length;j+=2) {		
		$(a).find('#'+v[j]).attr('id',v[j+1]+'_mom');
		$(a).find('#'+v[j+1]).attr('id',v[j]);
		$(a).find('#'+v[j+1]+'_mom').attr('id',v[j+1]);
	}
	
}
function live_page_options() {
	$('#page-prop').dialog('open');
	pmenu.CloseMenu();
}
function save_live_page_sub(data) {
	$.ajax({
		url : 'admin_pages.html',
		data : { title: $('#live_pg_t').val(), lev: $('#live_pg_l').val() , meta_desc: $('#live_pg_mtd').val(), meta_tags: $('#live_pg_mtt').val(),lng: page_attr.lng, text_name: "mydata", modf: page_attr.n, page_mydata : data, live : 'y'},
		dataType : 'json',
		type : 'post',
		success : function (d) {
			if (d.r=='y')
				alert(__saved);
		}
		
	})
}