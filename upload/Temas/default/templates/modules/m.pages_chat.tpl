{
*********************************************************************************
* m.pages_chat.tpl                                                              *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
<div style="width:630px" class="floatL">
	<div class="box_title">
		<div class="box_txt chat">Chat {$tsConfig.titulo}</div>
		<div class="box_rrs">
			<div class="box_rss"></div> 
		</div>
	</div>
	<div class="box_cuerpo">	
        {if $tsConfig.chat_id}<embed src="http://{$tsConfig.chat_id}.chatango.com/group" width="615" height="472" wmode="transparent" allowScriptAccess="always" allowNetworking="all" type="application/x-shockwave-flash" allowFullScreen="true" flashvars="cid={$tsConfig.chat_id}&v=0&w=0"></embed>
        {else}
        <div class="emptyData">Estamos por agregar el chat para que todos ustedes se puedan divertir y hacer nuevos amigos.</div>
        {/if}		
	</div>
</div>
<div style="width:300px" class="floatR">
    {include file='modules/m.global_ads_300.tpl'}
    {if $tsConfig.chat_id}<br />
    {include file='modules/m.global_ads_300.tpl'}
    {/if}
</div>