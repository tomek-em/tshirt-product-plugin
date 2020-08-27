<?php
/**
*Plugin Name: T-Shirt Product Viewer
*Author: Tomasz Mejer
*Description: T-shirt product viewer. Use T-shirt product viewer form frommain Wp admin menu to add product viewer to woocommerce product.
**/

  // Exit if accessed directly
  if(!defined('ABSPATH')) {
    exit;
  }

  // require custom tshirt custom field php file
  require_once(plugin_dir_path(__FILE__).'/assets/custom_field.php');


  // Enqueu CSS
  function tshirt_enqueue_styles() {
      wp_enqueue_style( 'tshirt_product_css', plugins_url() . '/tshirt-product-viewer/css/product_viewer.css' );
  }
  add_action('wp_enqueue_scripts', 'tshirt_enqueue_styles');



  // Init - register scripts
  function register_tshirt_scripts() {
    wp_register_script( 'tshirt_img_canvas_js', plugins_url().'/tshirt-product-viewer/js/img-canvas.js' );
    wp_register_script( 'tshirt_main_js', plugins_url().'/tshirt-product-viewer/js/main.js' );
  }
  add_action('wp_head', 'register_tshirt_scripts');


  // Enqueue scripts
  function enqueue_tshirt_scripts() {
    wp_enqueue_script( 'tshirt_img_canvas_js' );
    wp_enqueue_script( 'tshirt_main_js' );
  }


  function show_tshirt_product_viewer () {

    global $product;
    $product_id = $product->get_id();
    $product_name = $product->get_name();

    // get currnt product category
    foreach( wp_get_post_terms( get_the_id(), 'product_cat' ) as $term ){
      if( $term ){
        $product_category = $term->name;
      }
    }
    // get tshirt options
    $tshirt_name = get_option('tshirt_title');
    $tshirt_category = get_option('tshirt_category');
    $tshirt_path = get_option('tshirt_path');

    // echo $product_category.' '.$tshirt_category.' '.$terms->name.' <br/>';

    // show viewer for selected products
    if ( ($product_name == $tshirt_name) || ($product_category == $tshirt_category) ) {


      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
      remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

      $wpVar = array(
        'themeUrl' => get_template_directory_uri(),
        'pluginUrl' => plugins_url()
      );

      $prod = array(
          'id' => $product_id,
          'category' => $product_category
          );
      ?>
      <script type="text/javascript">
        let tshirtProduct = <?php echo json_encode($prod); ?>;
        let tshirtWp = <?php echo json_encode($wpVar, JSON_UNESCAPED_SLASHES); ?>;
      </script> <?php

      // Enqueue js and css product viewer scripts
      ?> <h3 class="extra_title"><?php the_title(); ?></h3> <?php
      // enqueue scripts
      enqueue_tshirt_scripts();
    }  // end of if

  }
  // add_action('wp_head','show_tshirt_product_viewer');
  add_action('woocommerce_before_single_product','show_tshirt_product_viewer');






  // Add shortcode function
  function show_tshirt_product_shortcode() {
    // Enqueue scripts
    wp_enqueue_script( 'tshirt_main_js' );

    $tshirt_title = get_option('tshirt_title','none');
    $tshirt_path = get_option('tshirt_path','none');

    $content .= "<h2>3d product viewer</h2><br/>";
    $content .= '<h3>'. $tshirt_title. '</h3>';
    $content .= $tshirt_path;

    return $content;
  }
  add_shortcode('tshirt-prod','show_tshirt_product_shortcode');




  // PRODUCT FORM --
  // Add product admin page
  function tshirt_admin_menu_option() {
    // enqueue bootstrap
    wp_enqueue_style('bootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');

    add_menu_page('tshirt product scripts','T-Shirt Product Viewer','manage_options','tshirt-admin-menu','tshirt_prod_viewer_page','',200);
  }
  add_action('admin_menu','tshirt_admin_menu_option');


  // Product form
  function tshirt_prod_viewer_page() {
    // update values on submit
    if(array_key_exists('submit_scripts_update',$_POST)) {
      update_option('tshirt_title', $_POST['tshirt_title']);
      update_option('tshirt_category', $_POST['tshirt_category']);
      update_option('tshirt_path', $_POST['tshirt_file_path']);
      ?>
      <div id="setting-error-settings-updated" class="updated settings-error notice is-dismissible"><strong>Settings have been saved.</strong></div>
      <?php
    }

    // fetch data to show in form
    $tshirt_title = get_option('tshirt_title','none');
    $tshirt_category = get_option('tshirt_category','none');
    $tshirt_path = get_option('tshirt_path','none');

    ?>
    <div class="wrap">
      <h2>T-shirt Product info</h2>
        <div class="row">
          <div class="md-6 mx-3">
            <form method="post" action="">
              <label for="tshirt_title">T-shirt product title</label>
              <input name="tshirt_title" class="large-text" value="<?php print $tshirt_title; ?>">
              <label for="tshirt_category">Product category</label>
              <input name="tshirt_category" class="large-text" value="<?php print $tshirt_category; ?>">
              <label for="tshirt_file_path">T-shirt file path</label>
              <input name="tshirt_file_path" class="large-text" value="<?php print $tshirt_path; ?>">
              <br/><br/>
              <input type="submit" name="submit_scripts_update" class="button button-primary" value="UPDATE SCRIPTS">
            </form>
          </div>
        </div>
    </div>
    <?php
  }


  // add values to head
  function tshirt_display_header_scripts() {
    $tshirt_scripts = get_option('tshirt_title','none');
    print $tshirt_scripts;
  }
  // add_action('wp_head','tshirt_display_header_scripts');
