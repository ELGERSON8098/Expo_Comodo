<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/productos_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla PRODUCTO.
 */
class ProductosData extends ProductosHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */
    public function setNombreProducto($value, $min = 2, $max = 100)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre del producto debe ser alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre_producto = $value;
            return true;
        } else {
            $this->data_error = 'El nombre del producto debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setDescripcion($value, $min = 2, $max = 200)
    {
        if (!Validator::validateText($value)) {
            $this->data_error = 'La descripción del producto no es válida';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->descripcion = $value;
            return true;
        } else {
            $this->data_error = 'La descripción del producto debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setCodigoInterno($value, $min = 1, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El código interno debe ser alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->codigo_interno = $value;
            return true;
        } else {
            $this->data_error = 'El código interno debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setReferenciaProveedor($value, $min = 1, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'La referencia del proveedor debe ser alfanumérica';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->referencia_proveedor = $value;
            return true;
        } else {
            $this->data_error = 'La referencia del proveedor debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setImagen($value, $min = 1, $max = 25)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre de la imagen debe ser alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->imagen = $value;
            return true;
        } else {
            $this->data_error = 'El nombre de la imagen debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del administrador es incorrecto';
            return false;
        }
    }


    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
