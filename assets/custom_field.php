<?php

  // add custom text field - not visile (has css style diplay: none)
  function tshirt_custom_option(){
    $value = isset( $_POST['_custom_option' ] ) ? sanitize_text_field( $_POST['_custom_option'] ) : '';
    printf( '<p><label class="tshirt_d_none">%s<input id="tshirt_custom_field" class="tshirt_d_none" name="_custom_option" value="%s" /></label></p>', __( 'Enter your custom text', 'tshirt-plugin-textdomain' ), esc_attr( $value ) );
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'tshirt_custom_option', 9 );


  // check if field is not empty - NOT used.  I want to allow order product without custom text
  function tshirt_add_to_cart_validation( $passed, $product_id, $qty ){
      if( isset( $_POST['_custom_option'] ) && sanitize_text_field( $_POST['_custom_option'] ) == '' ){
          $product = wc_get_product( $product_id );
          wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some custom text.', 'tshirt-plugin-textdomain' ), $product->get_title() ), 'error' );
          return false;
      }
      return $passed;
  }
  // add_filter( 'woocommerce_add_to_cart_validation', 'tshirt_add_to_cart_validation', 10, 3 );


  function tshirt_add_cart_item_data( $cart_item, $product_id ) {
    if( isset( $_POST['_custom_option'] ) ) {
        $cart_item['custom_option'] = sanitize_text_field( $_POST[ '_custom_option' ] );
    }
    return $cart_item;
  }
  add_filter( 'woocommerce_add_cart_item_data', 'tshirt_add_cart_item_data', 10, 2 );


  /**
   * Load cart data from session
   */
  function tshirt_get_cart_item_from_session( $cart_item, $values ) {
      if ( isset( $values['custom_option'] ) ){
          $cart_item['custom_option'] = $values['custom_option'];
      }
      return $cart_item;
  }
  add_filter( 'woocommerce_get_cart_item_from_session', 'tshirt_get_cart_item_from_session', 20, 2 );



  /**
   * Add metadata
   */
  function custom_order_item_meta( $item, $cart_item_key, $values, $order ) {
      if ( isset( $values['custom_option'] ) ) {
          $item->update_meta_data( __('custom option', 'woocommerce'), $values['custom_option'] );
      }
  }
  add_action( 'woocommerce_checkout_create_order_line_item', 'custom_order_item_meta', 20, 4 );



  /**
   * Display entered value in cart
   */
  function tshirt_get_item_data( $other_data, $cart_item ) {
      if ( isset( $cart_item['custom_option'] ) ){
          $other_data[] = array(
              'key' => __( 'Your custom text', 'tshirt-plugin-textdomain' ),
              'display' => sanitize_text_field( $cart_item['custom_option'] )
          );
      }
      return $other_data;
  }
  add_filter( 'woocommerce_get_item_data', 'tshirt_get_item_data', 10, 2 );



  function tshirt_order_item_display_meta_key( $display_key, $meta, $order_item ){

      if( $meta->key == 'custom_option' ){
          $display_key =  __( 'Your custom text', 'tshirt-plugin-textdomain' );
      }
      return $display_key;
  }
  add_filter( 'woocommerce_order_item_display_meta_key', 'tshirt_order_item_display_meta_key', 10, 3 );




  /**
   * Show custom text in backend order table
   */
  function add_admin_order_item_custom_fields( $item_id, $item ) {
      if( $item->get_type() !== 'line_item' ) return;

      $value = $item->get_meta('custom_option');

      if ( ! empty($value) ) {
          echo '<table cellspacing="0" class="display_meta">';

          if ( ! empty($value) ) {
              echo '<tr><th>' . __("Weight", "woocommerce") . ':</th><td>' . $value . 'g</td></tr>';
          }
          echo '</table>';
      }
  }
  add_action( 'woocommerce_before_order_itemmeta', 'add_admin_order_item_custom_fields', 10, 2 );
