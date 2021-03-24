{
*********************************************************************************
* m.home_last_post.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}				
                <div class="clearbeta lastPosts">
                    {if $tsPostsStickys}
                	<div class="header">
                    	<div class="box_txt ultimos_posts">Posts importantes en {$tsConfig.titulo}</div>
                        <div class="box_rss">
                            <img src="{$tsConfig.default}/images/icons/note.png" />
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="body">
                        <ul>
                        	{foreach from=$tsPostsStickys item=p}
                            <li class="categoriaPost sticky{if $p.post_sponsored == 1} patrocinado{/if}">
                            <a href="{$tsConfig.url}/posts/{$p.c_seo}/{$p.post_id}/{$p.post_title|seo}.html" style="background:url({$tsConfig.url}/Temas/default/images/icons/cat/{$p.c_img}) no-repeat 5px center" title="{$p.post_title}" target="_self" class="title">{$p.post_title|truncate:55}</a>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                    {/if}
                	<div class="header">
                    	<div class="box_txt ultimos_posts">&Uacute;ltimos posts en {$tsConfig.titulo}</div>
                        <div class="box_rss">
                            <a href="/rss/ultimos-post">
                                <span class="systemicons sRss" style="position:relative;z-index:87"></span>
                            </a>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="body">
                    	<ul>
                            {if $tsPosts}
                            {foreach from=$tsPosts item=p}
                            <li class="categoriaPost" style="background-image:url({$tsConfig.url}/Temas/default/images/icons/cat/{$p.c_img})">
                                <a class="title {if $p.post_private}categoria privado{/if}" alt="{$p.post_title}" title="{$p.post_title}" target="_self" href="{$tsConfig.url}/posts/{$p.c_seo}/{$p.post_id}/{$p.post_title|seo}.html">{$p.post_title|truncate:50}</a>
                                <span>{$p.post_date|hace} &raquo; <a href="{$tsConfig.url}/perfil/{$p.user_name}" class="hovercard" uid="{$p.post_user}"><strong>@{$p.user_name}</strong></a> &middot; Puntos <strong>{$p.post_puntos}</strong> &middot; Comentarios <strong>{$p.post_comments}</strong></span>
                                <span class="floatR"><a href="{$tsConfig.url}/posts/{$p.c_seo}/">{$p.c_nombre}</a></span>
                            </li>
                            {/foreach}
                            {else}
                            <li class="emptyData">No hay posts aqu&iacute;</li>
                            {/if}
                        </ul>
                        <br clear="left"/>
                    </div>
                    <div class="footer size13">
                        {if $tsPages.prev > 0 && $tsPages.max == false}<a href="pagina{$tsPages.prev}" class="floatL">&laquo; Anterior</a>{/if}
                        {if $tsPages.next <= $tsPages.pages}<a href="pagina{$tsPages.next}" class="floatR">Siguiente &raquo;</a>
                        {elseif $tsPages.max == true}<a href="pagina2">Siguiente &raquo;</a>{/if}
                    </div>
                 </div>