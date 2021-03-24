<?php
/********************************************************************************
* index.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

/*++++++++ = ++++++++*/

	include "header.php"; // INCLUIR EL HEADER
    if($tsCore->settings['c_allow_portal'] == 1 && $tsUser->is_member == true && $_GET['do'] != 'posts'){
        include("inc/php/portal.php");
    } else include("inc/php/posts.php");

/*++++++++ = ++++++++*/

?>

