<?php
/********************************************************************************
* c.tops.php 	                                                                *
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
class tsTops extends tsDatabase {

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsTops();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								TOPS Y ESTADISTICAS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		getHomeTopPosts()
		: TOP DE POST semana, histórico
	*/
	function getHomeTopPosts(){
		// AYER
		$data['ayer'] = $this->getHomeTopPostsQuery($this->setTime(2));
		// SEMANA
		$data['semana'] = $this->getHomeTopPostsQuery($this->setTime(3));
		// MES
		$data['mes'] = $this->getHomeTopPostsQuery($this->setTime(4));
		// HISTÓRICO
		$data['historico'] = $this->getHomeTopPostsQuery($this->setTime(5));
		//
		return $data;
	}
	/*
		getHomeTopUsers()
		: TOP DE USUARIOS semana, histórico
	*/
	function getHomeTopUsers(){
        // AYER
		$data['ayer'] = $this->getHomeTopUsersQuery($this->setTime(2));
        // SEMANA
		$data['semana'] = $this->getHomeTopUsersQuery($this->setTime(3));
        // MES
		$data['mes'] = $this->getHomeTopUsersQuery($this->setTime(4));
        // HISTÓRICO
		$data['historico'] = $this->getHomeTopUsersQuery($this->setTime(5));
        //
        return $data;
        //
        print_r($data);
        die;
	}
    /*
        getTopUsers()
    */
    function getTopUsers($fecha, $cat){
        //
        $data = $this->setTime($fecha);
        $category = empty($cat) ? '' : 'AND post_category = '.$cat;
		// PUNTOS
        $query = $this->query("SELECT SUM(p.post_puntos) AS total, u.user_id, u.user_name FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE p.post_status = 0  AND p.post_date BETWEEN {$data['start']} AND {$data['end']} {$category} GROUP BY p.post_user ORDER BY total DESC LIMIT 15");
        $array['puntos'] = $this->fetch_array($query);
        $this->free($query);
        // SEGUIDORES
        $query = $this->query("SELECT COUNT(f.follow_id) AS total, u.user_id, u.user_name FROM u_follows AS f LEFT JOIN u_miembros AS u ON f.f_id = u.user_id WHERE f.f_type = 1 AND f.f_date BETWEEN {$data['start']} AND {$data['end']} GROUP BY f.f_id ORDER BY total DESC LIMIT 15");
        $array['seguidores'] = $this->fetch_array($query);
        $this->free($query);
        //
        return $array;
    }
	/*
		getTopPosts()
	*/
	function getTopPosts($fecha, $cat){
		// PUNTOS
		$data['puntos'] = $this->getTopPostsVars($fecha, $cat, 'puntos');
		// SEGUIDORES
		$data['seguidores'] = $this->getTopPostsVars($fecha, $cat, 'seguidores');
		// COMENTARIOS
		$data['comments'] = $this->getTopPostsVars($fecha, $cat, 'comments');
		// FAVORITOS
		$data['favoritos'] = $this->getTopPostsVars($fecha, $cat, 'favoritos');
		//
		return $data;
	}
	/*
		setTopPostsVars($text, $type)
	*/
	function getTopPostsVars($fecha, $cat, $type){
		//
		$data = $this->setTime($fecha);
		if(!empty($cat)) $data['scat'] = "AND c.cid = {$cat}";
		//
		$data['type'] = "p.post_".$type;
		//
		return $this->getTopPostsQuery($data);
	}
	/*
		getTopPostsQuery($data)
	*/
	function getTopPostsQuery($data){
		global $tsdb;
		//
		$query = $tsdb->query("SELECT p.post_id, p.post_category, p.post_title, {$data['type']}, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND p.post_date BETWEEN {$data['start']} AND {$data['end']} {$data['scat']} ORDER BY {$data['type']} DESC LIMIT 15");
		$datos = $tsdb->fetch_array($query);
		$tsdb->free($query);
		//
		return $datos;
	}
	/*
		getHomeTopPostsQuery($data)
	*/
	function getHomeTopPostsQuery($date){
		global $tsdb;
		//
		$query = $tsdb->query("SELECT p.post_id, p.post_category, p.post_title, p.post_puntos, c.c_seo FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category  WHERE p.post_status = 0 AND p.post_date BETWEEN {$date['start']} AND {$date['end']} ORDER BY p.post_puntos DESC LIMIT 15");
		$data = $tsdb->fetch_array($query);
		$tsdb->free($query);
		//
		return $data;
	}
    /*
        getHomeTopUsersQuery($date)
    */
    function getHomeTopUsersQuery($date){
		// PUNTOS
        $query = $this->query("SELECT SUM(p.post_puntos) AS total, u.user_id, u.user_name FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE p.post_status = 0  AND p.post_date BETWEEN {$date['start']} AND {$date['end']} GROUP BY p.post_user ORDER BY total DESC LIMIT 15");
        $data = $this->fetch_array($query);
        $this->free($query);
        //
        return $data;
    }
	/*
		getStats() : NADA QUE VER CON LA CLASE PERO BUENO PARA AHORRAR ESPACIO...
		: ESTADISTICAS DE LA WEB
	*/
	function getStats(){
		global $tsdb, $tsCore;
		// OBTENEMOS LAS ESTADISTICAS
        $query = $tsdb->select("w_stats","*","stats_no = 1","",1);
		$return = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        // PARA SABER SI ESTA ONLINE
		$is_online = (time() - ($tsCore->settings['c_last_active'] * 60));
        // USUARIOS ONLINE
        $query = $tsdb->select("u_miembros","user_id","user_lastactive > $is_online","","");
		$return['stats_online'] = $tsdb->num_rows($query);
        // ACTUALIZAMOS LA DB
        $now = time();
		$tsdb->update("w_stats","stats_online = {$return['stats_online']}, stats_time = {$now}","stats_no = 1");
		//
		return $return;
	}
	/******************************************************************************/
	/*
		setTime($fecha)
	*/
	function setTime($fecha){
		// AHORA
		$tiempo = time();
		$dia = (int) date("d",$tiempo);
		$hora = (int) date("G",$tiempo);
		$min = (int) date("i",$tiempo);
		$seg = (int) date("s",$tiempo);
		//
		$resta = $this->setSegs($hora, 'hor') + $this->setSegs($min, 'min') + $seg;
		// TRANSFORMAR
		switch($fecha){
			// HOY
			case 1: 
				//
				$data['start'] = $tiempo - $resta;
				$data['end'] = $tiempo;
				//
			break;
			// AYER
			case 2: 
				//
				$restaDos = $resta + $this->setSegs(1,'dia') + $this->setSegs(1,'hor');
				$data['start'] = $tiempo - $restaDos;
				$data['end'] = $tiempo - $resta;
				//
			break;
			// SEMANA
			case 3: 
				//
				$restaDos = $resta + $this->setSegs(1,'sem')  + $this->setSegs(1,'hor');
				$data['start'] = $tiempo - $restaDos;
				$data['end'] = $tiempo - $resta;
				//
			break;
			// MES
			case 4: 
				//
				$restaDos = $resta + $this->setSegs(1,'mes')  + $this->setSegs(1,'hor');
				$data['start'] = $tiempo - $restaDos;
				$data['end'] = $tiempo - $resta;
				//
			break;
			// TODO EL TIEMPO
			case 5: 
				//
				$data['start'] = 0;
				$data['end'] = $tiempo;
				//
			break;
		}
		//
		return $data;
	}
	/*
		setSegs($tiempo, $tipo)
	*/
	function setSegs($tiempo, $tipo){
		//
		switch($tipo){
			case 'min' :
				$segundos = $tiempo * 60;
			break;
			case 'hor' :
				$segundos = $tiempo * 3600;
			break;
			case 'dia' :
				$segundos = $tiempo * 86400;
			break;
			case 'sem' :
				$segundos = $tiempo * 604800;
			break;
			case 'mes' :
				$segundos = $tiempo * 2592000;
			break;

		}
		//
		return $segundos;
	}
}
?>
