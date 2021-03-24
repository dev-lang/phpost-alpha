<?php
/********************************************************************************
* preview.php	 	                                                            *
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
		'moderacion-posts' => array('n' => 3, 'p' => 'main'),
        'moderacion-users' => array('n' => 3, 'p' => 'main'),
	);

/**********************************\

* (VARIABLES LOCALES ESTE ARCHIVO)	*

\*********************************/

	// REDEFINIR VARIABLES
    $tsPage = 'php_files/p.moderacion.'.$files[$action]['p'];
	$tsLevel = $files[$action]['n'];
	$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
	// DEPENDE EL NIVEL
	$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
	if($tsLevelMsg != 1) { echo '0: '.$tsLevelMsg['mensaje']; die();}
	// CLASE
	require('../class/c.moderacion.php');
	$tsMod =& tsMod::getInstance();
    //
    $do = $_GET['do'];
	// CODIGO
	switch($action){
		case 'moderacion-posts':
			//<--
                // POST ID
                $pid = $_POST['postid'];
                // ACCIONES SECUNDARIAS
                switch($do){
                    case 'view':
                        $tsPage = 'php_files/p.posts.preview';
                        $preview = $tsMod->getPreview($pid);
                        $smarty->assign("tsPreview",$preview);
                    break;
                    case 'reboot':
                        $tsAjax = 1;
                        echo $tsMod->rebootPost($_POST['id']);
                    break;
                    case 'borrar':
                        if($_POST['razon']){
                            $tsAjax = 1;
                            echo $tsMod->deletePost($pid);
                        }else {
                            include("../ext/datos.php");
                            $tsPage = 'php_files/p.posts.mod';
                            $smarty->assign("tsDenuncias",$tsDenuncias['posts']);   
                        }
                    break;
                }
			//-->
		break;
		case 'moderacion-users':
			//<--
                // POST ID
                $user_id = $_POST['uid'];
                $username = $tsUser->getUserName($user_id);
                // ACCIONES SECUNDARIAS
                switch($do){
                    case 'aviso':
                        if($_POST['av_body']){
                            $tsAjax = 1;
                            $aviso = $_POST['av_body']."\n\n".'Staff: <a href="#" class="hovercard" uid="'.$tsUser->uid.'">'.$tsUser->nick.'</a>';
                            $aviso_resp = $tsMonitor->setAviso($user_id, $_POST['av_subject'], $aviso, $_POST['av_type']);
                            if(!$aviso_resp) echo '0: Error al enviar el aviso a <b>'.$username.'</b>.';
                            else echo '1: El avioso fue enviado con &eacute;xito a <b>'.$username.'</b>.';
                        } else $smarty->assign("tsUsername", $tsUser->getUserName($user_id));
                    break;
                    case 'ban':
                        if($_POST['b_causa']){
                            $tsAjax = 1;
                            echo $tsMod->banUser($user_id);
                        }  else $smarty->assign("tsUsername", $tsUser->getUserName($user_id));
                    break;
                    case 'unban':
                        $tsAjax = 1;
                        echo $tsMod->rebootUser($_POST['id'], 'unban');
                    break;
                    case 'reboot':
                        $tsAjax = 1;
                        echo $tsMod->rebootUser($_POST['id'], 'reboot');
                    break;
                }
                // HACER
                $smarty->assign("tsDo",$do);
			//-->
		break;
	}
?>