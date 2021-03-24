<?php
/********************************************************************************
* fotos.php 	                                                                *
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

	$tsPage = "fotos";	// tsPage.tpl -> PLANTILLA PARA MOSTRAR CON ESTE ARCHIVO.

	$tsLevel = 2;		// NIVEL DE ACCESO A ESTA PAGINA. => VER FAQs

	$tsAjax = empty($_GET['ajax']) ? 0 : 1; // LA RESPUESTA SERA AJAX?
	
	$tsContinue = true;	// CONTINUAR EL SCRIPT
	
/*++++++++ = ++++++++*/

	include "../../header.php"; // INCLUIR EL HEADER

	$tsTitle = $tsCore->settings['titulo'].' - '.$tsCore->settings['slogan']; 	// TITULO DE LA PAGINA ACTUAL

/*++++++++ = ++++++++*/
	// PARA LAS FOTOS...
    $action = $_GET['action'];
    if($action == '' || $action == 'ver') $tsLevel = 0;
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
	//
	include("../class/c.fotos.php");
	$tsFotos =& tsFotos::getInstance();

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/

    switch($action){
        case '':
            $smarty->assign("tsLastFotos", $tsFotos->getLastFotos());
            $smarty->assign("tsLastComments", $tsFotos->getLastComments());
            $query = $tsdb->select("w_stats","stats_miembros, stats_fotos, stats_foto_comments","stats_no = 1","",1);
            $smarty->assign("tsStats", $tsdb->fetch_assoc($query));
            $tsdb->free($query);
        break;
        case 'agregar':
            if(!empty($_POST['titulo'])){
                $result = $tsFotos->newFoto();
                $tsPage = 'aviso';
                if(!is_array($result) && $result > 0){
                    $titulo = $tsCore->setSecure($_POST['titulo']);
                    $smarty->assign("tsAviso",array('titulo' => 'Foto Agregada', 'mensaje' => "La imagen <b>".$titulo."</b> fue agregada.", 'but' => 'Ver imagen', 'link' => "{$tsCore->settings['url']}/fotos/{$tsUser->nick}/{$result}/".$tsCore->setSEO($titulo).".html"));
                } else {
                    $smarty->assign("tsAviso",array('titulo' => 'Opps...', 'mensaje' => $result, 'but' => 'Volver', 'link' => "{$tsCore->settings['url']}/fotos/agregar.php"));
                }
            }
            
        break;
        case 'editar':
            if(empty($_POST['titulo'])){
                $tsFoto = $tsFotos->getFotoEdit();
                if(!is_array($tsFoto)){
                    $tsPage = 'aviso';
                    $smarty->assign("tsAviso",array('titulo' => 'Opps...', 'mensaje' => $tsFoto, 'but' => 'Ir a Fotos', 'link' => "{$tsCore->settings['url']}/fotos/"));
                }
                else $smarty->assign("tsFoto", $tsFoto);
            } else {
                $tsPage = 'aviso';
                $tsFoto = $tsFotos->editFoto();
                $smarty->assign("tsAviso",array('titulo' => 'Opps...', 'mensaje' => $tsFoto, 'but' => 'Ir a Fotos', 'link' => "{$tsCore->settings['url']}/fotos/"));
            }
        break;
        case 'borrar':
            $tsAjax = 1;
            echo $tsFotos->delFoto();
        break;
        case 'ver':
            $tsFoto = $tsFotos->getFoto();
            $smarty->assign("tsFoto", $tsFoto['foto']);
            $smarty->assign("tsUFotos", $tsFoto['last']);
            $smarty->assign("tsFFotos", $tsFoto['amigos']);
            $smarty->assign("tsFComments", $tsFoto['comments']);
            // TITULO
            $tsTitle = $tsFoto['foto']['f_title'].' - '.$tsFoto['foto']['user_name'].' - '.$tsCore->settings['titulo'];
        break;
        case 'album':
            $username = $tsCore->setSecure($_GET['user']);
            $user_id = $tsUser->getUserID($username);
            if(empty($user_id)){
                $tsPage = 'aviso';
                $smarty->assign("tsAviso",array('titulo' => 'Opps...', 'mensaje' => 'Este usuario no existe.', 'but' => 'Ir a Fotos', 'link' => "{$tsCore->settings['url']}/fotos/"));
            } else {
                $tsFotox = $tsFotos->getFotos($user_id);
                $smarty->assign("tsFotos", $tsFotox);
                $smarty->assign("tsFUser", array($user_id, $username));
            }

        break;
    }

/**********************************\

* (AGREGAR DATOS GENERADOS | SMARTY) *

\*********************************/
    $smarty->assign("tsAction",$action);
    
}

if(empty($tsAjax)) {	// SI LA PETICION SE HIZO POR AJAX DETENER EL SCRIPT Y NO MOSTRAR PLANTILLA, SI NO ENTONCES MOSTRARLA.

	$smarty->assign("tsTitle",$tsTitle);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL

	/*++++++++ = ++++++++*/
	include("../../footer.php");
	/*++++++++ = ++++++++*/
}

?>

