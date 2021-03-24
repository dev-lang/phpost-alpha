<?php
/********************************************************************************
* c.cuenta.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

/*

	CLASE CON LOS ATRIBUTOS Y METODOS PARA MANEJAR LA CUENTA
	
*/
class tsMedal extends tsDatabase {

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsMedal();
    	}
		return $instance;
	}
    /**
     * @name adGetMedals()
     * @access public
     * @uses Cargamos las medallas para la administracion
     * @param
     * @return array
     */
	public function adGetMedals(){
        //
		$query = $this->select("w_medallas","*","1","medal_id DESC");
		$medals = $this->fetch_array($query);
        $this->free($query);
        //
		return $medals;
	}
    /**
     * @name adNewMedal()
     * @access public
     * @uses Creamos nueva medalla
     * @param
     * @return void
     */
     public function adNewMedal(){
        // DATOS
        $titulo = $_POST['med_title'];
        $descripcion = $_POST['med_desc'];
        $imagen = $_POST['med_img'];
        $tipo = $_POST['med_type'];
        // INSERTAR
        if($this->insert("w_medallas","m_title, m_description, m_image, m_type","'{$titulo}', '{$descripcion}', '{$imagen}', {$tipo}")) return true;
        else return false;
     }
    /**
     * @name adNewMedal()
     * @access public
     * @uses Creamos nueva medalla
     * @param
     * @return void
     */
}
?>
