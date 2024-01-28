import React from 'react';

class CameraComponent extends React.Component {
  constructor(props) {
    super(props);
    this.videoRef = React.createRef();
    this.canvasRef = React.createRef();
  }

  componentDidMount() {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: { exact: 'environment' } } })
      .then(stream => {
        if (this.videoRef.current) {
          this.videoRef.current.srcObject = stream;
        }
      })
      .catch(error => {
        console.error('Error accessing the camera:', error);
      });
  }

  stopVideoStream() {
    const video = this.videoRef.current;
    if (video && video.srcObject) {
      const stream = video.srcObject;
      const tracks = stream.getTracks();
      tracks.forEach(track => track.stop());
      video.srcObject = null;
    }
  }

  captureImage = () => {
    const video = this.videoRef.current;
    const canvas = this.canvasRef.current;
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    if (video && canvas) {
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      const dataURL = canvas.toDataURL('image/png');
      this.props.onCapture(dataURL); 
    }
  };

  render() {
    return (
      <div>
        <video ref={this.videoRef} autoPlay width="100%"></video>
        <button className="btn btn-dark camera" onClick={this.captureImage}><i className="fas fa-camera"></i></button>
        <canvas ref={this.canvasRef} style={{ display: 'none' }}></canvas>
      </div>
    );
  }
}

export default CameraComponent;
