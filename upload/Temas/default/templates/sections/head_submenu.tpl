		<div class="subMenuContent">
        	<div id="subMenuPosts" class="subMenu {if $tsPage != 'tops'}here{/if}">
                <ul class="floatL tabsMenu">
                    <li{if $tsPage == 'home' || $tsPage == 'portal'} class="here"{/if}><a title="Inicio" href="{$tsConfig.url}/{if $tsPage == 'home' || $tsPage == 'posts'}posts/{/if}">Inicio</a></li>
                    <li{if $tsPage == 'buscador'} class="here"{/if}><a title="Buscador" href="{$tsConfig.url}/buscador/">Buscador</a></li>
                    {if $tsUser->is_member}
                    <li{if $tsSubmenu == 'agregar'} class="here"{/if}><a title="Agregar Post" href="{$tsConfig.url}/agregar/">Agregar Post</a></li>
                    <li class="{if $tsPage == 'mod-history'}here{/if}"><a title="Historial de Moderaci&oacute;n" href="{$tsConfig.url}/mod-history/">Historial</a></li>
        	            {if $tsUser->info.user_rango == 1 || $tsUser->info.user_rango == 2}
                    <li class="{if $tsPage == 'moderacion'}here{/if}"><a title="Moderaci&oacute;n" href="{$tsConfig.url}/moderacion/">Moderaci&oacute;n</a></li>
                    	{/if}
                    {/if}
                    <div class="clearBoth"></div>
                </ul>
                {include file='sections/head_categorias.tpl'}
                <div class="clearBoth"></div>
            </div>
            <div id="subMenuFotos" class="subMenu {if $tsPage == 'fotos'}here{/if}">
                <ul class="floatL tabsMenu">
                    <li{if $tsAction == '' && $tsAction != 'agregar' && $tsAction != 'album' && $tsAction != 'favoritas' || $tsAction == 'ver'} class="here"{/if}><a href="{$tsConfig.url}/fotos/">Inicio</a></li>
                    {if $tsAction == 'album' && $tsFUser.0 != $tsUser->uid}<li class="here"><a href="{$tsConfig.url}/fotos/{$tsFUser.1}">&Aacute;lbum de {$tsFUser.1}</a></li>{/if}
                    <li{if $tsAction == 'agregar'} class="here"{/if}><a href="{$tsConfig.url}/fotos/agregar.php">Agregar Foto</a></li>
                    <li{if $tsAction == 'album' && $tsFUser.0 == $tsUser->uid} class="here"{/if}><a href="{$tsConfig.url}/fotos/{$tsUser->nick}">Mis Fotos</a></li>
                </ul>
                <div class="clearBoth"></div>
            </div>
            <div id="subMenuTops" class="subMenu {if $tsPage == 'tops'}here{/if}">
                <ul class="floatL tabsMenu">
                    <li{if $tsAction == 'posts'} class="here"{/if}><a href="{$tsConfig.url}/top/posts/">Posts</a></li>
                    <li{if $tsAction == 'usuarios'} class="here"{/if}><a href="{$tsConfig.url}/top/usuarios/">Usuarios</a></li>
                </ul>
                <div class="clearBoth"></div>
            </div>
        </div>