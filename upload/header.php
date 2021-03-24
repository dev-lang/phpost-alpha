<?php
/********************************************************************************
* header.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/
/*****************************/
// PREVENIR INCLUSION

if( defined('TS_HEADER') ) return;

// SI NO EXISTE NINGUNA SESSION
if(!isset($_SESSION)) session_start();

// ARMAR REPORTE DE ERRORES

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

ini_set('display_errors', TRUE);

// LIMITE DE EJECUNCION

set_time_limit(300);

// CHECAR LA VARIABLE PAGE

if( !isset($page) ) $page = "";

//DEFINICION DE CONSTANTES

define(TS_ROOT, realpath(dirname(__FILE__)));

define(TS_HEADER, TRUE);

define(TS_CLASS, 'inc/class/');

define(TS_EXTRA, TS_ROOT.'/inc/ext/');

define(TS_FILES, TS_ROOT.'/files/');


// ARMAR LA RUTA PADRE DE TS

set_include_path(get_include_path() . PATH_SEPARATOR . realpath("./"));


// INCLUIR ARCHIVOS CLASS/FUNCTIONS

include "config.inc.php";		// DATOS PARA CONECTAR A LA DB

include TS_CLASS."c.db.php";		// FUNCIONES PARA LA DB

include TS_CLASS."c.core.php";		// NUCLEO

include TS_CLASS."c.user.php";		// CONTROLA LAS SESIONES DE USUARIO

include TS_CLASS."c.monitor.php";		// NOTIFICACIONES DE USUARIO

include TS_CLASS."c.actividad.php";		// ACTIVIDAD DE USUARIO

include TS_CLASS."c.mensajes.php";		// NOTIFICACIONES DE USUARIO

include TS_CLASS."c.smarty.php";	// CONTROLA LAS PLANTILLAS

include TS_EXTRA."QueryString.php";	//

// CONECTAR A LA BASE DE DATOS

$tsdb =& tsDatabase::getInstance();		// CONECTAMOS A LA BASE DE DATOS

// CARGAMOS EL NUCLEO

$tsCore =& tsCore::getInstance();		// CARGAMOS CONFIGURACIONES Y FUNCIONES GENERALES

// Limpia las variables de petición, agrega barras, etc
cleanRequest();

/*+++++++++++ DEFINIMOS EL TEMA A USAR ++++++++++++++*/
$tsTema = $tsCore->settings['tema']['t_path'];
if(empty($tsTema)) $tsTema = 'default';
define(TS_TEMA, $tsTema);
/*+++++++++++ DEFINIMOS EL TEMA A USAR ++++++++++++++*/

//ARMAR EL OBJETO USUARIO

$tsUser =& tsUser::getInstance();		// CARGAMOS AL USUARIO Y SU SESSION

//ARMAR EL OBJETO MONITOR

$tsMonitor = new tsMonitor();		// CARGAMOS LAS NOTIFICACIONES :)

//ARMAR EL OBJETO MONITOR

$tsActividad =& tsActividad::getInstance();		// CARGAMOS LAS NOTIFICACIONES :)

// ARMAR LOS MENSAJES

$tsMP = new tsMensajes(); // CARGAMOS LOS MENSAJES

// ARMAR EL OBJETO Smarty
$smarty =& tsSmarty::getInstance();		// CARGAMOS LAS PLANTILLAS SEGUN EL TEMA

/* SMARTY ADD */
$smarty->assign("tsConfig",$tsCore->settings);
// USER
$smarty->assign("tsUser",$tsUser);
// AVISOS
$smarty->assign("tsAvisos", $tsMonitor->avisos);
// NOTIFICACIONES
$smarty->assign("tsNots",$tsMonitor->notificaciones);
// MENSAJES
$smarty->assign("tsMPs",$tsMP->mensajes);

// KEYWORDS & DESCRIPTION

//$keywords = $tsCore->settings['keywords'];

//$description = $tsCore->settings['description'];

// COMPROBAR SI ESTA ONLINE :D

if($tsCore->settings['offline'] == 1 && $tsUser->is_admod != 1){
	$smarty->assign("tsTitle",$tsCore->settings['titulo'].' -  '.$tsCore->settings['slogan']);
    if(empty($_GET['action'])) 
	   $smarty->display("sections/mantenimiento.tpl");
    else die('Espera un poco...');
	exit();
// BANNEADO
} elseif($tsUser->is_banned) {
    $banned_data = $tsUser->getUserBanned();
    if(!empty($banned_data)){
        // SI NO ES POR AJAX
        if(empty($_GET['action'])){
            $smarty->assign("tsBanned",$banned_data);
            $smarty->display("sections/suspension.tpl");
        } 
        else die('<div class="emptyError">Usuario suspendido</div>');
        //
        exit;
    }
}
?>