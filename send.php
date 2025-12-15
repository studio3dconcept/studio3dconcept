<?php
/* -------------------------------
   Studio 3D Concept - send.php
   Version Gandi + Bytescale (fix affichage message)
-------------------------------- */

$TO_EMAIL   = "devis@studio3dconcept.fr";
$TO_NAME    = "Studio 3D Concept – Devis";
$FROM_EMAIL = "devis@studio3dconcept.fr";
$FROM_NAME  = "Formulaire Devis (site)";

// Anti-bot
if (!empty($_POST['website'] ?? '')) {
  header("Location: /success.html");
  exit;
}

// Sanitize
function clean($v) {
  $v = trim($v ?? "");
  return str_replace(["\r","\n","%0a","%0d"], " ", $v);
}
$name    = clean($_POST['name'] ?? "");
$email   = clean($_POST['email'] ?? "");
$phone   = clean($_POST['phone'] ?? "");
$message = trim($_POST['message'] ?? "");
$fileUrls = trim($_POST['file_urls'] ?? "");

// Validation
$errors = [];
if ($name === "") $errors[] = "Nom manquant";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
if ($message === "") $errors[] = "Message manquant";
if ($errors) { header("Location: /contact.html"); exit; }

// Échappage du message AVANT insertion HTML (et conservation des sauts de ligne)
$messageSafe = nl2br(htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

// Liste des liens
$urlsList = "";
if ($fileUrls !== "") {
  $lines = preg_split("/\r\n|\n|\r/", $fileUrls);
  $items = [];
  foreach ($lines as $u) {
    $u = trim($u);
    if ($u !== "" && filter_var($u, FILTER_VALIDATE_URL)) {
      $safe = htmlspecialchars($u, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      $items[] = "<li><a href=\"{$safe}\" target=\"_blank\" rel=\"noopener\">{$safe}</a></li>";
    }
  }
  if ($items) {
    $urlsList = "<h3 style=\"margin:16px 0 6px\">Fichiers (Bytescale)</h3>
                 <ul style=\"margin:0;padding-left:18px\">".implode("", $items)."</ul>";
  }
}

$subject = "Nouvelle demande de devis – {$name}";
$now = date('d/m/Y H:i');

$body = <<<HTML
<!doctype html>
<html lang="fr"><head><meta charset="utf-8"></head>
<body style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#0f1115">
  <div style="max-width:700px;margin:0 auto;padding:16px;border:1px solid #e5e7eb;border-radius:12px">
    <h2 style="margin:0 0 10px">Demande de devis – Studio 3D Concept</h2>
    <p style="margin:0 0 14px;color:#374151">Reçue le {$now}</p>

    <h3 style="margin:16px 0 6px">Coordonnées</h3>
    <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;background:#f9fafb;border-radius:8px">
      <tr><td><strong>Nom</strong></td><td>{$name}</td></tr>
      <tr><td><strong>Email</strong></td><td>{$email}</td></tr>
      <tr><td><strong>Téléphone</strong></td><td>{$phone}</td></tr>
    </table>

    <h3 style="margin:16px 0 6px">Message</h3>
    <div style="line-height:1.5;border:1px solid #e5e7eb;border-radius:8px;padding:10px">{$messageSafe}</div>

    {$urlsList}

    <p style="margin-top:18px;color:#6b7280;font-size:12px">
      Email auto-généré par le site studio3dconcept.fr (pas de pièces jointes).
    </p>
  </div>
</body></html>
HTML;

$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = "From: {$FROM_NAME} <{$FROM_EMAIL}>";
$headers[] = "Reply-To: {$name} <{$email}>";
$headers[] = "X-Mailer: PHP/".phpversion();

$ok = @mail(
  "{$TO_NAME} <{$TO_EMAIL}>",
  "=?UTF-8?B?".base64_encode($subject)."?=",
  $body,
  implode("\r\n", $headers)
);

header("Location: " . ($ok ? "/success.html" : "/contact.html"));
exit;
