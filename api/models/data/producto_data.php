<?php
// Se incluye la clase para validar los datos de entrada.
require_once ('../../helpers/validator.php');
// Se incluye la clase padre.
require_once ('../../models/handler/producto_handler.php');
/*
 *	Clase para manejar el encapsulamiento de los datos de la tabla PRODUCTO.
 */
class ProductoData extends ProductoHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

    /*
     *   Métodos para validar y establecer los datos.
     */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_producto = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del producto es incorrecto';
            return false;
        }
    }

    public function setNombre($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre_producto = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setCodigo_Interno($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El codigo interno del producto debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->codigo_interno = $value;
            return true;
        } else {
            $this->data_error = 'El nombre del producto debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    public function setReferenciaProveedor($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'La referencia del proveedor debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->referencia_proveedor = $value;
            return true;
        } else {
            $this->data_error = 'la referencia del proveedor debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    public function setPrecio($value)
    {
        // Valida que el precio sea un número válido.
        if (Validator::validateMoney($value)) {
            $this->precio = $value; // Asigna el valor del precio.
            return true;
        } else {
            $this->data_error = 'El precio debe ser un número positivo'; // Almacena mensaje de error.
            return false;
        }
    }

    public function setImagen($file, $filename = null)
    {
        if (Validator::validateImageFile($file, 1000)) {
            $this->imagen_producto = Validator::getFilename();
            return true;
        } elseif (Validator::getFileError()) {
            $this->data_error = Validator::getFileError();
            return false;
        } elseif ($filename) {
            $this->imagen_producto = $filename;
            return true;
        } else {
            $this->imagen_producto = 'default.png';
            return true;
        }
    }
    public function setMarca($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_marca = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la marca es incorrecto';
            return false;
        }
    }

    public function setGenero($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_genero = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del género de zapatos es incorrecto';
            return false;
        }
    }

    public function setCategoria($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_categoria = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la categoría es incorrecto';
            return false;
        }
    }

    public function setMaterial($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_material = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del material es incorrecto';
            return true;
        }
    }

    public function setDescuento($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_descuento = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del descuento es incorrecto';
            return false;
        }
    }

    // Método para establecer el nombre del archivo de imagen.
    public function setFilename()
    {
        // Lee el nombre de archivo del libro.
        if ($data = $this->readFilename()) {
            $this->filename = $data['imagen']; // Asigna el nombre de archivo obtenido.
            return true;
        } else {
            $this->data_error = 'Producto inexistente';
            return false;
        }
    }
    /*
     *  Métodos para obtener el valor de los atributos adicionales.
     */

    // Método para obtener el mensaje de error.
    public function getDataError()
    {
        return $this->data_error;
    }

    // Método para obtener el nombre del archivo de imagen.
    public function getFilename()
    {
        return $this->filename;
    }
}

