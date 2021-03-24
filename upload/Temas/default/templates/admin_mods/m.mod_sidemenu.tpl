{
*********************************************************************************
* m.mod_welcome.php 	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
                                    <script type="text/javascript">
										var action_menu = '{$tsAction}';
										//{literal} <-- no borrar
										$(function(){
											if(action_menu != '') $('#a_' + action_menu).addClass('active');
											else $('#a_main').addClass('active');
										});
                                        // {/literal}
									</script>
                                    <h4>Principal</h4>
                                    <ul class="cat-list">
                                        <li id="a_main"><span class="cat-title"><a href="{$tsConfig.url}/moderacion/">Centro de Moderaci&oacute;n</a></span></li>
                                    </ul>
                                    <h4>Moderaci&oacute;n</h4>
                                    <ul class="cat-list">
                                        <li id="a_posts"><span class="cat-title"><a href="{$tsConfig.url}/moderacion/posts">Post denunciados</a></span></li>
                                        <li id="a_mpreport"><span class="cat-title"><a href="{$tsConfig.url}/moderacion/mpreport">Mensajes denunciados</a></span></li>
                                        <li id="a_users"><span class="cat-title"><a href="{$tsConfig.url}/moderacion/users">Usuarios denunciados</a></span></li>
                                        <li id="a_banusers"><span class="cat-title"><a href="{$tsConfig.url}/moderacion/banusers">Usuarios suspendidos</a></span></li>
                                    </ul>