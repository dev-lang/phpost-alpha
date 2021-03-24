{
*********************************************************************************
* m.posts_metadata.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                	<div class="post-metadata floatL">
                    	<div style="padding: 12px">
                        	<div style="display:none" class="mensajes"></div>
                            {if $tsUser->is_member == 1 && $tsPost.post_user != $tsUser->uid && $tsUser->info.user_puntosxdar >= 1}
                            <div class="dar-puntos">
                                <span>Dar Puntos:</span>
                                {section name=puntos start=1 loop=$tsUser->info.user_puntosxdar+1 max=10}
                                <a href="#" onclick="votar_post({$smarty.section.puntos.index}); return false;">{$smarty.section.puntos.index}</a> {if $smarty.section.puntos.index < 10}-{/if}
                                {/section}
                                 (de {$tsUser->info.user_puntosxdar} Disponibles)
                            </div>
                            <hr class="divider"/>
                            {/if}
                            <div class="post-acciones">
                            	<ul>
                                    {if !$tsUser->is_member}
                                    <li><a class="btn_g follow_user_post" href="#" onclick="registro_load_form(); return false"><span class="icons follow_post follow">Seguir Post</span></a></li>
                                    {elseif $tsPost.post_user != $tsUser->uid}
                                    <li{if !$tsPost.follow} style="display: none;"{/if}>
                                    <a class="btn_g unfollow_post" onclick="notifica.unfollow('post', {$tsPost.post_id}, notifica.inPostHandle, $(this).children('span'))"><span class="icons follow_post unfollow">Dejar de seguir</span></a>
                                    </li>
                                    <li{if $tsPost.follow > 0} style="display: none;"{/if}>
                                    <a class="btn_g follow_post" onclick="notifica.follow('post', {$tsPost.post_id}, notifica.inPostHandle, $(this).children('span'))"><span class="icons follow_post follow">Seguir Post</span></a>
                                    </li>
                                    {/if}
                                    <li><a href="#" onclick="{if !$tsUser->is_member}registro_load_form(){else}add_favoritos(){/if}; return false" class="btn_g"><span class="icons agregar_favoritos">Agregar a Favoritos</span></a></li>
                                    {if $tsUser->is_member}<li><a href="#" onclick="denuncia.nueva('post',{$tsPost.post_id}, '{$tsPost.post_title}', '{$tsPost.user_name}'); return false;" class="btn_g"><span class="icons denunciar_post">Denunciar</span></a></li>{/if}
                                </ul>
                            </div>
                            <ul class="post-estadisticas">
                            	<li><span class="icons favoritos_post">{$tsPost.post_favoritos}</span><br />Favoritos</li>
                                <li><span class="icons visitas_post">{$tsPost.post_hits}</span><br />Visitas</li>
                                <li><span class="icons puntos_post">{$tsPost.post_puntos}</span><br />Puntos</li>
                                <li><span class="icons monitor">{$tsPost.post_seguidores}</span><br />Seguidores</li>
                            </ul>
                            <div class="clearfix"></div>
                            <hr class="divider"/>
                            <div class="tags-block">
                            	<span class="icons tags_title">Tags:</span>
                                {foreach from=$tsPost.post_tags key=i item=tag}
                                <a rel="tag" href="{$tsConfig.url}/buscador/?q={$tag|seo}&e=tags">{$tag}</a> {if $i < $tsPost.n_tags}-{/if}
                                {/foreach}
                            </div>
                            <ul class="post-cat-date">
                            	<li><strong>Categor&iacute;a:</strong> <a href="{$tsConfig.url}/posts/{$tsPost.categoria.c_seo}/">{$tsPost.categoria.c_nombre}</a></li>
                                <li><strong>Creado:</strong><span> {$tsPost.post_date}.</span></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </div>