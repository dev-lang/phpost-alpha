<?php
/********************************************************************************
* mensajes.php	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox �											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox �											*
*********************************************************************************/


/**********************************\

*	(VARIABLES POR DEFAULT)		*

\*********************************/

	// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCI�N
	$files = array(
		'mensajes-validar' => array('n' => 2, 'p' => ''),
        'mensajes-enviar' => array('n' => 2, 'p' => ''),
        'mensajes-respuesta' => array('n' => 2, 'p' => 'resp'),
        'mensajes-lista' => array('n' => 2, 'p' => 'lista'),
        'mensajes-editar' => array('n' => 2, 'p' => ''),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.mensajes.'.$files[$action]['p'];
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
		case 'mensajes-validar':
			// <!--
            echo $tsMP->getValid();
            // -->
		break;
        case 'mensajes-enviar':
			// <!--
            echo $tsMP->newMensaje();
            // -->
		break;
        case 'mensajes-respuesta':
			// <!--
            $smarty->assign("mp",$tsMP->newRespuesta());
            // -->
		break;
        case 'mensajes-lista':
			// <!--
            $smarty->assign("tsMensajes",$tsMP->getMensajes());
            // -->
		break;
        case 'mensajes-editar':
			// <!--
            echo $tsMP->editMensajes();
            // -->
		break;
	}
    
    /*
        HACK
    */
    $_GET['ts'] = true;
?>