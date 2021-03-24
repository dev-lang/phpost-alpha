<?php
/********************************************************************************
* afiliado.php 	                                                             *
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

	// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCIÓN
	$files = array(
		'afiliado-nuevo' => array('n' => 0, 'p' => ''),
        'afiliado-url' => array('n' => 0, 'p' => ''),
        'afiliado-detalles' => array('n' => 0, 'p' => 'detalles'),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.afiliado.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg['mensaje']; die();}
    // CLASS
    include("../class/c.afiliado.php");
    $tsAfiliado =& tsAfiliado::getInstance();
    //
	// CODIGO
	switch($action){
		case 'afiliado-nuevo':
			//<---
            echo $tsAfiliado->newAfiliado();
			//--->
		break;
		case 'afiliado-url':
			//<---
            $tsAfiliado->urlOut();
			//--->
		break;
		case 'afiliado-detalles':
			//<---
            $smarty->assign("tsAf",$tsAfiliado->getAfiliado());
			//--->
		break;
        default:
            die('0: Este archivo no existe.');
        break;
	}
?>