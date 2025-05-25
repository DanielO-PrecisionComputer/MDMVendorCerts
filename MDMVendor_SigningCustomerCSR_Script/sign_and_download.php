<?php
session_start();
$sessionId = session_id();
$uploadDir = __DIR__ . "/uploads/$sessionId/";
$outputDir = __DIR__ . "/output/$sessionId/";
$certDir = __DIR__ . "/certs/";
$p12File = $certDir . "vendor_bundle.p12";
$p12Pass = "1500"; // 🔐 Set your actual password
$wwdrPem = $certDir . "AppleWWDRCAG3.pem";
$rootPem = $certDir . "AppleRoot.pem";

@mkdir($uploadDir, 0755, true);
@mkdir($outputDir, 0755, true);
@mkdir($certDir, 0755, true);

// Auto-download Apple certs
if (!file_exists($wwdrPem)) {
    file_put_contents("$certDir/tmp_wwdr.cer", file_get_contents("https://www.apple.com/certificateauthority/AppleWWDRCAG3.cer"));
    shell_exec("openssl x509 -inform DER -in $certDir/tmp_wwdr.cer -out $wwdrPem");
    unlink("$certDir/tmp_wwdr.cer");
}
if (!file_exists($rootPem)) {
    file_put_contents("$certDir/tmp_root.cer", file_get_contents("http://www.apple.com/appleca/AppleIncRootCertificate.cer"));
    shell_exec("openssl x509 -inform DER -in $certDir/tmp_root.cer -out $rootPem");
    unlink("$certDir/tmp_root.cer");
}

// Clean temp files
function cleanup($sessionId) {
    foreach (['uploads', 'output'] as $base) {
        $path = __DIR__ . "/$base/$sessionId";
        if (is_dir($path)) {
            array_map('unlink', glob("$path/*"));
            rmdir($path);
        }
    }
}

if (isset($_GET['download']) && $_GET['download'] === '1') {
    $file = "$outputDir/PushCertRequest.csr";
    if (!file_exists($file)) {
        http_response_code(404);
        echo "File not found.";
        exit;
    }
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="PushCertRequest.csr"');
    readfile($file);
    cleanup($sessionId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csr'])) {
    $csrPath = "$uploadDir/customer.csr";
    if (!move_uploaded_file($_FILES['csr']['tmp_name'], $csrPath)) {
        echo "❌ Failed to upload file.";
        exit;
    }

    // Check for PEM header
$fileStart = file_get_contents($csrPath, false, null, 0, 40);
if (strpos($fileStart, "BEGIN CERTIFICATE REQUEST") !== false) {
    // Convert from PEM to DER
    $convertedPath = "$uploadDir/customer_converted.csr";
    $converted = shell_exec("openssl req -inform PEM -in '$csrPath' -outform DER -out '$convertedPath' 2>&1");
    if (!file_exists($convertedPath)) {
        echo "❌ Failed to convert PEM to DER: <pre>$converted</pre>";
        exit;
    }
    rename($convertedPath, $csrPath);
}

    // Extract cert & key from p12
    $certPem = "$outputDir/vendor_cert.pem";
    $keyPem = "$outputDir/vendor_key.pem";
    shell_exec("openssl pkcs12 -in '$p12File' -passin pass:$p12Pass -clcerts -nokeys -out '$certPem'");
    shell_exec("openssl pkcs12 -in '$p12File' -passin pass:$p12Pass -nocerts -nodes -out '$keyPem'");

    // Sign CSR (DER) using SHA256
    $sigB64 = "$outputDir/signature.b64";
    shell_exec("openssl dgst -sha256 -sign '$keyPem' '$csrPath' | base64 -w 0 > '$sigB64'");

    // Base64 encode CSR
    $csrB64 = "$outputDir/customer_csr.b64";
    shell_exec("base64 -w 0 '$csrPath' > '$csrB64'");

    // Build cert chain
    $chain = "$outputDir/full_chain.pem";
    shell_exec("cat '$certPem' '$wwdrPem' '$rootPem' | awk '/BEGIN CERTIFICATE/,/END CERTIFICATE/' > '$chain'");

    // Inject all into plist
    $csrContent = file_get_contents($csrB64);
    $sigContent = file_get_contents($sigB64);
    $chainContent = file_get_contents($chain);
    $plist = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>PushCertRequestCSR</key>
  <string>$csrContent</string>
  <key>PushCertCertificateChain</key>
  <string>$chainContent</string>
  <key>PushCertSignature</key>
  <string>$sigContent</string>
</dict>
</plist>
XML;

    file_put_contents("$outputDir/PushCertRequest.plist", $plist);
    shell_exec("base64 '$outputDir/PushCertRequest.plist' > '$outputDir/PushCertRequest.csr'");

    echo "<pre>✅ CSR signed. <a href='?download=1'>Download PushCertRequest.csr</a></pre>";
}
?>
<!DOCTYPE html>
<html>
<head><title>MDM Push CSR Signer</title></head>
<body>
<h2>Upload DER or PEM-formatted Customer CSR</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="csr" required>
    <button type="submit">Sign CSR</button>
</form>
</body>
</html>
