<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    die("Erro: Nenhum token encontrado. <a href='auth.php'>Faça login</a>");
}

$accessToken = $_SESSION['access_token'];
$workspaceId = "4b1a2b74-15ea-4538-b826-8491f5f4a297";
$reportId = "105b640a-72bb-4c20-a826-8a8b79967977/f92a610e7662b940d6e6";

$url = "https://api.powerbi.com/v1.0/myorg/groups/$workspaceId/reports/$reportId";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<pre>";
echo "HTTP Code: $httpCode\n";
print_r(json_decode($response, true));
echo "</pre>";
?>
