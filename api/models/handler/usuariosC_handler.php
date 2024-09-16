<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class UsuariosHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $alias = null;
    protected $correo = null;
    protected $clave = null;
    protected $telefono = null;
    protected $dui = null;
    protected $direccion = null;
    protected $estadocliente = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_usuario, nombre, usuario, correo, telefono, dui_cliente
                FROM tb_usuarios
                WHERE nombre LIKE ? OR usuario LIKE ? OR correo LIKE ?
                ORDER BY nombre';
        $params = array($value, $value, $value);
        return Database::getRows($sql, $params);
    }

    //Llamar los datos de la base de datos 
    public function readAll()
    {
        $sql = 'SELECT id_usuario, nombre, usuario, correo, telefono, dui_cliente, estado_cliente
                FROM tb_usuarios
                ORDER BY nombre ASC;';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_usuario, nombre, usuario, correo, dui_cliente, direccion_cliente, telefono, estado_cliente
                FROM tb_usuarios
                WHERE id_usuario = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_usuarios 
        SET estado_cliente = ?
        WHERE id_usuario = ?';
        $params = array(
            $this->estadocliente,
            $this->id
        );
        return Database::executeRow($sql, $params);
    }


    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_usuarios
                WHERE id_usuario = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    //Funcion de la consulta para generar reporte

    public function usuariosRegistrados()
    {
        $sql = 'SELECT usuario, correo, dui_cliente, telefono,
                   CASE
                       WHEN estado_cliente = 1 THEN "Activo"
                       ELSE "Inactivo"
                   END AS estado_cliente
            FROM tb_usuarios
            ORDER BY usuario ASC;'; // Filtra los usuarios según el estado proporcionado

        // Ejecuta la consulta y devuelve los resultados
        return Database::getRows($sql); // Llama a la función getRows de la clase Database para ejecutar la consulta
    }
}
