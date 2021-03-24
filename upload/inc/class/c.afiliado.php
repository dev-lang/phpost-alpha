<?php
/********************************************************************************
* c.afiliado.php 	                                                            *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/


/*

	CLASE CON LOS ATRIBUTOS Y METODOS PARA LOS Drafts
	
*/
class tsAfiliado {

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsAfiliado();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								AFILIADOS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		getAfiliadosHome()
	*/
    function getAfiliados($type = 'home'){
        global $tsdb;
        //
        if($type == 'home')
        $query = $tsdb->select("w_afiliados","*","a_active = 1","RAND()",5);
        elseif($type == 'admin')
        $query = $tsdb->select("w_afiliados","*","","aid");
        //
        $data = $tsdb->fetch_array($query);
        $tsdb->free($query);
        //
        return $data;
    }
    /*
        getAfiliado()
    */
    function getAfiliado(){
        global $tsdb, $tsCore;
        //
        $ref = $tsCore->setSecure($_POST['ref']);
        $query = $tsdb->select("w_afiliados","*","aid = {$ref}","");
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        return $data;
    }
    /*
        newAfiliado()
    */
    function newAfiliado(){
        global $tsdb, $tsCore;
        //
        $dataIn['titulo'] = $tsCore->setSecure($_POST['atitle']);
        $dataIn['url'] = $tsCore->setSecure($_POST['aurl']);
        $dataIn['banner'] = $tsCore->setSecure($_POST['aimg']);
        $dataIn['desc'] = $tsCore->setSecure($_POST['atxt']);
        $dataIn['sid'] = $tsCore->setSecure($_POST['aID']);
        $date = time();
        //
        if($tsdb->insert("w_afiliados","a_titulo, a_url, a_banner, a_descripcion, a_sid, a_date","'{$dataIn['titulo']}', '{$dataIn['url']}', '{$dataIn['banner']}', '{$dataIn['desc']}', {$dataIn['sid']}, {$date}")){
            $afid = $tsdb->insert_id();
            //
            $entit = $tsCore->settings['titulo'];
            $enurl = $tsCore->settings['url'].'/?ref='.$afid;
            $enimg = $tsCore->settings['banner'];
            //
            $return = '1: <div class="emptyData">Tu afiliaci&oacute;n ha sido agregada!</div><br>';
            $return .= '<div style="padding:0 35px;">Se le ha notificado al administrador tu afiliaci&oacute;n para que la apruebe, mientras tanto copia el siguiente c&oacute;digo, ser&aacute; con el cual nos debes enlazar.<br><br>';
            $return .= '<div class="form-line">';
            $return .= '<label for="atitle">C&oacute;digo HTML</label>';
            $return .= '<textarea tabindex="4" rows="10" style="height:60px; width:295px" onclick"select(this)">';
            $return .= '<a href="'.$enurl.'" target="_blank" title="'.$entit.'"><img src="'.$enimg.'"></a>';
            $return .= '</textarea>';
      		$return .= '</div>';
            $return .= '</div>';
        }
        //
        return $return;
        
    }
	/*
        urlOut()
    */
    function urlOut(){
        global $tsdb, $tsCore;
        //
        $ref = $tsCore->setSecure($_GET['ref']);
        //
        $query = $tsdb->select("w_afiliados","a_url, a_sid","aid = {$ref}","",1);
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        if(isset($data['a_url'])){
            $tsdb->update("w_afiliados","a_hits_out = a_hits_out + 1","aid = {$ref}");
            // Y REDIRECCIONAMOS
            $enref = empty($data['a_sid']) ? '/' : '/?ref='.$data['a_sid']; // REFERIDO
            $enurl = $data['a_url'].$enref;
            // REDIRECCIONAMOS
            $tsCore->redirectTo($enurl);
            exit();
        } else $tsCore->redirectTo($tsCore->settings['url']);
    }
    /*
        urlIn()
    */
    function urlIn(){
        global $tsdb, $tsCore;
        //
        $ref = $tsCore->setSecure($_GET['ref']);
        if($ref > 0) $tsdb->update("w_afiliados","a_hits_in = a_hits_in + 1","aid = {$ref}");
        // 
        $tsCore->redirectTo($tsCore->settings['url']);
    }
}
?>
