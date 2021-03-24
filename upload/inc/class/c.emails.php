<?php
/********************************************************************************
* c.emails.php 		                                                            *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

/*

	CLASE CON LOS ATRIBUTOS Y METODOS PARA MANEJAR EL ENVIO DE EMAILS
	
	METODOS DE LA CLASE:
	
	tsEmail()
	setEmail()
	sendEmail()
	setEmailHeaders()
	setEmailBody()
	
*/
class tsEmail {
	
	var $email_info = array();		// REFERENCIA PARA ENVIAR UN EMAIL
	var $emailSubject;
	var $emailHeaders;
	var $emailBody;
	var $emailTo;
	var $is_error;		// SI OCURRE UN ERROR ESTA VARIABLE CONTENDRA EL NUMERO DE ERROR

	/*
		$tsEmailRef : tipo de email
		$tsEmailData: datos del email
	*/
	function tsEmail($tsEmailData,$tsEmailRef){
		$this->email_info = array(
			'ref' => $tsEmailRef,
			'data' => $tsEmailData
			);
	}
	/*
		setEmailInfo()
	*/
	function setEmail(){
		$this->emailSubject = $this->setEmailSubject();
		$this->emailHeaders = $this->setEmailHeaders();
		$this->emailBody = $this->setEmailBody();
		$this->emailTo = $this->email_info['user_email'];
	}
	/*
		sendEmail()
	*/
	function sendEmail(){
		if(mail($this->emailTo,$this->emailSubject,$this->emailBody,$this->emailHeaders)) return true;
		else return false;
	}
	/*
		setEmailSubject()
	*/
	function setEmailSubject(){
		switch($this->email_info['ref']) {
			case 'signup' :
				$subject = "Por favor completa tu registro.";
			break;
		}
	
		// ENCODE SUBJECT FOR UTF8
		return "=?UTF-8?B?".base64_encode($subject)."?=";
	}
	/*
		setEmailHeaders()
	*/
	function setEmailHeaders(){
		global $tsCore;
		// SET HEADERS
		$sender = $tsCore->settings['titulo']." <no-reply@".$tsCore->settings['domino'].">";
		//
		$headers = "MIME-Version: 1.0"."\n";
		$headers .= "Content-type: text/html; charset=utf-8"."\n";
		$headers .= "Content-Transfer-Encoding: 8bit"."\n";
		$headers .= "From: $sender"."\n";
		$headers .= "Return-Path: $sender"."\n";
		$headers .= "Reply-To: $sender\n";
		//
		return $headers;
	}
	/*
		setEmailBody()
	*/
	function setEmailBody(){
		switch($this->email_info['ref']) {
			case 'signup' :
				return $this->setRegisterEmail();
			break;
		}
	}
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
							// EMAILS PARA EL REGISTRO DE USUARIOS \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function setRegisterEmail(){
		global $tsCore;
    return '<div style="background:#0f7dc1;padding:10px;font-family:Arial, Helvetica,sans-serif;color:#000">
				<h1 style="color:#FFFFFF; font-weight:bold; font-size:30px;">'.$tsCore->settings['titulo'].'</h1>
				<div style="background:#FFF;padding:10px;font-size:14px">
					<h2 style="font-family:Arial, Helvetica,sans-serif;color:#000;font-size:22px">Hola '.$this->email_info['data']['user_email'].'</h2>
					<p style="font-family:Arial, Helvetica,sans-serif;color:#000">¡Te damos la bienvenida a '.$tsCore->settings['titulo'].'!</p>
					<p>Para finalizar con el proceso por favor confirma tu dirección de email haciendo click aquí: <a target="_blank" href="http://'.$tsCore->settings['dominio'].'/registro-activar.php?key='.md5($this->email_info['data']['user_registro']).'&uid='.$this->email_info['data']['user_id'].'">http://'.$tsCore->settings['dominio'].'/registro-activar.php?key='.md5($this->email_info['data']['user_registro']).'&uid='.$this->email_info['data']['user_id'].'</a>
					</p>
					<p>Antes de empezar a realizar comentarios e interactuar en la comunidad te recomendamos que visites el protocolo de el sitio. (link a <a target="_blank" href="http://'.$tsCore->settings['dominio'].'/protocolo/">http://'.$tsCore->settings['dominio'].'/protocolo/</a>)</p>
					<p>Esperamos que disfrutes enormemente tu visita.</p>
					<p>¡Muchas gracias!</p>
					<p>Staff de '.$tsCore->settings['titulo'].'.</p>		
					<div style="border-top:#CCC solid 1px;padding:10px 0">
						<span style="color:#666;font-size:11px">
							<center>'.$tsCore->settings['titulo'].' &copy; 2010 - Powered by <a href="http://tscript.cubebox.mx">T!Script</a></center>
						</span> 
					</div>
				</div>
			</div>';
	}
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
}
?>
