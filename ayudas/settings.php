<?php
# Versión del sistema
const VERSION = "prod";
const SO_UNIX = true;

# Credenciales para la conexión con la base de datos MySQL
// En Docker estos valores llegan por variables de entorno. Se mantienen los
// nombres AYUDAS_* como compatibilidad con la primera instalación.
function app_env($nombre, $compatibilidad, $por_defecto) {
	$valor = getenv($nombre);
	if ($valor === false || $valor === '') $valor = getenv($compatibilidad);
	return ($valor === false || $valor === '') ? $por_defecto : $valor;
}

define('DB_HOST', app_env('APP_DB_HOST', 'AYUDAS_DB_HOST', 'localhost'));
define('DB_USER', app_env('APP_DB_USER', 'AYUDAS_DB_USER', 'municipiogfvarel_ayudas'));
define('DB_PASS', app_env('APP_DB_PASS', 'AYUDAS_DB_PASS', 'muni_ayudas'));
define('DB_NAME', app_env('APP_DB_NAME', 'AYUDAS_DB_NAME', 'municipiogfvarel_ayudas'));


# Algoritmos utilizados para la encriptación de credenciales
# para el registro y acceso de usuarios del sistema
const ALGORITMO_USER = 'crc32';
const ALGORITMO_PASS = 'sha512';
const ALGORITMO_FINAL = 'md5';


# Direcciones a recursos estáticos de interfaz gráfica
if (SO_UNIX == true) {
	define('URL_APP', app_env('APP_BASE_PATH', 'AYUDAS_BASE_PATH', ""));
	define('URL_STATIC', "/static/template/");
} else {
	define('URL_APP', "/ayudas");
	define('URL_STATIC', "/ayudas/static/template/");
}

const TEMPLATE = "static/template.html";

# Configuración estática del sistema
define('APP_TITTLE', app_env('APP_TITLE', 'AYUDAS_TITLE', "AYUDAS"));
define('APP_VERSION', app_env('APP_VERSION', 'AYUDAS_VERSION', "v1.0"));
define('APP_ABREV', app_env('APP_ABREV', 'AYUDAS_ABREV', "Ayudas"));
const LOGIN_URI = "/usuario/login";
const DEFAULT_MODULE = "usuario";
const DEFAULT_ACTION = "panel";

# Directorio private del sistema
$url_private = rtrim(app_env('APP_PRIVATE_DIR', 'AYUDAS_PRIVATE_DIR', "C:/appfiles"), '/') . '/';
define('URL_PRIVATE', $url_private);
ini_set("include_path", URL_PRIVATE);

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
ini_set('include_path', DOCUMENT_ROOT);

session_start();
$session_vars = array('login'=>false);
foreach($session_vars as $var=>$value) {
    if(!isset($_SESSION[$var])) $_SESSION[$var] = $value;
}
?>
