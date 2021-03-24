<?php
/********************************************************************************
* perfil.php 	                                                                *
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

	$tsPage = "perfil";	// tsPage.tpl -> PLANTILLA PARA MOSTRAR CON ESTE ARCHIVO.

	$tsLevel = 0;		// NIVEL DE ACCESO A ESTA PAGINA. => VER FAQs

	$tsAjax = empty($_GET['ajax']) ? 0 : 1; // LA RESPUESTA SERA AJAX?
	
	$tsContinue = true;	// CONTINUAR EL SCRIPT
	
/*++++++++ = ++++++++*/

	include "../../header.php"; // INCLUIR EL HEADER

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

	$username = $tsCore->setSecure($_GET['user']);
	$user_id = $tsUser->getUserID($username);
	// EXISTE?
	if(empty($user_id)) {
		$tsPage = 'aviso';
		$tsAjax = 0;
		$smarty->assign("tsAviso",array('titulo' => 'Opps!', 'mensaje' => 'Ese usuario no existe
', 'but' => 'Ir a p&aacute;gina principal'));
	} else {
	//
	include("../class/c.cuenta.php");
	$tsCuenta =& tsCuenta::getInstance();

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/

	
	include('../ext/datos.php');
	$tsInfo = $tsCuenta->loadHeadInfo($user_id);
    $tsInfo['uid'] = $user_id;
	// IS ONLINE?
    $is_online = (time() - ($tsCore->settings['c_last_active'] * 60));
    $is_inactive = (time() - (($tsCore->settings['c_last_active'] * 60) * 2)); // DOBLE DEL ONLINE
    //
    if($tsInfo['user_lastactive'] > $is_online) $tsInfo['status'] = array('t' => 'Online', 'css' => 'online');
    elseif($tsInfo['user_lastactive'] > $is_inactive) $tsInfo['status'] = array('t' => 'Inactivo', 'css' => 'inactive');
    elseif($tsInfo['user_baneado'] > 0) $tsInfo['status'] = array('t' => 'Suspendido', 'css' => 'banned');
    else $tsInfo['status'] = array('t' => 'Offline', 'css' => 'offline');
	// GENERAL
	$tsGeneral = $tsCuenta->loadGeneral($user_id);
    $tsInfo['nick'] = $tsInfo['user_name'];
    $tsInfo = array_merge($tsInfo,$tsGeneral);
    // PAIS
	$tsInfo['user_pais'] = $tsPaises[$tsInfo['user_pais']];
    // LO SIGO?
    $tsInfo['follow'] = $tsCuenta->iFollow($user_id);
    // MANDAR A PLANTILLA
	$smarty->assign("tsInfo",$tsInfo);
	$smarty->assign("tsGeneral",$tsGeneral);
    // MURO
    include("../class/c.muro.php");
    $tsMuro =& tsMuro::getInstance();
    // PERMISOS
    $priv = $tsMuro->getPrivacity($user_id, $username, $tsInfo['follow']);
    // SE PERMITE VER EL MURO?
    if($priv['m']['v'] == true){
        // CARGAR HISTORIA
        if(!empty($_GET['pid'])) {
            $pub_id = $tsCore->setSecure($_GET['pid']);
            $story = $tsMuro->getStory($pub_id, $user_id);
            //
            if(!is_array($story)){
                $tsPage = 'aviso';
                $smarty->assign("tsAviso",array('titulo' => 'Opps...', 'mensaje' => $story, 'but' => 'Ir a pagina principal', 'link' => "{$tsCore->settings['url']}"));
            }
            else {
                $story['data'][1] = $story;
                $smarty->assign("tsMuro", $story);
                $smarty->assign("tsType","story");
            }
        }elseif($tsCore->settings['c_allow_portal'] == 0 && $tsInfo['uid'] == $tsUser->uid){
            $smarty->assign("tsMuro",$tsMuro->getNews());
            $smarty->assign("tsType","news");
        }else{
            $smarty->assign("tsMuro",$tsMuro->getWall($user_id));
            $smarty->assign("tsType","wall");
        }
    }
    $smarty->assign("tsPrivacidad",$priv);
	// TITULO
	$tsTitle = 'Perfil de '.$tsInfo['nick'].' - '.$tsTitle;
 
	
/**********************************\

* (AGREGAR DATOS GENERADOS | SMARTY) *

\*********************************/
		}
	}

if(empty($tsAjax)) {	// SI LA PETICION SE HIZO POR AJAX DETENER EL SCRIPT Y NO MOSTRAR PLANTILLA, SI NO ENTONCES MOSTRARLA.

	$smarty->assign("tsTitle",$tsTitle);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL

	/*++++++++ = ++++++++*/
	include("../../footer.php");
	/*++++++++ = ++++++++*/
}

?>

