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

					<div id="webStats">
                        <div class="wMod clearbeta">
                            <div class="wMod-h">Estad&iacute;sticas</div>
                            <div class="box_cuerpo">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                	<td style="background-image:url({$tsConfig.default}/images/icons/power_on.png);"><a class="usuarios_online" href="{$tsConfig.url}/usuarios/?online=true">{$tsStats.stats_online} online</a></td>
        	                        <td style="background-image:url({$tsConfig.default}/images/icons/user.png);"><a href="{$tsConfig.url}/usuarios/">{$tsStats.stats_miembros} miembros</a></td>
                                </tr>
    	                        <tr>
        	                        <td style="background-image:url({$tsConfig.default}/images/icons/posts.png);">{$tsStats.stats_posts} posts</td>
            	                    <td style="background-image:url({$tsConfig.default}/images/icons/comment.png);">{$tsStats.stats_comments} comentarios</td>
                                </tr>
    	                        <tr>
        	                        <td style="background-image:url({$tsConfig.default}/images/icons/foto.png);">{$tsStats.stats_fotos} fotos</td>
            	                    <td style="background-image:url({$tsConfig.default}/images/icons/comment.png);">{$tsStats.stats_foto_comments} comentarios en fotos</td>
                                </tr>
                            </table>
                            </div>
                        </div>
                    </div>