#!/bin/bash

# === Configuration ===
ORG_NAME="Customer Company Name"
OUTPUT_DIR="customer_csr"
KEY_FILE="$OUTPUT_DIR/customer_key.pem"
CSR_FILE="$OUTPUT_DIR/customer.csr"

# === Setup ===
mkdir -p "$OUTPUT_DIR"

# === Step 1: Generate private key ===
openssl genrsa -out "$KEY_FILE" 2048

# === Step 2: Generate CSR in DER format ===
openssl req -new -key "$KEY_FILE" -outform DER -out "$CSR_FILE" -subj "/CN=$ORG_NAME"

# === Summary ===
echo "✅ CSR and key generated:"
echo "  CSR: $CSR_FILE"
echo "  Key: $KEY_FILE"
