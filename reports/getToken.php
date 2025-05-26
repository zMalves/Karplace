<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicia a sessão apenas se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php'; // Garante que o .env é carregado antes
$tenantId = $_ENV['TENANT_ID'];
$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$username = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];

function getPowerBIToken() {
    global $tenantId, $clientId, $clientSecret, $username, $password;

    $tokenUrl = "https://login.microsoftonline.com/$tenantId/oauth2/token";

    $postFields = [
        "grant_type" => "password",
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "resource" => "https://analysis.windows.net/powerbi/api",
        "username" => $username,
        "password" => $password
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $json = json_decode($response, true);
    curl_close($ch);

    echo "<h2>🔍 Resposta da API do Power BI:</h2>";
    echo "<pre>HTTP Code: $httpCode\n";
    print_r($json);
    echo "</pre>";

    if (isset($json['access_token'])) {
        $_SESSION['access_token'] = $json['access_token'];
        return $json['access_token'];
    } else {
        die("❌ Erro ao obter o token. Veja a resposta acima.");
    }
}

// Testar se o token está sendo gerado corretamente
$token = getPowerBIToken();
if ($token) {
    echo "✅ Token gerado com sucesso!";
} else {
    echo "❌ Erro ao gerar token.";
}
?>