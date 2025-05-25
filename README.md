# MDMVendorCerts
MDM Vendor Certs and Key Creation



<h2 id="bkmrk-signing-customer-csr">Signing Customer CSR Requests Script</h2>
<p id="bkmrk-script%3A-mdmvendor_si">Script: MDMVendor_SigningCustomerCSR_Script</p>
<p id="bkmrk-replace%3A-vendor_bund">Add: vendor_bundle.p12 to certs folder and update password in script</p>
<p id="bkmrk-upload-sign_and_down">Upload sign_and_download.php to host (can be internal or behind proxy with passcode requirement, NOT READY FOR PUBLIC SIDE)</p>
<p id="bkmrk-make-sure-the-folder">Make sure the folders are writeable chmod 777</p>
<p id="bkmrk-%2Aif-still-using-afte">*IF Still using after 2030ish, you may need to update certs to one that matches what Apple says, or when you get your Vendor MDM.cer open to see what the upstream chain is and make sure it matches to script so you can have it included in PushCertCertificateChain</p>
<p id="bkmrk-%C2%A0"></p>
<h2 id="bkmrk-create-mdm-vendor-ce">Create MDM Vendor Certs and Key</h2>
<p id="bkmrk-if-you-don%27t-have-va">If you don't have valid vendor_bundle.p12 to sign the Customer CSR Request, use this script</p>
<p id="bkmrk-create-mdm-vendor-ce-1">Create MDM Vendor Certs and Key</p>
<blockquote id="bkmrk-%23%21%2Fbin%2Fbash-mkdir--p">
<div>
<div>#!/bin/bash</div>
<br>
<div>mkdir -p output</div>
<br>
<div># File paths</div>
<div>KEY="output/vendor_signing_key.pem"</div>
<div>CSR="output/vendor_signing_request.csr"</div>
<br>
<div># Customize your subject here</div>
<div>ORG_NAME="Precision Computer MSP"</div>
<div>COMMON_NAME="Apple MDM Vendor: $ORG_NAME"</div>
<br>
<div># Step 1: Generate 2048-bit RSA private key</div>
<div>openssl genrsa -out "$KEY" 2048</div>
<br>
<div># Step 2: Generate CSR with only Common Name</div>
<div>openssl req -new -key "$KEY" -out "$CSR" -subj "/CN=$COMMON_NAME"</div>
<br>
<div># Confirm</div>
<div>echo "✅ Private key: $KEY"</div>
<div>echo "✅ CSR ready to upload to Apple: $CSR"</div>
</div>
</blockquote>
<div id="bkmrk-make-sure-output-is-">1. Make sure output folder is writable if have error (script should auto create it, if not, make it and chmod it to 777)</div>
<div id="bkmrk-"></div>
<div id="bkmrk-to-run-script-%28you-m">2. Run script (you may also be in sudo su if having permission issues):</div>
<blockquote id="bkmrk-chmod-%2Bx-create_mdm_">
<div>chmod +x create_mdm_vendor_csr.sh<br>./create_mdm_vendor_csr.sh</div>
</blockquote>
<div id="bkmrk-3.-take-the-csr-and-">3. Take the CSR and Create New Certificate in:&nbsp;<a href="https://developer.apple.com/account/resources/certificates/list">Certificates, Identifiers &amp; Profiles - Apple Developer</a> (MDM Vendor) and upload this CSR</div>
<div id="bkmrk-4.-download-mdm.cer-">4. Download MDM.cer and Upload to same output folder as key and csr</div>
<div id="bkmrk-5.-run-command-to-cr">5. Run Command to Create .p12 from the key and Cer File</div>
<blockquote id="bkmrk-openssl-pkcs12--expo">
<div>openssl pkcs12 -export&nbsp;-in mdm.cer&nbsp;-inkey vendor_signing_key.pem&nbsp;-out vendor_bundle.p12&nbsp;-name "MDM Vendor Cert"</div>
</blockquote>
<div id="bkmrk-%2Apretty-sure-that-wa">*Pretty sure that was the command I used that worked :)</div>
<h2 id="bkmrk-create-customer-push">Create Customer Push Certificates and CSR&nbsp;</h2>
<p id="bkmrk-custom-solution-%28not">Custom Solution (not using JamfPro or Mostyle, or need to import your own? or diagnostics?)</p>
<p id="bkmrk-generate_customer_cs">generate_customer_csr</p>
<blockquote id="bkmrk-%23%21%2Fbin%2Fbash-%23-%3D%3D%3D-co">
<div>
<div>#!/bin/bash</div>
<br>
<div># === Configuration ===</div>
<div>ORG_NAME="<strong>Customer Company Name</strong>"</div>
<div>OUTPUT_DIR="customer_csr"</div>
<div>KEY_FILE="$OUTPUT_DIR/customer_key.pem"</div>
<div>CSR_FILE="$OUTPUT_DIR/customer.csr"</div>
<br>
<div># === Setup ===</div>
<div>mkdir -p "$OUTPUT_DIR"</div>
<br>
<div># === Step 1: Generate private key ===</div>
<div>openssl genrsa -out "$KEY_FILE" 2048</div>
<br>
<div># === Step 2: Generate CSR in DER format ===</div>
<div>openssl req -new -key "$KEY_FILE" -outform DER -out "$CSR_FILE" -subj "/CN=$ORG_NAME"</div>
<br>
<div># === Summary ===</div>
<div>echo "✅ CSR and key generated:"</div>
<div>echo " &nbsp;CSR: $CSR_FILE"</div>
<div>echo " &nbsp;Key: $KEY_FILE"</div>
</div>
</blockquote>
<div id="bkmrk-make-sure-to-replace">1. Make sure to replace Customer Company Name</div>
<div id="bkmrk--1"></div>
<div id="bkmrk-make-sure-customer_c">
<div id="bkmrk-make-sure-customer_c-1">2. Make sure customer_csr folder is writable if have error (script should auto create it, if not, make it and chmod it to 777)</div>
<div id="bkmrk--2"></div>
<div id="bkmrk-to-run-script-%28you-m-1">3. To run script (you may also be in sudo su if having permission issues):</div>
</div>
<blockquote id="bkmrk-chmod-%2Bx-generate_cu">
<div>chmod +x generate_customer_csr.sh<br>./generate_customer_csr.sh</div>
</blockquote>
<div id="bkmrk-4.-in-the-customer_c">4. In the customer_csr folder you will get key and csr, use csr and run against MDM Vendor Customer Signing Script (Download Signed PushCertRequest.csr)</div>



