<?php
require_once 'core/helpers/user.php';
require_once 'modules/usuario/model.php';
require_once 'modules/configuracionmenu/model.php';


class SessionBaseHandler {
    function checkin() {  
        $user = hash(ALGORITMO_USER, $_POST['usuario']);
        $clave = hash(ALGORITMO_PASS, $_POST['contrasena']);
        $hash = hash(ALGORITMO_FINAL, $user . $clave);
        $usuariodetalle_id = User::get_usuariodetalle_id($hash);
        
        if ($usuariodetalle_id != 0) {
            $usuario_id = User::get_usuario_id($usuariodetalle_id);
            if ($usuario_id != 0) {
                $um = new Usuario();
                $um->usuario_id = $usuario_id;
                $um->get();

                $data_login = array(
                    "usuario-usuario_id"=>$um->usuario_id,
                    "usuario-denominacion"=>$um->denominacion,
                    "usuario-nivel"=>$um->configuracionmenu->nivel,
                    "nivel-denominacion"=>$um->configuracionmenu->denominacion,
                    "usuariodetalle-nombre"=>$um->usuariodetalle->nombre,
                    "usuariodetalle-apellido"=>$um->usuariodetalle->apellido,
                    "usuariodetalle-correoelectronico"=>$um->usuariodetalle->correoelectronico,
                    "usuario-configuracionmenu"=>$um->configuracionmenu->configuracionmenu_id);
                
                $_SESSION["data-login-" . APP_ABREV] = $data_login;
                $_SESSION['login' . APP_ABREV] = true;
                $redirect = URL_APP . "/usuario/panel";
            }
        } else {
            $_SESSION['login' . APP_ABREV] = false;
            $redirect = URL_APP . LOGIN_URI . "/mError";
        }

        header("Location: $redirect");
    }

    function check_session() {
        if($_SESSION['login' . APP_ABREV] !== true) {
            $this->checkout();
        }
    }

    function check_panel($usr_nivel) {
        switch ($usr_nivel) {
            case 1:
                $panel = "operador";
                break;
            case 2:
                $panel = "analista";
                break;
            case 3:
                $panel = "administrador";
                break;
            case 9:
                $panel = "administrador";
                break;
        }

        return $panel;
    }

    function check_admin_level() {
        $level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"]; 
        if ($level != 9) {
            $this->checkout();
        }
    }

    function check_level() {
        $level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"]; 
        if ($level > 1 ) {
            $_SESSION['login' . APP_ABREV] = true;
        } else {
            $this->checkout();
        }
    }

    function checkout() {
        $_SESSION[] = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"], 
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        $_SESSION['login' . APP_ABREV] = false;
        header("Location:" . URL_APP . LOGIN_URI);
    }
}

function SessionHandler() { return new SessionBaseHandler();}

function set_app_message($mensaje) {
    $_SESSION['message-' . APP_ABREV] = $mensaje;
}

function get_app_message() {
    $clave = 'message-' . APP_ABREV;
    $mensaje = isset($_SESSION[$clave]) ? $_SESSION[$clave] : '';
    unset($_SESSION[$clave]);
    return $mensaje;
}
