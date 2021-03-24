{
*********************************************************************************
* m.admin_welcome.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                                <div class="boxy-title">
                                    <h3>Medallas</h3>
                                </div>
                                <div id="res" class="boxy-content">
                                    {if $tsAct == ''}
                                    <div class="phpostAlfa">Pendiente</div>
                                    {elseif $tsAct == 'superman'}
                                    <table cellpadding="0" cellspacing="0" border="0" class="admin_table" width="550" align="center">
                                    	<thead>
                                        	<th>ID</th>
                                            <th>Imagen</th>
                                            <th>T&iacute;tulo</th>
                                            <th>Descripci&oacute;n</th>
                                            <th>Tipo</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </thead>
                                        <tbody>
                                        {foreach from=$tsMedals item=m}
                                        	<tr>
                                            	<td>{$m.medal_id}</td>
                                                <td><img src="{$tsConfig.default}/images/icons/med/{$m.m_image}_16.png" /></td>
                                                <td>{$m.m_title}</td>
                                                <td>{$m.m_description}</td>
                                                <td>{if $m.m_type == 0}Usuario{else}Post{/if}</td>
                                                <td>{$m.m_total}</td>
                                                <td class="admin_actions">
                                                <a href="?act=editar&mid={$m.medal_id}" title="Editar Medalla"><img src="{$tsConfig.default}/images/icons/editar.png" /></a>
                                                <a href="?act=borrar&mid={$m.medal_id}" title="Borrar Medalla"><img src="{$tsConfig.default}/images/icons/close.png" /></a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                        <tfoot>
                                        	<td colspan="7">&nbsp;</td>
                                        </tfoot>
                                    </table><hr />
                                    <input type="button"  onclick="location.href = '{$tsConfig.url}/admin/medals?act=nueva'" value="Agregar nueva medalla" class="mBtn btnOk" style="margin-left:280px;"/>
                                    {elseif $tsAct == 'nueva'}
                                    <script type="text/javascript">
    									// {literal}
    									$(function(){
    										$('#med_img').change(function(){
    											var cssi = $("#med_img option:selected").css('background');
    											$('#c_icon').css({"background" : cssi});
    										});
    										//
    									});
    									//{/literal}
                                    </script>
                                        <form action="" method="post" autocomplete="off">
                                        <fieldset>
                                            <legend>Nueva medalla</legend>
                                            <dl>
                                                <dt><label for="med_name">T&iacute;tulo de la medalla:</label></dt>
                                                <dd><input type="text" id="med_name" name="med_title" value="{$tsMed.m_title}" /></dd>
                                            </dl>
                                            <dl>
                                                <dt><label for="ai_desc">Descripci&oacute;n:</label><br /><span>Describe porque motivo el usuario gan&aacute;o esta medalla.</span></dt>
                                                <dd><textarea name="med_desc" id="ai_desc" rows="3" cols="50">{$tsMed.m_description}</textarea></dd>
                                            </dl>
                                            <dl>
                                                <dt><label for="ai_type">Tipo de medalla:</label><br /><span>Para un usuario, para el post de un usuario, etc.</span></dt>
                                                <dd><select name="med_type" id="ai_type" style="width:260px">
                                                    <option value="0"{if $tsMed.m_type == 0} selected="true"{/if}>Usuario</option>
                                                    <option value="1"{if $tsMed.m_type == 1} selected="true"{/if}>Post de un usuario</option>
                                                </select></dd>
                                            </dl>
                                            <dl>
                                                <dt><label for="cat_img">Icono de la categor&iacute;a:</label></dt>
                                                <dd>
                                                    <img src="{$tsConfig.images}/space.gif" style="background:url({$tsConfig.default}/images/icons/med/{if $tsMed.m_image}{$tsMed.m_image}{else}{$tsIcons.0}{/if}_16.png) no-repeat left center;" width="16" height="16" id="c_icon"/>
                                                    <select name="med_img" id="med_img" style="width:164px">
                                                    {foreach from=$tsIcons key=i item=img}
                                                    	<option value="{$img}" style="padding:2px 20px 0; background:#FFF url({$tsConfig.url}/Temas/default/images/icons/med/{$img}_16.png) no-repeat left center;" {if $tsCat.c_img == $img}selected="selected"{/if}>{$img}</option>
                                                    {/foreach}
                                                    </select>
                                                </dd>
                                            </dl>
                                            <hr />
                                            <p><input type="submit" name="save" value="Crear medalla" class="btn_g"/></p>
                                        </fieldset>
                                        </form>
                                    {/if}
                                </div>
                                    