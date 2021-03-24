{
*********************************************************************************
* p.perfil.medallas.tpl                                                         *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
1:
<div id="perfil_medallas" class="widget" status="activo">
	<div class="title-w clearfix">
        <h2>Medallas de {$tsUsername}</h2>
    </div>
    {if $tsData}
    <ul class="listado">
        {foreach from=$tsData.data item=u}
        <li class="clearfix">
        	<div class="listado-content clearfix">
        		<div class="listado-avatar">
        			<a href="{$tsConfig.url}/perfil/{$u.user_name}"><img src="{$tsConfig.url}/files/avatar/{$u.user_id}_50.jpg" width="32" height="32"/></a>
        		</div>
        		<div class="txt">
        			<a href="{$tsConfig.url}/perfil/{$u.user_name}">{$u.user_name}</a><br>
        			<img src="{$tsConfig.images}/flags/{$u.user_pais}.png"/> <span class="grey">{$u.p_mensaje}</span>
        		</div>
        	</div>
        </li>
        {/foreach}
    </ul>
    {else}
    <div class="emptyData">No tiene medallas</div>
    {/if}    
</div>