{
*********************************************************************************
* m.monitor_menu.php	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}

                <div class="menu-tabs clearfix">
                    <ul>
                        <li{if $tsAction == 'seguidores'} class="selected"{/if}><a href="{$tsConfig.url}/monitor/seguidores">Seguidores</a></li>
                        <li{if $tsAction == 'siguiendo'} class="selected"{/if}><a href="{$tsConfig.url}/monitor/siguiendo">Siguiendo</a></li>
                        <li{if $tsAction == 'posts'} class="selected"{/if}><a href="{$tsConfig.url}/monitor/posts">Posts</a></li>
                    </ul>
                </div>