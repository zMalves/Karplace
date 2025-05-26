<?php
session_start();

$tenantId = "6de4ee7d-882a-44cd-98cd-7687a9ed22f9";
$clientId = "e224b600-37d1-4490-9939-b6932233f5eb";
$redirectUri = "https://test.sketchdata.com.br/reports/callback.php"; // Defina um callback válido

// URL de login do Azure
$authorizeUrl = "https://login.microsoftonline.com/$tenantId/oauth2/authorize?";
$params = [
    "client_id" => $clientId,
    "response_type" => "code",
    "redirect_uri" => $redirectUri,
    "response_mode" => "query",
    "scope" => "https://graph.microsoft.com/.default",
];

header("Location: " . $authorizeUrl . http_build_query($params));
exit;
?>
