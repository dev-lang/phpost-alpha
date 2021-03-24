<?php
/********************************************************************************
* live.php 	                                                                    *
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
		'feed-support' => array('n' => 2, 'p' => ''),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.live.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg['mensaje']; die();}
	// CODIGO
	switch($action){
		case 'feed-support':
			//<---
            $json = $tsCore->getUrlContent('http://www.phpost.net/feed/index.php?type=support');
            echo $json;
			//--->
		break;
		case 'feed-version':
			//<---
            $json = $tsCore->getUrlContent('http://www.phpost.net/feed/index.php?type=version');
            echo $json;
			//--->
		break;
        default:
            die('0: Este archivo no existe.');
        break;
	}
?>