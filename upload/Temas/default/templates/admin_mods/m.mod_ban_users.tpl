{
*********************************************************************************
* m.admin_welcome.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox �											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox �											*
*********************************************************************************
}

                                <div class="boxy-title">
                                    <h3>Moderaci&oacute;n de usuarios</h3>
                                </div>
                                <div id="res" class="boxy-content">
                                    {if $tsUser->is_admod != 1}S&oacute;olo puedes quitar la suspenci&oacute;n a los usuarios que t&uacute; hayas suspendido.<hr class="separator" />{/if}
                                    <table cellpadding="0" cellspacing="0" border="0" class="admin_table" width="100%" align="center">
                                    	<thead>
                                        	<th>Usuario</th>
                                            <th>Causa</th>
                                            <th>Suspendido</th>
                                            <th>Termina</th>
                                            <th>Lo suspendi&oacute;</th>
                                            <th>Acciones</th>
                                        </thead>
                                        <tbody>
                                        	{if $tsSuspendidos}{foreach from=$tsSuspendidos item=s}
                                            <tr id="report_{$s.user_id}">
                                            	<td><a href="{$tsConfig.url}/perfil/{$s.user_name}" class="hovercard" uid="{$s.user_id}">{$s.user_name}</a></td>
                                                <td>{$s.susp_causa}</td>
                                                <td>{$s.susp_date|hace:true}</td>
                                                <td>{if $s.susp_termina == 0}Indefinidamente{elseif $s.susp_termina == 1}Permanentemente{else}{$s.susp_termina|date_format:"%d/%m/%Y a las %H:%M:%S"}{/if}</td>
                                                <td><a href="#" class="hovercard" uid="{$s.susp_mod}">{$tsUser->getUserName($s.susp_mod)}</a></td>
                                                <td class="admin_actions">
                                                    <a href="#" onclick="mod.reboot({$s.user_id}, 'users', 'unban', false); return false;"><img src="{$tsConfig.default}/images/icons/power_on.png" title="Reactivar usuario" /></a>
                                                </td>
                                            </tr>
                                            {/foreach}{else}
                                            <tr>
                                                <td colspan="6"><div class="emptyData">No hay usuarios denunciados hasta el momento.</div></td>
                                            </tr>
                                            {/if}
                                        </tbody>
                                        <tfoot>
                                            <th colspan="6">&nbsp;</th>
                                        </tfoot>
                                    </table>
                                </div>
                                    