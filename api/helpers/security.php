<?php
// Archivo: api/helpers/security.php

// Archivo: api/helpers/security.php

class Security {
    public static function setClickjackingProtection() {
        // Establecer la cabecera X-Frame-Options para evitar el clickjacking.
        header("X-Frame-Options: DENY");
        
        // Establecer la directiva frame-ancestors de Content-Security-Policy.
        header("Content-Security-Policy: frame-ancestors 'none';");
    }

    public static function setAdditionalSecurityHeaders() {
        // Evitar que el navegador detecte tipos MIME incorrectos.
        header("X-Content-Type-Options: nosniff");

        // Forzar HTTPS para las futuras conexiones.
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        }

        // Protección contra XSS reflejado.
        header("X-XSS-Protection: 1; mode=block");
    }
}
