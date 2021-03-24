{
*********************************************************************************
* m.fotos_home_sidebar.php	                                                    *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                <div style="width: 160px; float: left;">
                    <div class="categoriaList">
                        <h6 style="text-align:center;">Seguidores</h6>
                        <ul id="v_album">
                            {if $tsFFotos}
                            {foreach from=$tsFFotos item=f}
                            <li><a href="{$tsConfig.url}/fotos/{$f.user_name}/{$f.foto_id}/{$f.f_title|seo}.html"><img src="{$f.f_url}" title="{$f.f_title}" width="120" height="90" /></a><br /><a href="{$tsConfig.url}/perfil/{$f.user_name}" style="text-decoration:underline;"><strong>{$f.user_name}</strong></a></li>
                            {/foreach}
                            {else}
                            <li class="emptyData"><u>{$tsFoto.user_name}</u> no sigue usuarios o no han subido fotos.</li>
                            {/if}
                        </ul>
                        {if $tsFFotos}<a href="{$tsConfig.url}/fotos/{$tsFoto.user_name}/" class="fb_foot">Ver todas</a>{/if}
                    </div>
                    <div class="categoriaList estadisticasList">
                        <h6>Estad&iacute;sticas</h6>
                        <ul>
                            <li class="clearfix"><a href="{$tsConfig.url}/fotos/{$tsFoto.user_name}/"><span class="floatL">Fotos subidas</span><span class="floatR number">{$tsFoto.user_fotos}</span></a></li>
                            <li class="clearfix"><a href="#"><span class="floatL">Comentarios</span><span class="floatR number">{$tsFoto.user_foto_comments}</span></a></li>
                        </ul>
                    </div>
                </div>