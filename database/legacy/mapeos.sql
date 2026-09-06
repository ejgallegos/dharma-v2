/* ****************************************************************************************** */
/* PARA MENÚ 
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS menu (
    menu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenu (
    submenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
    , menu INT(11)
    , INDEX(menu)
    , FOREIGN KEY (menu)
        REFERENCES menu (menu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS item (
    item_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , url VARCHAR(50)
    , detalle VARCHAR(100)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS configuracionmenu (
    configuracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , nivel INT(11)
    , gerencia INT(11)
    , INDEX (gerencia)
    , FOREIGN KEY (gerencia)
        REFERENCES gerencia (gerencia_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenuconfiguracionmenu (
    submenuconfiguracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES submenu (submenu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS itemconfiguracionmenu (
    itemconfiguracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES item (item_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS archivo (
    archivo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , url VARCHAR(100)
    , fecha_carga DATE
    , formato VARCHAR(50)
) ENGINE=InnoDb;

/* ****************************************************************************************** */
/* PARA USUARIO DESARROLLADOR
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS usuariodetalle (
    usuariodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(50)
    , nombre VARCHAR(50)
    , correoelectronico VARCHAR(250)
    , token TEXT
    , gerencia INT(11)
    , INDEX (gerencia)
    , FOREIGN KEY (gerencia)
        REFERENCES gerencia (gerencia_id)
        ON DELETE CASCADE
    , unicom INT(11)
    , INDEX (unicom)
    , FOREIGN KEY (unicom)
        REFERENCES unicom (unicom_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS usuario (
    usuario_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , nivel INT(1)
    , usuariodetalle INT(11)
    , INDEX (usuariodetalle)
    , FOREIGN KEY (usuariodetalle)
        REFERENCES usuariodetalle (usuariodetalle_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

INSERT INTO usuariodetalle (usuariodetalle_id, apellido, nombre, correoelectronico, token, centrocosto, unicom) 
VALUES (1, 'Admin', 'admin', 'admin@admin.com', 'ff050c2a6dd7bc3e4602e9702de81d21', 1, 1);

INSERT INTO usuario (usuario_id, denominacion, nivel, usuariodetalle) 
VALUES (1, 'admin', 3, 1);

/* ****************************************************************************************** */
/* PARA OBJETOS DE CASOS DE USO 
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS localidad (
    localidad_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS proveedor (
    proveedor_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , cuit BIGINT(15)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS factura (
    factura_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , punto_venta INT(4)
    , numero INT(8)
    , fecha DATE
    , importe FLOAT
    , detalle TEXT
    , proveedor INT(11)
    , INDEX (proveedor)
    , FOREIGN KEY (proveedor)
        REFERENCES proveedor (proveedor_id)
        ON DELETE SET NULL
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS beneficiario (
    beneficiario_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(150)
    , nombre VARCHAR(150)
    , fecha_nacimiento DATE()
    , documento INT(11)
    , cuil BIGINT(13)
    , domicilio VARCHAR(250)
    , telefono BIGINT()
    , correoelectronico VARCHAR(250)
    , localidad INT(11)
    , INDEX (localidad)
    , FOREIGN KEY (localidad)
        REFERENCES localidad (localidad_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS plan (
    plan_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , fecha DATE
    , monto FLOAT
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS tipobeca (
    tipobeca_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS beca (
    beca_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , resolucion VARCHAR(150)
    , fecha DATE
    , detalle TEXT
    , tipobeca INT(11)
    , INDEX (tipobeca)
    , FOREIGN KEY (tipobeca)
        REFERENCES tipobeca (tipobeca_id)
        ON DELETE CASCADE
    , localidad INT(11)
    , INDEX (localidad)
    , FOREIGN KEY (localidad)
        REFERENCES localidad (localidad_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS anexo (
    anexo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , monto FLOAT
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS anexobeca (
    anexobeca_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES beca (beca_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES anexo (anexo_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuota (
    cuota_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , monto FLOAT
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS beneficio (
    beneficio_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha_alta DATE
    , estado TEXT
    , anexo INT(11)
    , INDEX (anexo)
    , FOREIGN KEY (anexo)
        REFERENCES anexo (anexo_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuotabeneficio (
    cuotabeneficio_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES beneficio (beneficio_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES cuota (cuota_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS beneficiobeneficiario (
    beneficiobeneficiario_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES beneficiario (beneficiario_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES beneficio (beneficio_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS backup (
    backup_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , usuario VARCHAR(100)
    , fecha DATE
    , hora TIME
    , detalle TEXT
) ENGINE=InnoDb;