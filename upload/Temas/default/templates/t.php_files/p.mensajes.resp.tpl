{
*********************************************************************************
* p.mensajes.lista.tpl                                                          *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************
}
1:
    <li>
        <div class="main clearBoth">
            <a href="{$tsConfig.url}/perfil/{$tsUser->nick}" class="autor-image"><img src="{$tsConfig.url}/files/avatar/{$tsUser->uid}_50.jpg" /></a>
            <div class="mensaje">
                <div><a href="{$tsConfig.url}/perfil/{$tsUser->nick}" class="autor-name">{$tsUser->nick}</a> <span class="mp-date">{$mp.mp_date|fecha:'d_Ms_a'}</span></div>
                <div>{$mp.mp_body|nl2br}</div>
            </div>
        </div>
    </li>