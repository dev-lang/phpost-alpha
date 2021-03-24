{
*********************************************************************************
* p.registro.form.tpl                                                           *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
Raz&oacute;n para borrar este post:<br /><br />
<select id="razon" onchange="if($(this).val() == 13) $('input[name=razon_desc]').show();">
{foreach from=$tsDenuncias item=d key=i}
    {if $d}<option value="{$i}">{$d}</option>{/if}
{/foreach}
</select><br /><br />
<input type="text" name="razon_desc" maxlength="150" size="35" style="display:none" />