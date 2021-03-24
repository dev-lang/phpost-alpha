{
*********************************************************************************
* m.cuenta_block.php	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}				

							<div class="content-tabs privacidad" style="display:none">
                                <fieldset>
                                    <div class="alert-cuenta cuenta-7"></div>
                                    <h2 class="active">&iquest;Qui&eacute;n puede...</h2>
                                    <div class="field">
                    					<label>ver tu muro?</label>
                    					<div class="input-fake">
                    						<select name="muro" class="cuenta-save-7">
                                                {foreach from=$tsPrivacidad item=p key=i}
                                                <option value="{$i}"{if $tsPerfil.p_configs.m == $i} selected="true"{/if}>{$p}</option>
                                                {/foreach}
                    						</select>
                    					</div>
                    				</div>
                                    {$tsPerfil.p_configs.muro}
                                    <div class="field">
                    					<label>firmar tu muro?</label>
                    					<div class="input-fake">
                    						<select name="muro_firm" class="cuenta-save-7">
                                                {foreach from=$tsPrivacidad item=p key=i}
                                                {if $i != 3}<option value="{$i}"{if $tsPerfil.p_configs.mf == $i} selected="true"{/if}>{$p}</option>{/if}
                                                {/foreach}
                    						</select>
                    					</div>
                    				</div>
                                </fieldset>
                                <div class="buttons">
                                    <input type="button" value="Guardar" onclick="cuenta.save(7)" class="mBtn btnOk">
                                </div>
                                <div class="clearfix"></div>
                            </div>