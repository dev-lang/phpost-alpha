<?php
/********************************************************************************
* denuncia.php 	                                                            	*
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
		'denuncia-post' => array('n' => 2, 'p' => 'form'),
        'denuncia-mensaje' => array('n' => 2, 'p' => 'form'),
        'denuncia-usuario' => array('n' => 2, 'p' => 'form'),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
	$tsPage = 'php_files/p.denuncia.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg; die();}
    // SWAT
	include("../class/c.swat.php");
	$tsSwat =& tsSwat::getInstance();
    // VARS
    $obj_id = $tsCore->setSecure($_POST['obj_id']);
	// CODIGO
	switch($action){
		case 'denuncia-post':   
        	// CREAR DENUNCIA
            if($_POST['razon']){
                $tsAjax = 1;
                echo $tsSwat->setDenuncia($obj_id);
            // FORMULARIO DE DENUNCIA
            } else {
                // VARS
                $tsData = array(
                    'obj_id' => $obj_id,
                    'obj_title' => $tsCore->setSecure($_POST['obj_title']),
                    'obj_user' => $tsCore->setSecure($_POST['obj_user']), 
                );
    			// DATOS
                include("../ext/datos.php");
    			$smarty->assign("tsData",$tsData);
    			$smarty->assign("tsDenuncias",$tsDenuncias['posts']);
            }
		break;
        case 'denuncia-mensaje':
            if($_POST['razon']){
                $tsAjax = 1;
                echo $tsSwat->setDenuncia($obj_id, 'mensaje');
            }
        break;
        case 'denuncia-usuario':
            if($_POST['razon']){
                $tsAjax = 1;
                echo $tsSwat->setDenuncia($obj_id, 'usuario');
            }
			// DATOS
            include("../ext/datos.php");
            $smarty->assign("tsData",array('nick' => $_POST['obj_user']));
    		$smarty->assign("tsDenuncias",$tsDenuncias['users']);
        break;
	}
    // ACCION
    $smarty->assign("tsAction",$action);
?>