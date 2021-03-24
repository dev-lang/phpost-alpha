{
*********************************************************************************
* t.agregar_sidebar.tpl	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}				
                        <div class="form-add-post">
                        	<form action="{$tsConfig.url}/agregar.php{if $tsAction == 'editar'}?action=editar&pid={$tsPid}{/if}" method="post" name="newpost" autocomplete="off">
                            	<input type="hidden" value="{$tsDraft.bid}" name="borrador_id"/>
                                <ul class="clearbeta">
                                    <li>
                                    <label>T&iacute;tulo</label>
                                    <span style="display: none;" class="errormsg"></span>
                                    <input type="text" tabindex="1" name="titulo" maxlength="60" size="60" class="text-inp required" value="{$tsDraft.b_title}" style="width:760px"/>
                                    <div id="repost"></div>
                                    </li>
                                    <li>
                                    <a name="post"></a>
                                    <label>Contenido del Post</label>
                                    <span style="display: none;" class="errormsg"></span>
                                    <textarea id="markItUp" name="cuerpo" tabindex="2" style="min-height:400px;" class="required">{$tsDraft.b_body}</textarea>
                                    <div style="margin:10px 0 0;">{include file='modules/m.global_emoticons.tpl'}</div>
                                    </li>
                                    <li>
                                    <label>Tags</label>
                                    <span style="display: none;" class="errormsg"></span>
                                    <input type="text" tabindex="4" name="tags" maxlength="128" class="text-inp required" value="{$tsDraft.b_tags}"/>
                                    Una lista separada por comas, que describa el contenido. Ejemplo: <b>gol, ingleses, Copa Oro, futbol, Chicharito, M&eacute;xico</b>
                                    </li>
                                    <li class="special-left clearbeta">
                                    <label>Categor&iacute;a</label>
                                    <span style="display: none;" class="errormsg"></span>
                                    <select class="agregar required" tabindex="5" size="9" style="width:300px; float:left" size="{$tsConfig.categorias.ncats}" name="categoria">
                                    <option value="" selected="selected" style="color: #000; font-weight: bold; padding: 3px; background:none;">Elegir una categor&iacute;a</option>
                                        {foreach from=$tsConfig.categorias item=c}
                                        <option value="{$c.cid}" {if $tsDraft.b_category == $c.cid}selected="selected"{/if} style="background-image:url({$tsConfig.images}/icons/cat/{$c.c_img})">{$c.c_nombre}</option>
                                        {/foreach}
                                    </select>
                                    </li>
                                    <li class="special-right clearbeta">
                                    <label>Opciones</label>
                                    <div class="option clearbeta">  
                                        <input type="checkbox" tabindex="6" name="privado" id="privado" class="floatL" {if $tsDraft.b_private == 1}checked="checked"{/if} />
                                        <p class="floatL">
                                            <label for="privado">S&oacute;lo usuarios registrados</label>
                                            Tu post ser&aacute; visto s&oacute;lo por los usuarios que tengan cuenta en {$tsConfig.titulo}
                                        </p>
                                    </div>
                                    <div class="option clearbeta">  
                                        <input type="checkbox" tabindex="7" name="sin_comentarios" id="sin_comentarios" class="floatL" {if $tsDraft.b_block_comments == 1}checked="checked"{/if}>
                                        <p class="floatL">
                                            <label for="sin_comentarios">Cerrar Comentarios</label>
                                            Si tu post es pol&eacute;mico ser&iacute;a mejor que cierres los comentarios.
                                        </p>
                                    </div>
                                    {if $tsUser->info.user_rango == 1}
                                    <div class="option clearbeta">  
                                        <input type="checkbox" tabindex="7" name="patrocinado" id="patrocinado" class="floatL" {if $tsDraft.b_sponsored == 1}checked="checked"{/if} >
                                        <p class="floatL">
                                            <label for="patrocinado">Patrocinado</label>
                                            Resalta este post entre los dem&aacute;s.
                                        </p>
                                    </div>
                                    <div class="option clearbeta">  
                                        <input type="checkbox" tabindex="7" name="sticky" id="sticky" class="floatL" {if $tsDraft.b_sticky == 1}checked="checked"{/if} >
                                        <p class="floatL">
                                            <label for="sticky">Sticky!</label>
                                            Si quieres que este post est&eacute; fijo.
                                        </p>
                                    </div>
                                    {/if}
                                    </li>
                                    {if $tsUser->is_admod > 0 && $tsDraft.b_title && $tsDraft.b_user != $tsUser->uid}
                                    <li style="clear:both;">
                                    <label>Raz&oacute;n</label>
                                    <span style="display: none;" class="errormsg"></span>
                                    <input type="text" tabindex="8" name="razon" maxlength="150" size="60" class="text-inp required" value="" style="width:760px"/>
                                    Este no es tu post pero tienes privilegios para editarlo, escribe la raz&oacute;n por la que estas editando este post.
                                    </li>
                                    {/if}
                                </ul>
                                <div class="end-form clearbeta">
                                    <input type="button" tabindex="9" title="Guardar en borradores" value="Guardar en borradores" onclick="save_borrador()" class="mBtn btnOk floatL" id="borrador-save">
                                	<input type="button" tabindex="8" title="Previsualizar" value="Continuar &raquo;" name="preview" class="mBtn btnGreen" style="width: auto; margin-left: 5px;">
                            <div id="borrador-guardado" style="float: right; font-style: italic; margin: 7px 0pt 0pt;"></div>
                                </div>
                            </form>
                        </div>