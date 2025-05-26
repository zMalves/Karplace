<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function getPowerBIToken() {
    $tenantId = "6de4ee7d-882a-44cd-98cd-7687a9ed22f9";
    $clientId = "e224b600-37d1-4490-9939-b6932233f5eb";
    $clientSecret = "XYU8Q~GnHHD7we8~s44O2tt77WN~lZJWUx9ALa~s";
    $url = "https://login.microsoftonline.com/$tenantId/oauth2/token";

    $postFields = [
        'grant_type'    => 'client_credentials',
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'resource'      => 'https://graph.microsoft.com'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    return $json['access_token'] ?? null;
}

// Apenas para teste: remover ou comentar quando for usar no outro arquivo
$token = getPowerBIToken();
if ($token) {
    echo "✅ Token gerado com sucesso!<br>";
    echo "Access Token: " . substr($token, 0, 50) . "...";
} else {
    echo "❌ Erro ao obter o token.";
}
?>
