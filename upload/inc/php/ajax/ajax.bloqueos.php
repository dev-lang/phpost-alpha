<?php
/********************************************************************************
* bloqueos.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ?											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ?											*
*********************************************************************************/


/**********************************\

*	(VARIABLES POR DEFAULT)		*

\*********************************/

	// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCI?N
	$files = array(
		'bloqueos-cambiar' => array('n' => 2, 'p' => ''),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.bloqueos.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg; die();}
    // CLASE
	include("../class/c.cuenta.php");
	$tsCuenta =& tsCuenta::getInstance();
    //
    //echo $tsUser->getUserName($_GET['user']);
	// CODIGO
	switch($action){
		case 'bloqueos-cambiar':
			//<---
            echo $tsCuenta->bloqueosCambiar();
			//--->
		break;
	}
?>