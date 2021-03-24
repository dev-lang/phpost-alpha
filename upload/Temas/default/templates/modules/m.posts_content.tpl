{
*********************************************************************************
* m.posts_content.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                	<div class="post-contenedor">
                    	<div class="post-title">
                        	<a title="Post Anterior (m&aacute;s viejo)" class="icons anterior" href="{$tsConfig.url}/posts/prev?id={$tsPost.post_id}"></a>
                            <h1>{$tsPost.post_title}</h1>
                            <a title="Post Siguiente (m&aacute;s nuevo)" class="icons siguiente" href="{$tsConfig.url}/posts/next?id={$tsPost.post_id}"></a>
                        </div>
                        <div class="post-contenido">
                        	{if !$tsUser->is_member}{include file='modules/m.global_ads_728.tpl'}{/if}
                            {if $tsUser->is_member}
                                {if $tsPost.post_user == $tsUser->uid || $tsUser->is_admod > 0}
    							<div class="floatR">
                                    <a title="Borrar Post" onclick="{if $tsUser->is_admod}mod.posts.borrar({$tsPost.post_id}, 'posts', null);{else} borrar_post();{/if} return false;" href="" class="btnActions">
                                        <img alt="Borrar" src="{$tsConfig.images}/borrar.png"/> Borrar</a>
                                    <a title="Editar Post" onclick="location.href='{$tsConfig.url}/agregar.php?action=editar&pid={$tsPost.post_id}'; return false" href="" class="btnActions">
                                        <img alt="Editar" src="{$tsConfig.images}/editar.png"/> Editar</a>
                                </div>
                                <br />
                                {/if}
                            {/if}
                            <span>
                            	{$tsPost.post_body}
                            </span>
                            {if $tsPost.user_firma && $tsConfig.c_allow_firma}
                            <hr class="divider" />
                            <span>
                            	{$tsPost.user_firma}
                            </span>
                            {/if}
                            <div class="compartir-mov" style="text-align: right; color:#888; font-size: 14px;margin: 30px 0 10px">
                            	<div class="m-left"></div>
                                <div class="m-right"></div>
                                <div class="movi-logo"></div>
                                <ul class="post-compartir clearbeta">
                                    <li class="share-big">
                                    	<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="{$tsConfig.titulo}" data-lang="es">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
									</li>
                                    <li class="share-big">
									 	<a name="fb_share" share_url="{$tsConfig.url}/posts/{$tsPost.c_ceo}/{$tsPost.post_id}/{$tsPost.post_title|seo}.html" type="box_count" href="http://www.facebook.com/sharer.php">Compartir</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
									</li>
                                    <li class="share-big">
									 	<span class="share-t-count">{$tsPost.post_shared}</span>
										<a href="{if !$tsUser->is_member}{$tsConfig.url}/registro{else}javascript:notifica.sharePost({$tsPost.post_id}){/if}" class="share-t"></a>
									</li>
                                    <li class="txt-movi">Compartir en:</li>
                                </ul>
                            </div>
                            {include file='modules/m.global_ads_728.tpl'}
                        </div>
	                    {include file='modules/m.posts_metadata.tpl'}
                    </div>