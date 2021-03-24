{
*********************************************************************************
* m.admin_afiliados.php 	                                                    *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                                <div class="boxy-title">
                                    <h3>Administrar Afiliados</h3>
                                </div>
                                <div id="res" class="boxy-content">
                                {if $tsSave}<div style="display: block;" class="mensajes ok">Tus cambios han sido guardados.</div>{/if}
                                {if $tsAfis}
                                	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="admin_table">
                                    	<thead>
                                        	<th>ID</th>
                                            <th>Afiliado</th>
                                            <th>Cuando</th>
                                            <th>Entrada</th>
                                            <th>Salida</th>
                                            <th>Acciones</th>
                                        </thead>
                                        <tbody>{foreach from=$tsAfiliados item=af}
                                        	<tr>
                                                <td>{$af.aid}</td>
                                                <td><a href="{$af.a_url}" target="_blank">{$af.a_titulo}</a></td>
                                                <td>Hace {$af.a_date|hace}</td>
                                                <td>{$af.a_hits_in}</td>
                                                <td>{$af.a_hits_out}</td>
                                                <td class="admin_actions">
                                                    <a href="{$tema.tid}"><img src="{$tsConfig.url}/Temas/default/images/icons/editar.png" title="Editar"/></a>
                                                    <a href="#" onclick="ad_afiliado.detalles({$af.aid}); return false;"><img src="{$tsConfig.url}/Temas/default/images/icons/details.png" title="Detalles"/></a>
                                                    {if $af.a_active == 1}
                                                    <a href="?act=editar&tid={$tema.tid}"><img src="{$tsConfig.url}/Temas/default/images/icons/power_off.png" title="Desactivar"/></a>
                                                    {else}
                                                    <a href="?act=editar&tid={$tema.tid}"><img src="{$tsConfig.url}/Temas/default/images/icons/power_on.png" title="Activar"/></a>
                                                    {/if}
                                                    <a href="?act=editar&tid={$tema.tid}"><img src="{$tsConfig.url}/Temas/default/images/icons/close.png" title="Eliminar"/></a>
                                                </td>
                                            </tr>{/foreach}
                                        </tbody>
                                    </table>
                                 {else}
                                 <div class="phpostAlfa">Pendiente</div>
                                 {/if}
                                </div>