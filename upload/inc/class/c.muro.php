<?php
/********************************************************************************
* c.core.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/


/*
    LO NECESARIO PARA LA FUNCION DEL MURO :D	
*/
class tsMuro extends tsDatabase {
	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsMuro();
    	}
		return $instance;
	}
	/*
        getPrivacity()
    */
    public function getPrivacity($user_id, $username, $follow){
        global $tsUser;
        $priv['m']['v'] = true;
        $priv['mf']['v'] = true;
        $is_me = ($tsUser->uid == $user_id) ? true : false;
        //
        $query = $this->select("u_perfil","p_configs","user_id = {$user_id}","",1);
        $data = $this->fetch_assoc($query);
        $this->free($query);
        $data['p_configs'] = unserialize($data['p_configs']);
        // VER MURO
        switch($data['p_configs']['m']){
            case 0:
                if(!$is_me) $priv['m']['v'] = false;
                $priv['m']['m'] = 'Lo sentimos pero '.$username.' no permite ver su muro a nadie.';
            break;
            case 1:
                if($follow == 0 && !$is_me) $priv['m']['v'] = false;
                $priv['m']['m'] = 'Debes seguir a '.$username.' para poder ver su muro.';
            break;
            case 2:
                if(!$tsUser->is_member) $priv['m']['v'] = false;
                $priv['m']['m'] = 'Solo usuarios <a onclick="registro_load_form();">registrados</a> pueden ver el muro de '.$username;
            break;
        }
        // FIRMAR MURO
        switch($data['p_configs']['mf']){
            case 0:
                if(!$is_me) $priv['mf']['v'] = false;
                $priv['mf']['m'] = 'Lo sentimos pero '.$username.' no permite firmar su muro a nadie.';
            break;
            case 1:
                if($follow == 0 && !$is_me) $priv['mf']['v'] = false;
                $priv['mf']['m'] = 'Debes seguir a '.$username.' para poder firmar y comentar su muro.';
            break;
            case 2:
                if(!$tsUser->is_member) $priv['mf']['v'] = false;
                $priv['mf']['m'] = 'Solo usuarios <a onclick="registro_load_form();">registrados</a> pueden firmar el muro de '.$username;
            break;
        }
        //
        return $priv;
    }
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /*
        ajaxCheck()
    */
    public function ajaxCheck($return = false, $urlin = null){
        global $tsCore;
        //
        $type = $_GET['type'];
        $url = empty($urlin) ? $_POST['url'] : $urlin;
        // VALIDAR
        if(empty($url)) return '0: El campo <b>url</b> es obligatorio.';
        //
        switch($type){
            // VALIDAR UNA FOTO
            case 'foto':
                if(strlen($url) > 300) return '0: La url de la imagen es demasiado larga.';
                $data = getimagesize($url);
                // TAMAÑO MINIMO
                $min_w = 130;
                $min_h = 130;
                // MAX PARA EVITAR CARGA LENTA
                $max_w = 1024;
                $max_h = 1024;
                //
                if(empty($data[0])) return '0: La url ingresada no existe o no es una imagen v&aacute;lida.';
                elseif($data[0] < $min_w || $data[1] < $min_h) return '0: Tu foto debe tener un tama&ntilde;o superior a 130x130 pixeles.';
                elseif($data[0] > $max_w || $data[1] > $max_h) return '0: Tu foto debe tener un tama&ntilde;o menor a 1024x1024 pixeles.';
                else {
                    if($return == false) return '1: <center><img src="'.$url.'"/></center>';
                    else return $tsCore->setSecure($url);
                }
            break;
            // VALIDAR URL
            case 'enlace':
                // VALIDAR
                if(strlen($url) > 400) return '0: La url es demasiado larga.';
                $data = $tsCore->getUrlContent($url);
                // VALIDAR #1
                if(!$data) return '0: El enlace ingresado no es v&aacute;lido, no esta disponible o no existe.';
                // OBTENER META TITULO
                $title = explode('<title>',$data);
                $title = explode('</title>',$title[1]);
                $title = empty($title[0]) ? $url : $title[0];
                // VALIDAR #2
                if(!$title) return '0: La url ingresada no es una p&aacute;gina web v&aacute;lida.';
                // RETORNAMOS HTML/VALOR
                if($return == false) return '1: <center><a href="'.$url.'" target="_blank" class="big a_blue">'.$title.'</a><br/><span class="desc">'.$url.'</span></center>';
                else return array('title' => $tsCore->setSecure($title), 'url' => $tsCore->setSecure($url));
            break;
            // VALIDAR UN VIDE DE YOUTUBE
            case 'video':
                // VALIDAR #1
                $video_id = explode('watch?v=',$url);
                if(!is_array($video_id)) return '0: La direcci&oacute;n del video no es v&aacute;lida.';
                // VALIDAR #2
                $video_id = substr($video_id[1],0,11);
                if(strlen($video_id) != 11) return '0: La direcci&oacute;n del video no es v&aacute;lida.';
                // META TAGS
                $data = @get_meta_tags('http://www.youtube.com/watch?v='.$video_id);
                if(empty($data['title'])) return '0: La URL contiene un ID de video incorrecto o el video ha sido eliminado.';
                else {
                    $description = str_replace('<br>','',html_entity_decode($data['description']));
                    // RETORNAMOS HTML/VALORES
                    if($return == false)
                    return '1: <div class="vContent"><img src="http://img.youtube.com/vi/'.$video_id.'/0.jpg" class="thumb"/><div class="vDesc"><strong><a href="http://www.youtube.com/watch?v='.$video_id.'" target="_blank" class="a_blue">'.$data['title'].'</a></strong><div style="margin-top:5px">'.$description.'</div></div><div class="clearBoth"></div></div>';
                    else return array('ID' => $video_id, 'title' => $tsCore->setSecure($data['title']), 'desc' => $tsCore->setSecure(substr($description,0,160)));
                }
                //
                
            break;
            default:
                return '0: El campo <b>type</b> es obligatorio.';
            break;
        }
    }
    /* 
        streamPost()
    */
    public function streamPost(){
        global $tsCore, $tsUser, $tsMonitor, $tsActividad;
        //
        $pid = $tsCore->setSecure($_POST['pid']);
        $data = $tsCore->setSecure($_POST['data']);
        $adj = $tsCore->setSecure($_POST['adj']);
        $type = $_GET['type'];
        // VALIDAMOS SI EXISTE EL PERFIL/USUARIO
        $exists = $tsUser->getUserName($pid);
        if(empty($exists)) return '0: El usuario al que intentas comentar no existe.';
        // VARIABLES COMUNES
        $date = time(); 
        // TIPO DE PUBLICACION
        switch($type){
            // PUBLICAR STATUS/PUBLICACION
            case 'status':
                //$text = preg_replace('# +#',"",$data);
                $text = str_replace(array("\n","\t",' '),"",$data);
                // VACIO?
                if(strlen($text) <= 0) return '0: Tu publicaci&oacute;n debe tener al menos una letra.';
                // ANTI FLOOD
                $tsCore->antiFlood();
                //
                if($this->insert("u_muro","p_user, p_user_pub, p_body, p_date, p_type","{$pid}, {$tsUser->uid}, '{$data}', {$date}, 1")){
                    $pub_id = $this->insert_id();
                    //
                    $type = ($pid == $tsUser->uid) ? 'status' : 'mpub';
                    // RETORNAMOS DATOS PARA EL TEMPLATE
                    $return = array('pub_id' => $pub_id, 'p_user' => $pid, 'p_user_pub' => $tsUser->uid, 'p_body' => $data, 'p_date' => $date, 'p_likes' => 0, 'p_type' => 1, 'likes' => array('link' => 'Me gusta'));
                }
            break;
            // PUBLICAR FOTO
            case 'foto':
                // VALIDAMOS
                $foto = $this->ajaxCheck(true, $adj);
                if(substr($foto,0,1) == '0') return $foto;
                // ANTI FLOOD
                $tsCore->antiFlood();
                // INSERTAMOS
                if($this->insert("u_muro","p_user, p_user_pub, p_body, p_date, p_type","{$pid}, {$tsUser->uid}, '{$data}', {$date}, 2")){
                    $pub_id = $this->insert_id();
                    // INSERTAR ADJUNTO
                    if($this->insert("u_muro_adjuntos","pub_id, a_title, a_url, a_img, a_desc","{$pub_id}, '', '{$foto}', '{$foto}', ''")){
                        $type = 'mfoto';
                        // RETORNAMOS DATOS PARA EL TEMPLATE
                        $return = array('pub_id' => $pub_id, 'p_user' => $pid, 'p_user_pub' => $tsUser->uid, 'p_body' => $data, 'p_date' => $date, 'p_likes' => 0, 'p_type' => 2, 'likes' => array('link' => 'Me gusta'), 'a_url' => $foto, 'a_img' => $foto);   
                    }
                }
            break;
            // PUBLICAR ENLACE
            case 'enlace':
                // VALIDAR
                $enlace = $this->ajaxCheck(true, $adj);
                // ANTI FLOOD
                $tsCore->antiFlood();
                // INSERTAR
                if($this->insert("u_muro","p_user, p_user_pub, p_body, p_date, p_type","{$pid}, {$tsUser->uid}, '{$data}', {$date}, 3")){
                    $pub_id = $this->insert_id();
                    // INSERTAR ADJUNTO
                    if($this->insert("u_muro_adjuntos","pub_id, a_title, a_url, a_img, a_desc","{$pub_id}, '{$enlace['title']}', '{$enlace['url']}', '', ''")){
                        $type = 'mlink';
                        // RETORNAMOS DATOS PARA EL TEMPLATE
                        $return = array('pub_id' => $pub_id, 'p_user' => $pid, 'p_user_pub' => $tsUser->uid, 'p_body' => $data, 'p_date' => $date, 'p_likes' => 0, 'p_type' => 3, 'likes' => array('link' => 'Me gusta'), 'a_title' => $enlace['title'], 'a_url' => $enlace['url']);   
                    }
                }
            break;
            // PUBLICAR VIDEO
            case 'video':
                // VALIDAR
                $video = $this->ajaxCheck(true, $adj);
                // ANTI FLOOD
                $tsCore->antiFlood();
                // INSERTAR
                if($this->insert("u_muro","p_user, p_user_pub, p_body, p_date, p_type","{$pid}, {$tsUser->uid}, '{$data}', {$date}, 4")){
                    $pub_id = $this->insert_id();
                    // INSERTAR ADJUNTO
                    if($this->insert("u_muro_adjuntos","pub_id, a_title, a_url, a_img, a_desc","{$pub_id}, '{$video['title']}', '{$video['ID']}', '', '{$video['desc']}'")){
                        $type = 'mvideo';
                        // RETORNAMOS DATOS PARA EL TEMPLATE
                        $return = array('pub_id' => $pub_id, 'p_user' => $pid, 'p_user_pub' => $tsUser->uid, 'p_body' => $data, 'p_date' => $date, 'p_likes' => 0, 'p_type' => 4, 'likes' => array('link' => 'Me gusta'), 'a_title' => $video['title'], 'a_url' => $video['ID'], 'a_desc' => $video['desc']);   
                    }
                }
            break;
            default:
                $return = '0: El campo <b>type</b> es obligatorio.';
            break;
        }
        //
        $return['user_name'] = $tsUser->nick;
        // MONITOR
        $tsMonitor->setNotificacion(12, $pid, $tsUser->uid, $pub_id);
        // ACTIVIDAD
        $is_my = ($pid == $tsUser->uid) ? 0 : 2;
        $tsActividad->setActividad(10, $pub_id, $is_my);
        // RETORNAR VALOR
        return $return;
        
    }
    /*
        streamRepost()
    */
    function streamRepost(){
        global $tsCore, $tsUser, $tsMonitor, $tsActividad;
        //
        $data = $tsCore->setSecure($_POST['data']);
        $pid = $tsCore->setSecure($_POST['pid']);
        //
        $query = $this->select("u_muro","p_user, p_user_pub","pub_id = {$pid}","",1);
        $pub = $this->fetch_assoc($query);
        $this->free($query);
        //
        if($pub['p_user'] > 0){
            // VACIO?
            $text = str_replace(array("\n","\t",' '),"",$data);
            if(strlen($text) <= 0) return '0: Tu comentario debe tener al menos una letra.';
            // ANTI FLOOD
            $tsCore->antiFlood();
            // CONTINUAMOS
            $date = time();
            if($this->insert("u_muro_comentarios","pub_id, c_user, c_date, c_body","{$pid}, {$tsUser->uid}, {$date}, '{$data}'")){
                $cid = $this->insert_id();
                // MONITOR
                $tsMonitor->setMuroRepost($pid, $pub['p_user'], $pub['p_user_pub']);
                // ACTIVIDAD
                $is_my = ($pub['p_user'] == $tsUser->uid) ? 1 : 3;
                $tsActividad->setActividad(10, $cid, $is_my);
                // UPDATES
                $this->update("u_muro","p_comments = p_comments + 1","pub_id = {$pid}");
                // PARA LA PANTILLA
                return array('cid' => $cid, 'c_body' => $data, 'c_date' => $date, 'c_user' => $tsUser->uid, 'c_likes' => 0, 'like' => 'Me gusta','user_name' => $tsUser->nick);
            } else return '0: '.$this->error();
        } else return '0: La publicaci&oacute;n no existe.';
    }
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /*
        getNews()
    */
    function getNews($start = 0, $limit = 10){
        global $tsUser;
        // SOLO MOSTRAREMOS LAS ULTIMAS 100 PUBLICACIONES
        if($start > 90) return array('total' => '-1');
        // SEGUIDORES
        $query = $this->query("SELECT f_id FROM u_follows WHERE f_user = {$tsUser->uid} AND f_type = 1");
        $follows = $this->fetch_array($query);
        $this->free($query);
        // ORDENAMOS 
        foreach($follows as $key => $val){
            // PERMISO PARA VER SUS PUBLICACIONES??
            $priv = $this->getPrivacity($val['f_id'], null, true);
            if($priv['m']['v'] == true)
                $amigos[] = "'".$val['f_id']."'";
        }
        $amigos = implode(', ',$amigos);
        // OBTENEMOS LAS ULTIMAS PUBLICACIONES
        $query = $this->query("SELECT p.*, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user_pub = u.user_id WHERE p.p_user IN({$amigos}) AND p.p_user = p.p_user_pub ORDER BY p.p_date DESC LIMIT {$start},{$limit}");
        while($row = mysql_fetch_array($query)){
            // CARGAR LIKES
            if($row['p_likes'] > 0){
                $row['likes'] = $this->getPubExtras($row['pub_id'], 'likes', $row['p_likes']);
            } else $row['likes'] = array('link' => 'Me gusta');
            // CARGAR COMENTARIOS
            if($row['p_comments'] > 0){
                $row['comments'] = $this->getPubExtras($row['pub_id'], 'comments', 2);
            }
            // CARGAR ADJUNTOS
            if($row['p_type'] != 1){
                $queryDos = $this->query("SELECT * FROM u_muro_adjuntos WHERE pub_id = {$row['pub_id']} LIMIT 1");
                $adj = $this->fetch_assoc($queryDos);
                $this->free($queryDos);
                //
                $data[] = array_merge($row,$adj); 
            } else $data[] = $row;
            //
        }
        $this->free($query);
        //die(count($data));
        // RETORNAMOS
        return array('total' => count($data), 'data' => $data);
    }
    /*
        getWall($count)
    */
    function getWall($user_id, $start = 0){
        global $tsCore;
        // PUBLICACION
        $query = $this->query("SELECT p.*, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user_pub = u.user_id WHERE p.p_user = {$user_id} ORDER BY p.pub_id DESC LIMIT {$start},10");
        while($row = mysql_fetch_array($query)){
            // CARGAR LIKES
            if($row['p_likes'] > 0){
                $row['likes'] = $this->getPubExtras($row['pub_id'], 'likes', $row['p_likes']);
            } else $row['likes'] = array('link' => 'Me gusta');
            // CARGAR COMENTARIOS
            if($row['p_comments'] > 0){
                $row['comments'] = $this->getPubExtras($row['pub_id'], 'comments', 2);
            }
            // CARGAR ADJUNTOS
            if($row['p_type'] != 1){
                $queryDos = $this->query("SELECT * FROM u_muro_adjuntos WHERE pub_id = {$row['pub_id']} LIMIT 1");
                $adj = $this->fetch_assoc($queryDos);
                $this->free($queryDos);
                //
                $data[] = array_merge($row,$adj); 
            } else $data[] = $row;
        }
        $this->free($query);
        //
        return array('total' => count($data), 'data' => $data);
    }
    /*
        getPubExtras($pud_id, $type)
    */
    function getPubExtras($pub_id, $type = 'likes', $likes = 0){
        global $tsUser, $tsCore;
        //
        switch($type){
            case 'likes':
                if(empty($likes)) return array('link' => 'Me gusta', 'text' => '');
                // VARIABLES
                $data['link'] = 'Me gusta';
                $i_like = false;
                // VEMOS SI ME GUSTA
                if($tsUser->is_member){
                    $query = $this->select("u_muro_likes","like_id","user_id = {$tsUser->uid} AND obj_id = {$pub_id} AND obj_type = 1");
                    $i_like = $this->num_rows($query);
                    $this->free($query);   
                }
                // TEXOS
                if($likes == 1){
                    if($i_like) {
                        $data['link'] = 'Ya no me gusta';
                        $data['text'] = 'Te gusta esto.';   
                    }else {
                        $query = $this->query("SELECT u.user_name FROM u_muro_likes AS l LEFT JOIN u_miembros AS u ON l.user_id = u.user_id  WHERE l.obj_id = {$pub_id} AND l.obj_type = 1");
                        $u_like = $this->fetch_assoc($query);
                        $this->free($query);
                        //
                        $data['text'] = 'A <a href="'.$tsCore->settings['url'].'/perfil/'.$u_like['user_name'].'">'.$u_like['user_name'].'</a> le gusta esto.';
                    }
                } elseif($likes == 2){
                    if($i_like){
                        $data['link'] = 'Ya no me gusta';
                        //
                        $query = $this->query("SELECT u.user_name FROM u_muro_likes AS l LEFT JOIN u_miembros AS u ON l.user_id = u.user_id  WHERE l.user_id != {$tsUser->uid} AND l.obj_id = {$pub_id} AND l.obj_type = 1");
                        $u_like = $this->fetch_assoc($query);
                        $this->free($query);
                        //
                        $data['text'] = 'A <a href="'.$tsCore->settings['url'].'/perfil/'.$u_like['user_name'].'">'.$u_like['user_name'].'</a> y a ti les gusta esto.';
                    } else {
                        $data['text'] = 'A <a onclick="muro.show_likes('.$pub_id.', \'pub\'); return false;">'.$likes.' personas</a> les gusta esto.';
                    }
                } elseif($likes > 2){
                    if($i_like){
                        $data['link'] = 'Ya no me gusta';
                        $data['text'] = 'A ti y a <a onclick="muro.show_likes('.$pub_id.', \'pub\'); return false;">otras '.($likes-1).' personas m&aacute;s</a> les gusta esto.';
                    } else {
                        $data['text'] = 'A <a onclick="muro.show_likes('.$pub_id.', \'pub\'); return false;">'.$likes.' personas</a> les gusta esto.';
                    }
                }
            break;
            case 'comments':
                $limit = ($likes > 0) ? "LIMIT {$likes}" : '';
                //
                $query = $this->query("SELECT c.*, u.user_name FROM u_muro_comentarios AS c LEFT JOIN u_miembros AS u ON c.c_user = u.user_id WHERE c.pub_id = {$pub_id} ORDER BY c.c_date DESC {$limit}");
                while($row = mysql_fetch_array($query)){
                    $row['like'] = 'Me gusta';
                    // ME GUSTA?
                    if($row['c_likes'] > 0){
                        //
                        $cuery = $this->select("u_muro_likes","like_id","user_id = {$tsUser->uid} AND obj_id = {$row['cid']} AND obj_type = 2");
                        $i_like = $this->num_rows($cuery);
                        $this->free($cuery);
                        if($i_like > 0) $row['like'] = 'Ya no me gusta';
                        //
                    }
                    //
                    $data[] = $row;
                }
                $this->free($query);
                // ORDENAMOS
                asort($data);
                //
            break;
        }
        //
        return $data;
    }
    /*
        getStory()
    */
    function getStory($pub_id, $user_id){
        global $tsUser;
        // ELEGIMOS
        $query = $this->query("SELECT p.*, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user_pub = u.user_id WHERE p.pub_id = {$pub_id} LIMIT 1");
        $pub = $this->fetch_assoc($query);
        $this->free($query);
        // COMPROBAMOS
        if(empty($pub['pub_id'])) return 'La publicaci&oacute;n que has solicitado no existe.';
        elseif($user_id != $pub['p_user']) return 'La publicaci&oacute;n que has solicitado no pertenece al perfil de <b>'.$tsUser->getUserName($user_id).'</b>.';
        // CARGAR LIKES
        if($pub['p_likes'] > 0){
            $pub['likes'] = $this->getPubExtras($pub['pub_id'], 'likes', $pub['p_likes']);
        } else $row['likes'] = array('link' => 'Me gusta');
        // CARGAR COMENTARIOS
        if($pub['p_comments'] > 0){
            $pub['comments'] = $this->getPubExtras($pub['pub_id'], 'comments');
        }
        // EXTRA
        $pub['hide_more_cm'] = true;
        // ADJUNTOS
        if($pub['p_type'] != 1){
            $query = $this->query("SELECT * FROM u_muro_adjuntos WHERE pub_id = {$pub_id} LIMIT 1");
            $adj = $this->fetch_assoc($query);
            $this->free($query);
            $data = array_merge($pub,$adj);
        } else $data = $pub;
        // RETORNAMOS
        return $data;
    }
    /*
        getComments()
    */
    function getComments(){
        global $tsCore;
        //
        $pid = $tsCore->setSecure($_POST['pid']);
        // EXISTE?
        $query = $this->select("u_muro","p_user, p_comments","pub_id = {$pid}","",1);
        $cmts = $this->fetch_assoc($query);
        $this->free($query);
        //
        if(!empty($cmts)){
            $data['data'] = $this->getPubExtras($pid, 'comments');
            // TOTAL / USER DUEÑO DE LA APLICACION
            $data['total'] = $cmts['p_comments'];
            $data['user'] = $cmts['p_user'];
            //
            return $data;
        } else return '0: La publicaci&oacute;n no existe.';
    }
    /*
        delete()
    */
    function deletePost(){
        global $tsCore, $tsUser;
        //
        $id = $tsCore->setSecure($_POST['id']);
        $type = ($_POST['type'] == 'pub') ? 'pub' : 'cmt';
        //
        switch($type){
            case 'pub':
                // DATOS
                $query = $this->select("u_muro","p_user, p_user_pub","pub_id = {$id}","",1);
                $data = $this->fetch_assoc($query);
                $this->free($query);
                //
                if(!empty($data['p_user'])){
                    // SI ES EL DUEÑO DEL MURO O DE LA PUBLICACION...
                    if($data['p_user'] == $tsUser->uid || $data['p_user_pub'] == $tsUser->uid){
                        if($this->delete("u_muro","pub_id = {$id}")){
                            // BORRAMOS LOS LIKES DE LA PUBLICACION
                            $this->delete("u_muro_likes","obj_id = {$id} AND obj_type = 1");
                            // BORRAMOS LOS LIKES DE TODOS LOS COMENTARIOS
                            $query = $this->select("u_muro_comentarios","cid","pub_id = {$id}");
                            $cmnts = $this->fetch_array($query);
                            $this->free($query);
                            // IDS A BORRAR
                            foreach($cmnts as $key => $val){
                                $delete_ids .= $val['cid'].', ';
                            }
                            $this->delete("u_muro_likes","obj_id IN ('{$delete_ids}') AND obj_type = 2");
                            // BORRAR COMENTARIOS
                            $this->delete("u_muro_comentarios","pub_id = {$id}");
                            // BORRAR ADJUNTOS
                            $this->delete("u_muro_adjuntos","pub_id = {$id}");
                            //
                            return '1: OK';
                        } else return '0: Error';
                    } else return '0: Hmmm... &iquest;Haciendo pruebas?';
                } else return '0: La publicaci&oacute;n no existe.';
            break;
            // ELIMINAR COMENTARIO
            case 'cmt':
                // DATOS
                $query = $this->query("SELECT c.cid, c.c_user, p.pub_id, p.p_user FROM u_muro_comentarios AS c LEFT JOIN u_muro AS p ON c.pub_id = p.pub_id WHERE c.cid = {$id} LIMIT 1");
                $data = $this->fetch_assoc($query);
                $this->free($query);
                //
                if(!empty($data['cid'])){
                    // SI ES EL DUEÑO DEL MURO O DEL COMENTARIO...
                    if($data['p_user'] == $tsUser->uid || $data['c_user'] == $tsUser->uid){
                        if($this->delete("u_muro_comentarios","cid = {$id}")) {
                            // UPDATES
                            $this->delete("u_muro_likes","obj_id = {$id} AND obj_type = 2");
                            $this->update("u_muro","p_comments = p_comments - 1","pub_id = {$data['pub_id']}");
                            //
                            return '1: Ok';
                        }
                    } else return '0: Hmmm... &iquest;Haciendo pruebas?';
                } else return '0: El comentario no existe.';
            break;
        }
    }
    /*
        likePost()
    */
    function likePost(){
        global $tsUser, $tsCore, $tsMonitor, $tsActividad;
        // ANTI FLOOD
        $text = $tsCore->antiFlood(false, 'like', 'No te pueden gustar tantas cosas en tan poco tiempo.');
        if($text != 1) return array('status' => 'error', 'text' => $text);
        //
        $id = $_POST['id'];
        $type = ($_POST['type'] == 'com') ? 2 : 1;
        $status = 'ok';
        // EXISTE O NO
        $sql = ($type == 1) ? "SELECT p_user AS uid FROM u_muro WHERE pub_id = {$id}" : "SELECT c_user AS uid FROM u_muro_comentarios WHERE cid = {$id}";
        $query = $this->query($sql);
        $exists = $this->fetch_assoc($query);
        $this->free($query);
        
        if(empty($exists['uid'])) return '0: La publicaci&oacute;n ya no existe.';
        // CHECAMOS SI YA LE GUSTA ESTO
        $query = $this->select("u_muro_likes","like_id, user_id","obj_id = {$id} AND obj_type = {$type}");
        $likes = $this->fetch_array($query);
        $this->free($query);
        $total = count($likes);
        // CHECAMOS
        $i_like = 0;
        foreach($likes as $key => $val){
            if($val['user_id'] == $tsUser->uid) $i_like = $val['like_id'];
        }
        // SI AUN NO ME GUSTA
        if(empty($i_like)){
            if($this->insert("u_muro_likes","user_id, obj_id, obj_type","{$tsUser->uid}, {$id}, {$type}")) {
                // SUMAR LIKE
                if($type == 1) {
                    $this->update("u_muro","p_likes = p_likes + 1","pub_id = {$id}");
                    $ac_type = ($exists['uid'] == $tsUser->uid) ? 0 : 2; 
                }
                else {
                    $this->update("u_muro_comentarios","c_likes = c_likes + 1","cid = {$id}");
                    $ac_type = ($exists['uid'] == $tsUser->uid) ? 1 : 3;
                }
                // MONITOR
                $tsMonitor->setNotificacion(14, $exists['uid'], $tsUser->uid, $id, $type);
                // ACTIVIDAD
                $tsActividad->setActividad(11, $id, $ac_type);
                //
            } else $status = 'error';
        }
        else {
            if($this->delete("u_muro_likes","like_id = {$i_like}")) {
                // RESTAR LIKE
                if($type == 1) $this->update("u_muro","p_likes = p_likes - 1","pub_id = {$id}");
                else $this->update("u_muro_comentarios","c_likes = c_likes - 1","cid = {$id}");
            }
            else $status = 'error';
        }
        // RESPUESTA
        if($type == 1){
            $t_likes = empty($i_like) ? ($total+1) : ($total-1);
            //
            $data = $this->getPubExtras($id, 'likes', $t_likes);
            $link = $data['link'];
            $text = $data['text'];
        } else {
            $t_likes = empty($i_like) ? ($total+1) : ($total-1);
            $ed_like = ($t_likes > 1) ? 's' : '';
            
            $link = empty($i_like) ? 'Ya no me gusta' : 'Me gusta';
            $text = ($t_likes > 0) ? $t_likes.' persona'.$ed_like : '';
        }
        //
        return array('status' => $status, 'link' => $link, 'text' => $text);
    }
    /*
        showLikes()
    */
    function showLikes(){
        //
        $id = $_POST['id'];
        $type = ($_POST['type'] == 'com') ? 2 : 1;
        //
        $query = $this->query("SELECT l.user_id, u.user_name FROM u_muro_likes AS l LEFT JOIN u_miembros AS u ON l.user_id = u.user_id WHERE obj_id = {$id} AND obj_type = {$type}");
        $data = $this->fetch_array($query);
        $this->free($query);
        //
        if(empty($data)) return array('status' => 0, 'data' => 'La publicaci&oacute;n no existe.');
        //
        return array('status' => 1, 'data' => $data);
    }
}
?>
