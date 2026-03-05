<?php
function get_gcp_credentials() {

    $debug_output = [];

    try {

        $client_email = vip_get_env_var('CARRERAS_CLIENT_EMAIL', '');
        $private_key  = vip_get_env_var('CARRERAS_PRIVATE_KEY', '');
        $bucket_name  = vip_get_env_var('CARRERAS_BUCKET_NAME', '');

        $debug_output[] = 'EMAIL LENGTH: ' . strlen($client_email);
        $debug_output[] = 'KEY RAW LENGTH: ' . strlen($private_key);
        $debug_output[] = 'BUCKET: ' . $bucket_name;

        if (empty($client_email) || empty($private_key) || empty($bucket_name)) {
            throw new Exception('Alguna env var está vacía.');
        }

        // 🔧 Normalización robusta
        $private_key = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $private_key);
        $private_key = trim($private_key);

        $debug_output[] = 'KEY NORMALIZED LENGTH: ' . strlen($private_key);
        $debug_output[] = 'STARTS WITH BEGIN: ' . (strpos($private_key, '-----BEGIN PRIVATE KEY-----') === 0 ? 'YES' : 'NO');
        $debug_output[] = 'ENDS WITH END: ' . (substr($private_key, -25) === '-----END PRIVATE KEY-----' ? 'YES' : 'NO');

        if (!is_email($client_email)) {
            throw new Exception('Email inválido.');
        }

        // 🔎 Validación real OpenSSL
        $res = openssl_pkey_get_private($private_key);

        if (!$res) {
            throw new Exception('OpenSSL no pudo parsear la clave privada.');
        }

        $debug_output[] = 'OpenSSL: KEY VALID ✔';

        return [
            'client_email' => sanitize_email($client_email),
            'private_key'  => $private_key,
            'bucket_name'  => $bucket_name,
            'debug'        => $debug_output
        ];

    } catch (Throwable $e) {

        // Registrar error en logs del servidor
        error_log('GCP ERROR: ' . $e->getMessage());
        error_log('GCP DEBUG: ' . print_r($debug_output, true));

        // Respuesta genérica al cliente
        wp_die(
            'Ocurrió un error al procesar la solicitud.',
            'Error',
            ['response' => 500]
        );
    }
}
function generate_jwt($credentials) {
    try {
        // Carga útil (Payload) del JWT
        $issuedAt = time() - 60;  // Hora de emisión

        // Cabecera del JWT (Header)
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $expirationTime = $issuedAt + 3600;  // Expira en una hora

        $payload = [
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/devstorage.read_write',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $issuedAt,
            'exp'   => $expirationTime,
        ];

        // Codificar el encabezado y la carga útil en base64url
        $encodedHeader  = base64url_encode(json_encode($header, JSON_THROW_ON_ERROR));
        $encodedPayload = base64url_encode(json_encode($payload, JSON_THROW_ON_ERROR));

        // Concatenar el encabezado y la carga útil
        $message   = $encodedHeader . '.' . $encodedPayload;
        $signature = sign_message($message, $credentials['private_key']);

        // Codificar la firma en base64url
        $encodedSignature = base64url_encode($signature);

        // Retornar el JWT
        return $message . '.' . $encodedSignature;
    } catch (Throwable $e) {
        error_log('[GCP] Error generate_jwt: ' . $e->getMessage());
        throw new RuntimeException('Error generando token.');
    }
}

function sign_message($message, $privateKey) {

    // Firmar el mensaje con la clave privada utilizando SHA256 y RSA
    $key = openssl_pkey_get_private($privateKey);
    if (!$key) {
        error_log('[GCP] Clave privada inválida');
        throw new RuntimeException('Error criptográfico.');
    }

    $signature = '';
    if (!openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256)) {
        error_log('[GCP] Fallo al firmar mensaje');
        throw new RuntimeException('Error criptográfico.');
    }

    openssl_pkey_free($key);
    return $signature;
}

