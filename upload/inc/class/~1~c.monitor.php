<?php
/********************************************************************************
* c.monitor.php 	                                                            *
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
class tsMonitor extends tsDatabase {
	/**
     * @name notificaciones 
     * @access public
     * @info NUMERO DE NOTIFICACIONES NUEVAS
     **/
	public $notificaciones = 0;
    /**
     * @name monitor
     * @access private
     * @info ORACIONES PARA CADA NOTIFICACION
     **/
    private $monitor = array();
    /**
     * @name show_type
     * @access public
     * @info COMO MOSTRAREMOS LAS NOTIFICACIONES -> AJAX/NORMAL
     **/
     public $show_type = 1;
    /**
     * @name getInstanse
     * @access public
     * @info CREAR INSTANCIA DE LA CLASE
     */
    public function &getInstance(){
		static $instance;
		if( is_null($instance) ){
			$instance = new tsMonitor();
    	}
		return $instance;
	}
	/*
		constructor()
	*/
	public function __construct(){
		global $tsUser;
		// VISITANTE?
		if(empty($tsUser->is_member)) return false;
		// NOTIFICACIONES
		$query = $this->query("SELECT COUNT(not_id) AS total FROM u_monitor WHERE user_id = {$tsUser->uid} AND not_menubar > 0");
		$data = $this->fetch_assoc($query);
		$this->free($query);
		//
		$this->notificaciones = $data['total'];
        /**
         * AVISOS
        */ 
        //$this->getAvisos();
	}
    /**
     * @name makeMonitor
     * @access private
     * @params none
     * @return none
     */
    private function makeMonitor(){
        $this->monitor = array(
            1 => array('text' => 'agreg&oacute; a favoritos tu', 'ln_text' => 'post', 'css' => 'star'),
            2 => array('text' => array('coment&oacute; tu','_REP_ nuevos comentarios en tu'), 'ln_text' => 'post', 'css' => 'comment_post'),
            3 => array('text' => 'dej&oacute; _REP_ puntos en tu', 'ln_text' => 'post', 'css' => 'points'),
            4 => array('text' => 'te est&aacute; siguiendo', 'ln_text' => 'Seguir a este usuario', 'css' => 'follow'),
            5 => array('text' => 'cre&oacute; un nuevo', 'ln_text' => 'post', 'css' => 'post'),
            6 => array('text' => array('te recomienda un', '_REP_ usuarios te recomiendan un'), 'ln_text' => 'post', 'css' => 'share'),
            7 => array('text' => array('coment&oacute; en un', '_REP_ nuevos comentarios en el'), 'ln_text' => 'post', 'extra' => 'que sigues', 'css' => 'blue_ball'),
            8 => array('text' => array('vot&oacute; _REP_ tu', '_REP_ nuevos votos a tu'), 'ln_text' => 'comentario', 'css' => 'voto_'),
            9 => array('text' => array('respondi&oacute; tu', '_REP_ nuevas respuestas a tu'), 'ln_text' => 'comentario', 'css' => 'comment_resp'),
            10 => array('text' => 'sub&oacute; una nueva', 'ln_text' => 'foto', 'css' => 'photo'),
            11 => array('text' => array('coment&oacute; tu','_REP_ nuevos comentarios en tu'), 'ln_text' => 'foto', 'css' => 'photo'),
            12 => array('text' => 'public&oacute; en tu', 'ln_text' => 'muro', 'css' => 'wall_post'),
            13 => array('text' => array('coment&oacute; ', '_REP_ nuevos comentarios en'), 'ln_text' => 'publicaci&oacute;n', 'extra' => 'coment&oacute;', 'css' => 'w_comment'),
            14 => array('text' => array('le gusta tu', 'A _REP_ personas les gusta tu'), 'ln_text' => array('publicaci&oacute;n','comentario'), 'css' => 'w_like'),
        );
    }
    /* 
        getAvisos()
    */
    function getAvisos(){
        global $tsUser, $tsCore;
        // CARGAMOS
		$query = $this->query("SELECT * FROM u_avisos WHERE av_user = {$tsUser->uid}");
		$data = $this->fetch_array($query);
		$this->free($query);
        //
        if($data) {
            $this->avisos = $tsCore->setJSON($data);
        }
        // SOLO SE MUESTRAN UNA VEZ
        $this->delete("u_avisos","av_user = {$tsUser->uid}");
    }
    /*
        setAviso($user_id, $body)
    */
    function setAviso($user_id, $body){
        $date = time();
        if($this->insert("u_avisos","av_user, av_body, av_date","{$user_id}, '$body', {$date}")) return true;
        else return false;
    }
	/**
     * @name setNotificacion
     * @access public
     * @param int
     * @return void
     */
    public function setNotificacion($type, $user_id, $obj_user, $obj_uno = 0, $obj_dos = 0, $obj_tres = 0){
		global $tsUser, $tsCore;
		# NO SE MOSTRARA MI PROPIA ACTIVIDAD
		if($user_id != $tsUser->uid){
            // VERIFICAR CUANTAS NOTIFICACIONES DEL MISMO TIPO Y EN POCO TIEMPO TENEMOS
            $tiempo = time() - 3600; //  HACE UNA HORA
            $query = $this->query("SELECT not_id FROM u_monitor WHERE user_id = {$user_id} AND obj_uno = {$obj_uno} AND obj_dos = {$obj_dos} AND not_type = {$type} AND not_date > {$tiempo} AND not_menubar > 0 ORDER BY not_id DESC LIMIT 1");
            $not_data = $this->fetch_assoc($query);
            $this->free($query);
            //....
            if(!empty($not_data['not_id'])) $not_db_type = 'update'; //ACTUALIZAR
            else $not_db_type = 'insert'; // INSERTAR
			// COMPROVAR LIMITE DE NOTIFICACIONES
			$query = $this->query("SELECT not_id FROM u_monitor WHERE user_id = {$user_id} ORDER BY not_id DESC");
            $data = $this->fetch_array($query);
            $this->free($query);
            $ntotal = count($data);
            $delid = $data[$ntotal-1]['not_id']; // ID DE ULTIMA NOTIFICACION
			// ELIMINAR NOTIFICACIONES?
			if($ntotal > $tsCore->settings['c_max_nots']){
                $this->delete("u_monitor","not_id = {$delid}");
			}
            // ACTUALIZAMOS / INSERTAMOS
            if($not_db_type == 'update'){
                if($this->update("u_monitor","obj_user = {$obj_user}, not_total = not_total + 1","not_id = {$not_data['not_id']}"))
                return true;
            } else {
                if($this->insert("u_monitor","user_id, obj_user, obj_uno, obj_dos, obj_tres, not_type, not_date","{$user_id}, {$obj_user}, {$obj_uno}, {$obj_dos}, {$obj_tres}, {$type}, unix_timestamp()"))
                return true;   
            }
		}
	}
	/**
     * @name setFollowNotificacion
     * @access public
     * @params int
     * @return void
     * @info Envia notificaciones a los usuarios que siguen a un post o usuario.
	*/
	function setFollowNotificacion($notType, $f_type, $user_id, $obj_uno, $obj_dos = 0, $excluir){
		global $tsCore;
		# TIPO DE FOLLOW USER o POST
        if($f_type == 1) $f_id = $user_id;
        elseif($f_type == 2) $f_id = $obj_uno;
		# BUSCAMOS LOS Q SIGAN A ESTE POST/ USER
		$query = $this->select("u_follows","f_user","f_id = {$f_id} AND f_type = {$f_type}");
		$data = $this->fetch_array($query);
		$this->free($query);
		//
		foreach($data as $key => $val){
			// A CADA USUARIO LE NOTIFICAMOS SI NO ESTA EN LAS EXCLUSIONES
            if(!in_array($val['f_user'],$excluir)){
                $this->setNotificacion($notType, $val['f_user'], $user_id, $obj_uno, $obj_dos);
            }
		}
		//
		return true;
	}
    /**
     * @name setMuroRepost
     * @access public
     * @params int
     * @return void
     * @info NOTIFICA CUANDO ALGUIEN RESPONDE UNA PUBLICACION EN UN MURO
     */
    public function setMuroRepost($pub_id, $p_user, $p_user_pub){
       global $tsUser;
        //
        $query = $this->select("u_muro_comentarios","c_user","pub_id = {$pub_id} AND c_user NOT IN ({$tsUser->uid}, {$p_user})");
        $data = $this->fetch_array($query);
		$this->free($query);
        // ENVIAMOS NOTIFICACION A LOS QUE HAYAN COMENTADO
        $enviados = array();
        foreach($data as $key => $val){
            if(!in_array($val['c_user'], $enviados)){
                $this->setNotificacion(13, $val['c_user'], $tsUser->uid, $pub_id, 3);
                $enviados[] = $val['c_user'];
            }
        }
        // ENVIAMOS AL DUEÑO DEL MURO
        $this->setNotificacion(13, $p_user, $tsUser->uid, $pub_id, 1);
        // ENVIAMOS AL QUE PUBLICO SI NO FUE EL DUEÑO DEL MURO
        if(($p_user != $p_user_pub) && !in_array($p_user_pub, $enviados)){
            $this->setNotificacion(13, $p_user_pub, $tsUser->uid, $pub_id, 2);    
        }
    }
    /**
     * @name getNotificaciones
     * @access public
     * @param int
     * @return array
     * @info CREAR UN ARRAY CON LAS NOTIFICAIONES DEL USUARIO
     */
	public function getNotificaciones($unread = false){
		global $tsUser, $tsCore;
		# SI HAY MAS DE 5 NOTIS MOSTRAMOS TODAS LAS NO LEIDAS
		if($this->show_type == 1) {
            // VIEW TYPE
            $not_view = ($unread == true) ? '= 2' : ' > 0';
            $not_del = ($unread == true) ? 1 : 0;
            //
            if($this->notificaciones > 5 || $unread == true ){
    			// CONSULTA
    			$query = $this->query("SELECT m.*, u.user_name AS usuario FROM u_monitor AS m LEFT JOIN u_miembros AS u ON m.obj_user = u.user_id WHERE m.user_id = {$tsUser->uid} AND m.not_menubar {$not_view} ORDER BY m.not_id DESC");
            } else {
    			// CONSULTA
    			$query = $this->query("SELECT m.*, u.user_name AS usuario FROM u_monitor AS m LEFT JOIN u_miembros AS u ON m.obj_user = u.user_id WHERE m.user_id = {$tsUser->uid} ORDER BY m.not_id DESC LIMIT 5");
            }
            // UPDATE
            $this->update("u_monitor","not_menubar = {$not_del}","user_id = {$tsUser->uid} AND not_menubar > 0");
		// SI VA AL MONITOR ENTONCES ACTUALIZAMOS PARA QUE YA NO SE VEAN EN EL MENUBAR
		} elseif($this->show_type == 2) {
            // DATOS
            $query = $this->query("SELECT m.*, u.user_name AS usuario FROM u_monitor AS m LEFT JOIN u_miembros AS u ON m.obj_user = u.user_id WHERE m.user_id = {$tsUser->uid} ORDER BY m.not_id DESC");
            //
			$this->update("u_monitor","not_menubar = 0, not_monitor = 0","user_id = {$tsUser->uid} AND not_monitor = 1");
			// CARGAMO LAS ESTADISTICAS
			$cuery = $this->query("SELECT f_type, COUNT(follow_id) AS total FROM u_follows WHERE f_user = {$tsUser->uid} GROUP BY f_type");
			$stats = $this->fetch_array($cuery);
			$this->free($cuery);
			// ARMAMOS PARA MAS COMODIDAD
			for($i = 1; $i <= 5; $i++){
				$dataDos['stats'][$i] = empty($stats[$i-1]['total']) ? 0 : $stats[$i-1]['total'];
			}
            // CARGO LOS FILTROS
            $filtros = $_COOKIE['monitor'];
            $filtros = unserialize($filtros);
            foreach($filtros as $key => $val){
                $dataDos['filtro'][$val] = true;
            }
		} 
        // PROCESOS
		$data = $this->fetch_array($query);
		$this->free($query);
		if(empty($data)) return false; // NO HAY NOTIFICACIONES
        // TOTAL DE NOTIDICACIONES
        $dataDos['total'] = count($data);
		// ARMAR TEXTOS Y LINKS :)
		$dataDos['data'] = $this->armNotificaciones($data);
		//
		return $dataDos;
	}
	/**
     * @name armarNotificacion
     * @access private
     * @param array, int
     * @return array
     * @info CREA LAS NOTIFICACIONES
	*/
	private function armNotificaciones($array){
        # ARMAMOS LAS ORACIONES
        $this->makeMonitor();
		# PARA CADA VALOR CREAR UNA CONSULTA
		foreach($array as $key => $val){
			// CREAR CONSULTA
			$sql = $this->makeConsulta($val);
			// CONSULTAMOS
			if(is_array($sql)){
				$dato = $sql;
			}else {
				$query = $this->query($sql);
				$dato = $this->fetch_assoc($query);
				$this->free($query);
			}
			$dato = array_merge($dato, $val);
            // SI AUN EXISTE LO QUE VAMOS A NOTIFICAR..
            $data[] = $this->makeOracion($dato);
            /*if($dato)
			 $data[] = $this->armarTextos($dato,$val['not_type'],$type);*/
		}
		//
		return $data;
	}
	/**
     * @name makeConsulta
     * @access private
     * @param array
     * @return string
     * @info RETORNA UNA CONSULTA DEPENDIENDO EL TIPO DE NOTIFICACION
	*/
	function makeConsulta($data){
		# CON UN SWITCH ESCOGEMOS LA CONSULTA APROPIADA
		switch($data['not_type']){
			// EN ESTOS CASOS SE NECESITA LO MISMO
			// $nombredeusuario ********** tu $titulodelpost;
			case 1: 
			case 2: 
			case 3: 
			case 5: 
			case 6:
			case 7:
            case 8:
            case 9:
                return "SELECT p.post_id, p.post_title, c.c_seo FROM p_posts AS p LEFT JOIN p_categorias AS c ON p.post_category = c.cid WHERE p.post_id = {$data['obj_uno']} LIMIT 1";
			break;
			// FOLLOW
			case 4:
                global $tsUser;
				// CHECAR SI YA LO SEGUIMOS
                $i_follow = $tsUser->iFollow($data['obj_user']);
                return array('follow' => $i_follow);
			break;
            // PUBLICO EN TU MURO
            case 12:
                return "SELECT p.pub_id, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user_pub = u.user_id WHERE p.pub_id = {$data['obj_uno']} LIMIT 1";
            break;
            case 13:
                global $tsUser;
                // HAY MAS DE UNA NOTIFICACION DEL MISMO TIPO
                $query = $this->query("SELECT p.pub_id, p.p_user, p.p_user_pub, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user = u.user_id WHERE p.pub_id = {$data['obj_uno']} LIMIT 1");
				$dato = $this->fetch_assoc($query);
				$this->free($query);
                //
                $dato['p_user_resp'] = $data['obj_user'];
                $dato['p_user_name'] = $dato['user_name']; // // DUEÑO DEL MURO
                $dato['user_name'] = $tsUser->getUserName($data['obj_user']); // QUIEN PUBLICO
                //
                return $dato;
            break;
            case 14:
                return array('value' => 'hack');
            break;
		}
	}
    /**
     * @name makeOracion
     * @access private
     * @param array, int, int
     * @return array
     * @info RETORNA LAS ORACIONES A MOSTRAR EN EL MONITOR
    */
    private function makeOracion($data){
        # GOBALES
        global $tsCore, $tsUser;
        # LOCALES
        $site_url = $tsCore->settings['url'];
        $no_type = $data['not_type'];
        $txt_extra = ($this->show_type == 1) ? '' : ' '.$this->monitor[$no_type]['ln_text'];
        $ln_text = $this->monitor[$no_type]['ln_text'];
        $ln_text = is_array($ln_text) ? $ln_text[$data['obj_dos']-1] : $ln_text;
        //
        $oracion['unread'] = ($this->show_type == 1) ? $data['not_menubar'] : $data['not_monitor'];
        $oracion['style'] = $this->monitor[$no_type]['css'];
        $oracion['date'] = $data['not_date'];
        $oracion['user'] = $data['usuario'];
        $oracion['avatar'] = $data['obj_user'].'_32.jpg';
        $oracion['total'] = $data['not_total'];
        # CON UN SWITCH ESCOGEMOS QUE ORACION CONSTRUIR
        switch($no_type){
            case 1:
            case 3:
            case 5:
                // 
                $oracion['text'] = $this->monitor[$no_type]['text'].$txt_extra;
                if($no_type == 3) $oracion['text'] = str_replace('_REP_', "<b>{$data['obj_dos']}</b>", $oracion['text']);
                $oracion['link'] = $site_url.'/posts/'.$data['c_seo'].'/'.$data['post_id'].'/'.$tsCore->setSEO($data['post_title']).'.html';
                $oracion['ltext'] = ($this->show_type == 1) ? $ln_text : $data['post_title'];
                $oracion['ltit'] = ($this->show_type == 1) ? $data['post_title'] : '';
            break;
            // FOLLOW
            case 4:
                $oracion['text'] = $this->monitor[$no_type]['text'];
                if($data['follow'] != true && $this->show_type == 2) {
                    $oracion['link'] = '#" onclick="notifica.follow(\'user\', '.$data['obj_user'].', notifica.userInMonitorHandle, this)';
                    $oracion['ltext'] = $this->monitor[$no_type]['ln_text'];    
                }
            break;
            // PUEDEN SER MAS DE UNO
            case 2:
            case 6:
            case 7:
            case 8:
            case 9:
                // CUANTOS
                $no_total = $data['not_total'];
                // MAS DE UNA ACCION
                if($no_total > 1) {
                    $text = $this->monitor[$no_type]['text'][1].$txt_extra;
                    $oracion['text'] = str_replace('_REP_', "<b>{$no_total}</b>", $text);
                }
                else $oracion['text'] = $this->monitor[$no_type]['text'][0].$txt_extra;
                // ID COMMENT
                if($no_type == 8 || $no_type == 9){
                    $id_comment = '#div_cmnt_'.$data['obj_dos'];
                }
                //
                $oracion['link'] = $site_url.'/posts/'.$data['c_seo'].'/'.$data['post_id'].'/'.$tsCore->setSEO($data['post_title']).'.html'.$id_comment;
                $oracion['ltext'] = ($this->show_type == 1) ? $ln_text : $data['post_title'];
                $oracion['ltit'] = ($this->show_type == 1) ? $data['post_title'] : '';
            break;
            // PUBLICACION EN MURO
            case 12:
                $oracion['text'] = $this->monitor[$no_type]['text'].$txt_extra;
                $oracion['link'] = $site_url.'/perfil/'.$tsUser->nick.'/'.$data['obj_uno'];
                $oracion['ltext'] = ($this->show_type == 1) ? $ln_text : $tsUser->nick;
                $oracion['ltit'] = ($this->show_type == 1) ? $tsUser->nick : '';
            break;
            case 13:
                // DE QUIEN?
                if($tsUser->uid == $data['p_user']) {
                    $de = ' tu';
                }
                elseif($data['p_user'] == $data['p_user_resp']) {
                    $de = ' su'; 
                }
                else {
                    $de = ' la publicaci&oacute;n de';
                    //$data['link'][1] = ($typeDos == 1) ?  array($array['p_user_name'], $array['p_user_name']) : $array['p_user_name'];   
                }
                // CUANTOS
                $no_total = $data['not_total'];
                if($no_total > 1) {
                    $text = $this->monitor[$no_type]['text'][1].$de.$txt_extra;
                    $oracion['text'] = str_replace('_REP_', "<b>{$no_total}</b>", $text);
                }
                else $oracion['text'] = $this->monitor[$no_type]['text'][0].$de.$txt_extra;
                //
                //$oracion['text'] = $this->monitor[$no_type]['text'].$de.$txt_extra;
                $oracion['link'] = $site_url.'/perfil/'.$data['p_user_name'].'/'.$data['pub_id'];
                $oracion['ltext'] = ($this->show_type == 1) ? $ln_text : $tsUser->nick;
                $oracion['ltit'] = ($this->show_type == 1) ? $tsUser->nick : '';
            break;
            case 14:
                // CUANTOS
                $no_total = $data['not_total'];
                // MAS DE UNA ACCION
                if($no_total > 1) {
                    $text = $this->monitor[$no_type]['text'][1].' '.$ln_text;
                    $oracion['text'] = str_replace('_REP_', "<b>{$no_total}</b>", $text);
                }
                else $oracion['text'] = $this->monitor[$no_type]['text'][0].' '.$ln_text;
                //
                $oracion['link'] = $site_url.'/perfil/'.$tsUser->nick.'/'.$data['obj_uno'];
                $oracion['ltext'] = ($this->show_type == 1) ? $ln_text : $tsUser->nick;
                $oracion['ltit'] = ($this->show_type == 1) ? $tsUser->nick : '';
            break;
        }
        # RETORNAMOS
        return $oracion;
    }
	/**
     * @name setFollow
     * @access public
     * @param none
     * @return string
     * @info MANEJA EL SEGUIR USUARIO/POST
	*/
	public function setFollow(){
		global $tsUser, $tsCore, $tsActividad;
		// VARS
		$notType = 4; // NOTIFICACION
		$fw = $this->getFollowVars();
        // ANTI FLOOD
        $flood = $tsCore->antiFlood(false,'follow');
        if(strlen($flood) > 1) {
            $flood = str_replace('0: ','',$flood);
            return '1-'.$fw['obj'].'-0-'.$flood;
        }
		// YA EXISTE?
		$query = $this->select("u_follows","follow_id","f_user = {$tsUser->uid} AND f_id = {$fw['obj']} AND f_type = {$fw['type']}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
		// SEGUIR
		if(empty($data['follow_id'])){
			if($this->insert("u_follows","f_user, f_id, f_type, f_date","{$tsUser->uid}, {$fw['obj']}, {$fw['type']}, unix_timestamp()")){
				// MONITOR?
				if($fw['notUser'] > 0) $this->setNotificacion($notType, $fw['notUser'], $tsUser->uid);
				// CUANTOS?
				$query = $this->select("u_follows","COUNT(follow_id) AS total","f_id = {$fw['obj']} AND f_type = {$fw['type']}");
				$total = $this->fetch_assoc($query);
				$this->free($query);
				// SUMAR
				if($fw['type'] == 1) {
				    $this->update("u_miembros","user_seguidores = user_seguidores + 1","user_id = {$fw['obj']}");
                    $this->update("u_miembros","user_siguiendo = user_siguiendo + 1","user_id = {$tsUser->uid}");
				}
				elseif($fw['type'] == 2) $this->update("p_posts","post_seguidores = post_seguidores + 1","post_id = {$fw['obj']}");
                // ACTIVIDAD
                $ac_type = ($fw['type'] == 1) ? 8 : 7;
                $tsActividad->setActividad($ac_type, $fw['obj']);
				// RESPUESTA
				return '0-'.$fw['obj'].'-'.$total['total'];
			} else return '1-'.$fw['obj'].'-0-No se pudo completar la acci&oacute;n.';
		} else return '2-'.$fw['obj'].'-0';
	}
	/**
     * @name setUnFollow
     * @access public
     * @param none
     * @return string
     * @info MANEJA EL DEJAR DE SEGUIR UN USUARIO/POST
	*/
	public function setUnFollow(){
		global $tsUser, $tsCore;
		// VARS
		$notType = 4; // NOTIFICACION
		$fw = $this->getFollowVars();
        // ANTI FLOOD
        $flood = $tsCore->antiFlood(false, 'follow');
        if(strlen($flood) > 1) {
            $flood = str_replace('0: ','',$flood);
            return '1-'.$fw['obj'].'-0-'.$flood;
        }
		// DEJAR DE SEGUIR
		if($this->delete("u_follows","f_user = {$tsUser->uid} AND f_id = {$fw['obj']} AND f_type = {$fw['type']}")){
				// CUANTOS?
				$query = $this->select("u_follows","COUNT(follow_id) AS total","f_id = {$fw['obj']} AND f_type = {$fw['type']}");
				$total = $this->fetch_assoc($query);
				$this->free($query);
				// SUMAR
				if($fw['type'] == 1) {
				    $this->update("u_miembros","user_seguidores = user_seguidores - 1","user_id = {$fw['obj']}");
				    $this->update("u_miembros","user_siguiendo = user_siguiendo - 1","user_id = {$tsUser->uid}");
				}
				elseif($fw['type'] == 2) $this->update("p_posts","post_seguidores = post_seguidores - 1","post_id = {$fw['obj']}");
				// RESPUESTA
				return '0-'.$fw['obj'].'-'.$total['total'];
		} else return '1-'.$fw['obj'].'-0-No se pudo completar la acci&oacute;n.';
	}
	/**
     * @name getFollowVars
     * @access private
     * @param none
     * @return array
     * @info GENERA Y CREA UN ARRAY CON LA INFORMACION QUE RESIBE POR AJAX
	*/
	function getFollowVars(){
		global $tsCore;
		//
		$return['sType'] = $_POST['type'];
		$return['obj'] = $tsCore->setSecure($_POST['obj']);
		// TIPO EN NUMERO
		switch($return['sType']){
			case 'user': 
				$return['type'] = 1; 
				$return['notUser'] = $return['obj'];
			break;
			case 'post': 
				$return['type'] = 2; 
				$return['notUser'] = 0;
			break;
		}
		//
		return $return;
	}
	/**
     * @name getFollows
     * @access public
     * @param int
     * @return array
     * @info CARGA EN UN ARRAY LA INFORMACION DE LOS "FOLLOWs" DE UN USUARIO
	*/
	public function getFollows($type, $user_id = 0){
		global $tsCore, $tsUser;
		// VARS
        $user_id = empty($user_id) ? $tsUser->uid : $user_id;
		//
		switch($type){
			case 'seguidores':
				$query = "SELECT u.user_id, u.user_name, p.user_pais, p.p_mensaje, f.follow_id FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id LEFT JOIN u_follows AS f ON p.user_id = f.f_user WHERE f.f_id = {$user_id} AND f.f_type = 1 ORDER BY f.f_date DESC";
                // PAGINAR
                $total = $this->num_rows($this->query($query));
                $pages = $tsCore->getPagination($total, 12);
                $data['pages'] = $pages;
                //
				$dato = $this->fetch_array($this->query($query.' LIMIT '.$pages['limit']));
				$this->free($query);
				//
				foreach($dato as $key => $val){
					$query = $this->select("u_follows","follow_id","f_user = {$user_id} AND f_id = {$val['user_id']} AND f_type = 1");
					$siguiendo = $this->fetch_assoc($query);
					$this->free($query);
					if(!empty($siguiendo['follow_id'])) $val['follow'] = 1;
					else $val['follow'] = 0;
					//
					$data['data'][] = $val;
				}
			break;
			case 'siguiendo':
				$query = "SELECT u.user_id, u.user_name, p.user_pais, p.p_mensaje, f.follow_id FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id LEFT JOIN u_follows AS f ON p.user_id = f.f_id WHERE f.f_user = {$user_id} AND f.f_type = 1 ORDER BY f.f_date DESC";
                // PAGINAR
                $total = $this->num_rows($this->query($query));
                $pages = $tsCore->getPagination($total, 12);
                $data['pages'] = $pages;
                //
                $data['data'] = $this->fetch_array($this->query($query.' LIMIT '.$pages['limit']));
                $this->free($query);
                // 
			break;
            case 'posts':
				$query = "SELECT f.f_id, p.post_user, p.post_title, u.user_name, c.c_seo, c.c_nombre, c.c_img FROM u_follows AS f LEFT JOIN p_posts AS p ON f.f_id = p.post_id LEFT JOIN u_miembros AS u ON u.user_id = p.post_user LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE f.f_user = {$user_id} AND f.f_type = 2 ORDER BY f.f_date DESC";
                // PAGINAR
                $total = $this->num_rows($this->query($query));
                $pages = $tsCore->getPagination($total, 12);
                $data['pages'] = $pages;
                //
                $data['data'] = $this->fetch_array($this->query($query.' LIMIT '.$pages['limit']));
                $this->free($query);
            break;
		}
		//
		return $data;
	}
	/**
     * @name setSpam
     * @access public
     * @param none
     * @return string
     * @info ESTA FUNCION ES PARA REALIZAR RECOMENDACIONES
	*/
	public function setSpam(){
		global $tsCore, $tsUser, $tsActividad;
		//
		$postid = $_POST['postid'];
        // YA LO HA RECOMENDADO?
        $query = $this->select("u_follows","follow_id","f_id = {$postid} AND f_user = {$tsUser->uid} AND f_type = 3","",1);
        $recomendado = $this->num_rows($query);
        $this->free($query);
        if($recomendado > 0) return '0-No puedes recomendar el mismo post m&aacute;s de una vez.'; 
		//
		$query = $this->select("p_posts","post_user","post_id = {$postid}","",1);
		$data = $this->fetch_assoc($query);
		$this->free($query);
		//
		if($tsUser->uid != $data['post_user']){
			// SUMAR
			$this->update("p_posts","post_shared = post_shared + 1","post_id = {$postid}");
            // GUARDAMOS EN FOLLOWS PUES ES LA RECOMENDACION PARA SU SEGUIDORES! xD
            $this->insert("u_follows","f_id, f_user, f_type, f_date","{$postid}, {$tsUser->uid}, 3, unix_timestamp()");
			// NOTIFICAR
			if($this->setFollowNotificacion(6, 1, $tsUser->uid, $postid)) {
                $tsActividad->setActividad(4, $postid);
                return '1-La recomendaci&oacute; fue enviada.';
			}
		}
		else return '0-No puedes recomendar tus posts.';
	}
	/**
     * @name setFiltro
     * @access public
     * @param none
     * @return bool
     * @info GUARDA LOS FILTROS DE LA ACTIVIDAD
     */
    public function setFiltro(){
        # GLOBALES
        global $tsUser;
        # LOCALES
        $filtro_id = (int) $_POST['fid'];
        $filtro_id = 'f'.$filtro_id;
        # SACAMOS LA CONFIGURACION
        $query = $
        $cookie = $_COOKIE['monitor'];
        $cookie = unserialize($cookie);
        # GUARDAR
        if(in_array($filtro_id, $cookie)) {
            $aid = array_search($filtro_id, $cookie);
            array_splice($cookie, $aid);
        } else {
            $cookie[] = $filtro_id;
        }
        # GUARDAMOS LA NUEVA COOKIE
        $cookie = serialize($cookie);
        setcookie('monitor',$cookie,time()+16070400, '/');
        //
        return true;
    }
    /**
     * @name allowNotifi
     * @access private
     * @param int
     * @return bool
     * @info REVISA EN LA CONFIGURACION SI DESEA RESIBIR LA NOTIFICACION
     */
    private function allowNotifi($not_type){
        
    }
}
?>
