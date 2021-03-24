<?php
/********************************************************************************
* c.core.php 	                                                                *
*********************************************************************************
* TScript: Desarrollado por CubeBox ®											*
* ==============================================================================*
* Software Version:           TS 1.0 BETA          								*
* Software by:                JNeutron			     							*
* Copyright 2010:     		  CubeBox ®											*
*********************************************************************************/


/*

	CLASE CON LOS ATRIBUTOS Y METODOS GLOBALES
	
	METODOS DE LA CLASE CORE:
	
	tsCore()
	getInstance()
	++++++++ = ++++++++
	getSettings()
	setLevel($tsLevel)
	redirect($tsDir)
	currentUrl()
	setJSON($tsContent)
	
*/
class tsCore {
	var $settings;		// CONFIGURACIONES DEL SITIO
	var $querys = 0;	// CONSULTAS

	function tsCore(){
		global $tsdb;
		// CARGANDO CONFIGURACIONES
		$this->settings = $this->getSettings();
		$this->settings['domain'] = str_replace('http://','',$this->settings['url']);
		$this->settings['categorias'] = $this->getCategorias();
        $this->settings['default'] = $this->settings['url'].'/Temas/default';
		$this->settings['tema'] = $this->getTema();
		$this->settings['images'] = $this->settings['tema']['t_url'].'/images';
        $this->settings['css'] = $this->settings['tema']['t_url'].'/css';
		$this->settings['js'] = $this->settings['tema']['t_url'].'/js';
        // CARGAR NOTICIAS
        $this->settings['news'] = $this->getNews();
	}
	// INSTANCIA DE LA CLASE
	function &getInstance(){
		static $instance;
		
		if( is_null($instance) ){
			$instance = new tsCore();
    	}
		return $instance;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	
	/*
		getSettings() :: CARGA DESDE LA DB LAS CONFIGURACIONES DEL SITIO
	*/
	function getSettings(){
		global $tsdb;
		// CONSULTA
		$query = $tsdb->select("w_configuracion","*","","",1);
		// RETORNAMOS
		return $tsdb->fetch_assoc($query);
	}
	/*
		getCategorias()
	*/
	function getCategorias(){
		global $tsdb;
		// CONSULTA
		$query = $tsdb->select("p_categorias","*","","c_orden","");
		// GUARDAMOS
		$categorias = $tsdb->fetch_array($query);
		$tsdb->free($query);
        //
        return $categorias;
		// SUBCATEGORIAS
		$i = 0;
		$ncats = count($categorias);
		foreach($categorias as $cat){
			$return[$i]['cat'] = $cat;
			$query = $tsdb->select("p_subcategorias","*","s_cat = {$cat['cid']}","s_orden","");
			$return[$i]['sub'] = $tsdb->fetch_array($query);
			$tsdb->free($query);
			$ncats = $ncats + count($return[$i]['sub']);
			$i++;
		}
		//
		$return['ncats'] = $ncats;
		//
		return $return;
	}
	/*
		getTema()
	*/
	function getTema(){
		global $tsdb;
		//
		$query = $tsdb->select("w_temas","*","tid = {$this->settings['tema_id']}","",1);
		//
		$data = $tsdb->fetch_assoc($query);
		$tsdb->free($query);
		//
		return $data;
	}
	/*
        getNews()
    */
    function getNews(){
        global $tsdb;
        //
        $query = $tsdb->select("w_noticias","not_body","not_active = 1","not_date DESC","");
		$data = $tsdb->fetch_array($query);
		$tsdb->free($query);
        //
        return $data;
    }
	/*
		setLevel($tsLevel) :: ESTABLECE EL NIVEL DE LA PAGINA | MIENBROS o VISITANTES
	*/
	function setLevel($tsLevel, $msg = false){
		global $tsUser;
		
		// CUALQUIERA
		if($tsLevel == 0) return true;
		// SOLO VISITANTES
		elseif($tsLevel == 1) {
			if($tsUser->is_member == 0) return true;
			else {
				if($msg) $mensaje = 'Esta pagina solo es vista por los visitantes.';
				else $this->redirect('/');
			}
		}
		// SOLO MIEMBROS
		elseif($tsLevel == 2){
			if($tsUser->is_member == 1) return true;
			else {
				if($msg) $mensaje = 'Para poder ver esta pagina debes iniciar sesi&oacute;n.';
				else $this->redirect('/login/?r='.$this->currentUrl());
			}
		}
		// SOLO MODERADORES
		elseif($tsLevel == 3){
			if($tsUser->is_admod) return true;
			else {
				if($msg) $mensaje = 'Estas en un area restringida solo para moderadores.';
				else $this->redirect('/login/?r='.$this->currentUrl());
			}
		}
		// SOLO ADMIN
		elseif($tsLevel == 4) {
			if($tsUser->is_admod == 1) return true;
			else {
				if($msg) $mensaje = 'Estas intentando algo no permitido.';
				else $this->redirect('/login/?r='.$this->currentUrl());
			}
		}
		//
		return array('titulo' => 'Error', 'mensaje' => $mensaje);
	}

	/*
		redirect($tsDir)
	*/
	function redirectTo($tsDir){
		$tsDir = urldecode($tsDir);
		header("Location: $tsDir");
		exit();
	}
    /*
        getDomain()
    */
    function getDomain(){
        $domain = explode('/',str_replace('http://','',$this->settings['url']));
        if(is_array($domain)) {
        $domain = explode('.',$domain[0]);
        } else $domain = explode('.',$domain);
        //
        $t = count($domain);
        $domain = $domain[$t - 2].'.'.$domain[$t - 1];
        //
        return $domain;
    }
	/*
		currentUrl()
	*/
	function currentUrl(){
		$current_url_domain = $_SERVER['HTTP_HOST'];
		$current_url_path = $_SERVER['REQUEST_URI'];
		$current_url_querystring = $_SERVER['QUERY_STRING'];
		$current_url = "http://".$current_url_domain.$current_url_path;
		$current_url = urlencode($current_url);
		return $current_url;
	}
	/*
		setJSON($tsContent)
	*/
	function setJSON($tsContent){
		include(TS_EXTRA . 'JSON.php');	// INCLUIMOS LA CLASE
		$json = new Services_JSON;	// CREAMOS EL SERVICIO
		return $json->encode($tsContent);
	}
	/*
		setPagesLimit($tsPages, $start = false)
	*/
	function setPageLimit($tsLimit, $start = false, $tsMax = 0){
		if($start == false)
		$tsStart = empty($_GET['page']) ? 0 : (int) (($_GET['page'] - 1) * $tsLimit);
		else {
    		$tsStart = (int) $_GET['s'];
            $continue = $this->setMaximos($tsLimit, $tsMax);
            if($continue == true) $tsStart = 0;
        }
		//
		return $tsStart.','.$tsLimit;
	}
    /*
        setMaximos()
        :: MAXIMOS EN LAS PAGINAS
    */
    function setMaximos($tsLimit, $tsMax){
        // MAXIMOS || PARA NO EXEDER EL NUMERO DE PAGINAS
        $ban1 = ($_GET['page'] * $tsLimit);
        if($tsMax < $ban1){
            $ban2 = $ban1 - $tsLimit;
            if($tsMax < $ban2) return true;
        } 
        //
        return false;
    }
	/*
		getPages($tsTotal, $tsLimit)
		: PAGINACION
	*/
	function getPages($tsTotal, $tsLimit){
		//
		$tsPages = ceil($tsTotal / $tsLimit);
		// PAGINA
		$tsPage = empty($_GET['page']) ? 1 : $_GET['page'];
		// ARRAY
		$pages['current'] = $tsPage;
		$pages['pages'] = $tsPages;
		$pages['section'] = $tsPages + 1;
		$pages['prev'] = $tsPage - 1;
		$pages['next'] = $tsPage + 1;
        $pages['max'] = $this->setMaximos($tsLimit, $tsTotal);
		// RETORNAMOS HTML
		return $pages;
	}
    /*
        getPagination($total, $per_page)
    */
    function getPagination($total, $per_page = 10){
        // PAGINA ACTUAL
        $page = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        // NUMERO DE PAGINAS
        $num_pages = ceil($total / $per_page);
        // ANTERIOR
        $prev = $page - 1;
        $pages['prev'] = ($page > 0) ? $prev : 0;
        // SIGUIENTE 
        $next = $page + 1;
        $pages['next'] = ($next <= $num_pages) ? $next : 0;
        // LIMITE DB
        $pages['limit'] = (($page - 1) * $per_page).','.$per_page; 
        // TOTAL
        $pages['total'] = $total;
        //
        return $pages;
    }
    /**/
	// Constructs a page list.
	// $pageindex = constructPageIndex($scripturl . '?board=' . $board, $_REQUEST['start'], $num_messages, $maxindex, true);
	function pageIndex($base_url, &$start, $max_value, $num_per_page, $flexible_start = false){
        // QUITAR EL S de la base_url
        $base_url = explode('&s=',$base_url);
        $base_url = $base_url[0];
		// Save whether $start was less than 0 or not.
		$start_invalid = $start < 0;
	
		// Make sure $start is a proper variable - not less than 0.
		if ($start_invalid)
			$start = 0;
		// Not greater than the upper bound.
		elseif ($start >= $max_value)
			$start = max(0, (int) $max_value - (((int) $max_value % (int) $num_per_page) == 0 ? $num_per_page : ((int) $max_value % (int) $num_per_page)));
		// And it has to be a multiple of $num_per_page!
		else
			$start = max(0, (int) $start - ((int) $start % (int) $num_per_page));
	
		$base_link = '<a class="navPages" href="' . ($flexible_start ? $base_url : strtr($base_url, array('%' => '%%')) . '&s=%d') . '">%s</a> ';
	
			// If they didn't enter an odd value, pretend they did.
			$PageContiguous = (int) (5 - (5 % 2)) / 2;
	
			// Show the first page. (>1< ... 6 7 [8] 9 10 ... 15)
			if ($start > $num_per_page * $PageContiguous)
				$pageindex = sprintf($base_link, 0, '1');
			else
				$pageindex = '';
	
			// Show the ... after the first page.  (1 >...< 6 7 [8] 9 10 ... 15)
			if ($start > $num_per_page * ($PageContiguous + 1))
				$pageindex .= '<b> ... </b>';
	
			// Show the pages before the current one. (1 ... >6 7< [8] 9 10 ... 15)
			for ($nCont = $PageContiguous; $nCont >= 1; $nCont--)
				if ($start >= $num_per_page * $nCont)
				{
					$tmpStart = $start - $num_per_page * $nCont;
					$pageindex.= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
				}
	
			// Show the current page. (1 ... 6 7 >[8]< 9 10 ... 15)
			if (!$start_invalid)
				$pageindex .= '[<b>' . ($start / $num_per_page + 1) . '</b>] ';
			else
				$pageindex .= sprintf($base_link, $start, $start / $num_per_page + 1);
	
			// Show the pages after the current one... (1 ... 6 7 [8] >9 10< ... 15)
			$tmpMaxPages = (int) (($max_value - 1) / $num_per_page) * $num_per_page;
			for ($nCont = 1; $nCont <= $PageContiguous; $nCont++)
				if ($start + $num_per_page * $nCont <= $tmpMaxPages)
				{
					$tmpStart = $start + $num_per_page * $nCont;
					$pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
				}
	
			// Show the '...' part near the end. (1 ... 6 7 [8] 9 10 >...< 15)
			if ($start + $num_per_page * ($PageContiguous + 1) < $tmpMaxPages)
				$pageindex .= '<b> ... </b>';
	
			// Show the last number in the list. (1 ... 6 7 [8] 9 10 ... >15<)
			if ($start + $num_per_page * $PageContiguous < $tmpMaxPages)
				$pageindex .= sprintf($base_link, $tmpMaxPages, $tmpMaxPages / $num_per_page + 1);
	
		return $pageindex;
	}
	/*
		setSecure()
	*/
	function setSecure($var, $html = false, $spaces = false){
	   //
	   return $var;
		// PASAR ESTA FUNCION A TODOS LOS ELEMENTOS DE UN ARRAY
		if(is_array($var)) {
			$var = array_map('$this->setSecure',$var, $html, $spaces);
		}else {
            // SLASHES
            $var = get_magic_quotes_gpc() ? stripslashes($var) : $var;
            $var = htmlspecialchars($var, ENT_QUOTES);
			// PERMITIR HTML
			if($html == false) $var = strip_tags($var);
			// ESPACIOS
			if($spaces) {
				// QUITAR TODOS LOS ESPACIOS
				if($spaces == 1) $var = preg_replace('# +#',"",$var); 
				// QUITAR MAS DE 2 ESPACIOS CONSECUTIVOS
				elseif($spaces == 2) $var = preg_replace('#  +#'," ",$var); 
			}
		}
		// RETORNAR VALOR
		return $var;
	}
    /*
        antiFlood()
    */
    public function antiFlood($print = true, $type = 'post', $msg = ''){
        global $tsdb, $tsUser;
        //
        $now = time();
        $msg = empty($msg) ? 'No puedes realizar tantas acciones en tan poco tiempo.' : $msg;
        //
        $limit = $this->settings['c_anti_flood'];
        $resta = $now - $_SESSION['flood'][$type];
        if($resta < $limit) {
            $msg = '0: '.$msg.' Int&eacute;ntalo en '.($limit - $resta).' segundos.';
            // TERMINAR O RETORNAR VALOR
            if($print) die($msg);
            else return $msg;
        }
        else {
            // ANTIFLOOD
            if(empty($_SESSION['flood'][$type])) {
                $_SESSION['flood'][$type] = time();
            } else $_SESSION['flood'][$type] = $now;
            // TODO BIEN
            return true;
        }
    }
	/*
		setSEO($string, $max) $max : MAXIMA CONVERSION
		: URL AMIGABLES
	*/
	function setSEO($string, $max = false) {
		// ESPAÑOL
		$espanol = array('á','é','í','ó','ú','ñ');
		$ingles = array('a','e','i','o','u','n');
		// MINUS
		$string = str_replace($espanol,$ingles,$string);
		$string = trim($string);
		$string = trim(preg_replace("/[^ A-Za-z0-9_]/", "-", $string));
		$string = preg_replace("/[ \t\n\r]+/", "-", $string);
		$string = str_replace(" ", "-", $string);
		$string = preg_replace("/[ -]+/", "-", $string);
		//
		if($max) {
			$string = str_replace('-','',$string);
			$string = strtolower($string);
		}
		//
		return $string;
	}
	/*
		parseBBCode($bbcode)
	*/
	function parseBBCode($bbcode, $type = 'normal'){
        // CLASS BBCode
        require_once(TS_EXTRA . "bbcode.inc.php");
        $parser =& BBCode::getInstance();
        switch($type){
            // NORMAL
            case 'normal':
                // CONVERTIMOS
                $html = $parser->parseString($bbcode);
                // SMILES
                $html = $parser->parseSmiles($html, $this->settings['default'].'/images/smiles/');
                // MENCIONES
                $html = $this->setMenciones($html.' ');       
            break;
            // FIRMA
            case 'firma':
                // BBCodes permitidos
                $parser->restriction = array("url", "font", "size", "color", "img", "b", "i", "u", "align", "spoiler");
                // CONVERTIMOS
                $html = $parser->parseString($bbcode);
            break;
            // SOLO SMILES
            case 'smiles':
                // SMILES
                $html = $parser->parseSmiles($bbcode, $this->settings['default'].'/images/smiles/');
            break;
        }
        //
        return $html;
	}
    /**
     * @name setMenciones
     * @access private
     * @param string
     * @return string
     * @info PONE LOS LINKS A LOS MENCIONADOS
     */
    private function setMenciones($html){
        # GLOBALES
        global $tsUser, $tsCore;
        # BUSCAMOS A USUARIOS
        preg_match_all('/\B@([a-zA-Z0-9_-]{4,16}+)\b/',$html, $users);
        $menciones = $users[1];
        # VEMOS CUALES EXISTEN
        foreach($menciones as $key => $user){
            if(strtolower($user) != strtolower($tsUser->nick)) {
                $uid = $tsUser->getUserID($user);
                if(!empty($uid)) {
                    $find = '@'.$user.' ';
                    $replace = '@<a href="'.$tsCore->settings['url'].'/perfil/'.$user.'" class="hovercard" uid="'.$uid.'">'.$user.'</a> ';
                    $html = str_replace($find, $replace, $html);
                }
            }
        }
        # RETORNAMOS
        return $html;
    }
    /*
        parseSmiles($st)
    */
    public function parseSmiles($bbcode){
        return $this->parseBBCode($bbcode, 'smiles');
    }
	/*
		parseBBCodeFirma($bbcode)
	*/
	function parseBBCodeFirma($bbcode){
	   return $this->parseBBCode($bbcode, 'firma');
	}
	/*
		setHace()
	*/
	function setHace($fecha, $show = false){
		$fecha = $fecha; 
		$ahora = time();
		$tiempo = $ahora-$fecha; 
		if($fecha <= 0){
			return "Nunca";
		}
		elseif(round($tiempo / 31536000) <= 0){ 
			if(round($tiempo / 2678400) <= 0){ 
				 if(round($tiempo / 86400) <= 0){ 
					 if(round($tiempo / 3600) <= 0){ 
						if(round($tiempo / 60) <= 0){ 
							if($tiempo <= 60){ $hace = "instantes"; } 
						} else  { 
							$can = round($tiempo / 60); 
							if($can <= 1) {    $word = "minuto"; } else { $word = "minutos"; } 
							$hace = $can. " ".$word; 
						} 
					} else { 
						$can = round($tiempo / 3600); 
						if($can <= 1) {    $word = "hora"; } else {    $word = "horas"; } 
						$hace = $can. " ".$word; 
					} 
				} else  { 
					$can = round($tiempo / 86400); 
					if($can <= 1) {    $word = "d&iacute;a"; } else {    $word = "d&iacute;as"; } 
					$hace = $can. " ".$word;
				} 
			} else  { 
				$can = round($tiempo / 2678400);  
				if($can <= 1) {    $word = "mes"; } else { $word = "meses"; } 
				$hace = $can. " ".$word; 
			}
		 }else  {
			$can = round($tiempo / 31536000); 
			if($can <= 1) {    $word = "a&ntilde;o";} else { $word = "a&ntilde;os"; } 
			$hace = $can. " ".$word; 
		 }
		 //
		 if($show == true) return "Hace ".$hace;
		 else return $hace;
	}
	/*
		getUrlContent($tsUrl)
	*/
	function getUrlContent($tsUrl){
	   // USAMOS CURL O FILE
	   if(function_exists('curl_int')){
    		// User agent
    		$useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; es-ES; rv:1.9) Gecko/2008052906 Firefox/3.0";
    		//Abrir conexion  
    		$ch = curl_init();  
    		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    		curl_setopt($ch,CURLOPT_URL,$tsUrl);
    		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    		$result = curl_exec($ch);
    		curl_close($ch); 
        } else {
            $result = @file_get_contents($tsUrl);
        }
		return $result;
	}
	/*
		getUserCountry()
	*/
	function getUserCountry(){
		//
		require("../ext/geoip.inc.php");
		$abir_bd = geoip_open("../ext/GeoIP.dat",GEOIP_STANDARD);
		$country = geoip_country_code_by_addr($abir_bd, $_SERVER['REMOTE_ADDR']);
		geoip_close($abir_bd); 
		//
		return $country;
	}
    /*
        getIP
    */
    function getIP(){
	   if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) $ip = getenv("HTTP_CLIENT_IP");	
	   elseif(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
	   elseif(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) $ip = getenv("REMOTE_ADDR");
	   elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) $ip = $_SERVER['REMOTE_ADDR'];
	   else $ip = "unknown";
	   return $ip;
    }
	/*
		Encriptador KOX
		Encriptacion simple by JNeutron :D
		koxEncode($tsData,$kox_1)
	*/
	function koxEncode($tsData,$kox_l = NULL){
		$KOXkey = array(
			'a' => '0','b' => 1,'c' => 2,'d' => 3,'e' => 4,'f' => 5,'g' => 6,'h' => 7,'i' => 8,'j' => 9,
			'k' => 'z','l' => 'y','m' => 'x','n' => 'w','o' => 'v','p' => 'u','q' => 't','r' => 's',
			's' => 'r','t' => 'q','u' => 'p','v' => 'o','w' => 'n','x' => 'm','y' => 'l','z' => 'k',
			'0' => 'j',1 => 'i',2 => 'h',3 => 'g',4 => 'f',5 => 'e',6 => 'd',7 => 'c',8 => 'b',9 => 'a',
			'@' => '{', '.' => '}',
		);
		if(!$kox_l || $kox_l > 9) $kox_l = rand(1,9);
		$kox_n = $KOXkey[$kox_l];
		$kox_s = strlen($tsData);
		for($i=0;$i<$kox_s;$i++){
			$kox_c = $tsData[$i];
			for($j=0;$j<$kox_l;$j++){
				if($KOXkey[$kox_c] != '') $kox_c = $KOXkey[$kox_c];
				else $kox_c = $tsData[$i];
			}
			$kox_key .= $kox_c;
		}
		return $kox_key.$kox_n;
	}
	/*
		By JNeutron
		koxDecode($tsKey)
	*/
	function koxDecode($tsKey){
		$KOXkey = array(
			'0' => 'a',1 => 'b',2 => 'c',3 => 'd',4 => 'e',5 => 'f',6 => 'g',7 => 'h',8 => 'i',9 => 'j',
			'z' => 'k','y' => 'l','x' => 'm','w' => 'n','v' => 'o','u' => 'p','t' => 'q','s' => 'r',
			'r' => 's','q' => 't','p' => 'u','o' => 'v','n' => 'w','m' => 'x','l' => 'y','k' => 'z',
			'j' => '0','i' => 1,'h' => 2,'g' => 3,'f' => 4,'e' => 5,'d' => 6,'c' => 7,'b' => 8,'a' => 9,
			'{' => '@', '}' => '.',
		);
		$kox_s = strlen($tsKey);
		$kox_l = $tsKey[$kox_s-1];
		$kox_n = $KOXkey[$kox_l];
		for($i=0;$i<$kox_s-1;$i++){
			$kox_c = $tsKey[$i];
			for($j=$kox_n;$j>0;$j--){
				if($KOXkey[$kox_c] != '') $kox_c = $KOXkey[$kox_c];
				else $kox_c = $kox_c;
			}
			$kox_txt .= $kox_c;
		}
		return $kox_txt;
	}
	/* 
		getIUP()
	*/
	function getIUP($array, $prefix = ''){
		// NOMBRE DE LOS CAMPOS
		$fields = array_keys($array);
		// VALOR PARA LAS TABLAS
		$valores = array_values($array);
		// NUMERICOS Y CARACTERES
		foreach($valores as $i => $val) {
			if(!is_numeric($val)) $sets[$i] = $prefix.$fields[$i]." = '".$val."'";
			else $sets[$i] = $prefix.$fields[$i]." = ".$val;
		}
		$values = implode(', ',$sets);
		//
		return $values;
	}
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	
	
}
?>
