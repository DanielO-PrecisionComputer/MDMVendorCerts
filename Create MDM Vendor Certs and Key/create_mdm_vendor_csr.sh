#!/bin/bash

mkdir -p output

# File paths
KEY="output/vendor_signing_key.pem"
CSR="output/vendor_signing_request.csr"

# Customize your subject here
ORG_NAME="Precision Computer MSP"
COMMON_NAME="Apple MDM Vendor: $ORG_NAME"

# Step 1: Generate 2048-bit RSA private key
openssl genrsa -out "$KEY" 2048

# Step 2: Generate CSR with only Common Name
openssl req -new -key "$KEY" -out "$CSR" -subj "/CN=$COMMON_NAME"

# Confirm
echo "✅ Private key: $KEY"
echo "✅ CSR ready to upload to Apple: $CSR"
