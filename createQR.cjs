//Command node createQR.cjs

const QRCode = require('qrcode');

// Data to be encoded
const data = "https://www.hitchapp.se:40888/applink";

// Generating the QR Code
QRCode.toFile('hitch_applink.png', data, function (err) {
    if (err) throw err;
    console.log('QR Code saved to hitchapp_qrcode.png');
});
