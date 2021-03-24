<?php
/********************************************************************************
* posts.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/


/**********************************\

*	(VARIABLES POR DEFAULT)		*

\*********************************/

	$tsPage = "posts";	// tsPage.tpl -> PLANTILLA PARA MOSTRAR CON ESTE ARCHIVO.

	$tsLevel = 0;		// NIVEL DE ACCESO A ESTA PAGINA. => VER FAQs

	$tsAjax = empty($_GET['ajax']) ? 0 : 1; // LA RESPUESTA SERA AJAX?
	
	$tsContinue = true;	// CONTINUAR EL SCRIPT
	
/*++++++++ = ++++++++*/

	$tsTitle = $tsCore->settings['titulo']; 	// TITULO DE LA PAGINA ACTUAL

/*++++++++ = ++++++++*/

	// VERIFICAMOS EL NIVEL DE ACCSESO ANTES CONFIGURADO
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1){	
		$tsPage = 'aviso';
		$tsAjax = 0;
		$smarty->assign("tsAviso",$tsLevelMsg);
		//
		$tsContinue = false;
	}
	//
	if($tsContinue){

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/
    // AFILIADOS
    include(TS_CLASS."c.afiliado.php");
    $tsAfiliado =& tsAfiliado::getInstance();
    // NOS HAN REFERIDO?
    if(!empty($_GET['ref'])) $tsAfiliado->urlIn();
	// CLASE POSTS
	include(TS_CLASS."c.posts.php");
	$tsPosts =& tsPosts::getInstance();
    // CATEGORIAS
	$category = $_GET['cat'];
    // POST ANTERIOR O SIGUIENTE
    if($_GET['action'] == 'next' || $_GET['action'] == 'prev'){
        $tsPosts->setNP();
    }

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
    if(!empty($_GET['post_id'])){
        // DATOS DEL POST
    	$tsPost = $tsPosts->getPost();
    	//
    	if($tsPost['post_id'] > 0) {
    		// TITULO NUEVO
    		$tsTitle = $tsPost['post_title'].' - '.$tsTitle;
    		// ASIGNAMOS A LA PLANTILLA
    		$smarty->assign("tsPost",$tsPost);
    		// DATOS DEL AUTOR
    		$smarty->assign("tsAutor",$tsPosts->getAutor($tsPost['post_user']));
    		// RELACIONADOS
    		$tsRelated = $tsPosts->getRelated($tsPost['post_tags']);
    		$smarty->assign("tsRelated",$tsRelated);
    		// COMENTARIOS
    		/*$tsComments = $tsPosts->getComentarios($tsPost['post_id']);
    		$tsComments = array('num' => $tsComments['num'], 'data' => $tsComments['data']);
    		$smarty->assign("tsComments",$tsComments);*/
    		// PAGINAS
    		$total = $tsPost['post_comments'];
    		$tsPages = $tsCore->getPages($total, $tsCore->settings['c_max_com']);
    		$tsPages['post_id'] = $tsPost['post_id'];
    		$tsPages['autor'] = $tsPost['post_user'];
    		//
    		$smarty->assign("tsPages",$tsPages);
    
    	} else {
    		//
            if($tsPost[0] == 'privado'){
                $tsTitle = $tsPost[1].' - '.$tsTitle;
                $tsPage = "registro";
                include("../ext/datos.php");
                // SOLO MENORES DE 100 AÑOS xD Y MAYORES DE...
                $now_year = date("Y",time());
                $max_year = 100 - $tsCore->settings['c_allow_edad'];
                $end_year = $now_year - $tsCore->settings['c_allow_edad'];
                $smarty->assign("tsMax",$max_year);
                $smarty->assign("tsEndY",$end_year);
                $smarty->assign("tsPaises",$tsPaises);
                $smarty->assign("tsMeces",$tsMeces);
                $smarty->assign("tsType",'post');
            } else {
        		$tsTitle = $tsTitle.' - '.$tsCore->settings['slogan'];
        		//
        		$tsPage = "post.aviso";
        		$tsAjax = 0;
        		$smarty->assign("tsAviso",$tsPost);
        		//
        		$title = str_replace("-",",",$tsCore->setSecure($_GET['title']));
        		$title = explode(",",$title);
        		// RELACIONADOS
        		$tsRelated = $tsPosts->getRelated($title);
        		$smarty->assign("tsRelated",$tsRelated);
            }
    	}
    } else {
        // PAGINA
        $tsPage = "home";
        $tsTitle = $tsTitle.' - '.$tsCore->settings['slogan']; 	// TITULO DE LA PAGINA ACTUAL
    	// CLASE TOPS
    	include(TS_CLASS."c.tops.php");
    	$tsTops =& tsTops::getInstance();
    	// ULTIMOS POSTS
    	$tsLastPosts = $tsPosts->getLastPosts($category, $subcateg);
    	$smarty->assign("tsPosts",$tsLastPosts['data']);
        $smarty->assign("tsPages",$tsLastPosts['pages']);
    	// ULTIMOS POSTS FIJOS
        if($tsLastPosts['pages']['current'] == 1){
    	   $tsLastStickys = $tsPosts->getLastPosts($category, $subcateg, true);
    	   $smarty->assign("tsPostsStickys",$tsLastStickys['data']);
        }
    	// CAT
    	$smarty->assign("tsCat",$category);
    	$smarty->assign("tsStats",$tsTops->getStats());
    	// ULTIMOS COMENTARIOS
    	$smarty->assign("tsComments",$tsPosts->getLastComentarios());
    	// TOP POSTS
    	$smarty->assign("tsTopPosts",$tsTops->getHomeTopPosts());
    	// TOP USERS
    	$smarty->assign("tsTopUsers",$tsTops->getHomeTopUsers());
        // TITULO
        if(!empty($category)) {
            $catData = $tsPosts->getCatData();
            $tsTitle = $tsCore->settings['titulo'].' - '.$catData['c_nombre'];
            $smarty->assign("tsCatData",$catData);
        }
        // IMAGENES
        // FOTOS
    	include(TS_CLASS."c.fotos.php");
    	$tsFotos =& tsFotos::getInstance();
        $tsImages = $tsFotos->getLastFotos();
    	$smarty->assign("tsImages",$tsImages);
        $smarty->assign("tsImTotal",count($tsImages));
        
        // AFILIADOS
    	$smarty->assign("tsAfiliados",$tsAfiliado->getAfiliados());
        // DO <= PARA EL MENU
        $smarty->assign("tsDo",$_GET['do']);

    }

/**********************************\

* (AGREGAR DATOS GENERADOS | SMARTY) *

\*********************************/
	}

if(empty($tsAjax)) {	// SI LA PETICION SE HIZO POR AJAX DETENER EL SCRIPT Y NO MOSTRAR PLANTILLA, SI NO ENTONCES MOSTRARLA.

	$smarty->assign("tsTitle",$tsTitle);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL

	/*++++++++ = ++++++++*/
	include(TS_ROOT."/footer.php");
	/*++++++++ = ++++++++*/
}

?>

