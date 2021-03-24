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

                <div id="centroDerecha" style="width: 630px; float: left;">
                	<div class="">
                        <h2 style="font-size: 15px;">&Uacute;ltimas fotos</h2>
                    </div>
                    <ul class="fotos-detail listado-content">
                        {foreach from=$tsLastFotos item=f}
                    	<li>
                        	<div class="avatar-box" style="z-index: 99;">
                            	<a href="{$tsConfig.url}/fotos/{$f.user_name}/{$f.foto_id}/{$f.f_title|seo}.html">
                            		<img height="100" width="100" src="{$f.f_url}"/>
                                </a>
                            </div>
                            <div class="notification-info">
                            	<span>
                                    <a href="{$tsConfig.url}/fotos/{$f.user_name}/{$f.foto_id}/{$f.f_title|seo}.html">{$f.f_title}</a><br /> 
                            		<span title="{$f.f_date|date_format:"%d.%m.%y a las %H:%M hs."}" class="time"><strong>{$f.f_date|date_format:"%d.%m.%Y"}</strong> - Por <a href="{$tsConfig.url}/perfil/{$f.user_name}">{$f.user_name}</a></span><hr />
                                    <span class="time">{$f.f_description|truncate:100}</span>
                                </span>
                            </div>
                        </li>
                        {/foreach}
                    </ul>
                </div>
