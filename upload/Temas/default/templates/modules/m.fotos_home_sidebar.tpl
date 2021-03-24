{
*********************************************************************************
* m.fotos_home_sidebar.php	                                                    *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                <div style="width: 300px; float: right;" id="post-izquierda">
                    <div class="categoriaList">
                        <h6>&Uacute;ltimos comentarios</h6>
                        <ul>
                            {foreach from=$tsLastComments item=c}
                            <li><strong>{$tsUser->getUsername($c.c_user)}</strong> &raquo; <a href="{$tsConfig.url}/fotos/{$c.user_name}/{$c.foto_id}/{$c.f_title|seo}.html#div_cmnt_{$c.cid}">{$c.f_title}</a></li>
                            {/foreach}
                        </ul>
                    </div>
                    <center>{$tsConfig.ads_300}</center>
                    <br />
                    <div class="categoriaList estadisticasList">
                        <h6>Estad&iacute;sticas</h6>
                        <ul>
                            <li class="clearfix"><a href="#"><span class="floatL">Miembros</span><span class="floatR number">{$tsStats.stats_miembros}</span></a></li>
                            <li class="clearfix"><a href="#"><span class="floatL">Fotos</span><span class="floatR number">{$tsStats.stats_fotos}</span></a></li>
                            <li class="clearfix"><a href="#"><span class="floatL">Comentarios</span><span class="floatR number">{$tsStats.stats_foto_comments}</span></a></li>
                        </ul>
                    </div>
                </div>