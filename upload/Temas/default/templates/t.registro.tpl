{
*********************************************************************************
* t.aviso.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
{include file='sections/main_header.tpl'}
            <style>
            /* {literal} */
            .reg-login {
            	margin-top: 15px;
            }
            	.registro {
            		float: left;
            		width: 300px;
            	}
            	.login-panel {
            		float: left;
            		border-left: #CCC 1px solid;
            		padding-left: 25px;
            	}
            	
            	.login-panel label {
            		font-weight: bold;
            		display: block;
            		margin: 5px 0;
            	}
            	
            	.login-panel .mBtn {
            		margin-top: 10px;
            	}
                /*{/literal}*/
            </style>
<div class="post-deleted post-privado clearbeta">
	<div class="content-splash">
		<h3>{if $tsType == 'post'}Este post es privado, s&oacute;lo los usuarios registrados de {$tsConfig.titulo} pueden acceder.{else}Registrate en {$tsConfig.titulo}{/if}</h3>
        {if $tsType == 'post'}Pero no te preocupes, tambi&eacute;n puedes formar parte de nuestra gran familia.		{/if}
				<div class="reg-login">
			<div class="registro">
				<h4>Registrarme!</h4>
<div id="RegistroForm">

	<!-- Paso Uno -->
	<div class="pasoUno">
		<a style="font-size: 10px; font-weight: bold; display: block; margin: 0 0 10px" href="/reenviar-mail-form.php">&iquest;Te registraste y no recibiste el e-mail de confirmaci&oacute;n?</a>
		<div class="form-line">
			<label for="nick">Ingresa tu usuario</label>
			<input type="text" title="Ingrese un nombre de usuario &uacute;nico" autocomplete="off" onkeydown="registro.clear_time(this.name)" onkeyup="registro.set_time(this.name)" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="1" name="nick" id="nick"> <div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label for="password">Contrase&ntilde;a deseada</label>
			<input type="password" title="Ingresa una contrase&ntilde;a segura" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="2" name="password" id="password"> <div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label for="password2">Confirme contrase&ntilde;a</label>
			<input type="password" title="Vuelve a ingresar la contrase&ntilde;a" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="3" name="password2" id="password2"> <div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label for="email">E-mail</label>
			<input type="text" title="Ingresa tu direcci&oacute;n de email" autocomplete="off" onkeydown="registro.clear_time(this.name)" onkeyup="registro.set_time(this.name)" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="4" name="email" id="email"> <div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label>Fecha de Nacimiento</label>
			<select title="Ingrese d&iacute;a de nacimiento" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="5" name="dia" id="dia">
                <option value="">D&iacute;a</option>
            {section name=dias start=1 loop=32}
                <option value="{$smarty.section.dias.index}">{$smarty.section.dias.index}</option>
            {/section}
			</select>
			<select title="Ingrese mes de nacimiento" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="6" name="mes" id="mes">
				<option value="">Mes</option>
            {foreach from=$tsMeces key=mid item=mes}
                <option value="{$mid}">{$mes}</option>
            {/foreach}
			</select>
			<select title="Ingrese a&ntilde;o de nacimiento" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="7" name="anio" id="anio">
				<option value="">A&ntilde;o</option>
            {section name=year start=$tsEndY loop=$tsEndY step=-1 max=$tsMax}
                 <option value="{$smarty.section.year.index}">{$smarty.section.year.index}</option>
            {/section}
            </select> <div class="help"><span><em></em></span></div>
		</div>
		<div class="clearfix"></div>
	</div>

	<!-- Paso Dos -->
	<div class="pasoDos" style="display: none;">

		<div class="form-line">
			<label for="sexo">Sexo</label>
			<input type="radio" title="Selecciona tu g&eacute;nero" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" value="m" name="sexo" tabindex="8" id="sexo_m" class="radio"> <label for="sexo_m" class="list-label">Masculino</label>
			<input type="radio" title="Selecciona tu g&eacute;nero" autocomplete="off" onfocus="registro.focus(this)" onblur="registro.blur(this)" value="f" name="sexo" tabindex="8" id="sexo_f" class="radio"> <label for="sexo_f" class="list-label">Femenino</label>
			<div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label for="pais">Pa&iacute;s</label>
			<select title="Ingrese su pa&iacute;s" autocomplete="off" onfocus="registro.focus(this)" onchange="registro.blur(this)" onblur="registro.blur(this)" tabindex="9" name="pais" id="pais">
				<option value="">Pa&iacute;s</option>
            {foreach from=$tsPaises key=code item=pais}
                <option value="{$code}">{$pais}</option>
            {/foreach}
        </select> <div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label for="estado">Regi&oacute;n</label>
			<select title="Ingrese su estado" autocomplete="off" onfocus="registro.focus(this)" onchange="registro.blur(this)" onblur="registro.blur(this)" tabindex="10" name="estado" id="estado">
				<option value="">Regi&oacute;n</option>
				</select> <div class="help"><span><em></em></span></div>
		</div>

		<div class="form-line">
			<label for="recaptcha_response_field">Ingresa el c&oacute;digo de la imagen:</label>
			<div id="recaptcha_ajax" class=" recaptcha_nothad_incorrect_sol recaptcha_isnot_showing_audio">
				<div id="recaptcha_image" style="width: 300px; height: 57px;"><img width="300" height="57" src="http://www.google.com/recaptcha/api/image?c=03AHJ_Vuuy_SqNrT0Bv1JhILAHyVhZEv62BvNjRxtz21oQ3W-U3rY-tyV2ufO1hfBOXI-XWX-T1LDhHhXFfADGntVGyvohNFm08dV75QDJ4GKsOCGaY3WvmvDwPKuARBBjdhB8YBzyfvgLb-6nTFJ-zpxTZTGreVTS6Q" style="display:block;"></div>
				<span id="recaptcha_challenge_field_holder" style="display: none;"><input type="hidden" value="03AHJ_Vuuy_SqNrT0Bv1JhILAHyVhZEv62BvNjRxtz21oQ3W-U3rY-tyV2ufO1hfBOXI-XWX-T1LDhHhXFfADGntVGyvohNFm08dV75QDJ4GKsOCGaY3WvmvDwPKuARBBjdhB8YBzyfvgLb-6nTFJ-zpxTZTGreVTS6Q" id="recaptcha_challenge_field" name="recaptcha_challenge_field"></span><input type="text" name="recaptcha_response_field" id="recaptcha_response_field" autocomplete="off" tabindex="13" title="Ingrese el c&amp;oacute;digo de la imagen">
			</div> <div class="help recaptcha"><span><em></em></span></div>
		</div>

		<div class="footerReg">
			<div class="form-line">
				<input type="checkbox" title="Acepta los T&eacute;rminos y Condiciones?" onfocus="registro.focus(this)" onblur="registro.blur(this)" tabindex="14" name="terminos" id="terminos" class="checkbox"> <label for="terminos" class="list-label">Acepto los <a target="_blank" href="/terminos-y-condiciones/">T&eacute;rminos de uso</a></label> <div class="help"><span><em></em></span></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
//
$.getScript("{$tsConfig.default}/js/registro.js{literal}", function(){
	//Seteo el pais seleccionado
	//registro.datos['pais']='MX';
	//registro.datos_status['pais']='ok';
	//registro.datos_text['pais']='OK';
	//
	registro.change_paso(1);
	
	//Genero el autocomplete de la ciudad
	/*$('#RegistroForm .pasoDos #ciudad').autocomplete('/registro-geo.php', {
		minChars: 2,
		width: 298
	}).result(function(event, data, formatted){
		registro.datos['ciudad_id'] = (data) ? data[1] : '';
		registro.datos['ciudad_text'] = (data) ? data[0].toLowerCase() : '';
		if(data)
			$('#RegistroForm .pasoDos #terminos').focus();
	});*/
	mydialog.procesando_fin();
});

//Load recaptcha
$.getScript("http://api.recaptcha.net/js/recaptcha_ajax.js", function(){
	Recaptcha.create('6LcXvL0SAAAAAPJkBrro96lnXGZ56TBRExEmVM3L', 'recaptcha_ajax', {
		theme:'custom', lang:'es', tabindex:'13', custom_theme_widget: 'recaptcha_ajax',
		callback: function(){
			$('#recaptcha_response_field').blur(function(){
				registro.blur(this);
			}).focus(function(){
				registro.focus(this);
			}).attr('title', 'Ingrese el c&oacute;digo de la imagen');
		}
	});
});

/*{/literal}*/
</script>
				<div style="display: inline-block;" id="buttons">
					<input type="button" tabindex="8" class="mBtn btnOk" style="display:inline-block;" value="Siguiente &raquo;" onclick="registro.change_paso(2)" id="sig"/>
					<input type="button" tabindex="15" class="mBtn btnOk btnGreen" style="display:none;" value="Terminar" onclick="registro.submit()" id="term"/>
				</div>
			</div>
			<div class="login-panel">
				<h4>...O quizas ya tengas usuario</h4>
								<div class="social-connect">
					<a class="facebook-login" onclick="FB.signin()">Identificarme Facebook</a>
				</div>
								<div style="width:210px;font-size:13px;border: 5px solid rgb(195, 0, 20); background: none repeat scroll 0% 0% rgb(247, 228, 221); color: rgb(195, 0, 20); padding: 8px; margin: 10px 0;">
					<strong>&iexcl;Atenci&oacute;n!</strong>
					<br>Antes de ingresar tus datos asegurate que la URL de esta p&aacute;gina pertenece a <strong>taringa.net</strong>
				</div>
				<div class="login_cuerpo">
					<span class="gif_cargando floatR" id="login_cargando"></span>
					<div id="login_error"></div>
					<form action="javascript:login_ajax('registro-logueo')" id="login-registro-logueo" method="POST">
						<input type="hidden" value="/registro" name="redirect">
						<label>Usuario</label>
						<input type="text" tabindex="20" class="ilogin" id="nickname" name="nick" maxlength="64">
						<label>Contrase&ntilde;a</label>
						<input type="password" tabindex="21" class="ilogin" id="password" name="pass" maxlength="64">
						<input type="submit" tabindex="22" title="Entrar" value="Entrar" class="mBtn btnOk">
						<div style="color: #666; padding:5px;font-weight: normal; display:none" class="floatR">
							<input type="checkbox"> Recordarme?
						</div>
					</form>
					<div class="login_footer">
						<a tabindex="23" href="/password/">&iquest;Olvidaste tu contrase&ntilde;a?</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{include file='sections/main_footer.tpl'}