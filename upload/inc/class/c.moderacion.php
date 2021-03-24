<?php
/********************************************************************************
* c.moderacion.php 	                                                                *
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
class tsMod  extends tsDatabase{
	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsMod();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
  								// ADMINISTRAR \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		getAdmins()
	*/
	function getMods(){
		global $tsdb;
		//
		$query = $this->select("u_miembros","user_id, user_name","user_rango = 2","user_id","");
		//
		$data = $this->fetch_array($query);
		//
		return $data;
	}
	/*
		getDenuncias()
	*/
	function getDenuncias($type = 'posts'){
	   // TIPO DE DENUNCIAS
        switch($type){
            case 'posts':
                $query = $this->query("SELECT r.*, SUM(d_total) AS total, p.post_id, p.post_title, p.post_status, c.c_nombre, c.c_seo, c.c_img FROM w_denuncias AS r LEFT JOIN p_posts AS p ON r.obj_id = p.post_id LEFT JOIN p_categorias AS c ON p.post_category = c.cid WHERE d_type = 1 AND p.post_status < 2 GROUP BY r.obj_id ORDER BY total DESC, r.d_date DESC");
                $data = $this->fetch_array($query);
                $this->free($query);
            break;
            case 'users':
                $query = $this->query("SELECT r.*, SUM(d_total) AS total, u.user_name FROM w_denuncias AS r LEFT JOIN u_miembros AS u ON r.obj_id = u.user_id WHERE d_type = 3 AND u.user_baneado = 0 GROUP BY r.obj_id ORDER BY total, r.d_date DESC");
                $data = $this->fetch_array($query);
                $this->free($query);
            break;
        }
		//
		return $data;
	}
	
    /*
        getDenuncia()
    */
    function getDenuncia($type = 'posts'){
        global $tsCore;
        // VARIABLES
        $obj = $tsCore->setSecure($_GET['obj']);
        // TIPO DE DENUNCIA
        switch($type){
            case 'posts':
                $d_type = 1; 
                $query = $this->query("SELECT p.post_id, p.post_title, p.post_status, c.c_nombre, c.c_seo, c.c_img, u.user_name FROM p_posts AS p LEFT JOIN p_categorias AS c ON p.post_category = c.cid LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE p.post_id = {$obj} LIMIT 1");
            break;
            case 'users':
                $d_type = 3;
                $query = $this->select("u_miembros","user_id, user_name","user_id = {$obj}","",1);
            break;
        }
        // CARGAMSO AL ARRAY...
        $data['data'] = $this->fetch_assoc($query);
		$this->free($query);
        // DENUNCIAS
        $query = $this->query("SELECT d.*, u.user_name FROM w_denuncias AS d LEFT JOIN u_miembros AS u ON d.d_user = u.user_id WHERE d.obj_id = {$obj} AND d.d_type = {$d_type}");
        $data['denun'] = $this->fetch_array($query);
        $this->free($query);
        //
        return $data;
    }
	/*
		getPreview()
	*/
	function getPreview($pid){
		global $tsCore;
        $query = $this->select("p_posts","post_title, post_body","post_id = {$pid}","",1);
        $data = $this->fetch_assoc($query);
        $this->free($query);
		//
		return array('titulo' => $data['post_title'], 'cuerpo' => $tsCore->parseBBCode($data['post_body']));
	}
    /**
     * @name rebootPost()
     * @access public
     * @param int
     * @return string
     */
     public function rebootPost($pid){
        // PRIMERO BORRAMOS LA DENUNCIAS
        if($this->delete("w_denuncias","obj_id = {$pid} AND d_type = 1")){
            // REGRESAMOS EL POST
            if($this->update("p_posts","post_status = 0, post_denuncias = 0","post_id = {$pid}")) return '1: El post ha sido restaurado.';
            else return '0: No se pudo restaurar el post.';
        } else return '0: No se pudo restaurar el post.';
     }
    /**
     * @name deletePost($pid)
     * @access public
     * @param int
     * @return string
     */
     public function deletePost($pid){
        global $tsCore, $tsMonitor; 
        // RAZON
        $razon = $_POST['razon'];
        $razon_desc = $_POST['razon_desc'];
        $razon_db = ($razon != 13) ? $razon : $razon_desc;
        //
        if($this->update("p_posts","post_status = 2","post_id = {$pid}")) {
            // ELIMINAR DENUNCIAS
            $this->delete("w_denuncias","obj_id = {$pid} AND d_type = 1");
            // ENVIAR AVISO
            $query = $this->query("SELECT p.post_user, p.post_title, u.user_name FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE p.post_id = {$pid} LIMIT 1");
            $data = $this->fetch_assoc($query);
            $this->free($query);
            // RAZON
            if(is_numeric($razon_db)) {
                include(TS_EXTRA . 'datos.php');
                $razon_db = $tsDenuncias['posts'][$razon_db];
            }
            // AVISO
            $aviso = 'Hola <b>'.$data['user_name']."</b>\n\n Lamento contarte que tu post titulado <b>".$data['post_title']."</b> ha sido eliminado.\n\n Causa: <b>".$razon_db."</b>\n\n Te recomendamos leer el <a href=\"".$tsCore->settings['url']."/pages/protocolo/\">Protocolo</a> para evitar futuras sanciones.\n\n Muchas gracias por entender!";
            $status = $tsMonitor->setAviso($data['post_user'],'Post eliminado', $aviso, 1);
            //
            $status = $this->setHistory('borrar', $pid);
            if($status == true) return '1: El post ha sido eliminado.';
        }  
        //
        return '0: El post NO pudo ser eliminado.';
     }
    /**
     * @name getSuspendidos
     * @access public
     * @param
     * @return array
     * @info OBTIENE LOS USUARIOS SUSPENDIDOS
     */
    public function getSuspendidos(){
        #
        $query = $this->query("SELECT s.*, u.user_name FROM u_suspension AS s LEFT JOIN u_miembros AS u ON s.user_id = u.user_id WHERE 1");
        $data = $this->fetch_array($query);
        $this->free($query);
        //
        return $data;
    }
    /**
     * @name banUser
     * @access public
     * @param int
     * @return string
     * @info PARA SUSPENDER A UN USUARIO
     */
    public function banUser($user_id){
        # GLOBALES 
        global $tsUser;
        # LOCALES
        $b_time = $_POST['b_time'];
        $b_cant = empty($_POST['b_cant']) ? 1 : $_POST['b_cant'];
        $b_causa = $_POST['b_causa'];
        $b_times = array(0, 1, 3600, 86400); // HORA, DIA
        # NO INTENTO BANEARME?
        if($user_id == $tsUser->uid) return '0: No puedes suspenderte a ti mismo';
        # COMPROBAMOS RANGOS
        $query = $this->select("u_miembros","user_rango, user_baneado","user_id = {$user_id}","",1);
        $data = $this->fetch_assoc($query);
        $this->free($query);
        if($data['user_baneado'] == 0){
            # Y SI QUIERO SUSPENDER A UN ADMIN o MOD?
            if($tsUser->is_admod < $data['user_rango']) {
                // TIEMPO
                $ahora = time();
                $termina = ($b_cant * $b_times[$b_time]);
                $termina = ($b_time >= 2) ? ($ahora+$termina) : $termina;
                // ACTUALIZAMOS
                $this->update("u_miembros","user_baneado = 1","user_id = {$user_id}");
                if($this->insert("u_suspension","user_id, susp_causa, susp_date, susp_termina, susp_mod","{$user_id}, '$b_causa', {$ahora}, {$termina}, {$tsUser->uid}")){
                    // ELIMINAR DENUNCIAS
                    $this->delete("w_denuncias","obj_id = {$user_id} AND d_type = 3");
                    // RETORNAR
                    if($b_time < 2) {
                        $rdate = ($b_time == 0) ? 'Indefinidamente' : 'Permanentemente';   
                    } else $rdate = '</b>hasta el <b>'.date("d/m/Y H:i:s",$termina);
                    //
                    return '1: Usuario suspendido <b>'.$rdate.'</b>'; 
                } return '0: El usuario no pudo ser suspendido';
            } else return '0: No puedes suspender a usuarios de tu mismo rango o superior al tuyo.';
        } else return '0: Este usuario ya fue suspendido';
    }
    /**
     * @name rebootUser
     * @access public
     * @param int
     * @return string
     * @info ELIMINA LAS DENUNCIAS DEL USUARIO O LE QUITA UNA SUSPENSION
     */
    public function rebootUser($user_id, $type = 'unban'){
        # GLOBALES
        global $tsUser;
        # PRIMERO BORRAMOS LA DENUNCIAS
        $this->delete("w_denuncias","obj_id = {$user_id} AND d_type = 3");
        // HAY QUE QUITAR LA SUSPENSION?
        if($type == 'unban') {
            $query = $this->select("u_suspension","susp_mod","user_id = {$user_id}");
            $data = $this->fetch_assoc($query);
            $this->free($query);
            //
            if(empty($data)) return '0: El usuario no est&aacute; suspendido.';
            //
            if($tsUser->is_admod == 1 || $data['susp_mon'] == $tsUser->uid){
                $this->delete("u_suspension","user_id = {$user_id}");
                $this->update("u_miembros","user_baneado = 0","user_id = {$user_id}");   
                return '1: El usuario fue reactivado.';
            } else return '0: S&oacute;lo puedes quitar la suspensi&oacute;n a los usuarios que t&uacute; suspendiste.';
        }
        //
        return '1: Las denuncias fueron eliminadas.';
    }
    /**
     * @name deletePost
     * @access public
     * @param int
     * @return string
     */
     public function setHistory($type, $data){
        global $tsUser, $tsMonitor;
		// COMPROVAR LIMITE 20
		$query = $this->query("SELECT id FROM w_historial WHERE 1 ORDER BY id DESC");
        $total = $this->fetch_array($query);
        $this->free($query);
        $ntotal = count($total);
        $delid = $total[$ntotal-1]['id']; // ID DE ULTIMA NOTIFICACION
		// ELIMINAR NOTIFICACIONES?
		if($ntotal >= 20){
            $this->delete("w_historial","id = {$delid}");
		}
        //
        switch($type){
            case 'borrar':
                // RAZON
                $razon = $_POST['razon'];
                $razon_desc = $_POST['razon_desc'];
                $razon_db = ($razon != 13) ? $razon : $razon_desc;
                // DATOS
                $query = $this->select("p_posts","post_title, post_user","post_id = {$data}","",1);
                $post = $this->fetch_assoc($query);
                $this->free($query);
                // INSERTAR
                //$post['post_title'] = htmlentities($post['post_title'],ENT_QUOTES);
                $this->insert("w_historial","post_title, post_autor, post_action, post_mod, post_reason","'{$post['post_title']}', {$post['post_user']}, 2, {$tsUser->uid}, '{$razon_db}'");
                //
                return true;
            break;
            // EDITAR
            case 'editar':
                $aviso = 'Hola <b>'.$tsUser->getUserName($data['autor'])."</b>\n\n Te informo que tu post <b>".$data['title']."</b> ha sido editado por <a href=\"#\" class=\"hovercard\" uid=\"".$tsUser->uid."\">".$tsUser->nick."</a>\n\n Causa: <b>".$data['razon']."</b>\n\n Te recomendamos leer el <a href=\"".$tsCore->settings['url']."/pages/protocolo/\">protocolo</a> para evitar futuras sanciones.\n\n Muchas gracias por entender!";
                $tsMonitor->setAviso($data['autor'], 'Post editado', $aviso);
                $this->insert("w_historial","post_title, post_autor, post_action, post_mod, post_reason","'{$data['title']}', {$data['autor']}, 1, {$tsUser->uid}, '{$data['razon']}'");
                return 1;
            break;
        }

     }
    /**
     * @name getHistory()
     * @access public
     * @param 
     * @return array
     */
     public function getHistory(){
        global $tsUser;
        //
        $query = $this->select("w_historial","*","1","id DESC");
        // DENUCNIAS
        include("../ext/datos.php");
        //
        while($row = mysql_fetch_assoc($query)){
            $row['post_autor'] = $tsUser->getUserName($row['post_autor']);
            $row['post_mod'] = $tsUser->getUserName($row['post_mod']);
            $row['post_reason'] = (is_numeric($row['post_reason'])) ? $tsDenuncias['posts'][$row['post_reason']] : $row['post_reason'];
            //
            $data[] = $row;
        }
        //
        return $data;
     }
}
?>
