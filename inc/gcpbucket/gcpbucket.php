<?php
function generate_jwt($credentials) {
    // Cabecera del JWT (Header)
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    // Carga útil (Payload) del JWT
    $issuedAt = time(); // Hora de emisión
    $expirationTime = $issuedAt + 3600;  // Expira en una hora
    $payload = [
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/devstorage.read_write',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $issuedAt,
        'exp' => $expirationTime,
    ];

    // Codificar el encabezado y la carga útil en base64url
    $encodedHeader = base64url_encode(json_encode($header, JSON_THROW_ON_ERROR));
    $encodedPayload = base64url_encode(json_encode($payload, JSON_THROW_ON_ERROR));

    // Concatenar el encabezado y la carga útil
    $message = $encodedHeader . '.' . $encodedPayload;

    // Generar la firma
    // $privateKey = file_get_contents(get_template_directory() . '/json/thd-careers-447904-b68e48031aa6.json');
    $privateKey = file_get_contents(get_template_directory() . '/json/thdmx-careers-bucket-test-daa3254feacf.json');

    $decodedPrivateKey = json_decode($privateKey, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar el JSON de la clave privada: ' . json_last_error_msg());
    }

    $privateKey = $decodedPrivateKey['private_key'] ?? null;

    if (is_null($privateKey)) {
        throw new Exception('La clave privada no se encontró en el JSON proporcionado.');
    }
    $signature = sign_message($message, $privateKey);

    // Codificar la firma en base64url
    $encodedSignature = base64url_encode($signature);

    // Retornar el JWT
    return $message . '.' . $encodedSignature;
}

function sign_message($message, $privateKey) {
    // Firmar el mensaje con la clave privada utilizando SHA256 y RSA
    $privateKeyResource = openssl_pkey_get_private($privateKey);
    if (!$privateKeyResource) {
        die('No se pudo cargar la clave privada');
    }

    $signature = '';
    openssl_sign($message, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
    return $signature;
}

function base64url_encode($data) {
    // Base64 URL Safe encoding (reemplazar "+" y "/" por "-" y "_", respectivamente)
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function upload_to_gcp($file) {
    // Ruta a las credenciales de tu cuenta de servicio
    // $json_key_file = get_template_directory_uri(  ).'/json/thd-careers-447904-b68e48031aa6.json';
    $json_key_file = get_template_directory_uri(  ).'/json/thdmx-careers-bucket-test-daa3254feacf.json';
    try {
        $jsonContent = file_get_contents($json_key_file);

        if ($jsonContent === false) {
            throw new Exception('Error al leer el archivo JSON: ' . $json_key_file);
        }

        $credentials = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar el archivo JSON: ' . json_last_error_msg());
        }
    } catch (Exception $e) {
        error_log('Error en upload_to_gcp: ' . $e->getMessage());
        throw $e; // Opcional: volver a lanzar la excepción para manejarla en otro nivel
    }

    // Generar el JWT para la autenticación
    $jwt = generate_jwt($credentials);

    // Obtener el nombre del bucket y el archivo
    // $bucket_name = 'thdcareers';
    $bucket_name = 'thdmx-bucket-test-careers_docs';
    $object_name = $file['name'];
    $object_content = file_get_contents($file['tmp_name']);

    // Solicitar el token de acceso usando el JWT
    $auth_url = 'https://oauth2.googleapis.com/token';
    $data = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ];

    // Realizar la solicitud de autenticación
    $response = wp_remote_post($auth_url, [
        'body' => $data
    ]);
    try {
        $body = wp_remote_retrieve_body($response);

        if (empty($body)) {
            throw new Exception('La respuesta del servidor está vacía.');
        }

        $auth_response = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar el JSON de la respuesta: ' . json_last_error_msg());
        }
    } catch (Exception $e) {
        error_log('Error en upload_to_gcp: ' . $e->getMessage());
        throw $e; // Opcional: volver a lanzar la excepción para manejarla en otro nivel
    }

    if (isset($auth_response['access_token'])) {
        // Usar el token de acceso para subir el archivo
        $upload_url = "https://storage.googleapis.com/upload/storage/v1/b/{$bucket_name}/o?uploadType=media&name={$object_name}";

        $headers = [
            'Authorization' => 'Bearer ' . $auth_response['access_token'],
            'Content-Type' => mime_content_type($file['tmp_name']),
        ];

        $file_upload_response = wp_remote_post($upload_url, [
            'headers' => $headers,
            'body' => $object_content
        ]);

        return wp_remote_retrieve_body($file_upload_response);
    } else {
        return 'Error en la autenticación';
    }
}

function permitir_archivos_json($mime_types) {
    $mime_types['json'] = 'application/json'; // Permite la subida de archivos .json
    return $mime_types;
}
add_filter('upload_mimes', 'permitir_archivos_json');


// Genera un nuevo token de acceso para ver archivos en el bucket
function generar_token_acceso() {
    $json_key_file = get_template_directory() . '/json/thdmx-careers-bucket-test-daa3254feacf.json';

    // Leer las credenciales desde el archivo JSON
    $jsonContent = file_get_contents($json_key_file);
    if ($jsonContent === false) {
        throw new Exception('Error al leer el archivo JSON: ' . $json_key_file);
    }

    $credentials = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar el archivo JSON: ' . json_last_error_msg());
    }

    // Generar el JWT utilizando la función existente
    $jwt = generate_jwt($credentials);

    // Solicitar el token de acceso usando el JWT
    $auth_url = 'https://oauth2.googleapis.com/token';
    $data = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ];

    // Realizar la solicitud para obtener el token de acceso
    $response = wp_remote_post($auth_url, [
        'body' => $data
    ]);

    $body = wp_remote_retrieve_body($response);
    if (empty($body)) {
        throw new Exception('La respuesta del servidor está vacía.');
    }

    $auth_response = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar el JSON de la respuesta: ' . json_last_error_msg());
    }

    // Retornar el token de acceso
    if (isset($auth_response['access_token'])) {
        return $auth_response['access_token'];
    } else {
        throw new Exception('Error al obtener el token de acceso.');
    }
}

function obtener_url_archivo($file_name) {
    try {
        $access_token = generar_token_acceso(); // Generar un nuevo token de acceso
        $bucket_name = 'thdmx-bucket-test-careers_docs';
        $url = "https://storage.googleapis.com/download/storage/v1/b/{$bucket_name}/o/{$file_name}?alt=media&access_token={$access_token}";
        return $url;
    } catch (Exception $e) {
        error_log('Error al generar la URL del archivo: ' . $e->getMessage());
        return 'Error al generar la URL del archivo.';
    }
}