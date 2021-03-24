{
*********************************************************************************
* t.admin.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
{include file='sections/main_header.tpl'}
                <script type="text/javascript" src="{$tsConfig.default}/js/moderacion.js"></script>
                <div id="borradores">
					<div class="clearfix">
                    	<div class="left" style="float:left;width:200px">
                   			<div class="boxy">
                                <div class="boxy-title">
                                    <h3>Opciones</h3>
                                    <span></span>
                                </div><!-- boxy-title -->
                                <div class="boxy-content" id="admin_menu">
									{include file='admin_mods/m.mod_sidemenu.tpl'}
                                </div><!-- boxy-content -->
                            </div>
                        </div>
                        <div class="right" style="float:left;margin-left:10px;width:730px">
                            <div class="boxy" id="admin_panel">
                            	{* Q WEBA PERO NO HAY DE OTRA xD*}
                            	{if $tsAction == ''}
                            	{include file='admin_mods/m.mod_welcome.tpl'}
                                {elseif $tsAction == 'posts'}
                            	{include file='admin_mods/m.mod_report_posts.tpl'}
                                {elseif $tsAction == 'mpreport'}
                                <div class="phpostAlfa">PHPost Alfa v1.0</div>
                                {elseif $tsAction == 'users'}
                            	{include file='admin_mods/m.mod_report_users.tpl'}
                                {elseif $tsAction == 'banusers'}
                                {include file='admin_mods/m.mod_ban_users.tpl'}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear:both"></div>
                
{include file='sections/main_footer.tpl'}