<?php
/********************************************************************************
* c.mensajes.php 	                                                            *
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
class tsMensajes extends tsDatabase {
    
    var $mensajes = 0; // SIN LEER

	// INSTANCIA DE LA CLASE
	function tsMensajes(){
		global $tsUser;
		// VISITANTE?
		if(empty($tsUser->is_member)) return false;
		// MENSAJES
		$query = $this->query("SELECT COUNT(mp_id) AS total FROM u_mensajes WHERE mp_to = {$tsUser->uid} AND mp_read_mon_to = 0");
		$data = $this->fetch_assoc($query);
		$this->free($query);
        $this->mensajes = $data['total'];
        // RESPUESTAS
        $query = $this->query("SELECT COUNT(mp_id) AS total FROM u_mensajes WHERE mp_answer = 1 AND mp_from = {$tsUser->uid} AND mp_read_mon_from = 0");
		$data = $this->fetch_assoc($query);
		$this->free($query);
		$this->mensajes = $this->mensajes + $data['total'];
        //
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
	                           MENSAJES PERSONALES
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /*
        getValid()
    */
    function getValid(){
        global $tsCore, $tsUser;
        //
        $para = $tsCore->setSecure(strtolower($_POST['para']));
        //
        if($para == strtolower($tsUser->nick)) return '1';
        //
        $query = $this->select("u_miembros","user_id","LOWER(user_name) = '{$para}'","",1);
        $exists = $this->num_rows($query);
        $this->free($query);
        //
        if(empty($exists)) return '2';
        else return '0';
    }
    /**
     * @name newMensaje
     * @access public
     * @param string
     * @return string;
     * @info ENVIA UN NUEVO MENSAJE
    */
    function newMensaje(){
        global $tsCore, $tsUser;
        //
        $para = $tsCore->setSecure($_POST['para']);
        $asunto = empty($_POST['asunto']) ? '(sin asunto)' : $tsCore->setSecure($_POST['asunto']);
        $mensaje = $tsCore->setSecure(substr($_POST['mensaje'],0,1000));
        if(str_replace(array("\n","\t",' '),'',$mensaje) == '') die('0: Debes ingresar el contenido de tu mensaje.');
        //
        $user_id = $tsUser->getUserID($para);
        //
        if(!empty($user_id) && !empty($mensaje)){
            $fecha = time();
            $preview = substr($mensaje,0,75);
            if($this->insert("u_mensajes","mp_to, mp_from, mp_subject, mp_preview, mp_date","{$user_id}, {$tsUser->uid}, '{$asunto}', '{$preview}', {$fecha}")) {
                $mp_id = mysql_insert_id();
                if(empty($mp_id)) return 'Error al enviar el mensaje.';
                if($this->insert("u_respuestas","mp_id, mr_from, mr_body, mr_date","{$mp_id}, {$tsUser->uid}, '{$mensaje}', {$fecha}")){
                    return 'El mensaje ha sido enviado a <a href="'.$tsCore->settings['url'].'/perfil/'.$para.'">'.$para.'</a>.';   
                } else return $this->error();
            } else return 'Ocurri&oacute; un error. Int&eacute;ntalo nuevamente.';
        } else return 'El usuario no existe. Int&eacute;ntalo nuevamente.';
         
    }
    /*
        newRespuesta()
    */
    function newRespuesta(){
        global $tsCore, $tsUser;
        //
        $mp_id = $tsCore->setSecure($_POST['id']);
        $mp_body = $tsCore->setSecure(substr($_POST['body'],0,1000));
        if(str_replace(array("\n","\t",' '),'',$mp_body) == '') die('0: Debes ingresar tu respuesta.');
        //
        $query = $this->select("u_mensajes","mp_to, mp_from, mp_answer","mp_id = {$mp_id}","",1);
        $msg = $this->fetch_assoc($query);
        $this->free($query);
        // 
        if(!empty($msg)){
            $fecha = time();
            $preview = substr($mp_body,0,75);
            if($this->insert("u_respuestas","mp_id, mr_from, mr_body, mr_date","{$mp_id}, {$tsUser->uid}, '{$mp_body}', {$fecha}")){
                // CUANDO RESPONDA EL DESTINATARIO...
                if($msg['mp_from'] != $tsUser->uid){
                    if($msg['mp_answer'] == 0) $update = ', mp_answer = 1';
                    $update .= ', mp_read_to = 1, mp_read_mon_to = 1';
                    $update .= ', mp_read_from = 0, mp_read_mon_from = 0';  
                    $update .= ', mp_del_from = 0';
                }
                else {
                    $update .= ', mp_read_to = 0, mp_read_mon_to = 0';
                    $update .= ', mp_read_from = 1, mp_read_mon_from = 1';
                    $update .= ', mp_del_to = 0';
                }
                // ACTUALIZAMOS EL MENSAJE
                $this->update("u_mensajes","mp_preview = '{$preview}', mp_date = {$fecha}{$update}","mp_id = {$mp_id}");
                //
                $return['mp_date'] = $fecha;
                $return['mp_body'] = $tsCore->parseSmiles($mp_body);
                //
                return $return;
            }
        } else return die('0: El mensaje no existe.');
    }
    /*
        getMensajes($type)
        :: FALTA LA PAGINACION :/ 
    */
    function getMensajes($type = 1, $unread = false){
		global $tsCore, $tsUser;
		// MONITOR DE MENSAJES SOLO SI HAY MAS  DE 5 NUEVOS
        if($type == 1) {
            // SI HAY MAS DE 5 MENSAJES NUEVOS SOLO LEEMOS LOS NUEVOS
            if($this->mensajes > 0 || $unread == true) {
                $funread = "AND mp_read_mon_to = 0";
                $sunread = "AND mp_read_mon_from = 0";
                $limit = "";
            } else {
                $limit = "LIMIT 5";
            }
            $sql = "SELECT mp_id, mp_to, mp_from, mp_read_to, mp_read_mon_to, mp_subject, mp_preview, mp_date, user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON mp_from = user_id WHERE mp_to = {$tsUser->uid} AND mp_del_to = 0 {$funread} UNION (SELECT mp_id, mp_to, mp_from, mp_read_from, mp_read_mon_from, mp_subject, mp_preview, mp_date, user_name user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON mp_to = user_id WHERE mp_from = {$tsUser->uid} AND mp_del_from = 0 AND mp_answer = 1 {$sunread}) ORDER BY mp_id DESC {$limit}";
			// CONSULTA
            $query = $this->query($sql);
            $data['total'] = 0;
            while($row = mysql_fetch_assoc($query)){
                $row['mp_from'] = ($row['mp_from'] == $tsUser->uid) ? $row['mp_to'] : $row['mp_from'];
                $data['data'][$row['mp_date']] = $row;
                // AHORA ACTUALIZAMOS PARA QUE NO SE VUELVAN A NOTIFICAR EN EL MONITOR
                if($tsUser->uid == $row['mp_to']) $update = 'mp_read_mon_to = 1';
                else $update = 'mp_read_mon_from = 1';
                $this->update("u_mensajes","{$update}","mp_id = {$row['mp_id']}");
                //
                $data['total']++;
            }
			$this->free($query);
        // RESIBIDOS
		} elseif($type == 2){
            // MOSTRAR LOS NO LEIFOS
            if($unread == true){
                $funread = "AND mp_read_to = 0";
                $sunread = "AND mp_read_from = 0";
            }
            // CONSULTA
            $sql = "SELECT mp_id, mp_to, mp_from, mp_read_to, mp_subject, mp_preview, mp_date, user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON mp_from = user_id WHERE mp_to = {$tsUser->uid} AND mp_del_to = 0 {$funread} UNION (SELECT mp_id, mp_to, mp_from, mp_read_from, mp_subject, mp_preview, mp_date, user_name user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON mp_to = user_id WHERE mp_from = {$tsUser->uid} AND mp_del_from = 0 AND mp_answer = 1 {$sunread}) ORDER BY mp_id DESC";
            // PAGINAR
            $total = $this->num_rows($this->query($sql));
            $pages = $tsCore->getPagination($total, 12);
            $data['pages'] = $pages;
			// CONSULTA
			$query = $this->query($sql.' LIMIT '.$pages['limit']);
            while($row = mysql_fetch_assoc($query)){
                // PARA SABER SI ES RESPUESTA O MENSAJE NORMAL
                $row['mp_type'] = ($row['mp_from'] != $tsUser->uid) ? 1 : 2;
                $row['mp_from'] = ($row['mp_from'] == $tsUser->uid) ? $row['mp_to'] : $row['mp_from'];
                $data['data'][$row['mp_date']] = $row;
            }
			$this->free($query);
        // ENVIADOS POR MI
		}elseif($type == 3){
            $sql = "SELECT m.mp_id, m.mp_to, m.mp_read_to, m.mp_subject, m.mp_preview, m.mp_date, u.user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON m.mp_to = u.user_id WHERE m.mp_from = {$tsUser->uid} ORDER BY m.mp_id DESC";
            // PAGINAR
            $total = $this->num_rows($this->query($sql));
            $pages = $tsCore->getPagination($total, 12);
            $data['pages'] = $pages;
			// CONSULTA
			$query = $this->query($sql.' LIMIT '.$pages['limit']);
            while($row = mysql_fetch_assoc($query)){
                $row['mp_type'] = 2;
                $row['mp_from'] = $row['mp_to'];
                $row['mp_read_to'] = 1;
                $data['data'][$row['mp_date']] = $row;
            }
			$this->free($query);
        // RESPONDIDOS POR MI
		}elseif($type == 4){
            $sql = "SELECT m.mp_id, m.mp_from, m.mp_read_from, m.mp_subject, m.mp_preview, m.mp_date, u.user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON m.mp_from = u.user_id WHERE m.mp_to = {$tsUser->uid} AND m.mp_answer = 1 ORDER BY m.mp_id DESC";
            // PAGINAR
            $total = $this->num_rows($this->query($sql));
            $pages = $tsCore->getPagination($total, 12);
            $data['pages'] = $pages;
			// CONSULTA
			$query = $this->query($sql.' LIMIT '.$pages['limit']);
            while($row = mysql_fetch_assoc($query)){
                $row['mp_type'] = 1;
                $row['mp_read_to'] = 1;
                $data['data'][$row['mp_date']] = $row;
            }
			$this->free($query);
		}
        // ORDENAR Y RETORNAR
        krsort($data['data']);
        return $data;
    }
    /*
        readMensaje()
    */
    function readMensaje(){
        global $tsCore, $tsUser;
        //
        $mp_id = $tsCore->setSecure($_GET['id']);
        //
		$query = $this->query("SELECT m.*, u.user_name FROM u_mensajes AS m LEFT JOIN u_miembros AS u ON m.mp_from = u.user_id WHERE m.mp_id = {$mp_id}");
		$data = $this->fetch_assoc($query);
		$this->free($query);
        // NO PUEDE LEER MENSAJES DE OTROS USUARIOS NI RESPUESTAS POR SEPARADO...
        if($data['mp_to'] != $tsUser->uid && $data['mp_from'] != $tsUser->uid) $tsCore->redirectTo($tsCore->settings['url'].'/mensajes/');
        // MENSAJE
        $history['msg'] = $data;
        // RESPUESTAS
        $query = $this->query("SELECT r.*, u.user_name FROM u_respuestas AS r LEFT JOIN u_miembros AS u ON r.mr_from = u.user_id WHERE r.mp_id = {$mp_id} ORDER BY mr_id");
		//$history['res'] = $this->fetch_array($query);
        while($row = mysql_fetch_assoc($query)){
            $row['mr_body'] = $tsCore->parseSmiles($row['mr_body']);
            $history['res'][] = $row;
        }
		$this->free($query);
        // ACTUALIZAR
        $resp = count($history['res']);
        $from = $history['res'][$resp-1]['mr_from']; // ULTIMO EN RESPONDER
        //
        if($tsUser->uid == $data['mp_to']) {$update = 'mp_read_to = 1, mp_read_mon_to = 1'; $history['msg']['mp_type'] = 1;} // PARA MI
        elseif($from == $data['mp_to'] && $data['mp_from'] == $tsUser->uid) {$update = 'mp_read_from = 1, mp_read_mon_from = 1'; $history['msg']['mp_type'] = 2;} // ME RESPONDIERON
        elseif($from == $data['mp_from']) {$update = 'mp_read_from = 1, mp_read_mon_from = 1'; $history['msg']['mp_type'] = 2;}
        //
        $this->update("u_mensajes","{$update}","mp_id = {$mp_id}"); // LEIDO
		// BLOQUEADO
        $user_id = ($data['mp_from'] != $tsUser->uid) ? $data['mp_from'] : $data['mp_to'];
        $query = $this->select("u_bloqueos","bid AS block","b_user = {$tsUser->uid} AND b_auser = {$user_id}","",1);
        $history['ext'] = $this->fetch_assoc($query);
        $this->free($query);
        $history['ext']['uid'] = $user_id;
        $history['ext']['user'] = $tsUser->getUserName($user_id);
        //
        return $history;
    }
    /*
        editMensajes();
    */
    function editMensajes(){
        global $tsCore, $tsUser;
        //
        $ids = explode(',',$tsCore->setSecure($_POST['ids']));
        // ARMAR IDS
        foreach($ids as $nid){
            $id = explode(':',$nid);
            $nids[$id[1]][] = $id[0];
        }
        if(empty($nids)) return false;
        $act = $_POST['act'];
        // HMM SI NO LE ENTIENDES A ESTO NTP YO TAMPOCO xD PERO FUNCIONA :D
        switch($act){
            case 'read':
                $this->query("UPDATE u_mensajes SET mp_read_to = 1 WHERE mp_id IN(".implode(',',$nids[1]).") AND mp_to = {$tsUser->uid}");
                $this->query("UPDATE u_mensajes SET mp_read_from = 1 WHERE mp_id IN(".implode(',',$nids[2]).") AND mp_from = {$tsUser->uid}");
            break;
            case 'unread':
                $this->query("UPDATE u_mensajes SET mp_read_to = 0 WHERE mp_id IN(".implode(',',$nids[1]).") AND mp_to = {$tsUser->uid}");
                $this->query("UPDATE u_mensajes SET mp_read_from = 0 WHERE mp_id IN(".implode(',',$nids[2]).") AND mp_from = {$tsUser->uid}");
            break;
            case 'delete':
                $this->query("UPDATE u_mensajes SET mp_del_to = 1 WHERE mp_id IN(".implode(',',$nids[1]).") AND mp_to = {$tsUser->uid}");
                $this->query("UPDATE u_mensajes SET mp_del_from = 1 WHERE mp_id IN(".implode(',',$nids[2]).") AND mp_from = {$tsUser->uid}");
                // BORRAMOS SOLO SI LOS DOS LO HAN DECIDIDO :D
                $query = $this->select("u_mensajes","mp_id","mp_del_to = 1 AND mp_del_from = 1 AND (mp_to = {$tsUser->uid} OR mp_from = {$tsUser->uid})");
                while($row = mysql_fetch_assoc($query)){
                    if($this->delete("u_mensajes","mp_id = {$row['mp_id']}")){
                        $this->delete("u_respuestas","mp_id = {$row['mp_id']}");
                    }
                }
                $this->free($query);
                //
            break;
        }
    }
}
?>
