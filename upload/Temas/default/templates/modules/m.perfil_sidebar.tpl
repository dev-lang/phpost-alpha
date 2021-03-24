{
*********************************************************************************
* m.perfil_sidebar.tpl	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}				

                        <div style="margin-bottom: 10px">
                        	{$tsConfig.ads_300}
                        </div>
                        <div class="widget w-medallas clearfix">
                    		<div class="title-w clearfix">
                    			<h3>Medallas</h3>
                    			<span>0</span>
                    		</div>
                			<div class="emptyData">No tiene medallas</div>
                		</div>
                        <div class="widget w-seguidores clearfix">
                    		<div class="title-w clearfix">
                    			<h3>Seguidores</h3>
                    			<span>{$tsInfo.stats.user_seguidores}</span>
                    		</div>
                            {if $tsGeneral.segs.data}
            				<ul class="clearfix">
                                {foreach from=$tsGeneral.segs.data item=s}
            					<li><a href="{$tsConfig.url}/perfil/{$s.user_name}" class="hovercard" uid="{$s.user_id}" style="display:inline-block;"><img src="{$tsConfig.url}/files/avatar/{$s.user_id}_50.jpg" width="32" height="32"/></a></li>
                                {/foreach}
            				</ul>
                            {if $tsGeneral.segs.total >= 21}<a href="#seguidores" onclick="perfil.load_tab('seguidores', $('#seguidores'));" class="see-more">Ver m&aacute;s &raquo;</a>{/if}
                            {else}
                            <div class="emptyData">No tiene seguidores</div>
                            {/if}
             			</div>
                        <div class="widget w-siguiendo clearfix">
                            <div class="title-w clearfix">
                              <h3>Siguiendo</h3>
                              <span>{$tsInfo.stats.user_siguiendo}</span>
                            </div>
                            {if $tsGeneral.sigd.data}
            				<ul class="clearfix">
                                {foreach from=$tsGeneral.sigd.data item=s}
            					<li><a href="{$tsConfig.url}/perfil/{$s.user_name}" class="hovercard" uid="{$s.user_id}" style="display:inline-block;"><img src="{$tsConfig.url}/files/avatar/{$s.user_id}_50.jpg" width="32" height="32"/></a></li>
                                {/foreach}
            				</ul>
                            {if $tsGeneral.sigd.total >= 21}<a href="#siguiendo" onclick="perfil.load_tab('siguiendo', $('#siguiendo'));" class="see-more">Ver m&aacute;s &raquo;</a>{/if}
                            {else}
                            <div class="emptyData">No sigue usuarios</div>
                            {/if}
            			</div>