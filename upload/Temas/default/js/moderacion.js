var mod = {
    posts : {
        view: function(pid){
			$.ajax({
				type: 'post',
				url: global_data.url + '/moderacion-posts.php?do=view',
				data: 'postid=' + pid,
				success: function(r) {
        			mydialog.class_aux = 'preview';
        			mydialog.show(true);
        			mydialog.title('...');
					mydialog.body(r);
					mydialog.buttons(true, true, 'Cerrar', 'close', true, false);
					mydialog.center();
				}
			});
        },
        // BORRAR
        borrar:function(pid, redirect, aceptar){
        	if(!aceptar){
            	$.ajax({
            		type: 'POST',
            		url: global_data.url + '/moderacion-posts.php?do=borrar',
            		success: function(h){
            			mydialog.show();
            			mydialog.title('Borrar Post');
            			mydialog.body(h);
            			mydialog.buttons(true, true, 'Borrar', 'mod.posts.borrar(' + pid + ", '" + redirect + "', 1);", true, false, true, 'Cancelar', 'close', true, true);
                        $('#modalBody').css('padding', '20px 10px 0');
            			mydialog.center();
            			return;	  
            		}
            	});
        	} else {
            	mydialog.procesando_inicio('Eliminando...', 'Borrar Post');
                var razon = $('#razon').val()
                var razon_desc = $('input[name=razon_desc]').val();
            	$.ajax({
            		type: 'POST',
            		url: global_data.url + '/moderacion-posts.php?do=borrar',
            		data: 'postid=' + pid + '&razon=' + razon + '&razon_desc=' + razon_desc,
            		success: function(h){
            			switch(h.charAt(0)){
            				case '0': //Error
            					mydialog.alert('Error', h.substring(3));
            					break;
            				case '1':
                                    if(redirect == 'true') mod.redirect("/moderacion/posts", 1200);
                                    else if(redirect == 'posts') {
                                        mydialog.alert('Aviso', h.substring(3));
                                        mod.redirect("/posts/", 2000);
                                    } 
                                    else {
                                        mydialog.close();
                                        $('#report_' + pid).slideUp();   
                                    }
            					break;
            			}
            		},
            		complete: function(){
            			mydialog.procesando_fin();
            		}
            	});
            }
        },
    },
    users: {
        action: function(uid, action, redirect){
            var btn_txt = (action == 'aviso') ? 'Enviar' : 'Suspender';
            var titulo = (action == 'aviso') ? 'Enviar Aviso/Alerta' : 'Suspender usuario';
            //
            mod.load_dialog('/moderacion-users.php?do=' + action, 'uid=' + uid, titulo, btn_txt, 'mod.users.set_' + action + '(' + uid + ', ' + redirect + ');');
        },
        set_aviso: function(uid, redirect){
            var av_type = $('#mod_type').val();
            var av_subject = $('#mod_subject').val();
            var av_body = $('#mod_body').val();
            //
            mod.send_data('/moderacion-users.php?do=aviso', 'uid=' + uid + '&av_type=' + av_type + '&av_subject=' + av_subject + '&av_body=' + av_body, uid, redirect);
        },
        set_ban: function(uid, redirect){
            var b_time = $('#mod_time').val();
            var b_cant = $('#mod_cant').val();
            var b_causa = $('#mod_causa').val();
            //
            mod.send_data('/moderacion-users.php?do=ban', 'uid=' + uid + '&b_time=' + b_time + '&b_cant=' + b_cant + '&b_causa=' + b_causa, uid, "'" + redirect + "'");
        }
    },
    load_dialog: function(url_get, url_data, titulo, btn_txt, fn_txt){
    	$.ajax({
    		type: 'POST',
    		url: global_data.url + url_get,
            data: url_data,
    		success: function(h){
    			mydialog.show();
    			mydialog.title(titulo);
    			mydialog.body(h);
    			mydialog.buttons(true, true, btn_txt, fn_txt, true, false, true, 'Cancelar', 'close', true, true);
    		}, complete: function(){
    		  mydialog.center();
    		}
    	});
    },
    send_data: function(url_post, url_data, id, redirect){
    	mydialog.procesando_inicio('Procesando...', 'Espere');
    	$.ajax({
    		type: 'POST',
    		url: global_data.url + url_post,
    		data: url_data,
    		success: function(h){
    			switch(h.charAt(0)){
    				case '0': //Error
    					mydialog.alert('Error', h.substring(3));
    					break;
    				case '1':
                        mydialog.alert('Aviso', h.substring(3));
                        if(redirect == 'true') mod.redirect("/moderacion/" + type, 1200);
                        else if(redirect == 'false') $('#report_' + id).slideUp(); 
    					break;
    			}
    		},
    		complete: function(){
    			mydialog.procesando_fin();
    		}
    	});
    },
    reboot: function(id, type, hdo, redirect){
		$.ajax({
			type: 'post',
			url: global_data.url + '/moderacion-' + type +'.php?do=' + hdo,
			data: 'id=' + id,
			success: function(h) {
                switch(h.charAt(0)){
                    case '0':
                        mydialog.alert("Error", h.substring(3));
                    break;
                    case '1':
                        mydialog.alert("Aviso", '<div class="dialog_box">' + h.substring(3) + '</div>');
                        if(redirect) if(redirect) mod.redirect("/moderacion/" + type, 1200);
                        else $('#report_' + id).slideUp();
                    break;
                }
			}
		});
    },
    redirect: function(url_ref, time){
        setTimeout(function(){document.location.href = global_data.url + url_ref;}, time)
    }
}