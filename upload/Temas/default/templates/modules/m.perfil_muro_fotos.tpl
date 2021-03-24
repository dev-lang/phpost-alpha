{
*********************************************************************************
* m.perfil_muro_form.tpl	                                                    *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}				
                            <ul>
                                {foreach from=$tsGeneral.fotos item=f key=i}
                                {if $f.foto_id}<li><div class="foto"><a href="{$tsConfig.url}/fotos/{$tsInfo.nick}/{$f.foto_id}/{$f.f_title|seo}.html" title="{$f.f_title}"><img border="0" src="{$f.f_url}"/></a></div></li>{else}
                                <li><div class="foto">&nbsp;</div></li>{/if}
                                {/foreach}
                            </ul>