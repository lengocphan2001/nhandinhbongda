@echo off
echo Creating SSL Certificate for nhandinhbongda.io.vn
echo.

REM Create SSL directory if it doesn't exist
if not exist "C:\xampp\apache\conf\ssl" mkdir "C:\xampp\apache\conf\ssl"

REM Navigate to SSL directory
cd /d "C:\xampp\apache\conf\ssl"

REM Generate private key
echo Generating private key...
openssl genrsa -out nhandinhbongda.io.vn.key 2048

REM Generate certificate signing request
echo Generating certificate signing request...
openssl req -new -key nhandinhbongda.io.vn.key -out nhandinhbongda.io.vn.csr -subj "/C=VN/ST=HoChiMinh/L=HoChiMinh/O=XOILAC TV/CN=nhandinhbongda.io.vn"

REM Generate self-signed certificate (valid for 365 days)
echo Generating self-signed certificate...
openssl x509 -req -days 365 -in nhandinhbongda.io.vn.csr -signkey nhandinhbongda.io.vn.key -out nhandinhbongda.io.vn.crt

REM Create chain file (copy of certificate for chain)
copy nhandinhbongda.io.vn.crt nhandinhbongda.io.vn.chain.crt

echo.
echo SSL Certificate created successfully!
echo Files location: C:\xampp\apache\conf\ssl\
echo.
echo Note: This is a self-signed certificate. For production, use Let's Encrypt or a trusted CA.
pause

