{
*********************************************************************************
* m.posts_autor.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                	<div class="post-autor vcard">
                    	<div class="box_title">
                        	<div class="box_txt post_autor">Posteado por:</div>
                            <div class="box_rss">
                            	<a href="{$tsConfig.url}/rss/posts-usuario/{$tsAutor.user_name}">
                                	<span style="position:relative;">
                                    <img border="0" title="RSS con posts de {$tsAutor.user_name}" alt="RSS con posts de Usuario" style="position:absolute; top:-354px; clip:rect(352px 16px 368px 0px);" src="{$tsConfig.images}/big1v12.png"/>
                                    <img border="0" style="width:14px;height:12px" src="{$tsConfig.images}/space.gif"/>
                                    </span>
                                 </a>
                            </div>
                        </div>
                        <div class="box_cuerpo">
                        	<div class="avatarBox">
                                <a href="{$tsConfig.url}/perfil/{$tsAutor.user_name}">
                                    <img title="Ver perfil de {$tsAutor.user_name}" alt="Ver perfil de {$tsAutor.user_name}" class="avatar" src="{$tsConfig.url}/files/avatar/{$tsPost.post_user}_120.jpg"/>
                                </a>
							</div>
                            <a href="{$tsConfig.url}/perfil/{$tsAutor.user_name}" style="text-decoration:none">
								<span class="given-name" style="color:#{$tsAutor.rango.r_color}">{$tsAutor.user_name}</span>
							</a>
                            <br />
                            <span class="title">{$tsAutor.rango.r_name}</span>
                            <br />
                            <img src="{$tsConfig.default}/images/space.gif" class="status {$tsAutor.status.css}" title="{$tsAutor.status.t}"/>
                            <img src="{$tsConfig.default}/images/icons/ran/{$tsAutor.rango.r_image}" title="{$tsAutor.rango.r_name}" />
                            <img src="{$tsConfig.default}/images/icons/{if $tsAutor.user_sexo == 0}female{else}male{/if}.png" title="{if $tsAutor.user_sexo == 0}Mujer{else}Hombre{/if}" />
                            <img src="{$tsConfig.default}/images/flags/{$tsAutor.pais.icon}.png" style="padding:2px" title="{$tsAutor.pais.name}" />
                            {if $tsPost.post_user != $tsUser->uid}<a href="#" onclick="{if !$tsUser->is_member}registro_load_form();{else}mensaje.nuevo('{$tsAutor.user_name}','','','');{/if}return false"><img title="Enviar mensaje privado" src="{$tsConfig.images}/icon-mensajes-recibidos.gif"/></a>{/if}
                            {if !$tsUser->is_member}
                            <hr class="divider"/>
                            <a class="btn_g follow_user_post" href="#" onclick="registro_load_form(); return false"><span class="icons follow">Seguir Usuario</span></a>
                            {elseif $tsPost.post_user != $tsUser->uid}
                            <hr class="divider"/>
                            <a class="btn_g unfollow_user_post" onclick="notifica.unfollow('user', {$tsPost.post_user}, notifica.userInPostHandle, $(this).children('span'))" {if !$tsAutor.follow}style="display: none;"{/if}><span class="icons unfollow">Dejar de seguir</span></a>
                            <a class="btn_g follow_user_post" onclick="notifica.follow('user', {$tsPost.post_user}, notifica.userInPostHandle, $(this).children('span'))" {if $tsAutor.follow > 0}style="display: none;"{/if}><span class="icons follow">Seguir Usuario</span></a>
                            {/if}
                            <hr class="divider"/>
                            <div class="metadata-usuario">
                            	<span class="nData user_follow_count">{$tsAutor.user_seguidores}</span>
                                <span class="txtData">Seguidores</span>
                                <span class="nData" style="color: #0196ff">{$tsAutor.user_puntos}</span>
                                <span class="txtData">Puntos</span>
                                <span class="nData">{$tsAutor.user_posts}</span>
                                <span class="txtData">Posts</span>
                                <span style="color: #456c00" class="nData">{$tsAutor.user_comentarios}</span>
                                <span class="txtData">Comentarios</span>
                            </div>
                            {if $tsUser->is_admod}
                            <hr class="divider"/>
                            <div class="mod-actions">
                                <b>Herramientas</b>
                                <a href="http://www.geoiptool.com/?IP={$tsAutor.user_last_ip}" class="geoip" target="_blank">{$tsAutor.user_last_ip}</a>
                            </div>
                            {/if}
                        </div>
                    </div>