<?php
/********************************************************************************
* c.posts.php 	                                                                *
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
class tsPosts extends tsDatabase{

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsPosts();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								PUBLICAR POSTS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /** simiPosts($q)
     * @access public
     * @param string
     * @return array
     */
    public function simiPosts($q){
        $query = $this->query("SELECT p.post_id, p.post_user, p.post_category, p.post_title, p.post_date, u.user_name, c.c_seo, c.c_nombre, c.c_img FROM p_posts AS p LEFT JOIN u_miembros AS u ON u.user_id = p.post_user LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND MATCH(p.post_title) AGAINST('{$q}' IN BOOLEAN MODE) ORDER BY RAND() DESC LIMIT 5");
        $data = $this->fetch_array($query);
        $this->free($query);
        //
        return $data;
    }
    /** genTags($q)
     * @access public
     * @param string
     * @return string
     */
     public function genTags($q){
        $content = trim(preg_replace("/[^ A-Za-z0-9]/", "", $q));
        $ketxt = preg_replace('/ {2,}/si', " ", $content);
        $t = explode(" ", $ketxt);
        $total = count($t);
        $tg = "";
        $i = 0;
        foreach($t as $v){ $i++;
            $coma = ($i < $total) ? ", " : " ";
            $tg .= (strlen($v) >= 4 && strlen($v) <= 8) ? ($v.$coma) : "";
        }
        $tag = strtolower($tg);
        //
        return ($tag);
     }
	/*
		getPreview()
	*/
	function getPreview(){
		global $tsCore;
		//
		$titulo = $tsCore->setSecure($_POST['titulo']);
		$cuerpo = $tsCore->setSecure($_POST['cuerpo']);
		//
		return array('titulo' => $titulo, 'cuerpo' => $tsCore->parseBBCode($cuerpo));
	}
    /*
        validTags($tags)
    */
    function validTags($tags){
        $tags = trim(preg_replace("/[^ A-Za-z0-9,]/", "", $tags));
        $tags = str_replace(' ','',$tags);
        if(empty($tags)) return false;
        else {
            $tags = explode(',',$tags);
            if(count($tags) < 4) return false;
            foreach($tags as $val){
                if(empty($val)) return false;
            }   
        }
        //
        return true;
    }
	/*
		newPost()
	*/
	function newPost(){
		global $tsCore, $tsUser, $tsMonitor, $tsActividad;
		//
		$postData = array(
			'date' => time(),
			'title' => $tsCore->setSecure($_POST['titulo'],2),
			'body' => $tsCore->setSecure($_POST['cuerpo']),
			'tags' => $tsCore->setSecure($_POST['tags'],true,1),
			'category' => $tsCore->setSecure($_POST['categoria']),
		);
		// VACIOS
		foreach($postData as $key => $val){
            $val = trim(preg_replace("/[^ A-Za-z0-9]/", "", $val));
            $val = str_replace(' ', '', $val);
			if(empty($val)) return 0;
		}
        // TAGS
        $tags = $this->validTags($postData['tags']);
        if(empty($tags)) return 'Tienes que ingresar por lo menos <b>4</b> tags.';
		// ESTOS PUEDEN IR VACIOS
		$postData['private'] = empty($_POST['privado']) ? 0 : 1;
		$postData['block_comments'] = empty($_POST['sin_comentarios']) ? 0 : 1;
        // SOLO MODERADORES Y ADMINISTRADORES
        if(empty($tsUser->is_admod)) {
    		$postData['sponsored'] = 0;
            $postData['sticky'] = 0;   
        } else {
    		$postData['sponsored'] = empty($_POST['patrocinado']) ? 0 : 1;
            $postData['sticky'] = empty($_POST['sticky']) ? 0 : 1;
        }
		// ANTI FLOOD
		if($tsUser->info['user_lastpost'] < (time() - 60)) {
            // EXISTE LA CATEGORIA?
            $query = $this->select("p_categorias","cid","cid = {$postData['category']}",'',1);
            if($this->num_rows($query) == 0) return 'La categor&iacute;a especificada no existe.';
			// INSERTAMOS
			if($this->insert("p_posts","post_user, post_category, post_title, post_body, post_date, post_tags, post_private, post_block_comments, post_sponsored, post_sticky","{$tsUser->uid}, {$postData['category']}, '{$postData['title']}', '{$postData['body']}', {$postData['date']}, '{$postData['tags']}', {$postData['private']}, {$postData['block_comments']}, {$postData['sponsored']}, {$postData['sticky']}")) {
				$postID = $this->insert_id();
				// UNO MAS...
				$this->update("w_stats","stats_posts = stats_posts + 1","stats_no = 1");
				$time = time();
				// ULTIMO POST
				$this->update("u_miembros","user_posts = user_posts + 1, user_lastpost = $time","user_id = {$tsUser->uid}");
				// AGREGAR AL MONITOR DE LOS USUARIOS QUE ME SIGUEN
				$tsMonitor->setFollowNotificacion(5, 1, $tsUser->uid, $postID);
                // REGISTRAR MI ACTIVIDAD
                $tsActividad->setActividad(1, $postID);
				// SUBIR DE RANGO?
				$this->subirRango($tsUser->uid);
				//
				return $postID;
			} else return $this->error();
		} else return -1;
	}
	/*
		savePost()
	*/
	function savePost(){
		global $tsCore, $tsUser;
		//
		$post_id = $_GET['pid'];
		$query = $this->select("p_posts","post_user, post_sponsored, post_sticky","post_id = {$post_id}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
		//
		$postData = array(
			'title' => $_POST['titulo'],
			'body' => $_POST['cuerpo'],
			'tags' => $_POST['tags'],
			'category' => $_POST['categoria'],
		);
		// VACIOS
		foreach($postData as $key => $val){
            $val = trim(preg_replace("/[^ A-Za-z0-9]/", "", $val));
            $val = str_replace(' ', '', $val);
			if(empty($val)) return 0;
		}
        // TAGS
        $tags = $this->validTags($postData['tags']);
        if(empty($tags)) return 'Tienes que ingresar por lo menos <b>4</b> tags.';
		// 
		$postData['private'] = empty($_POST['privado']) ? 0 : 1;
		$postData['block_comments'] = empty($_POST['sin_comentarios']) ? 0 : 1;
        // SOLO MODERADORES Y ADMINISTRADORES
        if(empty($tsUser->is_admod)) {
    		$postData['sponsored'] = $data['post_sponsored'];
            $postData['sticky'] = $data['post_sticky'];   
        } else {
    		$postData['sponsored'] = empty($_POST['patrocinado']) ? 0 : 1;
            $postData['sticky'] = empty($_POST['sticky']) ? 0 : 1;
        }
		// ACTUALIZAMOS
		if($tsUser->uid == $data['post_user'] || !empty($tsUser->is_admod)){
			if($this->update("p_posts","post_title = '{$postData['title']}', post_body = '{$postData['body']}', post_tags = '{$postData['tags']}', post_category = {$postData['category']}, post_private = {$postData['private']}, post_block_comments = {$postData['block_comments']}, post_sponsored = {$postData['sponsored']}, post_sticky = {$postData['sticky']}","post_id = {$post_id}")) {
			     // GUARDAR EN EL HISTORIAL	DE MODERACION		 
			     if($tsUser->is_admod && $tsUser->uid != $data['post_user']){
			         include("c.moderacion.php");
                     $tsMod =& tsMod::getInstance();
                     return $tsMod->setHistory('editar', array('title' => $postData['title'], 'autor' => $data['post_user'], 'razon' => $_POST['razon']));                                          		         
			     } else return 1;
			}
		}
	}
    /*
        setNP()
        :: POST ANTERIOR o SIGUIENTE
    */
    function setNP(){
        global $tsCore;
        //
        $act = $_GET['action'];
        $post_id = $tsCore->setSecure($_GET['id']);
        $found = false;
        // LIMITES
        $query = $this->select("p_posts","post_id","1","post_id ASC",1);
        $limit = $this->fetch_assoc($query);
        $limitD = $limit['post_id'];
        $query = $this->select("p_posts","post_id","1","post_id DESC",1);
        $limit = $this->fetch_assoc($query);
        $limitU = $limit['post_id'];
        $this->free($query);
        //
        do{
            // ANTERIOR O SIGUIENTE
            if($act == 'prev') $post_id--;
            elseif($act == 'next') $post_id++;
            // LIMITE DE POSTS
            if($post_id > $limitU || $post_id < $limitD) $tsCore->redirectTo($tsCore->settings['url'].'/posts/');
            //
            $query = $this->query("SELECT p.post_id, p.post_title, p.post_category, c.c_seo FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE post_status = 0 AND post_id = {$post_id} LIMIT 1");
            $data = $this->fetch_assoc($query);
            $this->free($query);
            if($data) $found = true;
            //
        } while($found == false);
        // REDIRECCIONAMOS
        $url = $tsCore->settings['url'].'/posts/'.$data['c_seo'].'/'.$data['post_id'].'/'.$tsCore->setSEO($data['post_title']).'.html';
        $tsCore->redirectTo($url);
    }
    /*
        getCatData()
        :: OBTENER DATOS DE UNA CATEGORIA
    */
    function getCatData(){
        global $tsCore;
        //
        $cat = $tsCore->setSecure($_GET['cat']);
        //
        $query = $this->select("p_categorias","c_nombre, c_seo","c_seo = '{$cat}'","",1);
        $data = $this->fetch_assoc($query);
        $this->free($query);
        //
        return $data;
    }
	/*
		getLastPosts($category, $sticky)
	*/
	function getLastPosts($category = NULL, $subcateg = NULL, $sticky = false){
		global $tsCore, $tsUser;
		/**********/
		// TIPO DE POSTS A MOSTRAR
		if(!empty($category)){
			// EXISTE LA CATEGORIA?
			$cat = $this->fetch_assoc($this->select("p_categorias","cid","c_seo = '{$category}'","",1));
			if($cat['cid'] > 0) {
			     $c_where = "AND p.post_category = {$cat['cid']}"; // SUBCATEGORIA EN ESPECIAL
                 $p_where = "post_category = {$cat['cid']}";
            }
		} else $p_where = "post_status = 0";
		// Stickys
		if($sticky) {
			$s_where = 'AND p.post_sticky = 1';
            $s_order = 'p.post_sponsored';
			$start = '0, 10';
		} else {
			$s_where = 'AND p.post_sticky = 0';
            $s_order = 'p.post_id';
            // TOTAL DE POSTS
            $query = $this->select("p_posts","COUNT(post_id) AS total","{$p_where}");
            $posts = $this->fetch_assoc($query);
            $this->free($query);
            //
			$start = $tsCore->setPageLimit($tsCore->settings['c_max_posts'],false,$posts['total']);
            $lastPosts['pages'] = $tsCore->getPages($posts['total'], $tsCore->settings['c_max_posts']);
		}
		/*********/
		$query = $this->query("SELECT p.post_id, p.post_user, p.post_category, p.post_title, p.post_date, p.post_comments, p.post_puntos, p.post_private, p.post_sponsored, p.post_sticky, u.user_name, c.c_nombre, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 {$c_where} {$s_where} ORDER BY {$s_order} DESC LIMIT {$start}");
		$lastPosts['data'] = $this->fetch_array($query);
		$this->free($query);
		//
		return $lastPosts;
	}
	/*
		getPost()
	*/
	function getPost(){
		global $tsCore, $tsUser;
		//
		$post_id = $tsCore->setSecure($_GET['post_id']);
		if(empty($post_id)) return array('deleted','Oops! Este post no existe o fue eliminado.');
		// DATOS DEL POST
		$query = $this->query("SELECT * FROM p_posts WHERE post_id = {$post_id} LIMIT 1");
		//
		$postData = $this->fetch_assoc($query);
		$this->free($query);
		//
		if(empty($postData['post_id'])) {
			$tsDraft = $this->fetch_assoc($this->select("p_borradores","b_title, ","b_post_id = $post_id","",1));
			if(!empty($tsDraft['b_title'])) return array('deleted','Oops! Este post no existe o fue eliminado.');
			else return array('deleted','Oops! El post fue eliminado!');
		}
		elseif($postData['post_status'] == 1 && !$tsUser->is_admod) return array('denunciado','Oops! El Post se encuentra en revisi&oacute;n por acumulaci&oacute;n de denuncias.');
        elseif($postData['post_status'] == 2 && !$tsUser->is_admod) return array('deleted','Oops! El post fue eliminado!');
		elseif(!empty($postData['post_private']) && empty($tsUser->is_member)) return array('privado', $postData['post_title']);
        // BLOQUEADO
        $query = $this->select("u_bloqueos","bid","b_user = {$postData['post_user']} AND b_auser = {$tsUser->uid}","",1);
		$postData['block'] = $this->num_rows($query);
		$this->free($query);
		// FOLLOWS
		if($postData['post_seguidores'] > 0){
			$query = $this->query("SELECT follow_id FROM u_follows WHERE f_id = {$postData['post_id']} AND f_user = {$tsUser->uid} AND f_type = 2");
			$postData['follow'] = $this->num_rows($query);
			$this->free($query);
		}
		// CATEGORIAS
		$query = $this->query("SELECT c.c_nombre, c.c_seo FROM p_categorias AS c  WHERE c.cid = {$postData['post_category']}");
		$postData['categoria'] = $this->fetch_assoc($query);
		$this->free($query);
		// BBCode
		$postData['post_body'] = $tsCore->parseBBCode($postData['post_body']);
		$postData['user_firma'] = $tsCore->parseBBCodeFirma($postData['user_firma']);
		// TAGS
		$postData['post_tags'] = explode(",",$postData['post_tags']);
		$postData['n_tags'] = count($postData['post_tags']) - 1;
		// FECHA
		$postData['post_date'] = strftime("%d.%m.%Y a las %H:%M hs",$postData['post_date']);
		// NUEVA VISITA : FUNCION SIMPLE
		if($tsUser->is_member) $this->update("p_posts","post_hits = post_hits + 1","post_id = $post_id AND post_user != {$tsUser->uid}");
        // AGREGAMOS A VISITADOS... PORTAL
        if($tsCore->settings['c_allow_portal']){
            $query = $this->select("u_portal","last_posts_visited","user_id = {$tsUser->uid}","",1);
            $data = $this->fetch_assoc($query);
            $this->free($query);
            $visited = unserialize($data['last_posts_visited']);
            if(!is_array($visited)) $visited = array();
            $total = count($visited);
            if($total > 10){
                array_splice($visited, 0, 1); // HACK
            }
            //
            if(!in_array($postData['post_id'],$visited))
                array_push($visited,$postData['post_id']);
            //
            $visited = serialize($visited);
            $this->update("u_portal","last_posts_visited = '{$visited}'","user_id = {$tsUser->uid}");
        }
		//
		return $postData;
	}
	/*
		getSideData($array)
	*/
	function getAutor($user_id){
	   global $tsUser, $tsCore;
        // DATOS DEL AUTOR
        $query = $this->query("SELECT u.user_name, u.user_rango, u.user_seguidores, u.user_posts, u.user_comentarios, u.user_puntos, u.user_lastactive, u.user_last_ip, p.user_pais, p.user_sexo, p.user_firma FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id WHERE u.user_id = {$user_id} LIMIT 1");
        $data = $this->fetch_assoc($query);
        $this->free($query);
		// RANGOS DE ESTE USUARIO
		$query = $this->query("SELECT r_name, r_color, r_image FROM u_rangos WHERE rango_id = {$data['user_rango']} LIMIT 1");
		$data['rango'] = $this->fetch_assoc($query);
		$this->free($query);
        // STATUS
        $is_online = (time() - ($tsCore->settings['c_last_active'] * 60));
        $is_inactive = (time() - (($tsCore->settings['c_last_active'] * 60) * 2)); // DOBLE DEL ONLINE
        if($data['user_lastactive'] > $is_online) $data['status'] = array('t' => 'Online', 'css' => 'online');
        elseif($data['user_lastactive'] > $is_inactive) $data['status'] = array('t' => 'Inactivo', 'css' => 'inactive');
        else $data['status'] = array('t' => 'Offline', 'css' => 'offline');
		// PAIS
		include("../ext/datos.php");
		$data['pais'] = array('icon' => strtolower($data['user_pais']),'name' => $tsPaises[$data['user_pais']]);
		// FOLLOWS
		if($data['user_seguidores'] > 0){
			$query = $this->query("SELECT follow_id FROM u_follows WHERE f_id = {$user_id} AND f_user = {$tsUser->uid} AND f_type = 1");
			$data['follow'] = $this->num_rows($query);
			$this->free($query);
		}
		// RETURN
		return $data;
	}
	/*
		getEditPost()
	*/
	function getEditPost(){
		global $tsCore, $tsUser;
		//
		$pid = $tsCore->setSecure($_GET['pid']);
		//
		$query = $this->select("p_posts","*","post_id = {$pid}","",1);
		$ford = $this->fetch_assoc($query);
		$this->free($query);
        //
        if(empty($ford['post_id'])) return 'El post elegido no existe.';
        elseif(($tsUser->uid != $ford['post_user']) && ($tsUser->is_admod == 0)) return 'No puedes editar un post que no es tuyo.';
		// PEQUEÑO HACK
		foreach($ford as $key => $val){
			$iden = str_replace('post_','b_',$key);
			$data[$iden] = $val;
		}
		//
		return $data;
	}
	/*
		setDenuncia()
	*/
	function setDenuncia(){
		global $tsCore, $tsUser;
		//
		$post_id = $tsCore->setSecure($_POST['id']);
		$tsPost = $this->fetch_assoc($this->select("p_posts","post_user, post_denuncias","post_id = $post_id","",1));
		if(empty($tsPost['post_user'])) return array('titulo' => 'Error', 'mensaje' => 'Has querido denunciar un post que no existe.', 'but' => 'Ir a p&aacute;gina principal.');
		$tsDen = $this->num_rows($this->select("p_denuncias","den_id","d_post_id = $post_id AND d_user = {$tsUser->uid}","",1));
		if(!empty($tsDen)) return array('titulo' => 'Error', 'mensaje' => 'Ya habias denunciado este post.', 'but' => 'Ir a p&aacute;gina principal', 'link' => $tsCore->settings['url'], 'return' => 2);
		//
		$denData = array(
			'post_id' => $post_id,
			'post_user' => $tsPost['post_user'],
			'user' => $tsUser->uid,
			'razon' => $tsCore->setSecure($_POST['razon']),
			'extra' => $tsCore->setSecure($_POST['cuerpo']),
			'date' => time(),
		);
		//
		if($this->insert("p_denuncias","d_post_id, d_post_user, d_user, d_razon, d_extra, d_date","{$denData['post_id']}, {$denData['post_user']}, {$denData['user']}, {$denData['razon']}, '{$denData['extra']}', {$denData['date']}")){
			// DES ACTIVAR POST SI TIENE 3 DENUNCIAS
			$denuncias = $tsPost['post_denuncias'] + 1;
			$desactivar = ($denuncias >= 3) ? 1 : 0;
			if($this->update("p_posts","post_denuncias = post_denuncias + 1, post_status = $desactivar","post_id = $post_id")) return array('titulo' => 'Muchas gracias', 'mensaje' => 'La denuncia fue enviada', 'but' => 'Ir a p&aacute;gina principal','link' => $tsCore->settings['url'], 'return' => 2);
		}
	}
	/* 
		deletePost()
	*/
	function deletePost(){
		global $tsCore, $tsUser;
		//
		$post_id = $tsCore->setSecure($_POST['postid']);
		// ES SU POST EL Q INTENTA BORRAR?
		$query = $this->select("p_posts","post_id, post_title, post_body, post_category","post_id = {$post_id} AND post_user = {$tsUser->uid}");
		$data = $this->fetch_assoc($query);
		$this->free($query);
		// ES MIO O SOY MODERADOR/ADMINISTRADOR...
		if(!empty($data['post_id']) || !empty($tsUser->is_admod)){
            // SI ES MIS POST LO BORRAMOS Y MANDAMOS A BORRADORES
			if($this->delete("p_posts","post_id = {$post_id}")) {
			     if($this->delete("p_comentarios","c_post_id = {$post_id}")) {
                  if($this->insert("p_borradores","b_user, b_date, b_title, b_body, b_tags, b_category, b_status, b_causa","{$tsUser->uid}, unix_timestamp(), '{$data['post_title']}', '{$data['post_body']}', '', {$data['post_category']}, 2, ''"))
                    return "1: El post fue eliminado satisfactoriamente.";  
                 }
			}else {
                if($this->update("p_posts","post_status = 2","post_id = {$post_id}")) return "1: El post fue eliminado satisfactoriamente.";
			}
            
		} else return '0: Lo que intentas no est&aacute; permitido.';
	}
	/*
		getRelated()
	*/
	function getRelated($tags){
		global $tsCore, $tsUser;
		// ES UN ARRAT AHORA A UNA CADENA
		if(is_array($tags)) $tags = implode(", ",$tags);
		else str_replace('-',', ',$tags);
		//
		$query = $this->query("SELECT DISTINCT p.post_id, p.post_title, p.post_category, p.post_private, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE MATCH (post_tags) AGAINST ('$tags' IN BOOLEAN MODE) AND p.post_status = 0 AND post_sticky = 0 ORDER BY rand() LIMIT 0,10");
		//
		$data = $this->fetch_array($query);
		$this->free($query);
		//
		return $data;
	}
	/*
		getLastComentarios()
		: PARA EL PORTAL
	*/
	function getLastComentarios(){
		global $tsCore;
		//
		$query = $this->query("SELECT cm.cid, u.user_name, p.post_id, p.post_title, c.c_seo FROM p_comentarios AS cm LEFT JOIN u_miembros AS u ON cm.c_user = u.user_id LEFT JOIN p_posts AS p ON p.post_id = cm.c_post_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category ORDER BY cid DESC LIMIT 15");
		if(!$query) die($this->error());
		$data = $this->fetch_array($query);
		$this->free($query);
		//
		return $data;
	}
	/*
		getComentarios()
	*/
	function getComentarios($post_id){
		global $tsCore, $tsUser;
		//
		$start = $tsCore->setPageLimit($tsCore->settings['c_max_com']);
		$query = $this->query("SELECT u.user_name, c.* FROM u_miembros AS u LEFT JOIN p_comentarios AS c ON u.user_id = c.c_user WHERE c.c_post_id = $post_id ORDER BY c.cid LIMIT {$start}");
		// COMENTARIOS TOTALES
		$return['num'] = $this->num_rows($this->select("p_comentarios","cid","c_post_id = $post_id","",""));
		//
		$comments = $this->fetch_array($query);
		$this->free($query);
		// PARSEAR EL BBCODE
		$i = 0;
		foreach($comments as $comment){
			// CON ESTE IF NOS AHORRAMOS CONSULTAS :)
			if($comment['c_votos'] != 0){
				$query = $this->select("p_votos","voto_id","tid = {$comment['cid']} AND tuser = {$tsUser->uid} AND type = 2","",1);
				$votado = $this->num_rows($query);
				$this->free($query);
			} else $votado = 0;
			//
			$return['data'][$i] = $comment;
			$return['data'][$i]['votado'] = $votado;
			$return['data'][$i]['c_html'] = $tsCore->parseBBCode($return['data'][$i]['c_body']);
			$i++;
		}
		//
		return $return;
	}
	/*
		newComentario()
	*/
	function newComentario(){
		global $tsCore, $tsUser, $tsActividad;
		// NO MAS DE 1500 CARACTERES PUES NADIE COMENTA TANTO xD
		$comentario = $tsCore->setSecure(substr($_POST['comentario'],0,1500));
		$post_id = $tsCore->setSecure($_POST['postid']);
		/* DE QUIEN ES EL POST */
		$query = $this->select("p_posts","post_user, post_block_comments","post_id = {$post_id}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
        /* COMPROVACIONES */
        $tsText = preg_replace('# +#',"",$comentario);
        $tsText = str_replace("\n","",$tsText);
        if($tsText == '') return '0: El campo <b>Comentario</b> es requerido para esta operaci&oacute;n';
		/*        ------       */
		$most_resp = $_POST['mostrar_resp'];
		$fecha = time();
		//
        if($data['post_user']){
            if($data['post_block_comments'] != 1 || $data['post_user'] == $tsUser->uid){
                // ANTI FLOOD
                $tsCore->antiFlood();
                if($this->insert("p_comentarios","c_post_id, c_user, c_date, c_body","$post_id, {$tsUser->uid}, $fecha, '$comentario'")){
        		  	$cid = $this->insert_id();
        		  	// AGREGAR A LAS ESTADISTICAS
        		  	$this->update("w_stats","stats_comments = stats_comments + 1","stats_no = 1");
        		  	$this->update("u_miembros","user_comentarios = user_comentarios + 1","user_id = {$tsUser->uid}");
        		  	$this->update("p_posts","post_comments = post_comments + 1","post_id = {$post_id}");
                    // NOTIFICAR SI FUE CITADO Y A LOS QUE SIGUEN ESTE POST, DUEÑO
                    $this->quoteNoti($post_id, $data['post_user'], $cid, $comentario);
                    // ACTIVIDAD
                    $tsActividad->setActividad(5, $post_id);
        		  	// array(comid, comhtml, combbc, fecha, autor_del_post)
        			if(!empty($most_resp)) return array($cid, $tsCore->parseBBCode($comentario),$comentario, $fecha, $_POST['auser']);
        			else return '1: Tu comentario fue agregado satisfactoriamente.';
        		} else return '0: Ocurri&oacute; un error int&eacute;ntalo m&aacute;s tarde.';
            } else return '0: El post se encuentra cerrado y no se permiten comentarios.';
        } else return '0: El post no existe.';
	}
    /*
        quoteNoti()
        :: Avisa cuando citan los comentarios.
    */
    function quoteNoti($post_id, $post_user, $cid, $comentario){
        global $tsCore, $tsUser, $tsMonitor;
        $ids = array();
        $total = 0;
        //
        preg_match_all("/\[quote=(.*?)\]/is",$comentario,$users);
        //
        if(!empty($users[1])) {
            foreach($users[1] as $user){
                # DATOS
                $udata = explode('|',$user);
                if(!is_array($udata)) {
                    $user = $user;   
                    $lcid = $cid;
                }
                else {
                    $user = $udata[0];
                    $lcid = (int) $udata[1];
                }
                # COMPROBAR
                if($user != $tsUser->nick){
                    $uid = $tsUser->getUserID($tsCore->setSecure($user));
                    if(!empty($uid) && $uid != $tsUser->uid && !in_array($uid, $ids)){
                        $ids[] = $uid;
                        $tsMonitor->setNotificacion(9, $uid, $tsUser->uid, $post_id, $lcid);
                    }
                    ++$total;
                }
            }
        }
	  	// AGREGAR AL MONITOR DEL DUEÑO DEL POST SI NO FUE CITADO
        if(!in_array($post_user, $ids)){
	  	    $tsMonitor->setNotificacion(2, $post_user, $tsUser->uid, $post_id);
        }
        // ENVIAR NOTIFICAIONES A LOS Q SIGUEN EL POST :D
        // PERO NO A LOS QUE CITARON :)
        $tsMonitor->setFollowNotificacion(7, 2, $tsUser->uid, $post_id, 0, $ids);
        // 
        return true;
    }
    /*
        editComentario()
    */
    function editComentario(){
        global $tsUser, $tsCore;
        //
        $cid = $_POST['cid'];
        $comentario =  $_POST['comentario'];
        /* COMPROVACIONES */
        $tsText = preg_replace('# +#',"",$comentario);
        $tsText = str_replace("\n","",$tsText);
        if($tsText == '') return '0: El campo <b>Comentario</b> es requerido para esta operaci&oacute;n';
        //
        $query = $this->select("p_comentarios","c_user","cid = {$cid}","",1);
        $cuser = $this->fetch_assoc($query);
        $this->free($query);
        //
        if($tsUser->is_admod > 0 || $tsUser->uid == $cuser['c_user']){
            // ANTI FLOOD
            $tsCore->antiFlood();
            if($this->update("p_comentarios","c_body = '{$comentario}'","cid = {$cid}"))
                return '1: El comentario fue editado.';
            else return '0: Ocurri&oacute; un error :(';
        } else return '0: Hey este comentario no es tuyo.';
    }
	/* 
		delComentario()
	*/
	function delComentario(){
		global $tsCore, $tsUser;
		//
		$comid = $tsCore->setSecure($_POST['comid']);
		$autor = $tsCore->setSecure($_POST['autor']);
		$post_id = $tsCore->setSecure($_POST['postid']);
		// ES DE MI POST EL COMENTARIO?
		$query = $this->select("p_posts","post_id","post_id = {$post_id} AND post_user = {$tsUser->uid}");
		$is_mypost = $this->num_rows($query);
		$this->free($query);
		// ES MI COMENTARIO?
		$query = $this->select("p_comentarios","cid","cid = {$comid} AND c_user = {$tsUser->uid}");
		$is_mycmt = $this->num_rows($query);
		$this->free($query);
		// SI ES....
		if(!empty($is_mypost) || !empty($is_mycmt) || !empty($tsUser->is_admod)){
			if($this->delete("p_comentarios","cid = $comid AND c_user = $autor AND c_post_id = $post_id")) {
				// RESTAR A LAS ESTADISTICAS
				$this->update("w_stats","stats_comments = stats_comments - 1","stats_no = 1");
				$this->update("u_miembros","user_comentarios = user_comentarios - 1","user_id = {$tsUser->uid}");
                $this->update("p_posts","post_comments = post_comments - 1","post_id = {$post_id}");
				// BORRAR LOS VOTOS
				$this->delete("p_votos","tid = {$comid}");
				//
				return '1: Comentario borrado.';
			}
			else return '0: Ocurri&oacute; un error, intentalo m&aacute;s tarde.';
		} else return '0: No tienes permiso de hacer esto.';
	}
	/*
		votarComentario()
	*/
	function votarComentario(){
		global $tsCore, $tsUser, $tsMonitor, $tsActividad;
		// VOTAR
		$cid = $tsCore->setSecure($_POST['cid']);
		$post_id = $tsCore->setSecure($_POST['postid']);
		$votoVal = ($_POST['voto'] == 1) ? 1 : 0;
		$voto = ($votoVal == 1) ? "+ 1" : "- 1";
		//
		$query = $this->select("p_comentarios","c_user","cid = {$cid}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
		// ES MI COMENTARIO?
		$is_mypost = ($data['c_user'] == $tsUser->uid) ? true : false;
		// NO ES MI COMENTARIO, PUEDO VOTAR
		if(!$is_mypost){
			// YA LO VOTE?
			$query = $this->select("p_votos","tid","tid = {$cid} AND tuser = {$tsUser->uid} AND type = 2","",1);
			$votado = $this->num_rows($query);
			$this->free($query);
			if(empty($votado)){
				// SUMAR VOTO
				$this->update("p_comentarios","c_votos = c_votos {$voto}","cid = {$cid}");
				// INSERTAR EN TABLA
				if($this->insert("p_votos","tid, tuser, type","{$cid}, {$tsUser->uid}, 2")){
					// SUMAR PUNTOS??
					if($votoVal == 1 && $tsCore->settings['c_allow_sump'] == 1) {
                        $this->update("u_miembros","user_puntos = user_puntos + 1","user_id = {$data['c_user']}");
                        $this->subirRango($data['c_user']);
					}
					// AGREGAR AL MONITOR
					$tsMonitor->setNotificacion(8, $data['c_user'], $tsUser->uid, $post_id, $cid, $votoVal);
                    // ACTIVIDAD
                    $tsActividad->setActividad(6, $post_id, $votoVal);
				}
				//
				return '1: Gracias por tu voto';
			} return '0: Ya has votado este comentario';
		} else return '0: No puedes votar tu propio comentario';
	}
	/*
		votarPost()
	*/
	function votarPost(){
		global $tsCore, $tsUser, $tsMonitor, $tsActividad;
		//
		$post_id = $tsCore->setSecure($_POST['postid']);
		$puntos = (int) $tsCore->setSecure($_POST['puntos']);
        $puntos = abs($puntos); // ERROR CORREGIDO :D
		// SUMAR PUNTOS
		$query = $this->select("p_posts","post_user","post_id = {$post_id}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
		// ES MI POST?
		$is_mypost = ($data['post_user'] == $tsUser->uid) ? true : false;
		// NO ES MI POST, PUEDO VOTAR
		if(!$is_mypost){
			// YA LO VOTE?
			$query = $this->select("p_votos","tid","tid = {$post_id} AND tuser = {$tsUser->uid} AND type = 1","",1);
			$votado = $this->num_rows($query);
			$this->free($query);
			if(empty($votado)){
				// TENGO SUFICIENTES PUNTOS
				if($tsUser->info['user_puntosxdar'] >= $puntos){
					// SUMAR PUNTOS AL POST
					$this->update("p_posts","post_puntos = post_puntos + {$puntos}","post_id = {$post_id}");
					// SUMAR PUNTOS AL DUEÑO DEL POST
					$this->update("u_miembros","user_puntos = user_puntos + {$puntos}","user_id = {$data['post_user']}");
					// RESTAR PUNTOS AL VOTANTE
					$this->update("u_miembros","user_puntosxdar = user_puntosxdar - {$puntos}","user_id = {$tsUser->uid}");
					// INSERTAR EN TABLA
					$this->insert("p_votos","tid, tuser, type","{$post_id}, {$tsUser->uid}, 1");
					// AGREGAR AL MONITOR
					$tsMonitor->setNotificacion(3, $data['post_user'], $tsUser->uid, $post_id, $puntos);
                    // ACTIVIDAD
                    $tsActividad->setActividad(3, $post_id, $puntos);
					// SUBIR DE RANGO
					$this->subirRango($data['post_user'], $post_id);
					//
					return '1: Puntos agregados!';
				} else return '0: Voto no v&aacute;lido. No puedes dar '.$puntos.' puntos, s&oacute;lo te quedan '.$tsUser->info['user_puntosxdar'].'.';
			} return '0: No es posible votar a un mismo post m&aacute;s de una vez.';
		} else return '0: No puedes votar tu propio post.';
	}
	/*
		saveFavorito()
	*/
	function saveFavorito(){
		global $tsCore, $tsUser, $tsMonitor, $tsActividad;
        # ANTIFLOOD
		//
		$post_id = $tsCore->setSecure($_POST['postid']);
		$fecha = (int) empty($_POST['reactivar']) ? time() : $tsCore->setSecure($_POST['reactivar']);
		/* DE QUIEN ES EL POST */
		$query = $this->select("p_posts","post_user","post_id = {$post_id}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
		/*        ------       */
		if($data['post_user'] != $tsUser->uid){
			// YA LO TENGO?
			$my_favorito = $this->num_rows($this->select("p_favoritos","fav_id","fav_post_id = {$post_id} AND fav_user = {$tsUser->uid}","",1));
			if(empty($my_favorito)){
				if($this->insert("p_favoritos","fav_user, fav_post_id, fav_date","{$tsUser->uid}, {$post_id}, {$fecha}")) {
					// UNO MAS
					$this->update("p_posts","post_favoritos = post_favoritos + 1","post_id = {$post_id}");
					// AGREGAR AL MONITOR
					$tsMonitor->setNotificacion(1, $data['post_user'], $tsUser->uid, $post_id);
                    // ACTIVIDAD 
                    $tsActividad->setActividad(2, $post_id);
                    //
					return '1: Bien! Este post fue agregado a tus favoritos.';
				}
				else return '0: '.$this->error();
			} else return '0: Este post ya lo tienes en tus favoritos.';
		} else return '0: No puedes agregar tus propios post a favoritos.';
	}
	/*
		getFavoritos()
	*/
	function getFavoritos(){
		global $tsCore, $tsUser;
		//
		$query = $this->query("SELECT f.fav_id, f.fav_date, p.post_id, p.post_title, p.post_date, p.post_puntos, p.post_comments, c.c_nombre, c.c_seo, c.c_img FROM p_favoritos AS f LEFT JOIN p_posts AS p ON p.post_id = f.fav_post_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE f.fav_user = {$tsUser->uid}");
		$data = $this->fetch_array($query);
		$this->free($query);
		//
		foreach($data as $fav){
			$favoritos .= '{"fav_id":'.$fav['fav_id'].',"post_id":'.$fav['post_id'].',"titulo":"'.$fav['post_title'].'","categoria":"'.$fav['c_seo'].'","categoria_name":"'.$fav['c_nombre'].'","imagen":"'.$fav['c_img'].'","url":"'.$tsCore->settings['url'].'/posts/'.$fav['c_seo'].'/'.$fav['post_id'].'/'.$tsCore->setSEO($fav['post_title']).'.html","fecha_creado":'.$fav['post_date'].',"fecha_creado_formato":"'.strftime("%d\/%m\/%Y a las %H:%M:%S hs",$fav['post_date']).'.","fecha_creado_palabras":"'.$tsCore->setHace($fav['post_date'],true).'","fecha_guardado":'.$fav['fav_date'].',"fecha_guardado_formato":"'.strftime("%d\/%m\/%Y a las %H:%M:%S hs",$fav['fav_date']).'.","fecha_guardado_palabras":"'.$tsCore->setHace($fav['fav_date'],true).'","puntos":'.$fav['post_puntos'].',"comentarios":'.$fav['post_comments'].'},';
		}
		//
		return $favoritos;
	}
	/*
		delFavorito()
	*/
	function delFavorito(){
		global $tsCore, $tsUser;
		//
		$fav_id = $tsCore->setSecure($_POST['fav_id']);
		$query = $this->select("p_favoritos","fav_post_id","fav_id = {$fav_id} AND fav_user = {$tsUser->uid}","",1);
		$data = $this->fetch_assoc($query);
		$is_myfav = $this->num_rows($query);
		$this->free($query);
		// ES MI FAVORITO?
		if(!empty($data['fav_post_id'])){
			if($this->delete("p_favoritos","fav_id = {$fav_id} AND fav_user = {$tsUser->uid}")){
				$this->update("p_posts","post_favoritos = post_favoritos - 1","post_id = {$data['fav_post_id']}");
				return '1: Favorito borrado.';
			} else return '0: No se pudo borrar.';
		} else return '0: No se pudo borrar, no es tu favorito.';
	}
	/*
		subirRango()
	*/
	function subirRango($user_id, $post_id = false){
		global $tsCore, $tsUser;
		// CONSULTA
        $query = $this->query("SELECT u.user_puntos, u.user_posts, u.user_rango, r.r_special FROM u_miembros AS u LEFT JOIN u_rangos AS r ON u.user_rango = r.rango_id WHERE u.user_id = {$user_id} LIMIT 1");
		$data = $this->fetch_assoc($query);
		$this->free($query);
		// SI TIEN RANGO ESPECIAL NO ACTUALIZAMOS....
        if(!empty($data['r_special']) && $data['user_rango'] != 3) return true;
        // SI SOLO SE PUEDE SUBIR POR UN POST
        if(!empty($post_id) && $tsCore->settings['c_newr_type'] == 0) {
            $query = $this->select("p_posts","post_puntos","post_id = {$post_id}","",1);
            $puntos = $this->fetch_assoc($query);
            $this->free($query);
            // MODIFICAMOS
            $data['user_puntos'] = $puntos['post_puntos'];
        }
        //
		$puntos_actual = $data['user_puntos'];
		$posts_actual = $data['user_posts'];
        $rango_actual = $data['user_rango'];
		// RANGOS
		$query = $this->select("u_rangos","rango_id, r_min_points, r_min_posts","rango_id != {$rango_actual} AND r_special = 0","r_min_points");
		$data = $this->fetch_array($query);
		$this->free($query);
		//
		foreach($data as $rango){
			// SUBIR USUARIO
			if(!empty($rango['r_min_points']) && $rango['r_min_points'] <= $puntos_actual){
				$newRango = $rango['rango_id'];
			}elseif(!empty($rango['r_min_posts']) && $rango['r_min_posts'] <= $posts_actual){
				$newRango = $rango['rango_id'];
			}
		}
		//HAY NUEVO RANGO?
		if(!empty($newRango)){
			//
			if($this->update("u_miembros","user_rango = {$newRango}","user_id = {$user_id}")) return true;
		}
	}
    /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								BUSCADOR
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /*
        getQuery()
    */
    function getQuery(){
        global $tsCore, $tsUser;
        //
        $q = $tsCore->setSecure($_GET['q']);
        $c = $tsCore->setSecure($_GET['cat']);
        $a = $tsCore->setSecure($_GET['autor']);
        $e = $_GET['e'];
        // ESTABLECER FILTROS
        if($c > 0) $where_cat = "AND p.post_category = {$c}";
        if($e == 'tags') $search_on = "p.post_tags";
        else $search_on = "p.post_title";
        // BUSQUEDA
        $w_search = "AND MATCH({$search_on}) AGAINST('{$q}' IN BOOLEAN MODE)";
        // SELECCIONAR USUARIO 
        if(!empty($a)){
            // OBTENEMOS ID
            $aid = $tsUser->getUserID($a);
            // BUSCAR LOS POST DEL USUARIO SIN CRITERIO DE BUSQUEDA
            if(empty($q) && $aid > 0) $w_search = "AND p.post_user = {$aid}";
            // BUSCAMOS CON CRITERIO PERO SOLO LOS DE UN USUARIO
            elseif($aid >= 1) $w_autor = "AND p.post_user = {$aid}";
            //
        }
        // PAGINAS
        $query = $this->query("SELECT COUNT(p.post_id) AS total FROM p_posts AS p WHERE p.post_status = 0 {$where_cat} {$w_autor} {$w_search} ORDER BY p.post_date");
        $total = $this->fetch_assoc($query);
        $total = $total['total'];
        $this->free($query);
        $data['pages'] = $tsCore->getPagination($total, 12);
        //
        $query = $this->query("SELECT p.post_id, p.post_user, p.post_category, p.post_title, p.post_date, p.post_comments, p.post_puntos, p.post_favoritos, u.user_name, c.c_seo, c.c_nombre, c.c_img FROM p_posts AS p LEFT JOIN u_miembros AS u ON u.user_id = p.post_user LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 {$where_cat} {$w_autor} {$w_search} ORDER BY p.post_date DESC LIMIT {$data['pages']['limit']}");
        $data['data'] = $this->fetch_array($query);
        $this->free($query);
        // ACTUALES
        $total = explode(',',$data['pages']['limit']);
        $data['total'] = ($total[0]) + count($data['data']);
        //
        return $data;
    }
    
}
?>