function base64url_encode($data) {
    // Base64 URL Safe encoding (reemplazar "+" y "/" por "-" y "_", respectivamente)
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function upload_to_gcp($file) {
    try {
        // Límite de tamaño (5MB)
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!isset($file['size']) || $file['size'] > $max_size) {
            throw new RuntimeException('Archivo excede tamaño permitido.');
        }

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Archivo inválido.');
        }

        $bucket_name = vip_get_env_var('CARRERAS_BUCKET_NAME');

        if (empty($bucket_name)) {
            throw new RuntimeException('Bucket no configurado.');
        }

        // Generar el JWT para la autenticación
        $credentials = get_gcp_credentials();
        $jwt         = generate_jwt($credentials);

        // Solicitar token OAuth
        $auth = wp_remote_post(
            'https://oauth2.googleapis.com/token',
            [
                'timeout' => 20,
                'body'    => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ]
            ]
        );

        if (is_wp_error($auth)) {
            throw new RuntimeException('Error de conexión OAuth.');
        }

        $auth_status = wp_remote_retrieve_response_code($auth);
        if ($auth_status !== 200) {
            throw new RuntimeException('OAuth rechazado por el proveedor.');
        }

        $auth_body = json_decode(
            wp_remote_retrieve_body($auth),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (empty($auth_body['access_token'])) {
            throw new RuntimeException('No se recibió access_token.');
        }

        // Sanitizar nombre de objeto
        $object_name = time() . '-' . wp_generate_uuid4() . '-' . sanitize_file_name($file['name']);
        $object_name = rawurlencode($object_name);

        $content = file_get_contents($file['tmp_name']);

        if ($content === false) {
            throw new RuntimeException('No se pudo leer el archivo.');
        }

        $mime = function_exists('mime_content_type')
            ? mime_content_type($file['tmp_name'])
            : 'application/octet-stream';

        $allowed_mimes = [
            'application/pdf',
            'image/jpeg',
            'image/png'
        ];

        if (!in_array($mime, $allowed_mimes, true)) {
            throw new RuntimeException('Tipo de archivo no permitido.');
        }

        $upload_url = sprintf(
            'https://storage.googleapis.com/upload/storage/v1/b/%s/o?uploadType=media&name=%s',
            rawurlencode($bucket_name),
            $object_name
        );

        $response = wp_remote_post(
            $upload_url,
            [
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $auth_body['access_token'],
                    'Content-Type'  => $mime,
                ],
                'body' => $content
            ]
        );

        if (is_wp_error($response)) {
            throw new RuntimeException('Error subiendo archivo.');
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code < 200 || $status_code >= 300) {
            throw new RuntimeException('Respuesta inválida de GCP.');
        }

        return wp_remote_retrieve_body($response);
    } catch (Throwable $e) {
        error_log('[GCP] upload_to_gcp: ' . $e->getMessage());
        return false; // nunca exponer error técnico
    }
}

// Genera un nuevo token de acceso para ver archivos en el bucket
function generar_token_acceso() {
    try {
        $credentials = get_gcp_credentials();
        $jwt         = generate_jwt($credentials);

        // Solicitar el token de acceso usando el JWT
        $response = wp_remote_post(
            'https://oauth2.googleapis.com/token',
            [
                'timeout' => 20,
                'body' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ]
            ]
        );

        if (is_wp_error($response)) {
            throw new RuntimeException('Error OAuth.');
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            throw new RuntimeException('OAuth rechazado por el proveedor.');
        }

        $data = json_decode(
            wp_remote_retrieve_body($response),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (empty($data['access_token'])) {
            throw new RuntimeException('Token inválido.');
        }

        return $data['access_token'];
    } catch (Throwable $e) {
        error_log('[GCP] generar_token_acceso: ' . $e->getMessage());
        throw new RuntimeException('No se pudo generar token.');
    }
}

function obtener_url_archivo($file_name) {
    try {
        $bucket = vip_get_env_var('CARRERAS_BUCKET_NAME');

        if (empty($bucket)) {
            throw new RuntimeException('Bucket no configurado.');
        }

        $file_name = rawurlencode(sanitize_file_name($file_name));
        $token     = generar_token_acceso();

        return sprintf(
            'https://storage.googleapis.com/download/storage/v1/b/%s/o/%s?alt=media&access_token=%s',
            rawurlencode($bucket),
            $file_name,
            rawurlencode($token)
        );
    } catch (Throwable $e) {
        error_log('[GCP] obtener_url_archivo: ' . $e->getMessage());
        return false;
    }
}
