<?php
/********************************************************************************
* c.db.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

/*

	CLASE PARA TRABAJAR CON LA BASE DE DATOS
	
	METODOS DE LA CLASE DB:
	
	tsdatabase()
	connect()
	query()
	select()
	update()
	replace()
	insert()
	delete()
	data()
	fetch_objects()
	fetch_assoc()
	num_rows()
	free()
	insert_id()
	error()
*/
class tsDatabase {
	var $dbhost;
	var $dbname;
	var $dbuser;
	var $dbpass;
	var $dbpersist;
	var $dblink;

	// ESTE ES EL CONSTRUCTOR
	// CONECTA Y SELECCIONA UNA BASE DE DATOS
	// INPUT: VOID
	// OUTPUT: $dblink
	function tsDatabase(){
		// DEFINICION DE VARIABLES
		$this->dbhost = db_host;
		$this->dbname = db_name;
		$this->dbuser = db_user;
		$this->dbpass = db_pass;
		$this->db_persist = db_persist;
		// CONECTAR
		$this->dblink = $this->connect();
		return $this->dblink;
		
	}
	// METODO PARA SELECCIONAR Y CONECTAR A LA BASE DE DATOS
	// INPUT: 
	// OUTPUT: instancia de la clase
	function &getInstance(){
		global $tsdb;
		static $database;
		
		if( !is_a($database, 'tsDatabase') ){
			// Backwards compatibility
			if( is_a($tsdb, 'tsDatabase') ){
				$database =& $tsdb;
			}
			// Instantiate
			else{
				$database = new tsDatabase();
				$tsdb =& $database;
			}
		}
		return $database;
	}

