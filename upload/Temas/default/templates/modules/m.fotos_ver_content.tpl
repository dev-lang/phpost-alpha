{
*********************************************************************************
* m.fotos_home_content.php	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                <div id="fg_centro" style="width: 600px; float: left; margin:0 10px">
                <div class="foto">
                    <div class="v_user">
                        <div class="avatar-box">
                            <a href="{$tsConfig.url}/perfil/{$tsFoto.user_name}"><img src="{$tsConfig.url}/files/avatar/{$tsFoto.f_user}_50.jpg"/></a>
                        </div>
                        <div class="v_info">
                            <a href="{$tsConfig.url}/perfil/{$tsFoto.user_name}" class="user">{$tsFoto.user_name}</a>
                            <div class="links">
                                <span style="background-image:url({$tsConfig.default}/images/icons/ran/{$tsFoto.r_image});color:#{$tsFoto.r_color}"><strong>{$tsFoto.r_name}</strong></span>
                                <span style="background-image:url({$tsConfig.default}/images/flags/{$tsFoto.user_pais.0|lower}.png);">{$tsFoto.user_pais.1}</span>
                                <span style="background-image:url({$tsConfig.default}/images/icons/{if $tsFoto.user_sexo == 0}fe{/if}male.png);">{if $tsFoto.user_sexo == 1}Hombre{else}Mujer{/if}</span>
                                {if $tsUser->is_member && $tsUser->uid != $tsFoto.f_user}<span style="background-image:url({$tsConfig.default}/images/icon-mensajes-recibidos.gif);"><a href="#" onclick="mensaje.nuevo('{$tsFoto.user_name}','','',''); return false;">Enviar Mensaje</a></span>{/if}
                            </div>
                            {if $tsUser->uid != $tsFoto.f_user && $tsUser->is_member}
                            <div class="v_follow">
                                <a class="btn_g unfollow_user_post" onclick="notifica.unfollow('user', {$tsFoto.f_user}, notifica.userInPostHandle, $(this).children('span'))" {if $tsFoto.follow == 0}style="display: none;"{/if}><span class="icons unfollow">Dejar de seguir</span></a>
                                <a class="btn_g follow_user_post" onclick="notifica.follow('user', {$tsFoto.f_user}, notifica.userInPostHandle, $(this).children('span'))" {if $tsFoto.follow == 1}style="display: none;"{/if}><span class="icons follow">Seguir Usuario</span></a>
                            </div>
                            {/if}
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <span class="spacer"></span>
                    <div id="imagen">
                        {if $tsFoto.f_user == $tsUser->uid || $tsUser->is_admod}
                        <div class="tools">
                        <a href="#" onclick="fotos.borrar({$tsFoto.foto_id}, 'foto'); return false;">
						  <img alt="Borrar" src="{$tsConfig.default}/images/borrar.png"/> Borrar</a>
                        <a href="#" onclick="location.href='{$tsConfig.url}/fotos/editar.php?id={$tsFoto.foto_id}'; return false">
						  <img alt="Editar" src="{$tsConfig.default}/images/editar.png"/> Editar</a>
                        </div>
                        {/if}
                        <img class="img" src="{$tsFoto.f_url}" />
                    </div>
                    <h2 class="floatL">{$tsFoto.f_title}</h2>
                    <span class="floatR"><b>{$tsFoto.f_date|date_format:"%d/%m/%Y"}</b></span>
                    <div class="clearBoth"></div>
                    <p>{$tsFoto.f_description|nl2br}</p>
                    <span class="spacer"></span>
                    <div class="infoPost">
                  		<div style="width:12%" class="rateBox">
                  			<strong class="title">Calificar:</strong>
                            <span id="actions">
                    			<a title="Votar positivo" class="thumbs thumbsUp" href="#" onclick="fotos.votar('pos'); return false;"></a>
                    			<a title="Votar negativo" class="thumbs thumbsDown" href="#" onclick="fotos.votar('neg'); return false;"></a>
                    		</span>
                  		</div><!-- END RateBox -->
                        <div style="width:12%" class="rateBox">
                  			<strong class="title">Positivos:</strong>
                            <span class="color_green" id="votos_total_pos">{$tsFoto.f_votos_pos}</span>
                  		</div><!-- END RateBox -->
                        <div style="width:12%" class="rateBox">
                  			<strong class="title">Negativos:</strong>
                            <span class="color_red" id="votos_total_neg">{$tsFoto.f_votos_neg}</span>
                  		</div><!-- END RateBox -->
                  		<div style="width: 10%" class="metaBox">
            	    		<strong class="title">Visitas:</strong>
                  			<span style="font-size:11px">{$tsFoto.f_hits}</span>
                 		</div><!-- END Visitas -->	
                 		<div style="width: 12%" class="metaBox">
                 			<strong class="title">Favoritos</strong>
                 			<span style="font-size:11px">{$tsFoto.f_favorites}</span>
                        </div><!-- END Visitas -->
                  		<div class="clearBoth"></div>
 	                </div>
                </div>
                <div class="bajo" style="margin-top:5px">
                    <div class="comments">
                        <div class="comentarios-title">
                            <a href="{$tsConfig.url}/rss/comentarios.php?id={$tsFoto.foto_id}&type=fotos">
                                <span class="floatL systemicons sRss" style="position: relative; z-index: 87; margin-right: 5px;"></span>
                            </a>
                            <h4 class="titulorespuestas floatL"><span id="ncomments">{$tsFoto.f_comments}</span> Comentarios</h4>
				           <div class="clearfix"></div>
                           <hr />
                        </div>
                        <div id="mensajes">
                            {if $tsFComments}
                            {foreach from=$tsFComments item=c}
                            <div class="item" id="div_cmnt_{$c.cid}">
                                <a href="{$tsConfig.url}/perfil/{$c.user_name}">
                                    <img src="{$tsConfig.url}/files/avatar/{$c.c_user}_50.jpg" width="50" height="50" class="floatL"/>
                                </a>
                                <div class="firma">
                                    <div class="options">
                                        {if $tsFoto.f_user == $tsUser->info.user_id}
                                        <a href="#" onclick="fotos.borrar({$c.cid}, 'com'); return false" class="floatR" style="margin:8px 5px">
                            			  <img title="Borrar Comentario" alt="borrar" src="{$tsConfig.default}/images/borrar.png"/>
                                        </a>
                                        {/if}
                                    </div>
                                    <div class="info"><a href="{$tsConfig.url}/fotos/{$c.user_name}">{$c.user_name}</a> <span>@ {$c.c_date|date_format:"%d/%m/%Y"} dijo:</span></div>
                                    <div class="clearfix">{$c.c_body|nl2br}</div>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                            {/foreach}
                            {elseif $tsFoto.f_closed == 0}
                            <div class="noComments">Esta foto no tiene comentarios, Se el primero!.</div>
                            {/if}
                        </div>
                        {if $tsFoto.f_closed == 1}
                        <div class="noComments">La foto se encuentra cerrada y no se permiten comentarios.</div>
                        {elseif $tsUser->is_member}
                        <div class="form">
                            <div class="avatar-box">
                                <img src="{$tsConfig.url}/files/avatar/{$tsUser->uid}_50.jpg" width="50" height="50"/>
                            </div>
                            <form method="post" action="" name="firmar">
                                <label for="mensaje" style="font-size:12px"><b>Mensaje</b></label>
                                <div class="error"></div>
                                <textarea name="mensaje" id="mensaje" rows="2" class="onblur_effect autorow" style="width:504px;margin:5px 0; min-height:36px; max-height:160px" title="Escribe un mensaje." onblur="onblur_input(this)" onfocus="onfocus_input(this)">Escribe un mensaje.</textarea>
                                <input type="hidden" name="auser_post" value="{$tsFoto.f_user}" />
                                <input type="button" id="btnComment" class="mBtn btnOk" value="Comentar" onclick="fotos.comentar()" />
                            </form>
                            <div class="clearBoth"></div>
                        </div>
                        {else}
                        <div class="emptyData">Para poder comentar necesitas estar <a onclick="registro_load_form(); return false" href="">Registrado.</a> O.. ya tienes usuario? <a onclick="open_login_box('open')" href="#">Logueate!</a></div>
                        {/if}
                    </div>
                </div>
                </div>
