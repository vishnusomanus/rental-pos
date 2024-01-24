import React, { useEffect, useRef } from 'react';
import * as Html5Qrcode from 'html5-qrcode';

const QRCodeScanner = ({ onScanComplete }) => {
  const qrCodeScannerRef = useRef(null);

  useEffect(() => {
    startScanner();

    return () => {
      stopScanner();
    };
  }, []);

  const startScanner = () => {
    const qrCodeSuccessCallback = (result) => {
      // Call the onScanComplete prop with the scanned value
      onScanComplete(result);
    };

    const qrCodeErrorCallback = (error) => {
      //console.error(error);
    };

    try {
      if (!qrCodeScannerRef.current) {
        qrCodeScannerRef.current = new Html5Qrcode.Html5Qrcode('qr-code-reader');
      }
      qrCodeScannerRef.current.start(
        { facingMode: "environment" },
        { qrbox: 250 },
        qrCodeSuccessCallback,
        qrCodeErrorCallback
      );
    } catch (error) {
      console.error(error);
    }
  };

  const stopScanner = () => {
    try {
      if (qrCodeScannerRef.current) {
        qrCodeScannerRef.current.stop()
          .then(() => {
            qrCodeScannerRef.current.clear();
            qrCodeScannerRef.current = null;
          })
          .catch((error) => {
            console.error(error);
          });
      }
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div style={{ width: "100%" }}>
      <div id="qr-code-reader"></div>
    </div>
  );
};

export default QRCodeScanner;
