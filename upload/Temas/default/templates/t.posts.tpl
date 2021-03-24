{
********************************************************************************
* t.posts.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
{include file='sections/main_header.tpl'}
				<a name="cielo"></a>
                {if $tsPost.post_status == 1 && $tsUser->is_admod == 2}
                    <div class="emptyData">Este post se encuentra inactivo por acomulaci&oacute;n de denuncias, tu puedes verlo porque eres Moderador.</div><br />
                {elseif $tsPost.post_status == 2 && $tsUser->is_admod == 1}
                    <div class="emptyData">Este post fue eliminado, tu puedes verlo porque eres Administrador. Para eliminarlo definitivamente clic <a href="#">aqu&iacute;</a>.</div><br />
                {/if}
				<div class="post-wrapper">
                	{include file='modules/m.posts_autor.tpl'}
                    {include file='modules/m.posts_content.tpl'}
                    <div class="floatR" style="width: 766px;">
                    	{include file='modules/m.posts_related.tpl'}
                        {include file='modules/m.posts_banner.tpl'}
                        <div class="clearfix"></div>
                    </div>
                    <a name="comentarios"></a>
                    {include file='modules/m.posts_comments.tpl'}
                    <a name="comentarios-abajo"></a>
                    <br />
                   	{if !$tsUser->is_member}
                    <div class="emptyData clearfix">
                    	Para poder comentar necesitas estar <a onclick="registro_load_form(); return false" href="">Registrado.</a> O.. ya tienes usuario? <a onclick="open_login_box('open')" href="#">Logueate!</a>
                    </div>
                    {elseif $tsPost.block > 0}
                    <div class="emptyData clearfix">
                    	&iquest;Te has portado mal? {$tsPost.user_name} te ha bloqueado y no podr&aacute;s comentar sus post.
                    </div>
                    {/if}
                    <div style="text-align:center"><a class="irCielo" href="#cielo"><strong>Ir al cielo</strong></a></div>
                </div>
                <div style="clear:both"></div>
                
{include file='sections/main_footer.tpl'}