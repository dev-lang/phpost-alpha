<?php
/********************************************************************************
* c.swat.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/


/*

	CONTORLAR A LOS USUARIOS Y POSTS :D
	
*/
class tsSwat extends tsDatabase{

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsSwat();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								METODOS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /*
        setDenuncia()
    */
    function setDenuncia($obj_id, $type = 'posts'){
        global $tsdb, $tsCore, $tsUser;
        // VARS
        $razon = $tsCore->setSecure($_POST['razon']);
        $extras = $tsCore->setSecure($_POST['extras']);
        $date = time();
        // QUE?
        switch($type){
            case 'posts':
            // ES MI POST?
            $query = $this->select("p_posts","post_user","post_id = {$obj_id}","",1);
            $my_post = $this->fetch_assoc($query);
            $this->free($query);
            if($my_post['post_user'] == $tsUser->uid) return '0: No puedes denunciar tus propios post.';
            // YA HA REPORTADO?
            $query = $tsdb->select("w_denuncias","did","obj_id = {$obj_id} AND d_user = {$tsUser->uid} AND d_type = 1");
            $denuncio = $tsdb->num_rows($query);
            $tsdb->free($query);
            if(!empty($denuncio)) return '0: Ya hab&iacute;as denunciado este post.';
            // CUANTAS DENUNCIAS LLEVA?
            $query = $tsdb->select("p_posts","post_id, post_denuncias AS total","post_id = {$obj_id}","",1);
            $denuncias = $tsdb->fetch_assoc($query);
            $tsdb->free($query);
            if(empty($denuncias['post_id'])) return '0: Opps... Este post no existe.';
            // OCULTAMOS EL POST SI YA LLEVA MAS DE 3 DENUNCIAS
            if($denuncias['total'] >= 2){
                $tsdb->update("p_posts","post_denuncias = post_denuncias + 1, post_status = 1","post_id = {$obj_id}");
            }
            // INSERTAR NUEVA DENUNCIA
            if($tsdb->insert("w_denuncias","obj_id, d_user, d_razon, d_extra, d_type, d_date","{$obj_id}, {$tsUser->uid}, {$razon}, '{$extras}', 1, {$date}")){
                return '1: La denuncia fue enviada.';
            } else return '0: Error, int&eacute;ntalo m&aacute;s tarde.';

            break;
            // MENSAJES
            case 'mensaje':
                // YA HA REPORTADO?
                $query = $this->select("w_denuncias","did","obj_id = {$obj_id} AND d_user = {$tsUser->uid} AND d_type = 2");
                $denuncio = $this->num_rows($query);
                $this->free($query);
                if(!empty($denuncio)) return '0: Ya hab&iacute;as denunciado este mensaje. Nuestros moderadores ya lo analizan.';
                // DONDE LO BORRAREMOS?
                $query = $this->select("u_mensajes","mp_id, mp_to, mp_from","mp_id = {$obj_id}","",1);
                $where = $this->fetch_assoc($query);
                $this->free($query);
                if(empty($where['mp_id'])) return '0: Opps... Este mensaje no existe.';
                //
                if($where['mp_to'] == $tsUser->uid) $del_table = "mp_del_to";
                elseif($where['mp_from'] == $tsUser->uid) $del_table = "mp_del_from";
                // INSERTAR NUEVA DENUNCIA
                if($this->insert("w_denuncias","obj_id, d_user, d_razon, d_extra, d_type, d_date","{$obj_id}, {$tsUser->uid}, 0, '', 2, {$date}")){
                    // BORRAMOS
                    $tsdb->update("u_mensajes","{$del_table} = 1","mp_id = {$obj_id}");
                    return '1: Has denunciado un mensaje como correo no deseado. <script>setTimeout(function(){document.location.href = global_data.url + "/mensajes/";}, 1500);</script>';
                } else return '0: Error! Int&eacute;ntalo m&aacute;s tarde.';
            break;
            // USUARIOS
            case 'usuario':
                // YA HA REPORTADO?
                $query = $this->select("w_denuncias","did","obj_id = {$obj_id} AND d_user = {$tsUser->uid} AND d_type = 3");
                $denuncio = $this->num_rows($query);
                $this->free($query);
                if(!empty($denuncio)) return '0: Ya hab&iacute;as denunciado a este usario.';
                $username = $tsUser->getUserName($obj_id);
                if(empty($username)) return '0: Opps... Este usuario no existe.';
                // LO REPORTAMOS...
                if($tsdb->insert("w_denuncias","obj_id, d_user, d_razon, d_extra, d_type, d_date","{$obj_id}, {$tsUser->uid}, {$razon}, '{$extras}', 3, {$date}")){
                    // SUMAMOS 
                    $tsdb->update("u_miembros","user_bad_hits = user_bad_hits + 1","user_id = {$obj_id}");
                    return '1: Este usuario ha sido denunciado.';
                } else return '0: Error! Int&eacute;ntalo m&aacute;s tarde;';
            break;
        }
    }
    /**
     * @name setAviso($user_id)
     * @access public
     * @param int
     * @return string
     */
     public function setAviso($user_id){
        
     }
}
?>
