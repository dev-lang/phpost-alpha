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
class tsPortal extends tsDatabase{

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsPortal();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								PUBLICAR POSTS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    /** getNews()
     * @access public
     * @param 
     * @return array
     */
     public function getNews(){
        // MURO
        include(TS_CLASS."c.muro.php");
        $tsMuro =& tsMuro::getInstance();
        return $tsMuro->getNews(0);
     }
    /** setPostsConfig()
     * @access public
     * @param 
     * @return string
     */
     public function savePostsConfig(){
        global $tsUser;
        //
        $cat_ids = substr($_POST['cids'],0,-1); // PARA QUITAR LA ULTIMA COMA xD
        $cat_ids = explode(',', $cat_ids);
        $cat_ids = serialize($cat_ids);
        //
        if($this->update("u_portal","last_posts_cats = '{$cat_ids}'","user_id = {$tsUser->uid}")) return '1: Tus cambios fueron aplicados.';
        else return '0: Int&eacute;ntalo mas tarde.';
     }
     /** composeCategories()
     * @access public
     * @param array
     * @return array
     */
     public function composeCategories(){
        global $tsCore, $tsUser;
        //
        $query = $this->select("u_portal","last_posts_cats","user_id = '{$tsUser->uid}'");
        $data = $this->fetch_assoc($query);
        $this->free($query);
        //
        $data = unserialize($data['last_posts_cats']);
        foreach($tsCore->settings['categorias'] as $key => $cat){
            if(in_array($cat['cid'], $data)) $cat['check'] = 1;
            else $cat['check'] = 0;
            $categories[] = $cat;
        }
        //
        return $categories;
     }
     /** getMyPosts()
     * @access public
     * @param
     * @return array
     */
     public function getMyPosts(){
        global $tsCore, $tsUser;
        //
        $query = $this->select("u_portal","last_posts_cats","user_id = '{$tsUser->uid}'");
        $data = $this->fetch_assoc($query);
        $this->free($query);
        //
        $cat_ids = unserialize($data['last_posts_cats']);
        if(is_array($cat_ids)){
            $cat_ids = implode(',',$cat_ids);
            $where = "p.post_category IN ({$cat_ids})";
            //
            $query = $this->query("SELECT COUNT(p.post_id) AS total FROM p_posts AS p WHERE p.post_status = 0 AND {$where}");
            $total = $this->fetch_assoc($query);
            $this->free($query);
            //
            if($total['total'] > 0)
                $pages = $tsCore->getPagination($total['total'], 20);
            else return false;
            //
            $query = $this->query("SELECT p.post_id, p.post_category, p.post_title, p.post_date, p.post_comments, p.post_puntos, p.post_private, u.user_name, c.c_nombre, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND {$where} ORDER BY p.post_id DESC LIMIT {$pages['limit']}");
            $posts['data'] = $this->fetch_array($query);
            $this->free($query);
            //
            $posts['pages'] = $pages;
            //
            return $posts;
        } else return false;
     }
    /** getLastPosts()
     * @access public
     * @param string
     * @return array
     */
	function getLastPosts($type = 'visited'){
		global $tsUser;
        //
        $query = $this->select("u_portal","last_posts_{$type}","user_id = {$tsUser->uid}","",1);
        $dato = $this->fetch_assoc($query);
        $this->free($query);
        $visited = unserialize($dato['last_posts_'.$type]);
        krsort($visited);
		// LO HAGO ASI PARA ORDENAR SIN NECESITAR OTRA VARIABLE
        foreach($visited as $key => $id){
            $query = $this->query("SELECT p.post_id, p.post_user, p.post_category, p.post_title, p.post_date, p.post_comments, p.post_puntos, p.post_private, u.user_name, c.c_nombre, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND p.post_id = {$id} LIMIT 1");
            $data[] = $this->fetch_assoc($query);
            $this->free($query);
        }
		//
		return $data;
	}
     /** getFavorites()
     * @access public
     * @param
     * @return array
     */
     public function getFavorites(){
        global $tsCore, $tsUser;
        //
        $query = $this->select("p_favoritos","COUNT(fav_id) AS total","fav_user = {$tsUser->uid}");
        $total = $this->fetch_assoc($query);
        $this->free($query);
        if($total['total'] > 0)
            $pages = $tsCore->getPagination($total['total'], 20);
        else return false;
        //
        $query = $this->query("SELECT f.fav_id, p.post_id, p.post_category, p.post_title, p.post_date, p.post_comments, p.post_puntos, p.post_private, u.user_name, c.c_nombre, c.c_seo, c.c_img FROM p_favoritos AS f LEFT JOIN p_posts AS p ON f.fav_post_id = p.post_id LEFT JOIN u_miembros AS u ON p.post_user = u.user_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND f.fav_user = {$tsUser->uid} ORDER BY f.fav_date DESC LIMIT {$pages['limit']}");
		$data['data'] = $this->fetch_array($query);
		$this->free($query);
        //
        $data['pages'] = $pages;
        //
        return $data;
     }
     /** getFotos()
     * @access public
     * @param
     * @return array
     */
     public function getFotos(){
        // FOTOS
    	include(TS_CLASS."c.fotos.php");
    	$tsFotos =& tsFotos::getInstance();
        return $tsFotos->getLastFotos();
     }
     /** getStats()
     * @access public
     * @param
     * @return array
     */
     public function getStats(){
    	// CLASE TOPS
    	include(TS_CLASS."c.tops.php");
    	$tsTops =& tsTops::getInstance();
        return $tsTops->getStats();
     }
}
?>
