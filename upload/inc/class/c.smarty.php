<?php
/********************************************************************************
* c.smarty.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ?											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ?											*
*********************************************************************************/

/*
	SUBCLASE DE LA CLASE SMARTY
	
	METODOS EN ESTA CLASE:
	
	ofSmarty()
	getInstance()
	assign_hooks()
*/

require(TS_ROOT.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');


class tsSmarty extends Smarty
{
  var $_tpl_hooks;
  
  var $_tpl_hooks_no_multi = TRUE;
  
  
  
  function tsSmarty()
  {
    global $tsCore;
    //
    $this->template_dir = TS_ROOT.DIRECTORY_SEPARATOR.'Temas'.DIRECTORY_SEPARATOR.TS_TEMA.DIRECTORY_SEPARATOR.'templates';
    $this->compile_dir = TS_ROOT.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'templates_c';
    $this->cache_dir = TS_ROOT.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'cache';
    $this->config_dir = TS_ROOT.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'configs'; 
    $this->template_cb = array('url' => $tsCore->settings['url'], 'title' => $tsCore->settings['titulo']);
    //
    $this->_tpl_hooks = array();
  }
  
  
  
  function &getInstance()
  {
    static $instance;
    
    if( is_null($instance) )
    {
      $instance = new tsSmarty();
    }
    
    return $instance;
  }  
  
  function assign_hook($hook, $include)
  {
    if( !isset($this->_tpl_hooks[$hook]) )
      $this->_tpl_hooks[$hook] = array();
    
    if( $this->_tpl_hooks_no_multi && in_array($include, $this->_tpl_hooks[$hook]) )
      return;
    
    $this->_tpl_hooks[$hook][] = $include;
  }
}

?>