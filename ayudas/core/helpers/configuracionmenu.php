<?php
require_once "modules/configuracionmenu/model.php";
require_once "modules/menu/model.php";


class HelperMenu {
	static function traer_configuracionmenu($configuracionmenu_id) {
	    $cmm = new ConfiguracionMenu();
	    $cmm->configuracionmenu_id = $configuracionmenu_id;
	    $cmm->get();

		// Estos módulos siguen disponibles por URL, pero no deben aparecer
		// como submenús en la navegación principal.
		$submenus_visibles = array();
		foreach ($cmm->submenu_collection as $submenu) {
			if ($submenu->url != '/comprobante/listado' && $submenu->url != '/proveedor/panel') {
				$submenus_visibles[] = $submenu;
			}
		}
		$cmm->submenu_collection = $submenus_visibles;

	 	return $cmm;
	}

	static function traer_menu_collection() {
	    $menu_collection = Collector()->get('Menu');
	 	return $menu_collection;
	}
}

function HelperMenu() {return new HelperMenu();}
?>
