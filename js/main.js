//  2d Canvas based product designer

  // load file
  const readFile = (e) => {
    return new Promise((resolve, reject) => {

      const file = e.target.files[0];
      if(!file) {
        reject();
      } else {
        const reader = new FileReader();
        reader.onload = function(e) {
          let content = e.target.result;

          resolve(content);
        }
        reader.readAsDataURL(file);
      }
    });
  }


  // create buttons panel
  const createButtonsPanel = ( canv_cont ) => {
  	const colors = [ 'black', 'white', 'red', 'blue' ];

  	const panel = document.createElement('div');
  	panel.id = 'btn_panel';
  	panel.classList.add('btn_panel');


    // select t-shirt color
    const select_color_cont = document.createElement('div');
    select_color_cont.classList.add('input_cont');
    const select_color_label = document.createElement('label');
    select_color_label.htmlFor = 'select_color';
    select_color_label.innerHTML = 'Shirt color:';

    const select_color = document.createElement('select');
    select_color.id = 'select_txColor';
    select_color.classList.add('select');


    //create and append options
    for (let i = 0; i < colors.length; i++) {
      let option = document.createElement('option');
      option.value = i;
      option.text = colors[i];
      select_color.appendChild(option);
    }
    select_color.value = 1;
    // Change tshirt color event
    select_color.addEventListener('change', () => {
      imgCanvas.setBgImg( select_color.value );
    });


  	// create file input not displayed
  	const load_file = document.createElement('input');
  	load_file.id = 'file_input';
  	load_file.type = 'file';
  	load_file.addEventListener( 'change', async (e) => {
      let file = await readFile(e);
      imgCanvas.changeCustomImage(file, 'm');
    }, false );

  	// load btn handle file input
  	const load_btn = document.createElement('a');
  	load_btn.id = 'load_btn';
  	load_btn.classList.add('btn');
  	load_btn.innerHTML = 'Load image';
  	load_btn.addEventListener('click', () => {
  		load_file.click();
  	});


  	// text input
    const text_cont = document.createElement('div');
    text_cont.classList.add('input_cont');

  	const text_input = document.createElement('input');
  	text_input.type = 'text';
  	text_input.id = 'text_input';
  	text_input.classList.add('text_input');
  	text_input.placeholder = 'your text';

  	text_input.addEventListener('click', () => {
  		text_input.value = '';
  		imgCanvas.clearCanvasObj();
      imgCanvas.setBgImg();
      // clean text input field value
      document.getElementById('tshirt_custom_field').value = '';
  	});
  	text_input.addEventListener('keyup', () => {
  		imgCanvas.changeCustomText( text_input.value, null );

      // change tshirt custom field val
      document.getElementById('tshirt_custom_field').value = text_input.value;
  	});

  	// select text colors
  	const select_txColor = document.createElement('select');
  	select_txColor.id = 'select_txColor';
  	select_txColor.classList.add('select');
  	//create and append options
  	for (let i = 0; i < colors.length; i++) {
  		let option = document.createElement('option');
  		option.value = colors[i];
  		option.text = colors[i];
  		select_txColor.appendChild(option);
  	}
  	select_txColor.addEventListener('change', () => imgCanvas.changeCustomText( null, select_txColor.value ) );


    // append elements to panel
    select_color_cont.append( select_color_label );
    select_color_cont.append( select_color );
    panel.append( select_color_cont );
    text_cont.append(text_input);
    text_cont.append(select_txColor);
    panel.append(text_cont);
    panel.append(load_btn);

    canv_cont.append(panel);
  }




// Create canvas container
const createCanvasCont = () => {
  const canvas_cont = document.createElement('div');    // canvas type
  canvas_cont.id = 'canvas_cont';
  canvas_cont.classList.add('canvas_cont');

  const contId = `product-${tshirtProduct.id}`;
  const product_cont = document.getElementById(contId);
  product_cont.prepend(canvas_cont);

  return canvas_cont;

}


const init = () => {
  const cont = createCanvasCont();

  // create canvas
  let canvas = document.createElement('canvas');    // canvas type
  canvas.id = 'canvas';
  canvas.classList.add('canvas');
  canvas_cont.appendChild( canvas );

  canvas.width = this.canvas.parentElement.clientWidth -2;
  canvas.height = 460;
  imgCanvas = new ImgCanvas( canvas );

  createButtonsPanel( cont );
  imgCanvas.setBgImg();

  // clean text input field value
  document.getElementById('tshirt_custom_field').value = '';

  window.addEventListener('resize',() => imgCanvas.onWindowResize() );
}

window.addEventListener('load', (e) => {
  init();
});
