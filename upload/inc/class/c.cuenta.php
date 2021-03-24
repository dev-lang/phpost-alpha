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
class tsCuenta extends tsDatabase {

	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsCuenta();
    	}
		return $instance;
	}
    /**
     * @name loadPerfil()
     * @access public
     * @uses Cargamos el perfil de un usuario
     * @param int
     * @return array
     */
	public function loadPerfil($user_id = 0){
		global$tsUser;
		//
		if(empty($user_id)) $user_id = $tsUser->uid;
		//
		$query = $this->query("SELECT p.*, u.user_registro, u.user_lastactive FROM u_perfil AS p LEFT JOIN u_miembros AS u ON p.user_id = u.user_id WHERE p.user_id = {$user_id} LIMIT 1");
		$perfilInfo = $this->fetch_assoc($query);
        $this->free($query);
		// CAMBIOS
        $perfilInfo = $this->unData($perfilInfo);
		// PORCENTAJE
        $total = unserialize($perfilInfo['p_total']);
		$perfilInfo['porcentaje'] = $this->getPorcentVal($total);
		//
		return $perfilInfo;
	}
    /*
        loadExtras()
    */
    private function unData($data){
        //
		$data['p_gustos'] = unserialize($data['p_gustos']);
		$data['p_tengo'] = unserialize($data['p_tengo']);
		$data['p_idiomas'] = unserialize($data['p_idiomas']);
        //
		$data['p_socials'] = unserialize($data['p_socials']);
		$data['p_socials']['f'] = $data['p_socials'][0];
		$data['p_socials']['t'] = $data['p_socials'][1];
        //
        $data['p_configs'] = unserialize($data['p_configs']);
        //
        return $data;
    }
	/*
		loadHeadInfo($user_id)
	*/
	function loadHeadInfo($user_id){
		global $tsUser;
		// INFORMACION GENERAL
		$query = $this->query("SELECT u.user_id, u.user_name, u.user_registro, u.user_lastactive, u.user_baneado, p.user_sexo, p.user_pais, p.p_nombre, p.p_avatar, p.p_mensaje, p.p_socials, p.p_empresa FROM u_miembros AS u, u_perfil AS p WHERE u.user_id = $user_id AND p.user_id = $user_id");
		$data = $this->fetch_assoc($query);
        $this->free($query);
        //
		$data['p_socials'] = unserialize($data['p_socials']);
		$data['p_socials']['f'] = $data['p_socials'][0];
		$data['p_socials']['t'] = $data['p_socials'][1];
		//  STATS
		$query = $this->query("SELECT u.user_rango, u.user_seguidores, u.user_siguiendo, u.user_posts, u.user_comentarios, u.user_puntos, u.user_fotos, r.r_name, r.r_color FROM u_miembros AS u LEFT JOIN u_rangos AS r ON u.user_rango = r.rango_id WHERE user_id = {$user_id} LIMIT 1");
		$data['stats'] = $this->fetch_assoc($query);
        $this->free($query);
		// BLOQUEADO
        $query = $this->select("u_bloqueos","*","b_user = {$tsUser->uid} AND b_auser = {$user_id}","",1);
        $data['block'] = $this->fetch_assoc($query);
        $this->free($query);
        //
		return $data;
	}
	/*
		loadGeneral($user_id)
	*/
	function loadGeneral($user_id){
		global $tsCore;
		// SEGUIDORES
        $query = $this->query("SELECT f.follow_id, u.user_id, u.user_name FROM u_follows AS f LEFT JOIN u_miembros AS u ON f.f_user = u.user_id WHERE f.f_id = {$user_id} AND f.f_type = 1 ORDER BY f.f_date DESC LIMIT 21");
        $data['segs']['data'] = $this->fetch_array($query);
        $data['segs']['total'] = count($data['segs']['data']);
        $this->free($query);
		// SIGUIENDO
        $query = $this->query("SELECT f.follow_id, u.user_id, u.user_name FROM u_follows AS f LEFT JOIN u_miembros AS u ON f.f_id = u.user_id WHERE f.f_user = {$user_id} AND f.f_type = 1 ORDER BY f.f_date DESC LIMIT 21");
        $data['sigd']['data'] = $this->fetch_array($query);
        $data['sigd']['total'] = count($data['sigd']['data']);
        $this->free($query);
        // ULTIMAS FOTOS
        if(empty($_GET['pid'])){
            $query = $this->select("f_fotos","foto_id, f_title, f_url","f_user = {$user_id}","foto_id DESC","6");
            $data['fotos'] = $this->fetch_array($query);
            $total = count($data['fotos']);
            $data['fotos_total'] = $total;
            if($total < 6){
                for($i = $total; $i <= 5; $i++){
                    $data['fotos'][$i] = NULL;
                }
            }
            $this->free($query);
        }
        //
		return $data;
	}
    /*
        iFollow()
    */
    function iFollow($user_id){
        global $tsUser;
        // SEGUIR
        $query = $this->query("SELECT follow_id FROM u_follows WHERE f_id = {$user_id} AND f_user = {$tsUser->uid} AND f_type = 1 LIMIT 1");
		$data = $this->num_rows($query);
		$this->free($query);
        //
        return ($data > 0) ? true : false;
    }
    /*
        loadPosts($user_id)
    */
    function loadPosts($user_id){
        global $tsUser;
        //
        $query = $this->query("SELECT p.post_id, p.post_title, p.post_puntos, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND p.post_user = {$user_id} ORDER BY p.post_date DESC LIMIT 18");
        $data['posts'] = $this->fetch_array($query);
        $data['total'] = count($data['posts']);
        $this->free($query);
        // USUARIO
        $data['username'] = $tsUser->getUserName($user_id);
        //
        return $data;
    }
	/*
		savePerfil()
	*/
	function savePerfil(){
		global $tsCore, $tsUser;
		//
		$save = $_POST['save'];
		$maxsize = 1000;	// LIMITE DE TEXTO
		// GUARDAR...
		switch($save){
			case 1:
                // NUEVOS DATOS
				$perfilData = array(
					'email' => $_POST['email'],
					'pais' => $_POST['pais'],
					'estado' => $_POST['estado'],
					'sexo' => ($_POST['sexo'] == 'f') ? 0 : 1,
					'dia' => $_POST['dia'],
					'mes' => $_POST['mes'],
					'ano' => $_POST['ano'],
					'firma' => $_POST['firma'],
				);
                //
                $year = date("Y",time());
                // ANTIGUOS DATOS
                $query = $this->select("u_perfil","user_dia, user_mes, user_ano, user_pais, user_estado, user_sexo, user_firma","user_id = {$tsUser->uid}","",1);
                $info = $this->fetch_assoc($query);
                $this->free($query);
                //
                $email_ok = $this->isEmail($perfilData['email']);
                // CORRECCIONES
				if(!$email_ok){
					$msg_return = array('field' => 'email', 'error' => 'El formato de email ingresado no es v&aacute;lido.');
					// EL ANTERIOR
					$perfilData['email'] = $tsUser->info['user_email'];
				}
				elseif(!checkdate($perfilData['mes'],$perfilData['dia'],$perfilData['ano']) || ($perfilData['ano'] > $year || $perfilData['ano'] < ($year - 100))){
					$msg_return = array('error' => 'La fecha de nacimiento no es v&aacute;lida.');
					// LOS ANTERIORES
					$perfilData['mes'] = $info['user_mes'];
					$perfilData['dia'] = $info['user_dia'];
					$perfilData['ano'] = $info['user_ano'];
				}
				elseif($perfilData['sexo'] > 2){
					$msg_return = array('error' => 'Especifica un g&eacute;nero sexual.');
					$perfilData['sexo'] = $info['user_sexo'];
				}
				elseif(empty($perfilData['pais'])){
					$msg_return = array('error' => 'Por favor, especifica tu pa&iacute;s.');
					$perfilData['pais'] = $info['user_pais'];
				}
				elseif(empty($perfilData['estado'])){
					$msg_return = array('error' => 'Por favor, especifica tu estado.'.$_POST['estado']);
					$perfilData['estado'] = $info['user_estado'];
				}
				elseif($tsUser->info['user_email'] != $perfilData['email']) {
				    $query = $this->select("u_miembros","user_id","user_email = '{$perfilData['email']}'",'',1);
                    $exists = $this->num_rows($query);
                    $this->free($query);
                    if($exists) {
                        $msg_return = array('error' => 'Este email ya existe, ingresa uno distinto.');
                        $perfilData['email'] = $tsUser->info['user_email'];
                    }
					else $msg_return = array('error' => 'Los cambios fueron aceptados y ser&aacute;n aplicados en los pr&oacute;ximos minutos. NO OBSTANTE, la nueva direcci&oacute;n de correo electr&oacute;nico especificada debe ser comprobada. '.$tsCore->settings['titulo'].' envi&oacute; un mensaje de correo electr&oacute;nico con las instrucciones necesarias');
				}
			break;
			case 2:
				// EXTERNAS
				$facebook = $tsCore->setSecure($_POST['facebook']);
				$twitter = $tsCore->setSecure($_POST['twitter']);
				for($i = 0; $i < 5; $i++) $gustos[$i] = $tsCore->setSecure($_POST['g_'.$i]);
				// IN DB
				$perfilData = array(
					'nombre' => $tsCore->setSecure($_POST['nombrez']),
					'mensaje' => $tsCore->setSecure($_POST['mensaje']),
					'sitio' => $tsCore->setSecure($_POST['sitio']),
					'socials' => serialize(array($facebook,$twitter)),
					'gustos' => serialize($gustos),
					'estado' => $tsCore->setSecure($_POST['estado']),
					'hijos' => $tsCore->setSecure($_POST['hijos']),
					'vivo' => $tsCore->setSecure($_POST['vivo']),
				);
			break;
			case 3:
				// EXTRAS
				$tengo = array($tsCore->setSecure($_POST['t_0']),$tsCore->setSecure($_POST['t_1']));
				$perfilData = array(
					'altura' => $tsCore->setSecure($_POST['altura']),
					'peso' => $tsCore->setSecure($_POST['peso']),
					'pelo' => $tsCore->setSecure($_POST['pelo_color']),
					'ojos' => $tsCore->setSecure($_POST['ojos_color']),
					'fisico' => $tsCore->setSecure($_POST['fisico']),
					'dieta' => $tsCore->setSecure($_POST['dieta']),
					'tengo' => serialize($tengo),
					'fumo' => $tsCore->setSecure($_POST['fumo']),
					'tomo' => $tsCore->setSecure($_POST['tomo_alcohol']),
				);
			break;
			case 4:
				// EXTRAS
				for($i = 0; $i<7;$i++) $idiomas[$i] = $tsCore->setSecure($_POST['idioma_'.$i]);
				$perfilData = array(
					'estudios' => $tsCore->setSecure($_POST['estudios']),
					'idiomas' => serialize($idiomas),
					'profesion' => $tsCore->setSecure($_POST['profesion']),
					'empresa' => $tsCore->setSecure($_POST['empresa']),
					'sector' => $tsCore->setSecure($_POST['sector']),
					'ingresos' => $tsCore->setSecure($_POST['ingresos']),
					'int_prof' => $tsCore->setSecure(substr($_POST['intereses_profesionales'],0,$maxsize)),
					'hab_prof' => $tsCore->setSecure(substr($_POST['habilidades_profesionales'],0,$maxsize)),
				);
			break;
			case 5:
				$perfilData = array(
					'intereses' => $tsCore->setSecure(substr($_POST['intereses'],0,$maxsize)),
					'hobbies' => $tsCore->setSecure(substr($_POST['hobbies'],0,$maxsize)),
					'tv' => $tsCore->setSecure(substr($_POST['tv'],0,$maxsize)),
					'musica' => $tsCore->setSecure(substr($_POST['musica'],0,$maxsize)),
					'deportes' => $tsCore->setSecure(substr($_POST['deportes'],0,$maxsize)),
					'libros' => $tsCore->setSecure(substr($_POST['libros'],0,$maxsize)),
					'peliculas' => $tsCore->setSecure(substr($_POST['peliculas'],0,$maxsize)),
					'comida' => $tsCore->setSecure(substr($_POST['comida'],0,$maxsize)),
					'heroes' => $tsCore->setSecure(substr($_POST['heroes'],0,$maxsize)),
				);
			break;
            // NEW PASSWORD
            case 6:
                $passwd = $_POST['passwd'];
                $new_passwd = $_POST['new_passwd'];
                $confirm_passwd = $_POST['confirm_passwd'];
                if(empty($new_passwd) || empty($confirm_passwd)) return array('error' => 'Debes ingresar una contrase&ntilde;a.');
                elseif(strlen($new_passwd) < 5) return array('error' => 'Contrase&ntilde;a no v&aacute;lida.');
                elseif($new_passwd != $confirm_passwd) return array('error' => 'Tu nueva contrase&ntilde;a debe ser igual a la confirmaci&oacute;n de la misma.');
                else {
                    $key = md5(md5($passwd).strtolower($tsUser->nick));
                    if($key != $tsUser->info['user_password']) return array('error' => 'Tu contrase&ntilde;a actual no es correcta.');
                    else {
                        $new_key = md5(md5($new_passwd).strtolower($tsUser->nick));
                        if($this->update("u_miembros","user_password = '{$new_key}'","user_id = {$tsUser->uid}")) return true;
                    }
                }
            break;
            case 7:
                $muro_firm = ($_POST['muro_firm'] > 2) ? 3 : $_POST['muro_firm'];
                $array = array('m' => $_POST['muro'], 'mf' => $muro_firm);
                //
                $perfilData['configs'] = serialize($array);
            break;
		}
		// COMPROVAR PORCENTAJE
		$total = array(5,8,9,8,9); // CAMPOS EN CADA CATEGORIA
		$tid = $save - 1;
        if($save > 1 && $save < 6){
    		$total[$tid] = $this->getPorcentTotal($perfilData, $total[$tid]);
    		if($save == 1) $total[$tid] = $total[$tid] - 2;
    		$porcen = $this->fetch_assoc($this->select("u_perfil","p_total","user_id = {$tsUser->uid}","",1));
    		$porcen = unserialize($porcen['p_total']);
    		$porcen[$tid] = $total[$tid];
    		$porcenNow = $this->getPorcentVal($porcen);
    		$porcen = serialize($porcen);
    		$this->update("u_perfil","p_total = '$porcen'","user_id = {$tsUser->uid}");   
        }
		// ACTUALIZAR
		if($save == 1) {
			$this->update("u_miembros","user_email = '{$perfilData['email']}'","user_id = {$tsUser->uid}");
            array_splice($perfilData, 0, 1); // HACK
            $updates = $tsCore->getIUP($perfilData, 'user_');
            if(!$this->update("u_perfil",$updates,"user_id = {$tsUser->uid}")) return array('error' => $this->error());
		} else {
			$updates = $tsCore->getIUP($perfilData, 'p_');
			if(!$this->update("u_perfil",$updates,"user_id = {$tsUser->uid}")) return array('error' => $this->error());
		}
		//
		if(is_array($msg_return)) return $msg_return;
		else return array('porc' => $porcenNow);
	}
	/*
		checkEmail()
	*/
	function isEmail($email){
		if(preg_match("/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@([_a-zA-Z0-9-]+.)*[a-zA-Z0-9-]{2,200}.[a-zA-Z]{2,6}$/",$email)) return true;
		else return false;
	}
	/*
		getPorcentVal($array)
	*/
	function getPorcentVal($array){
		//
		$total = $array[0] + $array[1] + $array[2] + $array[3] + $array[4] + $array[5];
		return round((100 * $total) / 40);
	}
	/*
		getPorcentTotal($array, $total) // Recursividad xD
	*/
	function getPorcentTotal($array, $total){
		//
		foreach($array as $i => $val) { 
			$valt = unserialize($val);
			if(is_array($valt)) {
				$stotal = $this->getPorcentTotal($valt, count($valt));
				if(empty($stotal)) $total--;
			}
			elseif(empty($val)) $total--;
		}
		//
		return $total;
	}
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
							// MANEJAR IMAGES \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/*
		loadImages($user_id)
	*/
	function loadImages($user_id = 0){
		global $tsUser;
		//
		if(empty($user_id)) $user_id = $tsUser->uid;
		$images = $this->fetch_array($this->select("u_fotos","*","f_user = $user_id","",""));
		//
		return $images;
	}
	/*
		addImagen()
	*/
	function addImagen(){
		global $tsCore, $tsUser;
		//
		$img_url = $tsCore->setSecure(substr($_POST['url'],0,255));
		$img_cap = $tsCore->setSecure(substr($_POST['caption'],0,50));
		// INSERTAMOS
		if(empty($img_url) || $img_url == 'http://') return array('field' => 'url', 'error' => 'Ingresa la URL de la imagen.');
		else {
			$this->insert("u_fotos","f_user, f_url, f_caption", "{$tsUser->uid}, '$img_url', '$img_cap'");
			return array('id' => $this->insert_id(), 'field' => '', 'error' => '');
		}
	}
	/*
		delImagen()
	*/
	function delImagen(){
		global $tsCore, $tsUser;
		//
		$img_id = $tsCore->setSecure($_POST['id']);
		// BORRANDO
		$this->delete("u_fotos","foto_id = $img_id AND f_user = {$tsUser->uid}");
	}
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
							// MANEJAR BLOQUEOS \\
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function bloqueosCambiar(){
        global $tsCore, $tsUser;
        //
        $auser = $tsCore->setSecure($_POST['user']);
        $bloquear = empty($_POST['bloquear']) ? 0 : 1;
        // EXISTE?
        $exists = $tsUser->getUserName($auser);
        // SI EXISTE Y NO SOY YO
        if($exists && $tsUser->uid != $auser){
            if($bloquear == 1){
                // YA BLOQUEADO?
                $query = $this->select("u_bloqueos","bid","b_user = {$tsUser->uid} AND b_auser = {$auser}","",1);
                $noexists = $this->num_rows($query);
                $this->free($query);
                // NO HA SIDO BLOQUEADO
                if(empty($noexists)) {
                    if($this->insert("u_bloqueos","b_user, b_auser","{$tsUser->uid}, {$auser}"))
                    return "1: El usuario fue bloqueado satisfactoriamente."; 
                } else return '0: Ya has bloqueado a este usuario.';
                // 
            } else{
                if($this->delete("u_bloqueos","b_user = {$tsUser->uid} AND b_auser = {$auser}"))
                return "1: El usuario fue desbloqueado satisfactoriamente.";
            }   
        } else return '0: El usuario seleccionado no existe.';
    }
    /*
        loadBloqueos()
    */
    function loadBloqueos(){
        global $tsUser;
        //
        $query = $this->query("SELECT b.*, u.user_name FROM u_miembros AS u LEFT JOIN u_bloqueos AS b ON u.user_id = b.b_auser WHERE b.b_user = {$tsUser->uid}");
        $data = $this->fetch_array($query);
        $this->free($query);
        //
        return $data;
    }
}
?>
