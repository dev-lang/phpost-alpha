{
********************************************************************************
* t.home.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
{include file='sections/main_header.tpl'}
                <div id="izquierda">
					{include file='modules/m.home_last_posts.tpl'}
                </div>
                <div id="centro">
                	{include file='modules/m.home_search.tpl'}
                    {include file='modules/m.home_stats.tpl'}
                	{include file='modules/m.home_last_comments.tpl'}
                	{include file='modules/m.home_top_posts.tpl'}
                	{include file='modules/m.home_top_users.tpl'}
                    <!--Poner aqui mas modulos-->
                </div>
                <div id="derecha">
                    {include file='modules/m.home_fotos.tpl'}
                    {include file='modules/m.home_afiliados.tpl'}
                    <br class="spacer"/>
                    {include file='modules/m.global_ads_160.tpl'}
                </div>
                <div style="clear:both"></div>

{include file='sections/main_footer.tpl'}