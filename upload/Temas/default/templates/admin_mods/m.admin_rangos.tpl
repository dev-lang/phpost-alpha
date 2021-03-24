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
								<script type="text/javascript">
								// {literal}
									$(function(){
										$('#cat_img').change(function(){
											var cssi = $("#cat_img option:selected").css('background');
											$('#c_icon').css({"background" : cssi});
										});
									});
								// {/literal}
								</script>
                                <div class="boxy-title">
                                    <h3>Administrar Rangos de Usuarios</h3>
                                </div>
                                <div id="res" class="boxy-content" style="position:relative">
                                {if $tsSave}<div class="mensajes ok">Tus cambios han sido guardados.</div>{/if}
                                {if $tsAct == ''}
                                <div style="width:350px; margin:0 auto 1em">
                                <h3 style="margin:0">Rangos Especiales</h3><hr style="margin:4px 0" />
                                <table cellpadding="0" cellspacing="0" border="0" class="admin_table" width="400" align="center">
                                	<thead>
                                    	<th>Rango</th>
                                        <th>Usuarios</th>
                                        <th>Puntos para dar</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                    {foreach from=$tsRangos.regular item=r}
                                    	<tr>
                                        	<td><a href="?act=list&rid={$r.id}&t=r" style="color:#{$r.color}">{$r.name}</a></td>
                                            <td>{$r.num_members}</td>
                                            <td>{$r.user_puntos}</td>
                                            <td><img src="{$tsConfig.url}/Temas/default/images/icons/ran/{$r.imagen}" /></td>
                                            <td class="admin_actions">
                                            <a href="?act=editar&rid={$r.id}&t=s" title="Editar Rango"><img src="{$tsConfig.url}/Temas/default/images/icons/editar.png" /></a>
                                            {if $r.id > 3}<a href="?act=borrar&rid={$r.id}" title="Borrar Rango"><img src="{$tsConfig.url}/Temas/default/images/icons/close.png" /></a>{/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                    <tfoot>
                                    	<td colspan="5" style="text-align:right"><a href="?act=nuevo&t=s">Agregar nuevo rango &raquo;</a></td>
                                    </tfoot>
                                </table>
                                </div>
                                <div style="width:550px; margin:0 auto">
                                <h3 style="margin:0">Rangos basados en el conteo de puntos y posts</h3><hr style="margin:4px 0" />
                                <table cellpadding="0" cellspacing="0" border="0" class="admin_table" width="550" align="center">
                                	<thead>
                                    	<th width="150">Rango</th>
                                        <th>Usuarios</th>
                                        <th>Puntos requeridos</th>
                                        <th>Posts requeridos</th>
                                        <th>Puntos para dar</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                    {foreach from=$tsRangos.post item=r}
                                    	<tr>
                                        	<td><a href="?act=list&rid={$r.id}&t=p" style="color:#{$r.color}">{$r.name}</a></td>
                                            <td>{$r.num_members}</td>
                                            <td>{$r.min_puntos}</td>
                                            <td>{$r.min_posts}</td>
                                            <td>{$r.user_puntos}</td>
                                            <td><img src="{$tsConfig.url}/Temas/default/images/icons/ran/{$r.imagen}" /></td>
                                            <td class="admin_actions">
                                            <a href="?act=editar&rid={$r.id}&t=p" title="Editar Rango"><img src="{$tsConfig.url}/Temas/default/images/icons/editar.png" /></a>
                                            {if $r.id > 3}<a href="?act=borrar&rid={$r.id}" title="Borrar Rango"><img src="{$tsConfig.url}/Temas/default/images/icons/close.png" /></a>{/if}
                                            
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                    <tfoot>
                                    	<td colspan="7" style="text-align:right"><a href="?act=nuevo">Agregar nuevo rango &raquo;</a></td>
                                    </tfoot>
                                </table>
                                </div>
                                {elseif $tsAct == 'list'}
                                {if !$tsMembers.data}
                                <div class="mensajes error">Aun no hay usuarios en este rango.</div>
                                {else}
                                <table cellpadding="0" cellspacing="0" border="0" class="admin_table" width="550" align="center">
                                	<thead>
                                    	<th>Usuario</th>
                                        <th>Email</th>
                                        <th>&Uacute;ltima vez activo</th>
                                        <th>Fecha de registro</th>
                                        <th>Posts</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                    	{foreach from=$tsMembers.data item=m}
                                        <tr>
                                        	<td align="left"><a href="{$tsConfig.url}/perfil/{$m.user_name}" class="hovercard" uid="{$m.user_id}" style="color:#{$m.r_color};">{$m.user_name}</a></td>
                                            <td>{$m.user_email}</td>
                                            <td>{$m.user_lastlogin|hace:true}</td>
                                            <td>{$m.user_registro|date_format:"%d/%m/%Y"}</td>
                                            <td>{$m.user_posts}</td>
                                            <td class="admin_actions"><a href="{$tsConfig.url}/admin/users?act=show&uid={$m.user_id}&t=7"><img src="{$tsConfig.url}/Temas/default/images/icons/editar.png" title="Editar rango" /></a></td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                    <tfoot>
                                    	<td colspan="6">P&aacute;ginas: {$tsMembers.pages}</td>
                                    </tfoot>
                                </table>
                                {/if}
                                {elseif $tsAct == 'nuevo' || $tsAct == 'editar'}
                                <script type="text/javascript" src="{$tsConfig.js}/jquery.color.js"></script>
                                {literal}
                                <style>
								#colores {width:200px; position:absolute; right:50px; padding:15px 8px 10px 10px; border:1px solid #ccc; background-color:#fafafa;}
								#cerrar {position:absolute; right:5px; top:3px; z-index:2}
								#colores .title {position:absolute; left:10px; top:0px; z-index:2; font-weight:bold}
								#colores span {display:block; float:left; cursor:pointer; border:1px solid #FFF; border-width:1px 1px 0 0}
                                /* ADMIN NEW LABEL */
                                fieldset tr.newLabel td{text-align:left;}
                                fieldset tr.newLabel label{
                                    float:none;
                                    width:80px;
                                    padding:0;
                                    text-align:center;
                                    cursor:pointer;
                                }
                                tr.newLabel label.yes:hover {
                                    background-color:#86F786;
                                }
                                tr.newLabel label.no:hover {
                                    background-color:#EFB0B2;
                                }
								</style>
                                {/literal}
                                <form action="" method="post">
                                <fieldset>
                                    <div id="colores"><span class="title">Colores</span><a href="#" id="cerrar"><img src="{$tsConfig.images}/borrar.png" /></a></div>
                                    <legend>Nuevo Rango</legend>
                                    <dl>
                                        <dt><label for="rName">T&iacute;tulo:</label></dt>
                                        <dd><input type="text" id="rName" name="rName" value="{$tsRango.r_name}" style="width:30%"/></dd>
                                    </dl>
                                	<dl>
                                        <dt><label for="rColor">Color:</label><br /><span>Color (<a href="http://es.wikipedia.org/wiki/Colores_HTML" target="_blank">hexadecimal</a>) del rango.</span></dt>
                                        <dd><input type="text" id="rColor" name="rColor" value="{$tsRango.r_color}" style="color:#{$tsRango.r_color}; font-weight:bold;width:30%"/></dd>
                                    </dl>
                                    <div id="superman"{if $tsType == 's'} style="display:none"{/if}>
                                        <dl>
                                            <dt><label for="minPuntos">Puntos requeridos:</label><br /><span>Puntos requeridos para alcanzar este rango.</span></dt>
                                            <dd><input type="text" id="minPuntos" name="minPuntos" value="{$tsRango.r_min_points}" style="width:30%"/></dd>
                                        </dl>
                                        <dl>
                                            <dt><label for="minPosts">Post requeridos:</label><br /><span>Escribir <strong>0</strong> para que el cambio solo sea por puntos.</span></dt>
                                            <dd><input type="text" id="minPosts" name="minPosts" value="{$tsRango.r_min_posts}" style="width:30%"/></dd>
                                        </dl>
                                    </div>
                                    <dl>
                                        <dt><label for="uPuntos">Puntos por d&iacute;a:</label><br /><span>Puntos que puede otorgar este rango a otros usuarios.</span></dt>
                                        <dd><input type="text" id="uPuntos" name="userPuntos" value="{$tsRango.r_user_points}" style="width:30%"/></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="rSpecial">Rango especial:</label><br /><span>Este rango s&oacute;lo podr&aacute; ser asignado por el administrador.</span></dt>
                                        <dd>
                                            <label onclick="$('#superman').hide();"><input name="rSpecial" type="radio" id="rSpecial" value="1" {if $tsType == 's'}checked="checked"{/if} class="radio"/>Si</label>
                                            <label onclick="$('#superman').show();"><input name="rSpecial" type="radio" id="rSpecial" value="0" {if $tsType != 's'}checked="checked"{/if} class="radio"/>No</label>
                                        </dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="cat_img">Icono del rango:</label></dt>
                                        <dd>
                                            <img src="{$tsConfig.images}/space.gif" style="background:url({$tsConfig.url}/Temas/default/images/icons/ran/{if $tsRango.r_image}{$tsRango.r_image}{else}{$tsIcons.0}{/if}) no-repeat left center;" width="16" height="16" id="c_icon"/>
                                            <select name="r_img" id="cat_img" style="width:164px">
                                            {foreach from=$tsIcons key=i item=img}
                                                <option value="{$img}" style="padding:2px 20px 0; background:#FFF url({$tsConfig.url}/Temas/default/images/icons/ran/{$img}) no-repeat left center;" {if $tsRango.r_image == $img}selected="selected"{/if}>{$img}</option>
                                            {/foreach}
                                            </select>   
                                        </dd>
                                    </dl>
                                    <hr /> 
                                    <input type="hidden" name="sp" value="{if $tsType == 's'}1{else}0{/if}" />
                                    <p><input type="submit" name="save" value="Guardar Cambios" class="btn_g"/></p>
                                </fieldset>
                                </form>
                                {elseif $tsAct == 'borrar'}
                                <form action="" method="post" id="admin_form">
                                	<div class="mensajes error">Si borras este rango todos los usuarios que esten en el, seran asignados al rango mas bajo. &iquest;Realmente deceas borrar este rango?</div>
                                    <label>&nbsp;</label> <input type="submit" name="save" value="SI, Continuar &raquo;" class="mBtn btnOk">
                                </form>
                                {/if}
                                </div>