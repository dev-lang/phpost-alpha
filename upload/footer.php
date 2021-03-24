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

// PREVENIR INCLUSION
if(!defined('TS_HEADER') ) die("Error al cargar archivo directo.");

// Smarty ADD
$smarty->assign("keywords",$keywords);

$smarty->assign("description",$description);

$smarty->assign("tsPage",$tsPage);

// DISPLAY PAGE
$smarty_next = false;
//
if(!$smarty->template_exists("t.$tsPage.tpl")){
	$smarty->template_dir = TS_ROOT.DIRECTORY_SEPARATOR.'Temas'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'templates';
	if($smarty->template_exists("t.$tsPage.tpl")) $smarty_next = true;
} else $smarty_next = true;
//
if($smarty_next == true) $smarty->display("t.$tsPage.tpl");
else die("0: Lo sentimos, se produjo un error al cargar la plantilla. Contacte al administrador");
//
?>