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
     // Método para validar y asignar el identificador del producto.
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
     // Método para validar y asignar el identificador del detalle del producto.
    public function setIdDetalle($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle_producto = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del detalle es incorrecto';
            return false;
        }
    }

    // Método para validar y asignar el nombre del producto.
    public function setNombre($value, $min = 2, $max = 50)
    {
        // Verificar si el nombre del producto ya existe en la base de datos, excluyendo el registro actual
        $checkSql = 'SELECT COUNT(*) as count FROM tb_productos WHERE nombre_producto = ? AND id_producto != ?';
        $checkParams = array($value, $this->id_producto ? $this->id_producto : 0);
        
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'El nombre del producto ya existe';
            return false;
        }
    
        // Validar que el nombre solo contenga caracteres alfabéticos
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre debe ser un valor alfabético y no puede contener números';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre_producto = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para validar y asignar el código interno del producto
    public function setCodigo_Interno($value, $min = 2, $max = 50)
    {
        // Verificar si el codigo interno del producto ya existe en la base de datos, excluyendo el registro actual
        $checkSql = 'SELECT COUNT(*) as count FROM tb_productos WHERE codigo_interno = ? AND id_producto != ?';
        $checkParams = array($value, $this->id_producto ? $this->id_producto : 0);
        
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'El codigo interno del producto ya existe';
            return false;
        }
        // Validar que el código interno sea alfanumérico.
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El codigo interno del producto debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->codigo_interno = $value;
            return true;
        } else {
            $this->data_error = 'El codigo interno del producto debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para validar y asignar la referencia del proveedor.
    public function setReferenciaProveedor($value, $min = 2, $max = 50)
    {
        // Verificar si la referencia del proveedor ya existe en la base de datos, excluyendo el registro actual
        $checkSql = 'SELECT COUNT(*) as count FROM tb_productos WHERE referencia_proveedor = ? AND id_producto != ?';
        $checkParams = array($value, $this->id_producto ? $this->id_producto : 0);
        
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'La referencia del proveedor ya existe';
            return false;
        }
        // Validar que la referencia del proveedor sea alfanumérica.
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'La referencia del proveedor debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->referencia_proveedor = $value;
            return true;
        } else {
            $this->data_error = 'La referencia del proveedor debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para validar y asignar el precio del producto.

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
    // Método para validar y asignar la imagen del producto.
    public function setImagen($file, $filename = null)
    {
        if ($file['size'] == 0 && $filename) {
            // Si no se subió un archivo nuevo y se proporcionó un nombre de archivo, mantener la imagen existente
            $this->imagen_producto = $filename;
            return true;
        } elseif (Validator::validateImageFile($file)) {
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
    // Método para validar y asignar el identificador de la marca del producto.
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

     // Método para validar y asignar el identificador del género del producto.
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

    // Método para validar y asignar el identificador de la categoría del producto
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
    // Método para validar y asignar el identificador del material del producto.
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
     // Método para validar y asignar el identificador del descuento del producto (si aplica).
    public function setDescuento($value)
    {
        if ($value !== null && !Validator::validateNaturalNumber($value)) {
            $this->id_descuento = null;
            return true;
        }
        $this->id_descuento = $value;
        return true;
    }
    // Método para validar y asignar la descripción del producto.
    public function setDescripcion($value, $min = 2, $max = 250)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'La descripción contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->descripcion = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para validar y asignar la cantidad de existencias del producto.
    public function setExistencias($value)
    {
        if (Validator::validateNaturalNumber2($value)) {
            $this->existencias = $value;
            return true;
        } else {
            $this->data_error = 'Las existencias debe ser un número entero positivo';
            return false;
        }
    }

    // Método para validar y asignar el identificador de la talla del producto.
    public function setTalla($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_talla = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la talla es incorrecto';
            return false;
        }
    }
    
    // Método para validar y asignar el identificador del color del producto.
    public function setColor($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_color = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del color es incorrecto';
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

