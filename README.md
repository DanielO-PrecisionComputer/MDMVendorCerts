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
<div id="bkmrk-5.-then-customer-log">5. Then customer logs into their own account <a href="https://identity.apple.com/pushcert/">Apple Push Certificates Portal</a> (or we do if were managing for them, or use ours if this is for us)</div>
<div id="bkmrk-6.-renew-existing-or">6. Renew existing or Create New (Depending what you need to do) and upload PushCertRequest.csr (That you downloaded from step 4)</div>
<div id="bkmrk-7.-download-the-pem">7. Download the PEM</div>
<div id="bkmrk-8.-upload-pem-to-sam">8. Upload PEM to Same Folder as Step 4 (customer_csr) and now you have all the pieces, you may convert to a p12 if need to import to Jamf (Just don't confuse this .p12 with Vendor_bundle.p12)</div>
