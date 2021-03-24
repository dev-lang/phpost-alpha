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
					<div id="webAffs">
                        <div class="wMod clearbeta">
                            <div class="wMod-h">Afiliados</div>
                            <div class="wMod-data">
                                <ul>
                                {foreach from=$tsAfiliados item=af}
                                <li><a href="#" onclick="afiliado.detalles({$af.aid}); return false;" title="{$af.a_titulo}">
                                    <img src="{$af.a_banner}" width="190" height="40"/>
                                </a></li>
                                {/foreach}
                                </ul>
                            </div>
                            <div class="floatR"><a onclick="afiliado.nuevo(); return false">Afiliate a {$tsConfig.titulo}</a></div>
                         </div>
                    </div>