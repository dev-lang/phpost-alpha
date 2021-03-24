{
*********************************************************************************
* m.admin_groups.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                                <div class="boxy-title">
                                    <h3>Administrar Usuarios</h3>
                                </div>
                                <div id="res" class="boxy-content" style="position:relative">
                                {if $tsAct == ''}
                                {if !$tsMembers.data}
                                <div class="phpostAlfa">No hay usuarios registrados.</div>
                                {else}
                                <table cellpadding="0" cellspacing="0" border="0" class="admin_table" width="100%" align="center">
                                	<thead>
                                        <th>Rango</th>
                                    	<th>Usuario</th>
                                        <th>Email</th>
                                        <th>&Uacute;ltima vez activo</th>
                                        <th>Fecha de registro</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                    	{foreach from=$tsMembers.data item=m}
                                        <tr>
                                            <td><img src="{$tsConfig.default}/images/icons/ran/{$m.r_image}" /></td>
                                        	<td align="left"><a href="{$tsConfig.url}/perfil/{$m.user_name}" class="hovercard" uid="{$m.user_id}" style="color:#{$m.r_color};">{$m.user_name}</a></td>
                                            <td>{$m.user_email}</td>
                                            <td>{$m.user_lastlogin|hace:true}</td>
                                            <td>{$m.user_registro|date_format:"%d/%m/%Y"}</td>
                                            <td>{if $m.user_baneado == 1}<font color="red">Suspendido</font>{elseif $m.user_activo == 0}<font color="purple">Inactivo</font>{else}<font color="green">Activo</font>{/if}</td>
                                            <td class="admin_actions">
                                                <a href="{$tsConfig.url}/admin/users?act=show&uid={$m.user_id}" title="Editar Usuario"><img src="{$tsConfig.default}/images/icons/editar.png" /></a>
                                                <a href="#" onclick="mod.users.action({$m.user_id}, 'aviso', false); return false;"><img src="{$tsConfig.default}/images/icons/warning.png" title="Enviar Alerta" /></a>
                                                <a href="#" onclick="mod.{if $m.user_baneado == 1}reboot({$m.user_id}, 'users', 'unban', false){else}action({$m.user_id}, 'ban', false){/if}; return false;"><img src="{$tsConfig.default}/images/icons/power_{if $m.user_baneado == 1}on{else}off{/if}.png" title="{if $m.user_baneado == 1}Reactivar{else}Suspender{/if} Usuario" /></a>
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                    <tfoot>
                                    	<td colspan="7">P&aacute;ginas: {$tsMembers.pages}</td>
                                    </tfoot>
                                </table>
                                {/if}
                                {elseif $tsAct == 'show'}
                                <div class="admin_header">
                                <h1>Administrar: <strong>{$tsUsername}</strong></h1>
                                <div class="floatR"><strong>Seleccionar:</strong> 
                                    <select onchange="location.href='{$tsConfig.url}/admin/users?act=show&uid={$tsUserID}&t=' + this.value;">
                                        <option value="1"{if $tsType == 1} selected="true"{/if}>Vista general</option>
                                        <option value="2"{if $tsType == 2} selected="true"{/if}>Avisos</option>
                                        <option value="3"{if $tsType == 3} selected="true"{/if}>Denuncias</option>
                                        <option value="4"{if $tsType == 4} selected="true"{/if}>Perfil</option>
                                        <option value="5"{if $tsType == 5} selected="true"{/if}>Preferencias</option>
                                        <option value="6"{if $tsType == 6} selected="true"{/if}>Avatar</option>
                                        <option value="7"{if $tsType == 7} selected="true"{/if}>Rango</option>
                                        <option value="8"{if $tsType == 8} selected="true"{/if}>Firma</option>
                                    </select>
                                </div>
                                <div class="clearBoth"></div>
                                </div>
                                {if $tsSave}<div class="mensajes ok">Tus cambios han sido guardados.</div>{/if}
                                {if $tsError}<div class="mensajes error">{$tsError}</div>{/if}
                                <form action="" method="post">
                                    <fieldset>
                                    {if !$tsType || $tsType == 1}
                                        <legend>Vista general</legend>
                                        <dl>
                                            <dt><label for="user">Nombre de Usuario:</label></dt>
                                            <dd><strong>{$tsUserD.user_name}</strong></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="user">Rango:</label></dt>
                                            <dd><strong style="color:#{$tsUserD.r_color}">{$tsUserD.r_name}</strong></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="registro">Registrado:</label></dt>
                                            <dd><strong>{$tsUserD.user_registro|date_format:"%d/%m/%Y a las %H:%M"}</strong></dd>
                                        </dl>
                                        <dl>
                                            <dt><label>&Uacute;ltima vez activo:</label></dt>
                                            <dd><strong>{$tsUserD.user_lastactive|hace}</strong></dd>
                                        </dl>
                                        <dl>
                                            <dt><label>Posts:</label></dt>
                                            <dd><strong>{$tsUserD.user_posts}</strong></dd>
                                        </dl>
                                        <dl>
                                            <dt><label>Puntos:</label></dt>
                                            <dd><strong>{$tsUserD.user_puntos}</strong></dd>
                                        </dl>
                                        <hr />
                                        <dl>
                                            <dt><label for="email">E-mail:</label></dt>
                                            <dd><input type="text" name="email" id="email" value="{$tsUserD.user_email}" size="" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="pwd">Nueva contrase&ntilde;a:</label><br /><span>Debe tener entre 5 y 35 caracteres.</span></dt>
                                            <dd><input type="password" name="pwd" id="pwd" /></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="cpwd">Confirmar contrase&ntilde;a:</label><br /><span>Necesita confirmar su contrase&ntilde;a s&oacute;lo si la ha cambiado arriba.</span></dt>
                                            <dd><input type="password" name="cpwd" id="cpwd" /></dd>
                                        </dl>
                                    {elseif $tsType == 7}
                                    <legend>Modificar rango de usuario</legend>
                                        <dl>
                                            <dt><label>Rango actual:</label></dt>
                                            <dd><strong style="color:#{$tsUserR.user.r_color}">{$tsUserR.user.r_name}</strong></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="user">Nuevo rango:</label></dt>
                                            <dd><select name="new_rango">
                                            {foreach from=$tsUserR.rangos item=r}
                                            <option value="{$r.rango_id}" style="color:#{$r.r_color}">{$r.r_name}</option>
                                            {/foreach}
                                            </select></dd>
                                        </dl>
                                    {else}
                                    <div class="phpostAlfa">Pendiente</div>
                                    {/if}
                                    <p><input type="submit" name="save" value="Enviar Cambios" class="btn_g"/></p>
                                    </fieldset>
                                </form>
                                {/if}
                                </div>