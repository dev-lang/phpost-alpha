{
********************************************************************************
* t.posts.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
{include file='sections/main_header.tpl'}
                <div id="resultados" style="width:100%">
                    <table class="linksList">
                        <thead>
                			<tr>
                				<th>Post</th>
                				<th>Acci&oacute;n</th>
                				<th>Moderador</th>
                				<th>Causa</th>
                			</tr>
                		</thead>
                        <tbody>
                            {foreach from=$tsHistory item=h}
                            <tr>
                                <td style="text-align: left;">
                        			{$h.post_title}<br/>
                        			Por <a href="{$tsConfig.url}/perfil/{$h.post_autor}">{$h.post_autor}</a>
                        		</td>
                                <td>
                        			{if $h.post_action == 1}
                                    <span class="color_green">Editado</span>
                                    {else}
                                    <span class="color_red">Eliminado</span>
                                    {/if}
                        		</td>
                                <td>
           							<a href="{$tsConfig.url}/perfil/{$h.post_mod}">{$h.post_mod}</a>
            					</td>
                                <td>{$h.post_reason}</td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <div style="clear:both"></div>
{include file='sections/main_footer.tpl'}