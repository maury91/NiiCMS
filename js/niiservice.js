var nii_history = [], nii_cur_history=-1;
var __langs;
function nii_install_ext() {
	that=this;
	$.ajax({
		url : 'admin_nii.html',
		data : {add_install:$(this).data('i')},
		dataType : 'json',
		success : function() {
			//Aggiamo sul bottone chiamante
			parent=$(that).parent();
			$(that).remove();
			
		}
	});
}
function nii_uninstall_ext() {
}
function nii_installer_exec() {
	$.ajax({
		url : 'admin_nii.html',
		data : {process_install:'x'},
		dataType : 'json',
		success : function(r) {
			if (r.exec) {
			}
		},
		complete : function() {
			if ($('.NiiService').length)
				setTimeout(nii_installer_exec,500);
		}
	});
}
function load_niiservice() {
	$('.NiiButton.NiiHome').click(nii_load_home);
	$('.NiiPrev').click(nii_history_back);
	$('.NiiNext').click(nii_history_front);
	$(".NiiSideBar li").each(function() { $(this).data("g",$(this).attr("class")[0]);}).click(nii_open_group_ext);
	nii_installer_exec();
}
function nii_load_home() {
	if (nii_history[nii_cur_history].url != 'home') {
		$('#NiiLoad').load('admin_nii.html?page=home');
	}
}
function nii_load_iframe() {
	size_em = $('#NiiLoad').html('<iframe id="nii_frame"></iframe>').css('font-size');
	size_em = size_em.substr(0,size_em.length-2);
	$('#nii_frame').attr('src','http://niicms.net/service/?login&lang='+__langs.name+'&size='+size_em);
}
function nii_history_back() {
	if (nii_cur_history>0) {
		nii_cur_history--;
		nii_history[nii_cur_history].f.apply(this,nii_history[nii_cur_history].p);
		$('.NiiNext').removeClass('inactive');
	}
	if (nii_cur_history<1)
		$('.NiiPrev').addClass('inactive');
}
function nii_history_front() {
	if (nii_cur_history<nii_history.length-1) {
		nii_cur_history++;
		nii_history[nii_cur_history].f.apply(this,nii_history[nii_cur_history].p);
		$('.NiiPrev').removeClass('inactive');
	}
	if (nii_cur_history==nii_history.length-1)
		$('.NiiNext').addClass('inactive');
}
function nii_add_history(set) {
	nii_cur_history++;
	nii_history.splice(nii_cur_history,nii_history.length-nii_cur_history,set);
	$('.NiiNext').addClass('inactive');
	if (nii_cur_history>0)
		$('.NiiPrev').removeClass('inactive');
	else
		$('.NiiPrev').addClass('inactive');
}
function nii_dep_name(s)
{
	s=s.split('/')[1];
    return s.charAt(0).toUpperCase() + s.slice(1);
}
function nii_load_group_ext_r(response) {
	ext = $('#NiiLoad .NiiGroup');
	for (j in response.exts) {	
		my_ext = $('<div></div>').addClass('NiiExt').click(nii_open_ext).data('id',response.exts[j].id);
		img = $('<span></span>').addClass('NiiImg').appendTo(my_ext);
		if (response.exts[j].icon != '')
			img.addClass('NiiPic').css('background-image','url(http://service.niicms.net/'+response.exts[j].icon+')');
		else if (response.exts[j].pic != '')
			img.css('background-image','url(http://service.niicms.net/'+response.exts[j].pic+')');
		else img.addClass('NiiNoImg');
		my_ext
			.append($('<span></span>').addClass('NiiTitle').text(response.exts[j].nome).append($('<i></i>').text('v. '+response.exts[j].v)))
			.append($('<span></span>').addClass('NiiType').text(response.exts[j].type_l));
		if (response.exts[j].price > 0) 
			my_ext.append($('<span></span>').addClass('NiiPrice').html(response.exts[j].price+' &euro;'));
		else
			my_ext.append($('<span></span>').addClass('NiiPrice').text(__langs.free));
		stars = $('<div></div>').addClass('NiiStars').appendTo(my_ext);
		for (k=0;k<5;k++) {
			star = $('<span></span>').addClass('NiiStar').appendTo(stars);
			if (k<response.exts[j].stars)
				star.addClass('on');
			else
				star.addClass('off');
		}
		ext.append(my_ext);
		if (response.remains)
			$('#NiiLoad a.NiiMore').show().data({'pg':response.page,'g':response.group});
		else
			$('#NiiLoad a.NiiMore').hide();
	}
}

function nii_load_group_np() {
	console.log(this);
	pg = parseInt($(this).data('pg'))+1;
	$.ajax({
		url : 'admin_nii.html',
		data : {group_ext : $(this).data('g'), page : pg},
		dataType : 'json',
		success : function(r) {
			if (r.found) 
				nii_load_group_ext_r(r);
			else
				alert('Error!');
		}
	});
}

