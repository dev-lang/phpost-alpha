<?php
/********************************************************************************
* c.user.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/

/*

	CLASE CON LOS ATRIBUTOS Y METODOS PARA MANEJAR A LOS USUARIOS
	
*/
class tsUser extends tsDatabase {
	
	var $info = array();	// SI EL USUARIO ES MIEMBRO CARGAMOS DATOS DE LA TABLA
	var $is_member = 0;		// EL USUARIO ESTA LOGUEADO?
    var $is_admod = 0;
    var $is_banned = 0;
	var $nick = 'Visitante';// NOMBRE A MOSTRAR
	var $uid = 0;			// USER ID
	var $is_error;			// SI OCURRE UN ERROR ESTA VARIABLE CONTENDRA EL NUMERO DE ERROR
	var $cookieName;

	function tsUser(){
		global $tsdb, $tsCore;
		/* CARGAR SESSION */
		$this->cookieName = TSCookieName.'_'.str_replace(".","_",$tsCore->settings['dominio']);
		$this->setSession();
		// ACTUALIZAR PUNTOS POR DIA :D SOLO A REGISTRADOS
		if(!empty($this->is_member)) $this->actualizarPuntos();
	}
	/*
		actualizarPuntos()
		: CASI 2 HORAS PARA PODER CREAR ESTA FUNCION D: 
		: SI QUE ERA DIFICIL XD
	*/
	function actualizarPuntos(){
		global $tsdb, $tsCore;
		// HORA EN LA CUAL RECARGAR PUNTOS 0 = MEDIA NOCHE DEL SERVIDOR
		$ultimaRecarga = $this->info['user_nextpuntos'];
		$tiempoActual = time();
		// SI YA SE PASO EL TIEMPO RECARGAMOS...
		if($ultimaRecarga < $tiempoActual){
			// OPERACION SIG RECARGA A LAS 24 HRS
			$sigRecarga = 24 - $horaActual;
			$sigRecarga = $sigRecarga * 3600;
			$sigRecarga = $tiempoActual + $sigRecarga;
			// LA SIGUIENTE RECARGA SE HARA A LAS 24 o MEDIA NOCHE DEL SIGUEINTE DIA o HASTA Q VUELVA A INICIAR SESION
			//
			$query = $tsdb->select("u_rangos","r_user_points","rango_id = {$this->info['user_rango']}","",1);
			$puntos = $tsdb->fetch_assoc($query);
			$tsdb->free($query);
			//
			$tsdb->update("u_miembros","user_puntosxdar = {$puntos['r_user_points']}, user_nextpuntos = {$sigRecarga}","user_id = {$this->uid}");
			// VAMONOS
			return true;
		}	
	}
	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsUser();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
							// MANEJAR SESSIONES \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		CARGA LA SESSION
		setSession()
	*/
	function setSession(){
		global $tsCore;
		/* CARGAR DESDE $_SESSION */
		if(!empty($_SESSION['tsUser'])){			// SI ESTA CARGADA LA SESION DEL USUARIO
			$this->loadUser($_SESSION['tsUser']);
		}
		/* HACER LOGIN DESDE COOKIE */
		elseif(isset($_COOKIE[$this->cookieName]) && empty($this->is_member)){ // TAL VEZ ES UNA COOKIE 
	      $tsv = unserialize(base64_decode($_COOKIE[$this->cookieName]));// CARGAR DATOS DESDE LA COOKIE
	      $this->loginUser($tsCore->koxDecode($tsv['tsUser']), $tsv['tsKey'],$tsv['tsRem']);// HACER EL LOGIN
		}
		else $_SESSION['tsUser'] = 0;
	}
	/*
		CARGAR USUARIO POR SU ID
		loadUser()
	*/
	function loadUser($tsUserID, $session = false){
		global $tsdb,$tsCore;
		/* CONSULTA */
		$query = $tsdb->select("u_miembros","*","user_id = $tsUserID","",1); // CONSULTA
		if($tsdb->num_rows($query) == 0) return false; // EL USUARIO NO EXISTE
		/* CARGAMOS DATOS */
		$tsArray = $tsdb->fetch_assoc($query);
        // PERMISOS SEGUN RANGO
        $query = $tsdb->select("u_rangos","r_name, r_color, r_image, r_special, r_allows","rango_id = {$tsArray['user_id']}","",1);
        $tsArray['rango'] = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        $tsArray['r_allows'] = unserialize($data['r_allows']);
        //
		$this->info = $tsArray;	// CARGAMOS EL ARRAY A LA VARIABLE
		/* SESSION */
		if($session == true) $_SESSION['tsUser'] = $tsArray['user_id'];	// CARGAR EN $_SESSION
		/* ES MIEMBRO */
		$this->is_member = 1;
        $this->is_admod = ($tsArray['user_rango'] <= 2) ? $tsArray['user_rango'] : 0;
		// NOMBRE
		$this->nick = $tsArray['user_name'];
		$this->uid = $tsArray['user_id'];
        $this->is_banned = $tsArray['user_baneado'];
		// ULTIMA ACCION 
		$tsdb->update("u_miembros","user_lastactive = unix_timestamp()","user_id = {$this->uid}");
		//
		return true;
	}
	/*
		HACEMOS LOGIN
		loginUser($username, $password, $remember = false, $redirectTo = NULL);
	*/
	function loginUser($username, $password, $remember = false, $redirectTo = NULL){
		global $tsdb,$tsCore;
		
		/* ARMAR VARIABLES */
		$username = strtolower($username);	// ARMAR VARIABLES
		if(strlen($password) == 32 ) $md5pwd = $password;	// KEY
		else $md5pwd = md5(md5($password).strtolower($username));	// CONVERTIMOS A MD5
		/* CONSULTA */
		$query = $tsdb->select("u_miembros","user_id, user_activo, user_baneado","LOWER(user_name) = '$username' AND user_password = '$md5pwd'","","1");	
		if($tsdb->num_rows($query) == 0) {	//
			return '0: Tus datos son incorrectos';
		} else {
			$tsUData = $tsdb->fetch_assoc($query);
			if($tsUData['user_activo'] == 1){
				/* CARGAMOS */
				if($this->loadUser($tsUData['user_id'],true)) {	// CARGAR SESSION
					$tsdb->update("u_miembros","user_lastlogin = ".time()."","user_id = {$tsUData['user_id']}");	// LAST LOGIN
					/* COOKIE */
					if($remember == true){
					  $cookie = base64_encode(serialize(array('tsUser'=>$tsCore->koxEncode($username),'tsKey'=>$md5pwd,'tsRem'=>$remember)));	// CREAR COOKIE
                      $domain = '.'.$tsCore->getDomain();
					  setcookie($this->cookieName,$cookie,time()+16070400, '/'); // GENERAR COOKIE
					}
                    /* REGISTAR IP */
                    $user_ip = $tsCore->getIP();
                    $tsdb->update("u_miembros","user_last_ip = '{$user_ip}'","user_id = {$tsUData['user_id']}");
					/* REDERIGIR */
					if($redirectTo != NULL) $tsCore->redirectTo($redirectTo);	// REDIRIGIR
					else return true;
				} else return '0: Error inesperado...';				
			} else return '0: Debes activar tu cuenta';
		}
	}
	/*
		CERRAR SESSION
		logoutUser($redirectTo)
	*/
	function logoutUser($redirectTo = '/'){
		global $tsCore;
		/* BORRAR COOKIE */
		setcookie($this->cookieName,'', time()-3600,'/');
		/* BORRAR LA SESSION */
		$_SESSION['tsUser'] = 0;
		/* LIMPIAR VARIABLES */
		$this->info = '';
		$this->is_member = 0;
		/* REDERIGIR */
		if($redirectTo != NULL) $tsCore->redirectTo($redirectTo);	// REDIRIGIR
		else return true;
	}
	/*
		userActivate()
	*/
	function userActivate($tsUserID = 0, $tsKey = 0){
		global $tsdb,$tsCore;
		//
		if(empty($tsUserID)) $tsUserID = $tsCore->setSecure($_GET['uid']);
		if(empty($tsKey)) $tsKey = $tsCore->setSecure($_GET['key']);
		//
		$query = $tsdb->select("u_miembros","user_name, user_password, user_registro","user_id = $tsUserID","",1);
		$tsData = $tsdb->fetch_assoc($query);	// CARGAMOS DATOS
		$tsKeyLocal = md5($tsData['user_registro']);
		//
		if($tsdb->num_rows($query) == 0 || $tsKey != $tsKeyLocal){
			return false;
		} else {
			if($tsdb->update("u_miembros","user_activo = 1","user_id = $tsUserID")) {
				$tsdb->update("w_stats","stats_miembros = stats_miembros + 1","stats_no = 1");
				//if($tsdb->insert("u_perfil","user_id","$tsUserID")) 
                return $tsData;
			}
			else return false;
		}
	}
    /*
        getUserBanned()
    */
    function getUserBanned(){
        global $tsdb;
        //
        $query = $tsdb->select("u_suspension","*","user_id = {$this->uid}","",1);
        $data = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
        $now = time();
        //
        if($data['end_date'] < $now){
            $tsdb->update("u_miembros","user_baneado = 0","user_id = {$this->uid}");
            $tsdb->delete("u_suspension","user_id = {$this->uid}");
            return false;
        } else return $data;
    }
	/*
		getUserID($tsUsername)
	*/
	function getUserID($tsUser){
		global $tsdb;
		//
		$tsUsername = strtolower($tsUser);
		$query = $tsdb->select("u_miembros","user_id","LOWER(user_name) = '$tsUsername'","",1);
		$tsUser = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
		$tsUserID = $tsUser['user_id'];
		//
		return empty($tsUserID) ? 0 : $tsUserID;
	}
	/*
        getUserName($user_id)
    */
    function getUserName($user_id){
		global $tsdb;
		//
		$query = $tsdb->select("u_miembros","user_name","user_id = {$user_id}","",1);
		$tsUser = $tsdb->fetch_assoc($query);
        $tsdb->free($query);
        //
		return $tsUser['user_name'];
    }
    /**
     * @name iFollow
     * @access public
     * @param int
     * @return void
     */
    public function iFollow($user_id){
        # SIGO A ESTE USUARIO
        $query = $this->query("SELECT follow_id FROM u_follows WHERE f_id = {$user_id} AND f_user = {$this->uid} AND f_type = 1 LIMIT 1");
		$data = $this->num_rows($query);
		$this->free($query);
        //
        return ($data > 0) ? true : false;
    }
    /**
     * @name getVCard
     * @access public
     * @param int
     * @return array
     * @info OBTIENE LA INFORMACION DE UN USUARIO PARA UNA VCARD
     */
    public function getVCard($user_id){
        # GLOBALES
        global $tsCore;
        # LOCALES
        $is_online = (time() - ($tsCore->settings['c_last_active'] * 60));
        $is_inactive = (time() - (($tsCore->settings['c_last_active'] * 60) * 2)); // DOBLE DEL ONLINE
		// INFORMACION GENERAL
		$query = $this->query("SELECT u.user_id, u.user_name, u.user_lastactive, u.user_baneado, p.user_sexo, p.user_pais, p.p_nombre, p.p_mensaje, p.p_sitio FROM u_miembros AS u, u_perfil AS p WHERE u.user_id = {$user_id} AND p.user_id = {$user_id}");
		$data = $this->fetch_assoc($query);
        $this->free($query);
		//  STATS
		$query = $this->query("SELECT u.user_seguidores, u.user_posts, u.user_comentarios, u.user_puntos, r.r_name, r.r_color, r.r_image FROM u_miembros AS u LEFT JOIN u_rangos AS r ON u.user_rango = r.rango_id WHERE user_id = {$user_id} LIMIT 1");
		$data['stats'] = $this->fetch_assoc($query);
        $this->free($query);
        // STATUS
        if($data['user_lastactive'] > $is_online) $data['status'] = array('t' => 'Online', 'css' => 'online');
        elseif($data['user_lastactive'] > $is_inactive) $data['status'] = array('t' => 'Inactivo', 'css' => 'inactive');
        else $data['status'] = array('t' => 'Offline', 'css' => 'offline');
		// SIGUIENDO
        $data['follow'] = $this->iFollow($user_id);
        //
		return $data;
    }
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
							// FUNCIONES EXTERNAS \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
        getUsuarios()
    */
    function getUsuarios(){
        global $tsdb, $tsCore;
        // FILTROS ||
        $is_online = (time() - ($tsCore->settings['c_last_active'] * 60));
        $is_inactive = (time() - (($tsCore->settings['c_last_active'] * 60) * 2)); // DOBLE DEL ONLINE
        // ONLINE?
        if($_GET['online'] == 'true'){
            $w_online = "AND u.user_lastactive > {$is_online}";
        }
        // CON FOTO
        if($_GET['avatar'] == 'true'){
            $w_avatar = "AND p.p_avatar = 1";
        }
        // SEXO
        if(!empty($_GET['sexo'])){
            $sex = ($_GET['sexo'] == 'f') ? 0 : 1;
            $w_sex = "AND p.user_sexo = '{$sex}'";
        }
        // PAIS
        if(!empty($_GET['pais'])){
            $pais = $tsCore->setSecure($_GET['pais']);
            $w_pais = "AND p.user_pais = '{$pais}'";
        }
        // STAFF
        if(!empty($_GET['rango'])){
            $rango = (int) $tsCore->setSecure($_GET['rango']);
            $w_rango = "AND u.user_rango = {$rango}";
        }
        // TOTAL Y PAGINAS
        $query = $tsdb->query("SELECT COUNT(u.user_id) AS total FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id WHERE u.user_activo = 1 AND u.user_baneado = 0 {$w_online} {$w_avatar} {$w_sex} {$w_pais} {$w_rango}");
        $total = $tsdb->fetch_assoc($query);
        $total = $total['total'];
        $tsdb->free($query);
        $pages = $tsCore->getPagination($total, 12);
        // CONSULTA
        $query = $tsdb->query("SELECT u.user_id, u.user_name, p.user_pais, p.user_sexo, p.p_avatar, p.p_mensaje, u.user_rango,u.user_posts, u.user_puntos, u.user_comentarios, u.user_lastactive, u.user_baneado FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id WHERE u.user_activo = 1 AND u.user_baneado = 0 {$w_online} {$w_avatar} {$w_sex} {$w_pais} {$w_rango} ORDER BY u.user_id DESC LIMIT {$pages['limit']}");
        // PARA ASIGNAR SI ESTA ONLINE HACEMOS LO SIGUIENTE
        while($row = mysql_fetch_assoc($query)){
            if($row['user_lastactive'] > $is_online) $row['status'] = array('t' => 'Online', 'css' => 'online');
            elseif($row['user_lastactive'] > $is_inactive) $row['status'] = array('t' => 'Inactivo', 'css' => 'inactive');
            else $row['status'] = array('t' => 'Offline', 'css' => 'offline');
            // RANGO
    		$queryD = $tsdb->query("SELECT r_name, r_color, r_image FROM u_rangos WHERE rango_id = {$row['user_rango']} LIMIT 1");
            $r = $tsdb->fetch_assoc($queryD);
    		$row['rango'] = array('title' => $r['r_name'], 'color' => $r['r_color'], 'image' => $r['r_image']);
    		$tsdb->free($queryD);
            // CARGAMOS
            $data[] = $row; 
        }
        $tsdb->free($query);
        // ACTUALES
        $total = explode(',',$pages['limit']);
        $total = ($total[0]) + count($data);
        //
        return array('data' => $data, 'pages' => $pages, 'total' => $total);
    }
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
}
?>
