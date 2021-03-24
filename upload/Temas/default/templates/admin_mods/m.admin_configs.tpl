{
*********************************************************************************
* m.admin_configs.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                                <div class="boxy-title">
                                    <h3>Caracter&iacute;sticas y Opciones</h3>
                                </div>
                                <div id="res" class="boxy-content">
                                {if $tsSave}<div style="display: block;" class="mensajes ok">Configuraciones guardadas</div>{/if}
                                	<form action="" method="post" autocomplete="off">
                                    <fieldset>
                                        <legend>Configuraci&oacute;n del Sitio</legend>
                                        <dl>
                                            <dt><label for="ai_titulo">Nombre del Sitio:</label></dt>
                                            <dd><input type="text" id="ai_titulo" name="titulo" maxlength="24" value="{$tsConfig.titulo}" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_slogan">Descripci&oacute;n del Sitio:</label></dt>
                                            <dd><input type="text" id="ai_slogan" name="slogan" maxlength="32" value="{$tsConfig.slogan}"/></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_url">Direcci&oacute;n del sitio:</label></dt>
                                            <dd><input type="text" id="ai_url" name="url" maxlength="32" value="{$tsConfig.url}" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_offline">Modo mantenimiento:</label><br /><span>Esto har&aacute; al Sitio inaccesible a los usuarios. Si quiere, tambi&eacute;n puede introducir un breve mensaje (255 caracteres) para mostrar.</span></dt>
                                            <dd>
                                                <label><input name="offline" type="radio" id="ai_offline" value="1" {if $tsConfig.offline == 1}checked="checked"{/if} class="radio"/> S&iacute;</label>
                                                <label><input name="offline" type="radio" id="ai_offline" value="0" {if $tsConfig.offline != 1}checked="checked"{/if} class="radio"/> No</label>
                                                <br />
                                                <input type="text" name="offline_message" id="ai_offline" value="{$tsConfig.offline_message}" />
                                            </dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_offline">Chatango ID:</label><br /><span>Por defecto puedes agregar un chat de <a href="http://chatango.com">Chatango</a> para tu web, solo crea tu grupo he ingresa el nombre.</span></dt>
                                            <dd><input type="text" id="ai_chat" name="chat" maxlength="20" value="{$tsConfig.chat_id}" /> </dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="ai_edad">Edad requerida:</label> <br /><span>A partir de que edad los usuarios pueden registrarse.</span></dt>
                                            <dd><input type="text" id="ai_edad" name="edad" style="width:10%" maxlength="2" value="{$tsConfig.c_allow_edad}" /> a&ntilde;os.</dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_active">Usuario online:</label><br /><span>Tiempo que debe trascurrir para considerar que un usuario est&aacute; en linea.</span></dt>
                                            <dd><input type="text" id="ai_active" name="active" style="width:10%" maxlength="2" value="{$tsConfig.c_last_active}" /> min.</dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_flood">Anti flood:</label><br /><span>Tiempo que debe trascurrir entre cada publicaci&oacute;n de un usuario.</span></dt>
                                            <dd><input type="text" id="ai_flood" name="flood" style="width:10%" maxlength="2" value="{$tsConfig.c_anti_flood}" /> seg.</dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="ai_reg_active">Registro abierto:</label><br /><span>Permitir el registro de nuevos usuarios</span></dt>
                                            <dd>
                                                <label><input name="reg_active" type="radio" id="ai_reg_active" value="1" {if $tsConfig.c_reg_active == 1}checked="checked"{/if} class="radio"/>S&iacute;</label>
                                                <label><input name="reg_active" type="radio" id="ai_reg_active" value="0" {if $tsConfig.c_reg_active != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_reg_activate">Activar usuarios:</label><br /><span>Activar autom&aacute;ticamente la cuenta de usuario.</span></dt>
                                            <dd>
                                                <label><input name="reg_activate" type="radio" id="ai_reg_activate" value="1" {if $tsConfig.c_reg_activate == 1}checked="checked"{/if} class="radio"/>S&iacute;</label>
                                                <label><input name="reg_activate" type="radio" id="ai_reg_activate" value="0" {if $tsConfig.c_reg_activate != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_firma">Firma de usuario:</label><br /><span>Las firmas de los usuarios son visibles en los post.</span></dt>
                                            <dd>
                                                <label><input name="firma" type="radio" id="ai_firma" value="1" {if $tsConfig.c_allow_firma == 1}checked="checked"{/if} class="radio"/>S&iacute;</label>
                                                <label><input name="firma" type="radio" id="ai_firma" value="0" {if $tsConfig.c_allow_firma != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="ai_upload">Carga externa:</label><br /><span>Si cuentas con un servidor de pago o la librer&iacute;a CURL puedes subir im&aacute;genes remotamente a imageshack.us</span></dt>
                                            <dd>
                                                <label><input name="upload" type="radio" id="ai_upload" value="1" {if $tsConfig.c_allow_upload == 1}checked="checked"{/if} class="radio"/>S&iacute;</label>
                                                <label><input name="upload" type="radio" id="ai_upload" value="0" {if $tsConfig.c_allow_upload != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_portal">Activar portal:</label><br /><span>Los usuarios podr&aacute;n tener un inicio perzonalizado.</span></dt>
                                            <dd>
                                                <label><input name="portal" type="radio" id="ai_portal" value="1" {if $tsConfig.c_allow_portal == 1}checked="checked"{/if} class="radio"/>S&iacute;</label>
                                                <label><input name="portal" type="radio" id="ai_portal" value="0" {if $tsConfig.c_allow_portal != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="ai_live">Notificaciones Live:</label><br /><span>Los usuarios podr&aacute;n ver en tiempo real sus notificaciones. (Esta opci&oacute;n puede consumir un poco m&aacute;s de recursos.)</span></dt>
                                            <dd>
                                                <label><input name="live" type="radio" id="ai_live" value="1" {if $tsConfig.c_allow_live == 1}checked="checked"{/if} class="radio"/>S&iacute;</label>
                                                <label><input name="live" type="radio" id="ai_live" value="0" {if $tsConfig.c_allow_live != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_max_nots">M&aacute;ximo de notificaciones:</label><br /><span>Cuantas notificaciones puede recibir un usuario.</span></dt>
                                            <dd><input type="text" id="ai_max_nots" name="max_nots" style="width:10%" maxlength="3" value="{$tsConfig.c_max_nots}" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_max_acts">M&aacute;ximo de actividades:</label><br /><span>Cuantas actividades puede registrar un usuario.</span></dt>
                                            <dd><input type="text" id="ai_max_acts" name="max_acts" style="width:10%" maxlength="3" value="{$tsConfig.c_max_acts}" /></dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="ai_max_post">Posts por p&aacute;gina:</label><br /><span>N&uacute;mero m&aacute;ximo de posts a mostrar en cada p&aacute;gina de la portada.</span></dt>
                                            <dd><input type="text" id="ai_max_post" name="max_posts" style="width:10%" maxlength="3" value="{$tsConfig.c_max_posts}" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_max_com">Comentarios por post:</label><br /><span>N&uacute;mero m&aacute;ximo de comentarios por p&aacute;gina en los post.</span></dt>
                                            <dd><input type="text" id="ai_max_com" name="max_com" style="width:10%" maxlength="3" value="{$tsConfig.c_max_com}" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="ai_sum_p">Los votos suman puntos:</label><br /><span>Cada voto positivo en un comentario es un punto m&aacute;s para el usuario. <strong>Nota:</strong> Los votos negativos no restan puntos</span></dt>
                                            <dd>
                                                <label><input name="sump" type="radio" id="ai_sum_p" value="1" {if $tsConfig.c_allow_sump == 1}checked="checked"{/if} class="radio"/>Si</label>
                                                <label><input name="sump" type="radio" id="ai_sum_p" value="0" {if $tsConfig.c_allow_sump != 1}checked="checked"{/if} class="radio"/>No</label>
                                            </dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="ai_nfu">Cambio de rango:</label><br /><span>Un usuario sube de rango cuando obtiene los puntos m&iacute;nimos en:</span></dt>
                                            <dd>
                                                <label><input name="newr" type="radio" id="ai_nfu" value="1" {if $tsConfig.c_newr_type == 1}checked="checked"{/if} class="radio"/>Todos sus post</label>
                                                <label><input name="newr" type="radio" id="ai_nfu" value="0" {if $tsConfig.c_newr_type != 1}checked="checked"{/if} class="radio"/>Solo en un post</label>
                                            </dd>
                                        </dl>
                                        <p><input type="submit" name="save" value="Guardar Cambios" class="btn_g"/></p>
                                    </fieldset>
                                    </form>
                                </div>