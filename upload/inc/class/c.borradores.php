<?php
/********************************************************************************
* c.borradores.php 	                                                             *
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
class tsDrafts {

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsDrafts();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								BORRADORES
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		newDraft()
	*/
	function newDraft($save = false){
		global $tsdb, $tsCore, $tsUser;
		//
		$draftData = array(
			'date' => time(),
			'title' => $tsCore->setSecure($_POST['titulo']),
			'body' => $tsCore->setSecure($_POST['cuerpo']),
			'tags' => $tsCore->setSecure($_POST['tags']),
			'category' => $tsCore->setSecure($_POST['categoria']),
			'private' => empty($_POST['privado']) ? 0 : 1,
			'block_comments' => empty($_POST['sin_comentarios']) ? 0 : 1,
			'sponsored' => empty($_POST['patrocinado']) ? 0 : 1,
            'sticky' => empty($_POST['sticky']) ? 0 : 1,
		);
		//
		if(!empty($draftData['title'])) {
			if(!empty($draftData['category']) && $draftData['category'] > 0) {
			if($save) {
				// UPDATE
				$bid = $tsCore->setSecure($_POST['borrador_id']);
				$updates = $tsCore->getIUP($draftData, 'b_');
				//
				if($tsdb->update("p_borradores",$updates,"bid = $bid AND b_user = {$tsUser->info['user_id']}")) return '1: '.$bid;
				else return '0: '.$tsdb->error();
			} else {
				// INSERT
				if($tsdb->insert("p_borradores","b_user, b_date, b_title, b_body, b_tags, b_category, b_private, b_block_comments, b_sponsored, b_sticky, b_status, b_causa","{$tsUser->info['user_id']}, {$draftData['date']}, '{$draftData['title']}', '{$draftData['body']}', '{$draftData['tags']}', {$draftData['category']}, {$draftData['private']}, {$draftData['block_comments']}, {$draftData['sponsored']}, {$draftData['sticky']}, 1, ''")) return '1: '.$tsdb->insert_id();
				else return '0: '.$tsdb->error();
			}
			} else $return = 'Categor&iacute;a';
		} else $return = 'T&iacute;tulo';
		//
		return '0: El campo <b>'.$return.'</b> es requerdio para esta operaci&oacute;n';
		//
	}
	/*
		getDrafts()
	*/
	function getDrafts(){
		global $tsdb, $tsCore, $tsUser;
		//
		$query = $tsdb->query("SELECT c.c_nombre, c.c_seo, c.c_img, b.bid, b.b_title, b.b_date, b.b_status, b.b_causa FROM p_categorias AS c LEFT JOIN p_borradores AS b ON c.cid = b.b_category WHERE b.b_user = {$tsUser->info['user_id']} ORDER BY b.b_date");
		//
		$drafts = $tsdb->fetch_array($query);
		// SET
		$tipos = array('eliminados','borradores');
		foreach($drafts as $draft){
            $causa = empty($draft['b_causa']) ? 'Eliminado por el autor' : $draft['b_causa'];
			$dft .= '{"id":'.$draft['bid'].',"titulo":"'.$draft['b_title'].'","categoria":"'.$draft['c_seo'].'","imagen":"'.$draft['c_img'].'","fecha_guardado":'.$draft['b_date'].',"status":'.$draft['b_status'].',"causa":"'.$causa.'","categoria_name":"'.$draft['c_nombre'].'","tipo":"'.$tipos[$draft['b_status']].'","url":"'.$tsCore->settings['url'].'/agregar/'.$draft['bid'].'","fecha_print":"'.strftime("%d\/%m\/%Y a las %H:%M:%S hs",$draft['b_date']).'"},';
		}
		return $dft;
	}
	/*
		getDraft()
	*/
	function getDraft($status = 1){
		global $tsdb, $tsCore, $tsUser;
		//
		$bid = $tsCore->setSecure($_GET['action']);
		$query = $tsdb->select("p_borradores","*","bid = $bid AND b_user = {$tsUser->info['user_id']} AND b_status = $status","",1);
		//
		return $tsdb->fetch_assoc($query);
	}
	/*
		delDraft()
	*/
	function delDraft(){
		global $tsdb, $tsCore, $tsUser;
		//
		$bid = $_POST['borrador_id'];
		if($tsdb->delete("p_borradores","bid = $bid AND b_user = {$tsUser->info['user_id']}")) return '1: Borrador eliminado';
		else return '0: Ocurri&oacute; un error';
	}
		
	
	
}
?>
