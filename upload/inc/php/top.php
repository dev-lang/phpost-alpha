<?php
/********************************************************************************
* top.php 	                                                               		*
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

	$tsPage = "tops";	// tsPage.tpl -> PLANTILLA PARA MOSTRAR CON ESTE ARCHIVO.

	$tsLevel = 0;		// NIVEL DE ACCESO A ESTA PAGINA. => VER FAQs

	$tsAjax = empty($_GET['ajax']) ? 0 : 1; // LA RESPUESTA SERA AJAX?
	
	$tsContinue = true;	// CONTINUAR EL SCRIPT
	
/*++++++++ = ++++++++*/

	include "../../header.php"; // INCLUIR EL HEADER

	$tsTitle = $tsCore->settings['titulo'].' - '.$tsCore->settings['slogan']; 	// TITULO DE LA PAGINA ACTUAL

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

	// CLASE TOPS
	include("../class/c.tops.php");
	$tsTops =& tsTops::getInstance();
	//
	$fecha = (int) empty($_GET['fecha']) || $_GET['fecha'] > 5 ? 5 : $_GET['fecha'];
	$smarty->assign("tsFecha",$fecha);
	$cat = (int) empty($_GET['cat']) ? 0 : $_GET['cat'];
	$smarty->assign("tsCat",$cat);
	//
	$action = empty($_GET['action']) ? 'posts' : $_GET['action'];
	$smarty->assign("tsAction",$action);
	

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/

		switch($action){
			case 'posts':
				$smarty->assign("tsTops",$tsTops->getTopPosts($fecha, $cat));
			break;
            case 'usuarios':
                $smarty->assign("tsTops",$tsTops->getTopUsers($fecha, $cat));
            break;
		}

/**********************************\

* (AGREGAR DATOS GENERADOS | SMARTY) *

\*********************************/
	}

if(empty($tsAjax)) {	// SI LA PETICION SE HIZO POR AJAX DETENER EL SCRIPT Y NO MOSTRAR PLANTILLA, SI NO ENTONCES MOSTRARLA.

	$smarty->assign("tsTitle",$tsTitle);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL

	/*++++++++ = ++++++++*/
	include("../../footer.php");
	/*++++++++ = ++++++++*/
}

?>

