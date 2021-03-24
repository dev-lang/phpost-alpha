<?php
/********************************************************************************
* c.fotos.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/


/*

	CLASE CON LOS ATRIBUTOS Y METODOS PARA LOS POSTS
	
*/
class tsFotos extends tsDatabase {

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsFotos();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								PUBLICAR FOTOS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		newFoto()
	*/
	function newFoto(){
		global $tsdb, $tsCore, $tsUser, $tsMonitor, $tsActividad;
		//
		$fData = array(
            'titulo' => $tsCore->setSecure($_POST['titulo']),
            'foto' => array('url' => $tsCore->setSecure($_POST['url']), 'file' => $_FILES['file']),
            'desc' => $tsCore->setSecure($_POST['desc']),
            'privada' => empty($_POST['privada']) ? 0 : 1,
            'closed' => empty($_POST['closed']) ? 0 : 1,
        );
        // COMPROBAR CAMPOS
        if(empty($fData['titulo'])) $error['titulo'] = 'true';
        // SE PERMITE SUBIDA DE ARCHIVOS?
        if($tsCore->settings['c_allow_upload'] == 1){
            if(empty($fData['foto']['url']) && empty($fData['foto']['file']['name'])) return 'No has seleccionado ningun archivo.';
        } else {
            if(empty($fData['foto']['url'])) return 'No has ingresado ninguna URL.';
        }
        // UPLOAD
        require('c.upload.php');
        $tsUpload =& tsUpload::getInstance();
        $tsUpload->image_scale = true;
        // HACER        
        if($tsCore->settings['c_allow_upload'] == 1 && $fData['foto']['file']['name'] != '') $result = $tsUpload->newUpload(1);
        else {
            $tsUpload->file_url = $fData['foto']['url'];
            $result = $tsUpload->newUpload(2);
        }
        //
        if($result[0][0] == 0) return $result[0][1];
        else{
            $img_url = $result[0][1];
            if(empty($img_url)) return 'Lo sentimos ocurri&oacute; un error al subir la imagen.';
            $date = time();
            //
            $fields = "f_title, f_date, f_description, f_url, f_user, f_access, f_closed, f_last";
            $values = "'{$fData['titulo']}', {$date}, '{$fData['desc']}', '{$img_url}', {$tsUser->uid}, {$fData['privada']}, {$fData['closed']}, 1";
            // INSERTAMOS
            $tsdb->update("f_fotos","f_last = 0","f_user = {$tsUser->uid} AND f_last = 1"); // LA ULTIMA DEJA DE SERLO
            if($tsdb->insert("f_fotos",$fields, $values)) {
                $fid = $tsdb->insert_id();
                // UPDATES
                $tsdb->update("w_stats","stats_fotos = stats_fotos + 1","stats_no = 1");
                $tsdb->update("u_miembros","user_fotos = user_fotos + 1","user_id = {$tsUser->uid}");
				// AGREGAR AL MONITOR DE LOS USUARIOS QUE ME SIGUEN
				$tsMonitor->setFollowNotificacion(10, 1, $tsUser->uid, $fid);
                // ACTIVIDAD
                $tsActividad->setActividad(9, $fid);
                //
                return $fid;
            }
            else die($tsdb->error());
        }        
        
        
	}
    /*
        getFotoEdit()
    */
    function getFotoEdit(){
        global $tsdb, $tsCore, $tsUser;
        //
        $fid = $tsCore->setSecure($_GET['id']);
        // DATOS
        $query = $tsdb->select("f_fotos","*","foto_id = {$fid}");
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        if(!empty($data['f_user'])){
            // ES EL DUEÑO DE LA FOTO?
            if($data['f_user'] == $tsUser->uid){
                return $data;
            } else return 'La foto que intentas editar no es tuya.';
        } else return 'La foto que intentas editar no existe.';
    }
    /*
        editFoto()
    */
    function editFoto(){
        global $tsdb, $tsCore, $tsUser;
        //
        $fid = $tsCore->setSecure($_GET['id']);
        // DATOS
        $query = $tsdb->query("SELECT f.f_user, u.user_name FROM f_fotos AS f LEFT JOIN u_miembros AS u ON f.f_user = u.user_id WHERE f.foto_id = {$fid}");
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        if(!empty($data['f_user'])){
            // ES EL DUEÑO DE LA FOTO?
            if($data['f_user'] == $tsUser->uid){
        		$fData = array(
                    'titulo' => $tsCore->setSecure($_POST['titulo']),
                    'desc' => $tsCore->setSecure($_POST['desc']),
                    'privada' => empty($_POST['privada']) ? 0 : 1,
                    'closed' => empty($_POST['closed']) ? 0 : 1,
                );
                // UPDATES
                $tsdb->update("f_fotos","f_title = '{$fData['titulo']}', f_description = '{$fData['desc']}', f_access = {$fData['privada']}, f_closed = {$fData['closed']}","foto_id = {$fid}");
                // REDIRIGIMOS
                $url = $tsCore->settings['url'].'/fotos/'.$data['user_name'].'/'.$fid.'/'.$tsCore->setSEO($fData['titulo']).'.html';
                //
                $tsCore->redirectTo($url);
            } else return 'La foto que intentas editar no es tuya.';
        } else return 'La foto que intentas editar no existe.';
    }
    /*
        delFoto()
    */
    function delFoto(){
        global $tsdb, $tsCore, $tsUser;
        //
        $fid = $tsCore->setSecure($_POST['fid']);
        // DATOS
        $query = $tsdb->select("f_fotos","f_user","foto_id = {$fid}");
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        if(!empty($data['f_user'])){
            // ES EL DUEÑO DE LA FOTO?
            if($data['f_user'] == $tsUser->uid || $tsUser->is_admod){
                if($tsdb->delete("f_fotos","foto_id = {$fid}")){
                    // BORRAMOS LOS COMENTARIOS
                    $tsdb->delete("f_comentarios","c_foto_id = {$fid}");
                    // UPDATES
                    $tsdb->update("u_miembros","user_fotos = user_fotos - 1","user_id = {$data['f_user']}");
                    return '1: OK';
                }
            } else return '0: Esta no es tu foto.';
        } else return '0: La foto no existe.';
    }
    /*
        getLastFotos()
    */
    function getLastFotos(){
        global $tsdb;
        //
        $query = $tsdb->query("SELECT f.foto_id, f.f_title, f.f_date, f.f_description, f.f_url, u.user_name FROM f_fotos AS f LEFT JOIN u_miembros AS u ON u.user_id = f.f_user WHERE f.f_status = 0 ORDER BY f.foto_id DESC LIMIT 10");
        $data = $tsdb->fetch_array($query);
        $tsdb->free($query);
        //
        return $data;
    }
    /*
        getLastComments()
    */
    function getLastComments(){
        global $tsdb;
        //
        $query = $tsdb->query("SELECT c.cid, c.c_user, f.foto_id, f.f_title, u.user_name FROM f_comentarios AS c LEFT JOIN f_fotos AS f ON c.c_foto_id = f.foto_id LEFT JOIN u_miembros AS u ON f.f_user = u.user_id WHERE f.f_status = 0 ORDER BY c.c_date DESC LIMIT 10");
        $data = $tsdb->fetch_array($query);
        $tsdb->free($query);
        //
        return $data;
    }
    /*
        getFotos($user_id)
    */
    function getFotos($user_id){
        global $tsdb, $tsCore;
        //
        $query = "SELECT f.foto_id, f.f_title, f.f_date, f.f_description, f.f_url, u.user_name FROM f_fotos AS f LEFT JOIN u_miembros AS u ON u.user_id = f.f_user WHERE f.f_status = 0 AND f.f_user = {$user_id} ORDER BY f.foto_id DESC";
        // PAGINAR
        $total = $tsdb->num_rows($tsdb->query($query));
        $pages = $tsCore->getPagination($total, 12);
        $data['pages'] = $pages;
        //
        $data['data'] = $tsdb->fetch_array($tsdb->query($query.' LIMIT '.$pages['limit']));
        $tsdb->free($query);
        //
        return $data;
    }
    /*
        getFoto()
    */
    function getFoto(){
        global $tsdb, $tsCore, $tsUser;
        //
        $fid = $tsCore->setSecure($_GET['fid']);
        // MORE FOTOS
        $query = $tsdb->query("SELECT f.*, u.user_name, p.user_pais, p.user_sexo, u.user_rango, u.user_fotos, u.user_foto_comments, r.r_name, r.r_color, r.r_image FROM f_fotos AS f LEFT JOIN u_miembros AS u ON u.user_id = f.f_user LEFT JOIN u_perfil AS p ON p.user_id = u.user_id LEFT JOIN u_rangos AS r ON u.user_rango = r.rango_id WHERE f.f_status = 0 AND f.foto_id = {$fid} LIMIT 1");
        $data['foto'] = $tsdb->fetch_assoc($query);
        $data['foto']['f_description'] = $tsCore->parseSmiles($data['foto']['f_description']);
        $tsdb->free($query);
        include('../ext/datos.php');
        $pais = $data['foto']['user_pais'];
        $data['foto']['user_pais'] = array($pais, $tsPaises[$pais]);
        // FOLLOW
        $query = $tsdb->select("u_follows","follow_id","f_user = {$tsUser->uid} AND f_id = {$data['foto']['f_user']} AND f_type = 1 LIMIT 1");
        $data['foto']['follow'] = $tsdb->num_rows($query);
        $tsdb->free($query);
        // SEGUIDORES
        $query = $tsdb->query("SELECT f.f_id, p.foto_id, p.f_title, p.f_url, u.user_name FROM u_follows AS f LEFT JOIN f_fotos AS p ON f.f_id = p.f_user LEFT JOIN u_miembros AS u ON p.f_user = u.user_id WHERE f.f_user = {$data['foto']['f_user']} AND f.f_type = 1 AND u.user_fotos > 0 AND p.f_last = 1 LIMIT 5");
        $data['amigos'] = $tsdb->fetch_array($query);
        $tsdb->free($query);
        // ULTIMAS FOTOS
        $query = $tsdb->query("SELECT f.foto_id, f.f_title, f.f_date, f.f_url, u.user_name FROM f_fotos AS f LEFT JOIN u_miembros AS u ON u.user_id = f.f_user WHERE f.f_status = 0 AND f.f_user = {$data['foto']['f_user']} ORDER BY f.foto_id DESC LIMIT 5");
        $data['last'] = $tsdb->fetch_array($query);
        $tsdb->free($query);
        // COMENTARIOS
        $query = $tsdb->query("SELECT c.*, u.user_name FROM f_comentarios AS c LEFT JOIN u_miembros AS u ON c.c_user = u.user_id WHERE c.c_foto_id = {$fid}");
        $comments = $tsdb->fetch_array($query);
        foreach($comments as $key => $val){
            $val['c_body'] = $tsCore->parseSmiles($val['c_body']);
            $data['comments'][] = $val;
        }
        $tsdb->free($query);
        // UPDATES
        $tsdb->update("f_fotos","f_hits = f_hits + 1","foto_id = {$fid}");
        //
        return $data;
    }
    /*
        votarFoto()
    */
    function votarFoto(){
        global $tsdb, $tsCore, $tsUser;
        // SOLO MIEMBROS
		if($tsUser->is_member){
			// VOTAR
			$fid = $tsCore->setSecure($_POST['fotoid']);
			$voto = $tsCore->setSecure($_POST['voto']);
			$voto = ($voto == 'pos') ? "f_votos_pos = f_votos_pos + 1" : "f_votos_neg = f_votos_neg + 1";
			//
			$query = $tsdb->select("f_fotos","f_user","foto_id = {$fid}","",1);
			$data = $tsdb->fetch_assoc($query);
			$tsdb->free($query);
			// ES MI COMENTARIO?
			$is_mypost = ($data['f_user'] == $tsUser->uid) ? true : false;
			// NO ES MI COMENTARIO, PUEDO VOTAR
			if(!$is_mypost){
				// YA LO VOTE?
				$query = $tsdb->select("f_votos","vid","v_foto_id = {$fid} AND v_user = {$tsUser->uid}","",1);
				$votado = $tsdb->num_rows($query);
				$tsdb->free($query);
				if(empty($votado)){
					// SUMAR VOTO
					$tsdb->update("f_fotos","{$voto}","foto_id = {$fid}");
					// INSERTAR EN TABLA
					if($tsdb->insert("f_votos","v_foto_id, v_user","{$fid}, {$tsUser->uid}")){
						// AGREGAR AL MONITOR
						//$tsMonitor->setNotificacion($data['c_user'], 11, $tsUser->uid, $post_id, $cid, $votoVal);
					}
					//
					return '1: Votado';
				} return '0: Ya has votado esta foto.';
			} else return '0: No puedes votar tu propia foto.';
		} else return '0: Lo sentimos, para poder votar debes estar registrado.';
    }
    /************ COMENTARIOS *******************/
    /*
        newComentario()
    */
    function newComentario(){
        global $tsdb, $tsCore, $tsUser, $tsMonitor;
		// NO MAS DE 1500 CARACTERES PUES NADIE COMENTA TANTO xD
		$comentario = $tsCore->setSecure(substr($_POST['comentario'],0,1500),2);
		$fid = $tsCore->setSecure($_POST['fotoid']);
        /* COMPROVACIONES */
        $tsText = preg_replace('# +#',"",$comentario);
        $tsText = str_replace(array("\n","\t"),"",$tsText);
        if($tsText == '') return '0: El campo <b>Mensaje</b> es requerido para esta operaci&oacute;n';
        /* DE QUIEN ES LA FOTO */
		$query = $tsdb->select("f_fotos","f_user, f_closed","foto_id = {$fid}","",1);
		$data = $tsdb->fetch_assoc($query);
		$tsdb->free($query);
        //
        $fecha = time();
        // VAMOS...
        if($data['f_user']){
            if($data['f_closed'] != 1 || $data['f_user'] == $tsUser->uid){
                // ANTI FLOOD
                $tsCore->antiFlood();
                //
                if($tsdb->insert("f_comentarios","c_foto_id, c_user, c_date, c_body","{$fid}, {$tsUser->uid}, {$fecha}, '{$comentario}'")){
        		  	$cid = $tsdb->insert_id();
        		  	// AGREGAR A LAS ESTADISTICAS
        		  	$tsdb->update("w_stats","stats_foto_comments = stats_foto_comments + 1","stats_no = 1");
        		  	$tsdb->update("u_miembros","user_foto_comments = user_foto_comments + 1","user_id = {$tsUser->uid}");
        		  	$tsdb->update("f_fotos","f_comments = f_comments + 1","foto_id = {$fid}");
                    // NOTIFICAR AL USUARIO
                    $tsMonitor->setNotificacion(11, $data['f_user'], $tsUser->uid, $fid);
        		  	// array(comid, com, fecha, autor_del_post)
        			return array($cid,$tsCore->parseSmiles($comentario), $fecha, $_POST['auser']);
        		} else return '0: Ocurri&oacute; un error int&eacute;ntalo m&aacute;s tarde.';
            } else return '0: La foto se encuentra cerrada y no se permiten comentarios.';
        } else return '0: La foto no existe.';
    }
    /*
        delComentario()
    */
    function delComentario(){
        global $tsdb, $tsCore, $tsUser;
        //
        $cid = $tsCore->setSecure($_POST['cid']);
        // DATOS
        $query = $tsdb->query("SELECT c.cid, c.c_user, f.foto_id, f.f_user FROM f_comentarios AS c LEFT JOIN f_fotos AS f ON c.c_foto_id = f.foto_id WHERE c.cid = {$cid} LIMIT 1");
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        if(!empty($data['cid'])){
            // ES EL DUEÑO DE LA FOTO?
            if($data['f_user'] == $tsUser->uid){
                if($tsdb->delete("f_comentarios","cid = {$cid}")){
                    // UPDATES
                    $tsdb->update("f_fotos","f_comments = f_comments - 1","foto_id = {$data['foto_id']}");
                    return '1: OK';
                }
            } else return '0: Hmmm... &iquest;Haciendo pruebas?';
        } else return '0: El comentario no existe.'; 
    }
}
?>
