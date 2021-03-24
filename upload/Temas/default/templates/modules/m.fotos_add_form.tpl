{
*********************************************************************************
* m.fotos_home_content.php	                                                        *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
                <div id="centroDerecha" style="width: 630px; float: left;">
                	<div class="">
                        <h2 style="font-size: 15px;">{if $tsAction == 'agregar'}Agregar nueva{else}Editar{/if} foto</h2>
                    </div>
                    <form name="add_foto" method="post" action="" enctype="multipart/form-data" id="foto_form" class="form-add-post" autocomplete="off">
                    <div class="loader">
                        <img src="{$tsConfig.default}/images/loading_bar.gif" /><br />
                        <h2>Cargando foto, espere por favor....</h2>
                    </div>
                    <div class="fade_out">
                        <ul class="clearbeta">
                            <li>
                            <label for="ftitle">T&iacute;tulo</label>
                            <span style="display: none;" class="errormsg"></span>
                            <input type="text" tabindex="1" name="titulo" id="ftitle" maxlength="40" class="text-inp required" value="{$tsFoto.f_title}"/>
                            </li>
                        {if $tsAction != 'editar'}
                            {if $tsConfig.c_allow_upload == 1}
                            <li>
                            <label for="ffile">Archivo</label>
                            <input type="file" name="file" id="ffile" />
                            </li>
                            {else}
                            <li>
                            <label for="furl">URL</label>
                            <span style="display: none;" class="errormsg"></span>
                            <input type="text" tabindex="2" name="url" id="furl" maxlength="200" class="text-inp required" value="{$tsFoto.f_url}"/>
                            </li>                            
                            {/if}
                        {/if}
                            <li>
                            <label for="fdesc">Descripci&oacute;n (<small>Max 500 car.</small>)</label>
                            <span style="display: none;" class="errormsg"></span>
                            <textarea name="desc" id="fdesc" cols="60" rows="5" onkeydown="return ControlLargo(this);" onkeyup="return ControlLargo(this);">{$tsFoto.f_description}</textarea>
                            </li>
                            <li>
                            <label>Opciones</label>
                            <div class="option clearbeta">  
                                <input type="checkbox" class="floatL" id="privada" name="privada"{if $tsFoto.f_access == 1} checked="true"{/if}/>
                                <p class="floatL">
                                    <label for="privada">S&oacute;lo usuarios registrados</label>
                                    Tu foto ser&aacute; vista s&oacute;lo por los usuarios que tengan cuenta en {$tsConfig.titulo}
                                </p>
                            </div>
                            <div class="option clearbeta">  
                                <input type="checkbox" class="floatL" id="sin_comentarios" name="closed"{if $tsFoto.f_closed == 1} checked="true"{/if}/>
                                <p class="floatL">
                                    <label for="sin_comentarios">Cerrar Comentarios</label>
                                    Si no quieres resibir comentarios en tu foto.
                                </p>
                            </div>
                            </li>
                        </ul>
                        <div class="end-form clearbeta">
                        	<input type="button" style="width: auto; margin-left: 5px;" class="mBtn btnGreen" name="new" value="{if $tsAction == 'agregar'}Agregar foto{else}Guardar cambios{/if}" onclick="fotos.agregar()"/>
                        </div>
                    </div>                    
                    </form>
                </div>
