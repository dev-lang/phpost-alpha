<?php
/********************************************************************************
* c.admin.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

/*

	CLASE CON LOS ATRIBUTOS Y METODOS PARA MANEJAR A LOS USUARIOS
	
*/
class tsAdmin {
	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsAdmin();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
  								// ADMINISTRAR \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		getAdmins()
	*/
	function getAdmins(){
		global $tsdb;
		//
		$query = $tsdb->select("u_miembros","user_name","user_rango = 1","user_id","");
		//
		$data = $tsdb->fetch_array($query);
		//
		return $data;
	}
	/*
		getVersions()
	*/
	function getVersions(){
		global $tsdb;
		//
		$data['php'] = PHP_VERSION;
		//
		$query = $tsdb->query("SELECT VERSION()");
		$data['mysql'] = mysql_fetch_row($query);
		$tsdb->free($query);
		//
		$data['server'] = $_SERVER['SERVER_SOFTWARE'];
		//
		$temp = @gd_info();
		$data['gd'] = $temp['GD Version'];
		//
		return $data;
	}
	/*
		saveConfigs()
	*/
	function saveConfig(){
		global $tsdb, $tsCore;
		//
		$c = array(
			'titulo' => $tsCore->setSecure($_POST['titulo']),
			'slogan' => $tsCore->setSecure($_POST['slogan']),
			'url' => $tsCore->setSecure($_POST['url']),
			'offline' => empty($_POST['offline']) ? 0 : 1,
            'offline_message' => $tsCore->setSecure($_POST['offline_message']),
            'chat' => $tsCore->setSecure($_POST['chat']),
			'edad' => $tsCore->setSecure($_POST['edad']),
			'active' => $tsCore->setSecure($_POST['active']),
            'flood' => $tsCore->setSecure($_POST['flood']),
			'reg_active' => empty($_POST['reg_active']) ? 0 : 1,
			'reg_activate' => empty($_POST['reg_activate']) ? 0 : 1,
			'firma' => empty($_POST['firma']) ? 0 : 1,
            'upload' => empty($_POST['upload']) ? 0 : 1,
            'portal' => empty($_POST['portal']) ? 0 : 1,
            'live' => empty($_POST['live']) ? 0 : 1,
            'max_nots' => $tsCore->setSecure($_POST['max_nots']),
            'max_acts' => $_POST['max_acts'],
            'max_posts' => $tsCore->setSecure($_POST['max_posts']),
			'max_com' => $tsCore->setSecure($_POST['max_com']),
            'sump' => empty($_POST['sump']) ? 0 : 1,
            'newr' => empty($_POST['newr']) ? 0 : 1
		);
		// UPDATE
		if($tsdb->update("w_configuracion","titulo = '{$c['titulo']}', slogan = '{$c['slogan']}', url = '{$c['url']}', chat_id = '{$c['chat']}', c_last_active = {$c['active']}, c_anti_flood = {$c['flood']}, c_reg_active = {$c['reg_active']}, c_reg_activate = {$c['reg_activate']}, c_allow_edad = {$c['edad']}, c_max_posts = {$c['max_posts']}, c_max_com = {$c['max_com']}, c_max_nots = {$c['max_nots']}, c_max_acts = {$c['max_acts']}, c_allow_sump = {$c['sump']}, c_newr_type = {$c['newr']}, c_allow_firma = {$c['firma']}, c_allow_upload = {$c['upload']}, c_allow_portal = {$c['portal']}, c_allow_live = {$c['live']}, offline = {$c['offline']}, offline_message = '{$c['offline_message']}'","tscript_id = 1")) return true;
		else die($tsdb->error());
	}
    /*
        getNoticias()
    */
    function getNoticias(){
        global $tsdb;
        //
        $query = $tsdb->select("w_noticias","*","not_id > 0","not_date DESC","");
		$data = $tsdb->fetch_array($query);
		$tsdb->free($query);
        //
        return $data;
    }
    /*
        getNoticia()
    */
    function getNoticia(){
        global $tsdb, $tsCore;
        //
        $not_id = $tsCore->setSecure($_GET['nid']);
        //
        $query = $tsdb->select("w_noticias","*","not_id = {$not_id}","",1);
		$data = $tsdb->fetch_assoc($query);
		$tsdb->free($query);
        //
        return $data;
    }
    /*
        newNoticia()
    */
    function newNoticia(){
        global $tsdb, $tsCore;
        //
        $not_body = $tsCore->setSecure(substr($_POST['not_body'],0,190));
        $not_active = empty($_POST['not_active']) ? 0 : 1;
        if(!empty($not_body)){
            $date = time();
            if($tsdb->insert("w_noticias","not_body, not_date, not_active","'{$not_body}', {$date}, {$not_active}")) return true;
        }
        //
        return false;
    }
    /*
        editNoticia()
    */
    function editNoticia(){
        global $tsdb, $tsCore;
        //
        $not_id = $tsCore->setSecure($_GET['nid']);
        $not_body = $tsCore->setSecure(substr($_POST['not_body'],0,190));
        $not_active = empty($_POST['not_active']) ? 0 : 1;
        //
        if(!empty($not_body)){
            if($tsdb->update("w_noticias","not_body = '{$not_body}', not_active = {$not_active}","not_id = {$not_id}")) return true;
        }
    }
	/*
		getTemas()
	*/
	function getTemas(){
		global $tsdb, $tsCore;
		//
		$query = $tsdb->select("w_temas","*","tid != 0","","");
		//
		$data = $tsdb->fetch_array($query);
		$tsdb->free($query);
		//
		return $data;
	}
	/*
		getTema()
	*/
	function getTema(){
		global $tsdb, $tsCore;
		//
		$tema_id = $tsCore->setSecure($_GET['tid']);
		//
		$query = $tsdb->select("w_temas","*","tid = $tema_id","",1);
		$data = $tsdb->fetch_assoc($query);
		$tsdb->free($query);
		//
		return $data;
	}
	/*
		saveTema()
	*/
	function saveTema(){
		global $tsdb, $tsCore;
		//
		$tema_id = $tsCore->setSecure($_GET['tid']);
		//
		$t = array('url' => $tsCore->setSecure($_POST['url']), 'path' => $tsCore->setSecure($_POST['path']));
		//
		if($tsdb->update("w_temas","t_url = '{$t['url']}', t_path = '{$t['path']}'","tid = {$tema_id}")) return true;
		else return false;
	}
	/*
		changeTema()
	*/
	function changeTema(){
		global $tsdb;
		//
		$tema = $this->getTema();
		//
		if(!empty($tema['tid'])) {$tsdb->update("w_configuracion","tema_id = {$tema['tid']}","tscript_id = 1"); return true;}
		else return false;
	}
	/*
		deleteTema()
	*/
	function deleteTema(){
		global $tsdb;
		//
		$tema = $this->getTema();
		//
		if(!empty($tema['tid'])) {$tsdb->delete("w_temas","tid = {$tema['tid']}"); return true;}
		else return false;
	}
	/*
		newTema()
	*/
	function newTema(){
		global $tsdb, $tsCore;
		//
		$tema_path = $tsCore->setSecure($_POST['path']);
		// ARCHIVO DE INSTALACION
		include("../../Temas/".$tema_path.'/install.php');
		//
		if(empty($tema)) return 'Revisa que la carpeta del tema sea correcta.';
		foreach($tema as $key => $val){
			if(empty($val)) return 'El archivo de instalaci&oacute;n del tema es incorrecto. Recuerda utilizar temas oficiales.';
			else $temadb[$key] = $tsCore->setSecure($val);
		}
		// NUEVO
		if($tsdb->insert("w_temas","t_name, t_url, t_path, t_copy","'{$temadb['nombre']}', '{$temadb['url']}', '{$tema_path}', '{$temadb['copy']}'")) return 1;
		else return 'Ocurri&oacute; un error durante la instalaci&oacute;n. Consulta el foro ofcial de T!script.';
	}
	/*
		saveAds()
	*/
	function saveAds(){
		global $tsdb;
		// D: Podria ser un riesgo de seguridad no limpiar estas variables? no lo creo pues cuando definimos el nivel de acceso solo pueden entrar 
		// administradores. Cualquier fallo sera culpa de ellos Dx
		$a = array(
			'ad300' => html_entity_decode($_POST['ad300']),
            'ad468' => html_entity_decode($_POST['ad468']),
			'ad160' => html_entity_decode($_POST['ad160']),
			'ad728' => html_entity_decode($_POST['ad728']),
            'sid' => $_POST['adSearch']
		);
		//
		if($tsdb->update("w_configuracion","ads_300 = '{$a['ad300']}', ads_468 = '{$a['ad468']}', ads_160 = '{$a['ad160']}', ads_728 = '{$a['ad728']}', ads_search = '{$a['sid']}'","tscript_id = 1")) return true;
	}
	/*
		savePConfigs()
		: PARECERIAN MUCHAS FUNCIONES PERO DE ESTA MANERA PODEMOS HACER MODIFICACIONES MAS FACILMENTE
	*/
	/*function savePConfigs(){
		global $tsdb, $tsCore;
		//
		$c = array(
			'max_posts' => $tsCore->setSecure($_POST['max_posts']),
			'max_com' => $tsCore->setSecure($_POST['max_com'])
		);
		//
		if($tsdb->update("w_configuracion","c_max_posts = {$c['max_posts']}, c_max_com = {$c['max_com']}","tscript_id = 1")) return true;
	}*/
	/*
		saveOrden()
		: GUARDA EL ORDEN DE LAS CAT Y SUBCAT
	*/
	function saveOrden(){
		global $tsdb, $tsCore;
		//
		$catid = $tsCore->setSecure($_POST['catid']);
		$subcats = $_POST[$catid];
		//
		//$db = $this->getDBtypes();
		// MODIFICAMOS		 
		$orden = 1;
		foreach($subcats as $key => $cid){
			if(!empty($cid)){
				$tsdb->update("p_categorias","c_orden = {$orden}","cid = {$cid}");
				$orden++;
			}
		}
	}
	/*
		getCat()
		: OBTIENE LOS DATOS DE LA CAT O SUBCATEGORIA
	*/
	function getCat(){
		global $tsdb, $tsCore;
		//
		//$db = $this->getDBtypes();
		$cid = $tsCore->setSecure($_GET['cid']);
		//
		$query = $tsdb->select("p_categorias","*","cid = {$cid}","",1);
		$data = $tsdb->fetch_assoc($query);
		$tsdb->free($query);
		//
		return $data;
	}
	/*
		saveCat()
		: EDITA LOS DATOS DE LA CAT O SUBCAT
	*/
	function saveCat(){
		global $tsdb, $tsCore;
		//
		//$db = $this->getDBtypes();
		$cid = $tsCore->setSecure($_GET['cid']);
		//
		$c_nombre = $tsCore->setSecure($_POST['c_nombre']);
        $cimg = $tsCore->setSecure($_POST['c_img']);
		$updates = "c_nombre = '{$c_nombre}', c_seo = '{$tsCore->setSEO($c_nombre,true)}', c_img = '{$cimg}'";
		if($tsdb->update("p_categorias",$updates,"cid = {$cid}")) return true;
	}
	/*
		newCat()
		: EDITA LOS DATOS DE LA CAT O SUBCAT
	*/
	function newCat(){
		global $tsdb, $tsCore;
		//
		//$db = $this->getDBtypes();
		// VALORES
		$c_nombre = $tsCore->setSecure($_POST['c_nombre']);
        $cimg = $tsCore->setSecure($_POST['c_img']);
        // ORDEN
        $query = $tsdb->query("SELECT COUNT(cid) AS total FROM p_categorias");
        $orden = $tsdb->fetch_assoc($query);
        $orden = $orden['total'] + 1;
		// INSERTS
		$insert_fields = "c_orden, c_nombre, c_seo, c_img";
		$insert_values = "{$orden}, '{$c_nombre}', '{$tsCore->setSEO($c_nombre,true)}', '{$cimg}'";
		//
		if($tsdb->insert("p_categorias",$insert_fields,$insert_values)) return true;
	}
	/*
		delCat()
		: BORRAR SUBCATEGORIA
	*/
	function delCat(){
		global $tsdb, $tsCore;
		//
		$cid = $tsCore->setSecure($_GET['cid']);
		$ncid = $tsCore->setSecure($_POST['ncid']);
		// MOVER
		if(!empty($ncid) && $ncid > 0){
			if($tsdb->update("p_subcategorias","s_cat = {$ncid}","s_cat = {$cid}")) {
				if($tsdb->delete("p_categorias","cid = {$cid}")) return 1;
			}
			// SI LLEGO HASTA AQUI HUBO UN ERROR.
			return 'Lo sentimos ocurri&oacute; un error, pongase en contacto con T!Script.';
		} else return 'Antes de eliminar una categor&iacute;a debes elegir a donde mover sus subcategor&iacute;as.';
	}
	/*
		delSubcat()
		: BORRAR SUBCATEGORIA
	*/
	function delSubcat(){
		global $tsdb, $tsCore;
		//
		$cid = $tsCore->setSecure($_GET['cid']);
		$sid = $tsCore->setSecure($_GET['sid']);
		$nsid = $tsCore->setSecure($_POST['nsid']);
		// MOVER
		if(!empty($nsid) && $nsid > 0){
			if($tsdb->update("p_posts","post_category = {$nsid}","post_category = {$sid}")) {
				if($tsdb->delete("p_subcategorias","sid = {$sid}")) return 1;
			}
			// SI LLEGO HASTA AQUI HUBO UN ERROR.
			return 'Lo sentimos ocurri&oacute; un error, pongase en contacto con T!Script.';
		} else return 'Antes de eliminar una subcategor&iacute;a debes elegir a donde mover los datos.';
	}
	/*
		getDBtypes()
		: DETERMINA EL NOMBRE DE LA TABLA SEGUN EL TIPO
	*/
	function getDBtypes(){
		// TIPO
		if($_GET['t'] == 'cat'){
			$data['table'] = 'p_categorias';
			$data['pre'] = 'c';
		} else {
			$data['table'] = 'p_subcategorias';
			$data['pre'] = 's';
		}
		//
		return $data;
	}
	/*
		getRangos()
	*/
	function getRangos(){
		global $tsdb, $tsCore;
		// RANGOS SIN PUNTOS
		$query = $tsdb->query("SELECT * FROM u_rangos ORDER BY rango_id, r_user_points");
		// ARMAR ARRAY
		while($row = mysql_fetch_assoc($query)){
			$data[$row['r_special'] == 1 ? 'regular' : 'post'][$row['rango_id']] = array(
				'id' => $row['rango_id'],
				'name' => $row['r_name'],
				'color' => $row['r_color'],
				'imagen' => $row['r_image'],
				'min_puntos' => $row['r_min_points'],
				'min_posts' => $row['r_min_posts'],
				'user_puntos' => $row['r_user_points'],
				'num_members' => 0
			);
		}
		$tsdb->free($query);
		// NUMERO DE USUARIOS EN CADA RANGO
		if (!empty($data['post'])){
			$query = $tsdb->query("
				SELECT user_rango AS ID_GROUP, COUNT(user_id) AS num_members
				FROM u_miembros
				WHERE user_rango IN (".implode(', ',array_keys($data['post'])).")
				GROUP BY user_rango");
			while ($row = mysql_fetch_assoc($query))
				$data['post'][$row['ID_GROUP']]['num_members'] += $row['num_members'];
			$tsdb->free($query);
		}
		// NUMERO DE USUARIOS EN RANGOS REGULARES
		if (!empty($data['regular'])){
			$query = $tsdb->query("
				SELECT user_rango AS ID_GROUP, COUNT(*) AS num_members
				FROM u_miembros
				WHERE user_rango IN (".implode(', ',array_keys($data['regular'])).")
				GROUP BY user_rango");
			while ($row = mysql_fetch_assoc($query))
				$data['regular'][$row['ID_GROUP']]['num_members'] += $row['num_members'];
			$tsdb->free($query);
		}
		//
		return $data;
	}
	/*
		getRango
	*/
	function getRango(){
		global $tsdb, $tsCore;
		//
		$rid = $tsCore->setSecure($_GET['rid']);
		//
		$query = $tsdb->select("u_rangos","*","rango_id = {$rid}","",1);
		$data = $tsdb->fetch_assoc($query);
		$tsdb->free($query);
		//
		return $data;
	}
	/*
		getRangoUsers()
	*/
	function getRangoUsers(){
		global $tsdb, $tsCore;
		//
		$rid = $tsCore->setSecure($_GET['rid']);
		$max = 10; // MAXIMO A MOSTRAR
		// TIPO DE BUSQUEDA
		$type = $_GET['t'];
		$where = "user_rango = {$rid}";
		// SELECCIONAMOS
		$limit = $tsCore->setPageLimit($max, true);
		$query = $tsdb->query("SELECT u.user_id, u.user_name, u.user_email, u.user_posts, u.user_registro, u.user_lastlogin FROM u_miembros AS u WHERE {$where} LIMIT {$limit}");
		//
		$data['data'] = $tsdb->fetch_array($query);
		$tsdb->free($query);
		// PAGINAS
		$query = $tsdb->query("SELECT COUNT(*) FROM u_miembros WHERE {$where}");
		list ($total) = $tsdb->fetch_row($query);
		$tsdb->free($query);
		$data['pages'] = $tsCore->pageIndex($tsCore->settings['url']."/admin/rangos?act=list&rid={$rid}&t={$type}",$_GET['s'],$total, $max);
		//
		return $data;
	}
	/*
		saveRango()
	*/
	function saveRango(){
		global $tsdb, $tsCore;
		//
		$rid = $tsCore->setSecure($_GET['rid']);
		$r = array(
			'name' => $tsCore->setSecure($_POST['rName']),
			'color' => $tsCore->setSecure($_POST['rColor']),
			'puntos' => empty($_POST['minPuntos']) ? 0 : $tsCore->setSecure($_POST['minPuntos']),
			'posts' => empty($_POST['minPosts']) ? 0 : $tsCore->setSecure($_POST['minPosts']),
			'puntox' => empty($_POST['userPuntos']) ? 0 :$tsCore->setSecure($_POST['userPuntos']),
			'img' => $tsCore->setSecure($_POST['r_img']),
            'special' => empty($_POST['rSpecial']) ? 0 : 1
		);
		//
		$updates = "r_name = '{$r['name']}', r_color = '{$r['color']}', r_image = '{$r['img']}', r_min_points = {$r['puntos']}, r_min_posts = {$r['posts']}, r_user_points = {$r['puntox']}, r_special = {$r['special']}";
		//
		if($tsdb->update("u_rangos",$updates,"rango_id = $rid")) return true;
		else die($tsdb->error());
	}
	/*
		newRango()
	*/
	function newRango(){
		global $tsdb, $tsCore;
		//
		$r = array(
			'name' => $tsCore->setSecure($_POST['rName']),
			'color' => $tsCore->setSecure($_POST['rColor']),
			'puntos' => empty($_POST['minPuntos']) ? 0 : $tsCore->setSecure($_POST['minPuntos']),
			'posts' => empty($_POST['minPosts']) ? 0 : $tsCore->setSecure($_POST['minPosts']),
			'puntox' => empty($_POST['userPuntos']) ? 0 :$tsCore->setSecure($_POST['userPuntos']),
			'img' => $tsCore->setSecure($_POST['r_img']),
            'special' => empty($_POST['rSpecial']) ? 0 : 1
		);
		//
		if(empty($r['name'])) return 'Debes ingresar el nombre del nuevo rango.';
		else {
			if($tsdb->insert("u_rangos","r_name, r_color, r_image, r_min_points, r_min_posts, r_user_points, r_special","'{$r['name']}', '{$r['color']}', '{$r['img']}', {$r['puntos']}, {$r['posts']}, {$r['puntox']}, {$r['special']}")) return 1;
		}
	}
	/*
		delRango()
	*/
	function delRango(){
		global $tsdb, $tsCore;
		//
		$rid = $tsCore->setSecure($_GET['rid']);
		//
		if($rid > 3){
			if($tsdb->update("u_miembros","user_rango = 3","user_rango_post = {$rid}")) {
				if($tsdb->delete("u_rangos","rango_id = {$rid}")) return true;
			}
		}
	}
	/*
		getExtraIcons()
	*/
	function getExtraIcons($f = 'cat', $size = null){
		// IMAGENES DEL TIPO...
		$arr_ext = array("jpg","png","gif");
		// DONDE... SOLO VAN EN EL TEMA DEFAULT
		$mydir = opendir("../../Temas/default/images/icons/".$f);
		// LEEMOS
		while($file = readdir($mydir)){
			$ext = substr($file,-3);
			// ES IMAGEN
			if(in_array($ext,$arr_ext)) {
                if(!empty($size)){
                    $im_size = substr($file,-6, 2);
                    if($size == $im_size) $icons[] = substr($file,0, -7);
                } else $icons[] = $file;
			}
		}
		//
		return $icons;
	}
    /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
							// ADMINISTRAR USUARIOS \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /*
        getUsuarios()
    */
    function getUsuarios(){
        global $tsdb, $tsCore;
        //
        $max = 20; // MAXIMO A MOSTRAR
		$limit = $tsCore->setPageLimit($max, true);
        //
        $query = $tsdb->query("SELECT u.user_id, u.user_name, u.user_email, u.user_registro, u.user_lastlogin, u.user_activo, u.user_baneado, r.r_name, r.r_color, r.r_image FROM u_miembros AS u LEFT JOIN u_rangos AS r ON r.rango_id = u.user_rango WHERE u.user_id > 0 ORDER BY u.user_id DESC LIMIT {$limit}");
        //
		$data['data'] = $tsdb->fetch_array($query);
		$tsdb->free($query);
		// PAGINAS
		$query = $tsdb->query("SELECT COUNT(*) FROM u_miembros WHERE user_id > 0");
		list ($total) = $tsdb->fetch_row($query);
		$tsdb->free($query);
		$data['pages'] = $tsCore->pageIndex($tsCore->settings['url']."/admin/users?",$_GET['s'],$total, $max);
        //
        return $data;
    }
    /*
        getUserData()
    */
    function getUserData(){
        global $tsdb, $tsCore;
        //
        $user_id = $tsCore->setSecure($_GET['uid']);
        //
        $query = $tsdb->query("SELECT u.user_name, u.user_email, u.user_posts, u.user_puntos, u.user_registro, u.user_lastactive, r.r_name, r.r_color FROM u_miembros AS u LEFT JOIN u_rangos AS r ON u.user_rango = r.rango_id WHERE u.user_id = {$user_id} LIMIT 1");
        $data = $tsdb->fetch_assoc($query);
        //
        return $data;
    }
    /*
        setUserData
    */
    function setUserData($user_id){
        global $tsdb;
        # DATA
        $query = $tsdb->select("u_miembros","user_name, user_email, user_password","user_id = {$user_id}");
        $data = $tsdb->fetch_assoc($query);
        # LOCALS
        $email = empty($_POST['email']) ? $data['user_email'] : $_POST['email'];
        $password = $_POST['pwd'];
        $cpassword = $_POST['cpwd'];
        #
        if(!empty($password) && !empty($cpassword)) {
            if(strlen($password) < 5) return 'Contrase&ntilde;a no v&aacute;lida.';
            if($password != $cpassword) return 'Las contrase&ntilde;as no coinciden';
            $new_key = md5(md5($password).strtolower($data['user_name']));
            $db_key = ", user_password = '{$new_key}'";
        }
        if($tsdb->update("u_miembros","user_email = '{$email}'{$db_key}","user_id = {$user_id}"))return true;
    }
    /*
        getUserRango
    */
    function getUserRango($user_id){
        global $tsdb;
        # CONSULTA
        $query = $tsdb->query("SELECT u.user_rango, r.r_name, r.r_color FROM u_miembros AS u LEFT JOIN u_rangos AS r ON u.user_rango = r.rango_id WHERE u.user_id = {$user_id} LIMIT 1");
        $data['user'] = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        # RANGOS DISPONIBLES
        $query = $tsdb->select("u_rangos","rango_id, r_name, r_color","1");
        $data['rangos'] = $tsdb->fetch_array($query);
        $tsdb->free($query);
        #
        return $data;
    }
    /*
        setUserRango($user_id)
    */
    function setUserRango($user_id){
        global $tsdb, $tsUser;
        # SOLO EL PRIMER ADMIN PUEDE PONER A OTROS ADMINS
        $new_rango = (int) $_POST['new_rango'];
        if($user_id == $tsUser->uid) return 'No puedes cambiarte el rango a ti mismo'; 
        elseif($tsUser->uid != 1 && $new_rango == 1) return 'Solo el primer Administrador puede crear más administradores';
        else {
            if($tsdb->update("u_miembros","user_rango = {$new_rango}","user_id = {$user_id}")) return true;
        }
    }

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
}
?>
