<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_GET['code'])) {
    die("❌ Erro: Nenhum código de autorização recebido.<br><pre>" . print_r($_GET, true) . "</pre>");
}

$authCode = $_GET['code'];
$tenantId = "6de4ee7d-882a-44cd-98cd-7687a9ed22f9";
$clientId = "e224b600-37d1-4490-9939-b6932233f5eb";
$clientSecret = "XYU8Q~GnHHD7we8~s44O2tt77WN~lZJWUx9ALa~s";
$redirectUri = "https://test.sketchdata.com.br/reports/callback.php";

$tokenUrl = "https://login.microsoftonline.com/$tenantId/oauth2/token";
$postFields = [
    "grant_type" => "authorization_code",
    "client_id" => $clientId,
    "client_secret" => $clientSecret,
    "code" => $authCode,
    "redirect_uri" => $redirectUri,
    "resource" => "https://graph.microsoft.com"
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
$json = json_decode($response, true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h2>🔍 Resposta da API do Power BI:</h2>";
echo "<pre>HTTP Code: $httpCode\n";
print_r($json);
echo "</pre>";
?>

