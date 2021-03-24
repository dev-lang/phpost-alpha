<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{$tsTitle}</title>
<link href="{$tsConfig.tema.t_url}/estilo.css" rel="stylesheet" type="text/css" />
<link href="{$tsConfig.tema.t_url}/phpost.css" rel="stylesheet" type="text/css" />
<link href="{$tsConfig.tema.t_url}/extras.css" rel="stylesheet" type="text/css" />
<!-- AGREGAMOS UN ESTILO EXTRA SI EXISTE -->
<link href="{$tsConfig.css}/{$tsPage}.css" rel="stylesheet" type="text/css" />

<link rel="shortcut icon" href="{$tsConfig.images}/favicon.ico" type="image/x-icon" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="{$tsConfig.js}/jquery.plugins.js" type="text/javascript"></script>
<script src="{$tsConfig.js}/acciones.js" type="text/javascript"></script>
<script src="{$tsConfig.js}/funciones.js" type="text/javascript"></script>
{if $tsUser->is_admod}<script src="{$tsConfig.js}/moderacion.js" type="text/javascript"></script>{/if}
{if $tsConfig.c_allow_live}
<link href="{$tsConfig.css}/live.css" rel="stylesheet" type="text/css" />
<script src="{$tsConfig.js}/live.js" type="text/javascript"></script>
{/if}
<script type="text/javascript">
// {literal}
var global_data={
// {/literal}
	user_key:'{$tsUser->uid}',
	postid:'{$tsPost.post_id}',
	fotoid:'{$tsFoto.foto_id}',
	img:'{$tsConfig.tema.t_url}/',
	url:'{$tsConfig.url}',
	domain:'{$tsConfig.domain}',
    s_title: '{$tsConfig.titulo}',
    s_slogan: '{$tsConfig.slogan}'
// {literal}
};
// {/literal} {literal}
$(document).ready(function(){
// {/literal}
    {if $tsNots > 0} 
	notifica.popup({$tsNots});
    {/if}
    {if $tsMPs > 0 &&  $tsAction != 'leer'}
    mensaje.popup({$tsMPs});
    {/if}
// {literal}
});
//	{/literal}
</script>

</head>

<body>
<!--JAVASCRIPT-->
<div id="swf"></div>
<div id="js" style="display:none"></div>
<div id="mask"></div>
<div id="mydialog"></div>
<div class="UIBeeper" id="BeeperBox"></div>
<div id="brandday">
    <div class="rtop"></div>
    <div id="maincontainer">
    	<!--MAIN CONTAINER-->
        <div id="head">
        	<div id="logo">
            	<a id="logoi" title="{$tsConfig.titulo}" href="{$tsConfig.url}">
                	<img border="0" align="top" title="{$tsConfig.titulo}" alt="{$tsConfig.titulo}" src="http://o2.t26.net/images/space.gif">
                </a>
            </div>
            <div id="banner">
                {if $tsPage == 'posts' && $tsPost.post_id}
                    {include file='modules/m.global_search.tpl'}
                {else}
                    {include file='modules/m.global_ads_468.tpl'}
                {/if}
            </div>
        </div>
        <div id="contenido_principal">
        {include file='sections/head_menu.tpl'}
        {include file='sections/head_submenu.tpl'}
        {include file='sections/head_noticias.tpl'}
        <div id="cuerpocontainer">
        <!--Cuperpo-->