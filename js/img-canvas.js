
class ImgCanvas {
  constructor( canvas ) {

    this.canvas = canvas;
    this.ctx = this.canvas.getContext('2d');

    // Set default values
    this.txColor = 'black';
    this.shirtColor = 1;  // white
    this.img = '';
    this.text = '';
  }

  // on resize
  onWindowResize() {
    const canvas_cont = document.getElementById('canvas_cont');
    this.canvas.width = canvas_cont.clientWidth -2;
    this.canvas.height = 460;

    this.setBgImg();
  }




  // load image from file
  loadImg( path ) {
    const img = new Image();

    return new Promise((resolve, reject) => {
      if(path) {
        this.img_path = path;
        img.src = `${path}`;
        img.onload = function( res ) {
          resolve( img );
        }
      } else {
        console.error('can not load bg file');
        reject();
      }
    });
  }


  // Set background tshirt img
  async setBgImg( bg_color ) {

    // LOAD ALL images in constr ?

    let shirtColor, bg_img;
    let bg = ['tshirt-m-black.png', 'tshirt-m-white.png', 'tshirt-m-red.png', 'tshirt-m-blue.png'];

    this.clearCanvas();

    // load bg tshirt img
    if(bg_color) this.shirtColor = bg_color;
    let bg_path = `${tshirtWp.pluginUrl}/tshirt-product-viewer/assets/pic/${bg[this.shirtColor]}`;

    bg_img = await this.loadImg( bg_path );

    let x = this.canvas.width /2 -200;
    this.ctx.drawImage(bg_img, x, -20, 400, 400);

    if(this.img) this.setCustomImage();
    if(this.text) this.setCustomText();
  }


  // Change custom image
  async changeCustomImage( path, bg_color, size ){
    this.clearCanvasObj();
    this.setBgImg();

    // load element
    if ( path ) {
      this.img = await this.loadImg( path );
    }

    this.setCustomImage( size );
  }

  // Set custom image
  setCustomImage( size ){
    let a;
    size == 'l' ? a = 140 : a = 80;
    let x = this.canvas.width /2 -a/2;

    this.ctx.drawImage(this.img, x, 160-a, a, a);
  }


  // Change custom text
  changeCustomText( text, color, size ) {
    if( color ) this.txColor = color;
    if( text && text.length <= 12 ) {
      this.text = text;
    } else if (text == ''){
      this.text = '';
    }
    this.setBgImg();
  }


  // Set custom text
  setCustomText( text, size ) {
    let x = this.canvas.width /2;
    let font_size = '16pt';
    if (this.text.length < 7) font_size = '30pt';

    this.ctx.font = `${font_size} Righteous`;
    this.ctx.textAlign = "center";
    this.ctx.textBaseline = "middle";
    this.ctx.fillStyle = this.txColor;

    this.ctx.fillText( this.text, x , 118 );

    this.ctx.needsUpdate = true;
  }


  // Clear elements - text, img
  clearCanvasObj() {
    this.img = '';
    this.text = '';
  }

  // Clear canvas
  clearCanvas() {
    this.ctx.fillStyle = this.color;
    this.ctx.clearRect(0, 0, canvas.width, canvas.height);
  }


}
