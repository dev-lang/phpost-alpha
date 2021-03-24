{
*********************************************************************************
* m.home_stats.php	                                                            *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}				

					<div id="lastCommBox">
                        <div class="box_title">
                            <div class="box_txt estadisticas">&Uacute;ltimos comentarios</div>
                            <div class="box_rss">
                            	<a onclick="actualizar_comentarios('-1','0'); return false;" class="size9" href="#"><span class="systemicons actualizar"></span></a>
                            </div>
                        </div>
                        <div class="box_cuerpo" id="ult_comm" style="padding: 0pt; height: 330px;">
                            <ol id="filterByTodos" class="filterBy cleanlist" style="display:block;">
                            	{foreach from=$tsComments key=i item=c}
								<li>
                                    {if $i+1 < 10}0{/if}{$i+1}.
                                	<strong>{$c.user_name}</strong> 
                                    <a href="{$tsConfig.url}/posts/{$c.c_seo}/{$c.post_id}/{$c.post_title|seo}.html#comentarios-abajo">
                                    {$c.post_title}</a>
                                </li>
                                {/foreach}
                            </ol>
                        </div>
                        <br class="space"/>
                    </div>