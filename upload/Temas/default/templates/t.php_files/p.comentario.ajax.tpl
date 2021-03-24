{
*********************************************************************************
* p.comentario.preview.tpl                                                      *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
                        	{if $tsComments.num > 0}
                        	{foreach from=$tsComments.data item=c}
                        	<div id="div_cmnt_{$c.cid}" class="{if $tsPost.autor == $c.c_user}especial1{elseif $c.c_user == $tsUser->uid}especial3{/if}">
                            	<span id="citar_comm_{$c.cid}" style="display:none">{$c.c_body}</span>
                                <div class="comentario-post clearbeta">
                                	<div class="avatar-box" style="z-index: 99;">
                                    	<a href="{$tsConfig.url}/perfil/{$c.user_name}">
											<img width="48" height="48" title="Avatar de {$c.user_name} en {$tsConfig.titulo}" src="{$tsConfig.url}/files/avatar/{$c.c_user}_50.jpg" class="avatar-48 lazy" style="position: relative; z-index: 1; display: inline;">
										</a>
                                        {if $tsUser->is_member && $tsUser->info.user_id != $c.c_user}
                                        <ul style="display: none;">
                                            <li class="enviar-mensaje"><a href="#" onclick="mensaje.nuevo('{$c.user_name}','','',''); return false">Enviar Mensaje <span></span></a></li>
                                            <li style="display: none" class="bloquear desbloquear_1"><a href="javascript:bloquear('1', false, 'comentarios')">Desbloquear<span></span></a></li>
                                            <li class="bloquear bloquear_1"><a href="javascript:bloquear('1', true, 'comentarios')">Bloquear<span></span></a></li>
										</ul>
                                        {/if}
                                    </div>
                                    <div class="comment-box">
                                    	<div class="dialog-c"></div>
                                        <div class="comment-info clearbeta">
                                        	<div class="floatL">
                                            	<a href="{$tsConfig.url}/perfil/{$c.user_name}" class="nick">{$c.user_name}</a> dijo
                                                <span>{$c.c_date|hace}</span> :
                                            </div>
                                            {if $tsUser->is_member}
                                            <div class="floatR answerOptions">
                                            	<ul id="ul_cmt_{$c.cid}">
	                                            	{*if $tsUser->info.user_rango || $tsUser->info.user_rango_post != 3*}
                                                    <li class="numbersvotes" {if $c.c_votos == 0}style="display: none"{/if}>
                            							<div class="overview">
                            								<span class="{if $c.c_votos >= 0}positivo{else}negativo{/if}" id="votos_total_{$c.cid}">{if $c.c_votos != 0}{if $c.c_votos >= 0}+{/if}{$c.c_votos}{/if}</span>
                            							</div>
                            						</li>
                                                    {if $tsUser->uid != $c.c_user && $c.votado == 0}
                                                    <li class="icon-thumb-up">
                                                        <a onclick="comentario.votar({$c.cid},1)">
                                                            <span class="voto-p-comentario"></span>
                                                        </a>
                                                    </li>
                                                    <li class="icon-thumb-down">
                                                        <a onclick="comentario.votar({$c.cid},-1)">
                                                            <span class="voto-n-comentario"></span>
                                                        </a>
                                                    </li>
                                                    {/if}
                                                    {*/if*}
	                                                {if $tsUser->is_member}
                                                	<li class="answerCitar">
                                                    	<a onclick="citar_comment({$c.cid}, '{$c.user_name}')" title="Citar">
                                                            <span class="citar-comentario"></span>
                                                        </a>
                                                    </li>
                                                    {if $c.c_user == $tsUser->uid || $tsUser->is_admod > 0}
                                                	<li>
                                                    	<a onclick="comentario.editar({$c.cid}, 'show')" title="Editar comentario">
                                                            <span class="{if $c.c_user == $tsUser->uid}editar{else}moderar{/if}-comentario"></span>
                                                        </a>
                                                    </li>
                                                    {/if}
                                                    {if $tsUser->uid == $tsPost.autor || $c.c_user == $tsUser->uid || $tsUser->is_admod > 0}
                                                    <li class="iconDelete">
                                                    	<a onclick="borrar_com({$c.cid}, {$c.c_user})" title="Borrar">
															<span class="borrar-comentario"></span>
														</a>
                                                    </li>
                                                    {/if}
                                                    {/if}
                                                </ul>
                                            </div>
                                            {/if}
                                        </div>
                                        <div id="comment-body-{$c.cid}" class="comment-content">
                                        	{if $c.c_votos <= -3}<div>Escondido por un puntaje bajo. <a href="#" onclick="$('#hdn_{$c.cid}').show(); $(this).parent().hide(); return false;">Click para verlo</a>.</div>
                                            <div id="hdn_{$c.cid}" style="display:none">{/if}
                                            {$c.c_html}
                                            {if $c.c_votos <= -3}</div>{/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {/foreach}
                            {else}
                            	<div id="no-comments">Este post no tiene comentarios, S&eacute; el primero!</div>
                            {/if}
                            <!---->
                            <div id="nuevos"></div>
                            {literal}
                            <script type="text/javascript">
                            $(function(){
                                	var zIndexNumber = 99;
                                	$('div.avatar-box').each(function(){
                                		$(this).css('zIndex', zIndexNumber);
                                		zIndexNumber -= 1;
                                	});
                            });
                            </script>
                            {/literal}