function nii_load_group_ext(response,button,__history) {
	console.log(arguments);
	$('.NiiButton').removeClass('selected');
	$('.NiiSideBar li').removeClass('selected');	
	if (!__history)
		nii_add_history({url:'extgroup_'+response.group,f:nii_load_group_ext,p:[response,button,true]});
	$('.NiiSideBar li.'+button).addClass('selected');
	$('#NiiLoad').html('');
	ext = $('<div></div>').addClass('NiiGroup NiiLarge').appendTo('#NiiLoad');
	$('#NiiLoad').append($('<div></div>').addClass('NiiButtonSet').append($('<a></a>').addClass('NiiMore').text(__langs.more).button().click(nii_load_group_np)));
	nii_load_group_ext_r(response);
}
function nii_load_ext(response,__history) {
	$('.NiiButton').removeClass('selected');
	$('.NiiSideBar li').removeClass('selected');
	if (!__history)
		nii_add_history({url:'ext_'+response.r.info.id,f:nii_load_ext,p:[response,true]});
	$('#NiiLoad').html('');
	my_ext_head = $('<div></div>').addClass('NiiExtHead').appendTo('#NiiLoad');
	if (response.r.info.pic != '')
		$(my_ext_head).append($('<span></span>').addClass('NiiImg NiiPic').css('background-image','url(http://service.niicms.net/'+response.r.info.pic+')'));
	stars = $('<div></div>').addClass('NiiStars').appendTo(my_ext);
	for (k=0;k<5;k++) {
		star = $('<span></span>').addClass('NiiStar').appendTo(stars);
		if (k<response.r.info.stars)
			star.addClass('on');
		else
			star.addClass('off');
	}
	button = $('<a></a>');
	if (response.p)
		button.text(response.l.instd).click(nii_uninstall_ext);
	else
		button.text(response.l.instl).click(nii_install_ext).data('i',response.r.info.id);
	button.button();
	$(my_ext_head)
		.append($('<div></div>').addClass('NiiRightPic')
			.append($('<span></span>').addClass('NiiTitle').text(response.r.info.nome).append($('<i></i>').text('v. '+response.r.info.v)))
			.append(stars)
			.append(button));
	desc = $('<div></div>').addClass('NiiDesc').appendTo('#NiiLoad');
	if ((response.r.info[response.l.name] != '')&&(response.r.info[response.l.name] != null))
		desc.text(response.r.info[response.l.name]);
	else
		desc.text(response.r.info.desc);
	if (response.r.info.image != '')
		desc.addClass('WithImage').append($('<span></span>').addClass('NiiImg').css('background-image','url(http://service.niicms.net/'+response.r.info.image+')'));	
	if (response.r.info.extra != '')
		$('#NiiLoad')
			.append($('<div></div>').append($('<h3></h3>').text(response.l[response.r.info.t])).append($('<p></p>').html(response.r.info.extra.replace(/&nbsp;/gi,'').replace(/\n/g,'<br/>'))));
	if (response.r.info.dep != '') {
		deps = response.r.info.dep.split(';');
		d_deps = $('<div></div>').append($('<h3></h3>').text(response.l.dep)).appendTo('#NiiLoad');
		for (i in deps)
			d_deps.append($('<a></a>').click(nii_open_ext).data('id',deps[i]).text(nii_dep_name(deps[i])));
	}
	$('#NiiLoad')
		.append($('<div></div>').addClass('NiiBy').text(response.l.by+' ').append($('<a></a>').attr({href:response.r.info.site,target:'_blank'}).text(response.r.info.author)));
	//
	
}
function nii_open_ext() {
	$.ajax({
		url : 'admin_nii.html',
		data : {ext : $(this).data('id')},
		dataType : 'json',
		success : function(r) {
			if (r.r.found)
				nii_load_ext(r);
			else
				alert('Error!');
		}
	});
	
}
function nii_open_group_ext() {
	that=this;
	$.ajax({
		url : 'admin_nii.html',
		data : {group_ext : $(this).data('g')},
		dataType : 'json',
		success : function(r) {
			if (r.found) 
				nii_load_group_ext(r,$(that).removeClass('selected').attr('class'));
			else
				alert('Error!');
		}
	});
}
function nii_load_first_page(response,__lang,__history) {
	__langs = __lang;
	$('.NiiButton,.NiiSideBar li').removeClass('selected');
	$('.NiiButton.NiiHome').addClass('selected');
	if (!__history)
		nii_add_history({url:'home',f:nii_load_first_page,p:[response,__lang,true]});
	if (response.response) {
		if (response.update) {
			//Bla bla update
		}
		$('#NiiLoad').html('');
		for (i in response.extentions) {
			ext = $('<div></div>').addClass('NiiGroup').append($('<p></p>').text(response.extentions[i].name)).appendTo('#NiiLoad');
			for (j in response.extentions[i].exts) {
				my_ext = $('<div></div>').addClass('NiiExt').click(nii_open_ext).data('id',response.extentions[i].exts[j].id);
				img = $('<span></span>').addClass('NiiImg').appendTo(my_ext);
				if (response.extentions[i].exts[j].icon != '')
					img.addClass('NiiPic').css('background-image','url(http://service.niicms.net/'+response.extentions[i].exts[j].icon+')');
				else if (response.extentions[i].exts[j].pic != '')
					img.css('background-image','url(http://service.niicms.net/'+response.extentions[i].exts[j].pic+')');
				else img.addClass('NiiNoImg');
				my_ext
					.append($('<span></span>').addClass('NiiTitle').text(response.extentions[i].exts[j].name).append($('<i></i>').text('v. '+response.extentions[i].exts[j].v)))
					.append($('<span></span>').addClass('NiiType').text(response.extentions[i].exts[j].type_l));
				if (response.extentions[i].exts[j].price > 0) 
					my_ext.append($('<span></span>').addClass('NiiPrice').html(response.extentions[i].exts[j].price+' &euro;'));
				else
					my_ext.append($('<span></span>').addClass('NiiPrice').text(__lang.free));
				stars = $('<div></div>').addClass('NiiStars').appendTo(my_ext);
				for (k=0;k<5;k++) {
					star = $('<span></span>').addClass('NiiStar').appendTo(stars);
					if (k<response.extentions[i].exts[j].stars)
						star.addClass('on');
					else
						star.addClass('off');
				}
				ext.append(my_ext);			
			}
		}
	} else nii_load_iframe();
}