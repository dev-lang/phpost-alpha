<?php
/********************************************************************************
* config.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

//=========================================================
//	EDITAR ESTOS DATOS
//=========================================================
define(db_host, "localhost"); // SERVIDOR
define(db_name, "mytaringa"); //
define(db_user, "root");
define(db_pass, "");
/*
define(db_host, "dbhost");  // SERVIDOR
define(db_name, "dbname");  // NOMBRE DE LA BASE DE DATOS
define(db_user, "dbuser");  // USUARIO DE LA BASE DE DATOS
define(db_pass, "dbpass");  // CONTRASEÑA
define(db_persist, 0);*/

//=========================================================
//	GENERALES
//=========================================================
define(TSCookieName,'PPCook');
//=========================================================
//	TIME
//=========================================================

//=========================================================
//	OTROS
//=========================================================
define(RC_PUK,"6LcXvL0SAAAAAPJkBrro96lnXGZ56TBRExEmVM3L"); //public key recaptcha aqui
define(RC_PIK,"6LcXvL0SAAAAAEg1zizOxJPTjlD0ZtbbzubF2NjE"); //private key recaptcha aqui
//
define(N_ACCESS,'<title>PHPost - Error</title><body><h1 align="center" style="color:#222; font-size:70px; font-family:Arial;">PHPost <br><br>No se pudo establecer la conexi&oacute;n a la base de datos. Por favor verifica tus datos en el archivo <u>config.inc.php<u></h1></body>');
?>