<h2 id="bkmrk-signing-customer-csr">Push Cert Format for MDM</h2>
<p id="bkmrk-after-months-of-rese">After months of research (over time, not consecutively) have gotten very close but still cannot figure out what is missing...</p>
<p id="bkmrk-%C2%A0">As of 12:41am on 5/25/2025 - I finally got a successful signing!!!</p>
<h2 id="bkmrk-format">Format</h2>
<p id="bkmrk-this-is-all-base64-e">This is all Base64 encoded into file that can have just about any extension for .plist to .cert or seems like any other name, before encoding (or when decoding) this is what it is:</p>
<blockquote id="bkmrk-%3C%3Fxml-version%3D%221.0%22-">
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;?xml version="1.0" encoding="UTF-8"?&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd"&gt; &lt;plist version="1.0"&gt;</span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;dict&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;key&gt;PushCertCertificateChain&lt;/key&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;string&gt;</span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">-----BEGIN CERTIFICATE-----</span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">MIIFjTCCBHWgAwIBAgIQZvXABGJtyHRHKOpJCIWzRzANBgkqhkiG9w0BAQsFADB1</span></p>
<p class="MsoNormal" style="line-height: normal;"><strong><span style="font-size: 11.0pt; color: red;">PEM </span></strong><strong><span lang="EN-US" style="font-size: 11.0pt; color: red; mso-ansi-language: EN-US;">Version of MDM.cer that is downloaded from Developer for MDM Vendor Request</span></strong></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">JxQdqymd53+zCJnUVggEvN9U6vcW0neCvJE+lGoBQXDW</span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">-----END CERTIFICATE----- </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">-----BEGIN CERTIFICATE----- </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">MIIEUTCCAzmgAwIBAgIQfK9pCiW3Of57m0R6wXjF7jANBgkqhkiG9w0BAQsFADBi </span></p>
<p class="MsoNormal" style="line-height: normal;"><strong><span style="font-size: 11.0pt; color: red;">PEM Version of AppleWWDRCAG3.cer</span></strong></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">NwMUGdXqapSqqdv+9poIZ4vvK7iqF0mDr8/LvOnP6pVxsLRFoszlh6oKw0E6eVza </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">UDSdlTs= </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">-----END CERTIFICATE----- </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">-----BEGIN CERTIFICATE----- </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">MIIEuzCCA6OgAwIBAgIBAjANBgkqhkiG9w0BAQUFADBiMQswCQYDVQQGEwJVUzET </span></p>
<p class="MsoNormal" style="line-height: normal;"><strong><span style="font-size: 11.0pt; color: red;">PEM Version of AppleIncRootCertificate.cer</span></strong></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">IQ7aunMZT7XZNn/Bh1XZp5m5MkL72NVxnn6hUrcbvZNCJBIqxw8dtk2cXmPIS4AX </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">UKqK1drk/NAJBzewdXUh </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">-----END CERTIFICATE----- </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;/string&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;key&gt;PushCertRequestCSR&lt;/key&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;string&gt;</span><strong><span style="font-size: 11.0pt; color: red;">Customer CSR</span></strong><span style="font-size: 11.0pt;">&lt;/string&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;key&gt;PushCertSignature&lt;/key&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;string&gt;<span style="color: rgb(224, 62, 45);"><strong>Generated PushCertSignature</strong></span>&lt;/string&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;/dict&gt; </span></p>
<p class="MsoNormal" style="line-height: normal;"><span style="font-size: 11.0pt;">&lt;/plist&gt;</span></p>
</blockquote>
<p id="bkmrk-if-in-pem-format-con" class="MsoNormal" style="line-height: normal;">If in PEM format convert to DER:</p>
<blockquote id="bkmrk-openssl-req--in-%22pus">
<p class="MsoNormal" style="line-height: normal;">openssl req -in "PushCertificateCSR.certSigningRequest" -outform DER -out customer.csr</p>
</blockquote>
<p id="bkmrk-for-pushcertsignatur" class="MsoNormal" style="line-height: normal;">For PushCertSignature:</p>
<blockquote id="bkmrk-openssl-dgst--sha256">
<p class="MsoNormal" style="line-height: normal;">openssl dgst -sha256 -sign VendorPrivateKey.key customer.csr | base64 -w 0</p>
</blockquote>
<p id="bkmrk-for-pushcertrequestc" class="MsoNormal" style="line-height: normal;">For PushCertRequestCSR:</p>
<blockquote id="bkmrk-base64--w-0-customer">
<p class="MsoNormal" style="line-height: normal;">base64 -w 0 customer.csr</p>
</blockquote>
<p id="bkmrk-%C2%A0-1" class="MsoNormal" style="line-height: normal;"><br></p>
<h4 id="bkmrk-things-to-keep-in-mi" class="MsoNormal" style="line-height: normal;">Things to Keep in mind if troubleshooting:</h4>
<p id="bkmrk-sha256-signed-code-w">Sha256 signed code will come out the same hash each time as long as Key and CSR Files are the same (if one has different spacing, it may change then)</p>
<div id="bkmrk-5.-then-customer-log">5. Then customer logs into their own account <a href="https://identity.apple.com/pushcert/">Apple Push Certificates Portal</a> (or you do if your're managing for them, or use yours if this is for your devices, Apple Recommends that each Business uses their own so device keys don't accidently get shared between companies)</div>
<div id="bkmrk-6.-renew-existing-or">6. Renew existing or Create New (Depending what you need to do) and upload PushCertRequest.csr (That you downloaded from step 4)</div>
<div id="bkmrk-7.-download-the-pem">7. Download the PEM</div>
<div id="bkmrk-8.-upload-pem-to-sam">8. Upload PEM to Same Folder as Step 4 (customer_csr) and now you have all the pieces, you may convert to a p12 if need to import to Jamf (Just don't confuse this .p12 with Vendor_bundle.p12)</div>
