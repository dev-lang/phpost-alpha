<?php
/********************************************************************************
* admin.php 	                                                                *
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

	$tsPage = "admin";	// tsPage.tpl -> PLANTILLA PARA MOSTRAR CON ESTE ARCHIVO.

	$tsLevel = 4;		// NIVEL DE ACCESO A ESTA PAGINA. => VER FAQs

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

	// ACTION
	$action = $_GET['action'];
	// ACTION 2
	$act = $_GET['act'];
	// CLASE POSTS
	include("../class/c.admin.php");
	$tsAdmin =& tsAdmin::getInstance();

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/

	if($action == ''){
		$smarty->assign("tsAdmins",$tsAdmin->getAdmins());
	} elseif($action == 'creditos'){
		$smarty->assign("tsVersion",$tsAdmin->getVersions());
	} elseif($action == 'configs'){
		// GUARDAR CONFIGURACION
		if(!empty($_POST['titulo'])) {
			if($tsAdmin->saveConfig()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/configs?save=true');
		}
    /** NOTICIAS **/
    } elseif($action == 'news'){
        if(empty($act)) $smarty->assign("tsNews",$tsAdmin->getNoticias());
        elseif($act == 'nuevo' && !empty($_POST['not_body'])){
            if($tsAdmin->newNoticia()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/news?save=true');
        } elseif($act == 'editar'){
            if(!empty($_POST['not_body'])){
                if($tsAdmin->editNoticia()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/news?save=true');
            } else $smarty->assign("tsNew",$tsAdmin->getNoticia());
        }
	} elseif($action == 'temas'){
		// VER TEMAS
		if(empty($act)){
			$smarty->assign("tsTemas",$tsAdmin->getTemas());
		// EDITAR TEMA
		} elseif($act == 'editar'){
			// GUARDAR
			if(!empty($_POST['save'])){
				if($tsAdmin->saveTema()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/temas?save=true');
			// MOSTRAR
			} else	$smarty->assign("tsTema",$tsAdmin->getTema());
		// CAMBIAR TEMA
		} elseif($act == 'usar'){
			// GUARDAR
			if(!empty($_POST['confirm'])) {
				if($tsAdmin->changeTema()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/temas?save=true');
			}
			// TITULO
			$smarty->assign("tt",$_GET['tt']);
		} elseif($act == 'borrar'){
			// GUARDAR
			if(!empty($_POST['confirm'])) {
				if($tsAdmin->deleteTema()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/temas?save=true');
			}
			// TITULO
			$smarty->assign("tt",$_GET['tt']);
		} elseif($act == 'nuevo'){
			// GUARDAR
			if(!empty($_POST['path'])) {
				$install = $tsAdmin->newTema();
				if($install == 1) $tsCore->redirectTo($tsCore->settings['url'].'/admin/temas?save=true');
				else $smarty->assign("tsError",$install);
			}
		}
	} elseif($action == 'ads'){
		if(!empty($_POST['save'])){
			if($tsAdmin->saveAds()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/ads?save=true');
		}
    /** MEDALLAS **/
    } elseif($action == 'medals'){
    	// CLASE MEDAL
    	include("../class/c.medals.php");
    	$tsMedal =& tsMedal::getInstance();
        if(empty($act)){
            $smarty->assign("tsMedals",$tsMedal->adGetMedals());
        } elseif($act == 'nueva'){
            if($_POST['save']){
                if($tsMedal->adNewMedal()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/medals?save=true');
            } else {
				// ICONOS PARA LAS MEDALLAS
				$smarty->assign("tsIcons",$tsAdmin->getExtraIcons('med', 16));
            }
        }
	} elseif($action == 'afs'){
        // CLASS
        include("../class/c.afiliado.php");
        $tsAfiliado =& tsAfiliado::getInstance();
        // QUE HACER
	   if($act == ''){
        // AFILIADOS
        $smarty->assign("tsAfiliados",$tsAfiliado->getAfiliados('admin'));
	   }
	} elseif($action == 'pconfigs'){
		if(!empty($_POST['save'])){
			if($tsAdmin->savePConfigs()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/pconfigs?save=true');
		}
	} elseif($action == 'cats'){
		if(!empty($_GET['ordenar'])){
			$tsAdmin->saveOrden();
		} elseif($act == 'editar'){
			if($_POST['save']){
				if($tsAdmin->saveCat()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/cats?save=true');
			} else {
				$smarty->assign("tsType",$_GET['t']);
				$smarty->assign("tsCat",$tsAdmin->getCat());
				// SOLO LAS CATEGORIAS TIENEN ICONOS
				$smarty->assign("tsIcons",$tsAdmin->getExtraIcons());
			}
		} elseif($act == 'nueva'){
			if($_POST['save']){
				if($tsAdmin->newCat()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/cats?save=true');
			} else {
				$smarty->assign("tsType",$_GET['t']);
				$smarty->assign("tsCID",$_GET['cid']);
				$smarty->assign("tsIcons",$tsAdmin->getExtraIcons());
			}
		} elseif($act == 'borrar'){
			if($_POST['save']){
				// BORRAR CATEGORIA
				if($_GET['t'] == 'cat'){
					$save = $tsAdmin->delCat();
					if($save == 1) $tsCore->redirectTo($tsCore->settings['url'].'/admin/cats?save=true');
					else $smarty->assign("tsError",$save); 
				// BORRAR SUBCATEGORIA
				} elseif($_GET['t'] == 'sub'){
					$save = $tsAdmin->delSubcat();
					if($save == 1) $tsCore->redirectTo($tsCore->settings['url'].'/admin/cats?save=true');
					else $smarty->assign("tsError",$save); 
				}
			}
			//
			$smarty->assign("tsType",$_GET['t']);
			$smarty->assign("tsCID",$_GET['cid']);
			$smarty->assign("tsSID",$_GET['sid']);
		}
	} elseif($action == 'rangos'){
			// PORTADA
			if(empty($act)) {
				$smarty->assign("tsRangos",$tsAdmin->getRangos());
			// LISTAR USUARIOS DEPENDIENDO EL RANGO
			} elseif($act == 'list'){
				$smarty->assign("tsMembers",$tsAdmin->getRangoUsers());
			// EDITAR RANGO
			} elseif($act == 'editar'){
				if(!empty($_POST['save'])){
					if($tsAdmin->saveRango()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/rangos?save=true');
				} else {
					$smarty->assign("tsRango",$tsAdmin->getRango());
					$smarty->assign("tsIcons",$tsAdmin->getExtraIcons('ran'));
                    $smarty->assign("tsType",$_GET['t']);
				}
			// NUEVO RANGO
			} elseif($act == 'nuevo'){
				if(!empty($_POST['save'])){
					$save = $tsAdmin->newRango();
					if($save == 1) $tsCore->redirectTo($tsCore->settings['url'].'/admin/rangos?save=true');
					else {
						$smarty->assign("tsError",$save); 
						$smarty->assign("tsIcons",$tsAdmin->getExtraIcons('ran'));
					}
				} else {
					$smarty->assign("tsIcons",$tsAdmin->getExtraIcons('ran'));
                    $smarty->assign("tsType",$_GET['t']);
				}
			} elseif($act == 'borrar'){
				if(!empty($_POST['save'])){
					if($tsAdmin->delRango()) $tsCore->redirectTo($tsCore->settings['url'].'/admin/rangos?save=true');
				}
			}
	} elseif($action == 'users'){
	   if(empty($act)){
	       $smarty->assign("tsMembers",$tsAdmin->getUsuarios());
	   }elseif($act == 'show'){
	       $do = $_GET['t'];
           $user_id = $_GET['uid'];
           // HACER
           switch($do){
                case 7:
        	       if(!empty($_POST['save'])){
        	           $update = $tsAdmin->setUserRango($user_id);
        	           if($update == 'OK') $tsCore->redirectTo($tsCore->settings['url'].'/admin/users?act=show&uid='.$user_id.'&save=true');
                       else $smarty->assign("tsError",$update);
                    }
                    $smarty->assign("tsUserR",$tsAdmin->getUserRango($user_id));
                break;
                default:
                    if(!empty($_POST['save'])){
        	           $update = $tsAdmin->setUserData($user_id);
        	           if($update == 'OK') $tsCore->redirectTo($tsCore->settings['url'].'/admin/users?act=show&uid='.$user_id.'&save=true');
                       else $smarty->assign("tsError",$update);
                    }
    	           $smarty->assign("tsUserD",$tsAdmin->getUserData());
                break;
           }
           // TIPO
           $smarty->assign("tsType",$_GET['t']);
           $smarty->assign("tsUserID",$user_id);
           $smarty->assign("tsUsername",$tsUser->getUserName($user_id));
	   }
	}

/**********************************\

* (AGREGAR DATOS GENERADOS | SMARTY) *

\*********************************/
	// ACCION?
	$smarty->assign("tsAction",$action);
	//
	$smarty->assign("tsAct",$act);
	//
	}

if(empty($tsAjax)) {	// SI LA PETICION SE HIZO POR AJAX DETENER EL SCRIPT Y NO MOSTRAR PLANTILLA, SI NO ENTONCES MOSTRARLA.

	$smarty->assign("tsTitle",$tsTitle);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL
	
	$smarty->assign("tsSave",$_GET['save']);	// AGREGAR EL TITULO DE LA PAGINA ACTUAL
	
	/*++++++++ = ++++++++*/
	include("../../footer.php");
	/*++++++++ = ++++++++*/
}

?>