	// METODO PARA CONECTAR A LA BASE DE DATOS
	// INPUT: void
	// OUTPUT: $db_link
	function connect() {
		// CONEXION PERSISTENTE?
		if($this->db_persist) $db_link = mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpass);
		else $db_link = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass);
		// NO SE PUDO CONECTAR?
		if(empty($db_link)) die(N_ACCESS);
		// SELECCIONAR BASE DE DATOS
		mysql_select_db($this->dbname);
		// ASIGNAR CONDIFICACION
		mysql_query("set names 'utf8'");
		mysql_query("set character set utf8");
		// REGRESAR LA CONEXCION
		return $db_link;
	}
	// METODO PARA HACER UNA CONSULTA
	// INPUT: $query
	// OUTPUT: $result
	function query($q){
		global $tsCore;
		$tsCore->querys++;
		// HACIENDO CONSULTA Y RETORNANDO
		return mysql_query($q);
	}
	// METODO PARA HACER UN SELECT
	// INPUT: 
	// 		$table | NOMBRE DE LA TABLA
	//		$fields | CAMPOS A SELECCIONAR DE LA TABLA
	//		$where | CONDICION DE LA CONSULTA
	//		$order | ORDEN DE LOS RESULTADOS
	//		$limit | LIMITE DE RESULTADOS
	// OUTPUT: $result
	function select($table, $fields, $where = NULL, $order = NULL, $limit = NULL){
		global $tsCore;
		$tsCore->querys++;
		// CREANDO LA CONSULTA
		$q = 'SELECT '.$fields.' FROM '.$table;
		if($where) $q .= ' WHERE '.$where;
		if($order) $q .= ' ORDER BY '.$order;
		if($limit) $q .= ' LIMIT '.$limit;
		// HACIENDO CONSULTA Y RETORNANDO
		return mysql_query($q);
	}
	// METODO PARA HACER UN UPDATE
	// INPUT: 
	// 		$table | NOMBRE DE LA TABLA
	//		$pairs | CAMPOS Y VALORES A ACTUALIZAR
	//		$where | CONDICION DE LA CONSULTA
	// OUTPUT: status
	function update($table, $pairs, $where){
		global $tsCore;
		$tsCore->querys++;
		// DESCOMPONER CAMPOS DE UN ARRAY
		if(is_array($pairs)) $fields = implode(", ", $pairs);
		else $fields = $pairs;
		// ARMANDO CONSULTA
		$q = 'UPDATE '.$table.' SET '.$fields.' WHERE '.$where;
		// REALIZANDO CONSULTA
		$result = mysql_query($q);
		// RETORNANDO ESTADO
		if($result) return true;
		else return false;
	}
	// METODO PARA HACER UN REPLACE
	// INPUT: 
	// 		$table | NOMBRE DE LA TABLA
	//		$fields | CAMPOS A REEMPLAZAR
	//		$values | VALORES A REEMPLAZAR
	// OUTPUT: status
	function replace($table, $fields, $values){
		global $tsCore;
		$tsCore->querys++;
		// ARMANDO CONSULTA
		$q = "REPLACE INTO $table ($fields) VALUES ($values)";
		// REALIZANDO CONSULTA
		$result = mysql_query($q);
		// RETORNANDO ESTADO
		if($result) return true;
		else return false;
	}
	// METODO PARA HACER UN INSERT
	// INPUT: 
	// 		$table | NOMBRE DE LA TABLA
	//		$fields | CAMPOS
	//		$values | VALORES
	// OUTPUT: status
	function insert($table, $fields, $values){
		global $tsCore;
		$tsCore->querys++;
		// ARMANDO CONSULTA
		$q = 'INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.')';
		// REALIZANDO CONSULTA
		$result = mysql_query($q);
		// RETORNANDO ESTADO
		if($result) return true;
		else return false;
	}
	// METODO PARA HACER UN DELETE
	// INPUT: 
	// 		$table | NOMBRE DE LA TABLA
	//		$where | CONDICION
	// OUTPUT: status
	function delete($table, $where){
		global $tsCore;
		$tsCore->querys++;
		// ARMANDO CONSULTA
		$q = 'DELETE FROM '.$table.' WHERE '.$where;
		// REALIZANDO CONSULTA
		$result = mysql_query($q);
		// RETORNANDO ESTADO
		if($result) return true;
		else return false;
	}
	// METODO PARA HACER UNA CONSULTA Y OBTENER OBJETOS
	// INPUT: 
	// 		$table | NOMBRE DE LA TABLA
	//		$fields | CAMPOS A SELECCIONAR DE LA TABLA
	//		$where | CONDICION DE LA CONSULTA
	//		$order | ORDEN DE LOS RESULTADOS
	//		$limit | LIMITE DE RESULTADOS
	// OUTPUT: $result
	function data($table, $fields, $where =NULL, $order = NULL, $limit = NULL){
		global $tsCore;
		$tsCore->querys++;
		// CREANDO LA CONSULTA
		$q = 'SELECT '.$fields.' FROM '.$table;
		if($where) $q .= ' WHERE '.$where;
		if($order) $q .= ' ORDER BY '.$order;
		if($limit) $q .= ' LIMIT '.$limit;
		// HACIENDO CONSULTA
		$result = mysql_query($q);
		// CREANDO Y RETORNANDO OBJETOS
		if($result) return mysql_fetch_object($result);
		else return false;
	}
	// METODO PARA CREAR OBJETOS DESDE UNA CONSULTA
	// INPUT: $result
	// OUTPUT: $objs
	function fetch_objects($result){
		if(!is_resource($result)) return false;
		while($obj = mysql_fetch_object($result)) $objs[] = $obj;
		return $objs;
	}
	// METODO PARA CREAR ARRAY DESDE UNA CONSULTA
	// INPUT: $result
	// OUTPUT: array
	function fetch_assoc($result){
		if(!is_resource($result)) return false;
		return mysql_fetch_assoc($result);
	}
	// METODO PARA CREAR ARRAY DESDE UNA CONSULTA
	// INPUT: $result
	// OUTPUT: array
	function fetch_array($result){
		if(!is_resource($result)) return false;
		while($row = mysql_fetch_assoc($result)) $array[] = $row;
		return $array;
	}
	// METODO PARA OBTENER EL VALOR DE UNA ROW
	// INPUT: $result
	// OUTPUT: array
	function fetch_row($result){
		return mysql_fetch_row($result);
	}
	// METODO PARA CONTAR EL NUMERO DE RESULTADOS
	// INPUT: $result
	// OUTPUT: num_rows	
	function num_rows($result){
		if(!is_resource($result)) return false;
		return mysql_num_rows($result);
	}
	// METODO PARA LIBERAR MEMORIA
	// INPUT: $result
	// OUTPUT: void	
	function free($result){
		return mysql_free_result($result);
	}
	// METODO PARA RETORNAR EL ULTIMO ID DE UN INSERT
	// INPUT: void
	// OUTPUT: status
	function insert_id(){
	  global $tsCore;
	  $tsCore->querys++;
	  return mysql_insert_id($this->dblink);
	}
	// METODO PARA RETORNAR LOS ERRORES
	// INPUT: void
	// OUTPUT: status
	function error(){
	  return mysql_error($this->dblink);
	}
}
?>
