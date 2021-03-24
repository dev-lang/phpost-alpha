{
*********************************************************************************
* m.posts_comments.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

<div id="form_div">
		<div class="container940">
			<div class="box_title">
				<div class="box_txt recuperar_pass">Recuperar mi password</div>
				<div class="box_rrs">
					<div class="box_rss"></div> 
				</div>
			</div>
			<div class="box_cuerpo">
				<center>
				<br>
				<b class="size13">Ingresa tu nueva contraseña</b>
				<br>
				<br>
				<form action="/password-recuperar-email.php" method="post" name="pass">
					<input type="hidden" value="f105befccc0028a8042d06e26d7592ea" name="key">
					<input type="hidden" value="4644358" name="id">
					<table width="500" cellspacing="4" cellpadding="2" border="0">
						<tbody><tr>
							<td width="30%" align="right"><strong>Password:</strong></td>
							<td><input type="password" name="password1" size="25"></td>
						</tr>
						<tr>
							<td width="30%" align="right"><strong>Confirmar password:</strong></td>
							<td><input type="password" name="password2" size="25"></td>
						</tr>
					</tbody></table>
					<br>
					<input type="submit" name="send_pass" value="Aceptar">
				</form>
				<br>
				<br>
				
				</center></div>
			</div>
		</div>
        <div style="clear:both"></div>