<?php
session_start();
include 'getToken.php'; // Obtém o token atualizado

$workspaceId = getenv('WORKSPACE_ID');
$reportId = getenv('REPORT_ID');
$accessToken = getPowerBIToken();

$url = "https://api.powerbi.com/v1.0/myorg/groups/$workspaceId/reports/$reportId";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$json = json_decode($response, true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!isset($json['embedUrl'])) {
    die("❌ Erro ao obter a URL do relatório.");
}

$embedUrl = $json['embedUrl'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Power BI</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const iframe = document.getElementById("reportIframe");
            let timeout = setTimeout(() => {
                iframe.style.display = "none";
                document.getElementById("errorMessage").style.display = "block";
            }, 15000); // Timeout de 15 segundos

            iframe.onload = () => {
                clearTimeout(timeout);
                iframe.style.display = "block";
                document.getElementById("loadingMessage").style.display = "none";
            };
        });
    </script>
</head>
<body>
    <h2>Relatório Power BI</h2>
    <p id="loadingMessage">🔄 Carregando relatório... Aguarde.</p>
    <p id="errorMessage" style="display:none; color: red;">⚠️ O relatório demorou muito para carregar. Tente novamente.</p>
    <iframe id="reportIframe" width="100%" height="600px" src="<?= $embedUrl; ?>" frameborder="0" allowFullScreen="true" style="display: none;"></iframe>
</body>
</html>
