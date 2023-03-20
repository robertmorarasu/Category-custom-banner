<?php
/**
 * Plugin Name: OMX Category custom banner
 * Plugin URI: https://github.com/robertmorarasu/OMX-Category-custom-banner
 * GitHub Plugin URI: https://github.com/robertmorarasu/OMX-Category-custom-banner
 * Description: A custom plugin who add an image in Product Categories page description. 
 * Author: Morarasu Robert
 * Copyright (c) 2023 [Morarasu Robert]
 * Version: 1.0
 */
 
 

 
 if ( ! defined( 'ABSPATH' ) ) {	exit(0);}
 
 

/**

The Showcase_Taxonomy_Images class adds an image field to product category pages in WordPress.
This class handles the image-related actions and filters.
*/

if( ! class_exists( 'Showcase_Taxonomy_Images' ) ) {
  class Showcase_Taxonomy_Images {
    
    public function __construct() {
     //
    }

    /**
     * Initiate the class and start calling our hooks and filters
     */
     public function init() {
     // Image actions
     add_action( 'product_cat_add_form_fields', array( $this, 'add_category_image' ), 10, 2 );
     add_action( 'created_product_cat', array( $this, 'save_category_image' ), 10, 2 );
     add_action( 'product_cat_edit_form_fields', array( $this, 'update_category_image' ), 10, 2 );
     add_action( 'edited_product_cat', array( $this, 'updated_category_image' ), 10, 2 );
     add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
     add_action( 'admin_footer', array( $this, 'add_script' ) );
   }



/**

Load media for the category image field.
This method enqueues the WordPress media scripts and styles if the current page is a product category page.
@since 1.0.0
*/

   public function load_media() {
     if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != 'download_category' ) {
       return;
     }
     wp_enqueue_media();
   }
  
   /**
    * Adding a form field in the new category page
    * @since 1.0.0
    */
  
   public function add_category_image( $taxonomy ) { 
   ?>
     <div class="form-field term-group">
       <label for="showcase-taxonomy-image-id"><?php _e( 'Image', 'showcase' ); ?></label>
       <input type="hidden" id="showcase-taxonomy-image-id" name="showcase-taxonomy-image-id" class="custom_media_url" value="">
       <div id="category-image-wrapper"></div>
       <p>
         <input type="button" class="button button-secondary showcase_tax_media_button" id="showcase_tax_media_button" name="showcase_tax_media_button" value="<?php _e( 'Add Image Category', 'showcase' ); ?>" />
         <input type="button" class="button button-secondary showcase_tax_media_remove" id="showcase_tax_media_remove" name="showcase_tax_media_remove" value="<?php _e( 'Remove Image Category', 'showcase' ); ?>" />
       </p>
     </div>
   <?php }

   /**
    * Save the form field
    * @since 1.0.0
    */
   public function save_category_image( $term_id, $tt_id ) {
     if( isset( $_POST['showcase-taxonomy-image-id'] ) && '' !== $_POST['showcase-taxonomy-image-id'] ){
       add_term_meta( $term_id, 'showcase-taxonomy-image-id', absint( $_POST['showcase-taxonomy-image-id'] ), true );
     }
    }

    /**
     * Edit the form field
     * @since 1.0.0
     */
   public function update_category_image( $term, $taxonomy ) { 
	?>
      <tr class="form-field term-group-wrap">
        <th scope="row">
          <label for="showcase-taxonomy-image-id"><?php _e( 'Image', 'showcase' ); ?></label>
        </th>
        <td>
          <?php $image_id = get_term_meta( $term->term_id, 'showcase-taxonomy-image-id', true ); ?>
          <input type="hidden" id="showcase-taxonomy-image-id" name="showcase-taxonomy-image-id" value="<?php echo esc_attr( $image_id ); ?>">
          <div id="category-image-wrapper">
            <?php if( $image_id ) { ?>
              <?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
            <?php } ?>
          </div>
          <p>
            <input type="button" class="button button-secondary showcase_tax_media_button" id="showcase_tax_media_button" name="showcase_tax_media_button" value="<?php _e( 'Add Image Category', 'showcase' ); ?>" />
            <input type="button" class="button button-secondary showcase_tax_media_remove" id="showcase_tax_media_remove" name="showcase_tax_media_remove" value="<?php _e( 'Remove Image Category', 'showcase' ); ?>" />
          </p>
        </td>
      </tr>
   <?php }

   /**
    * Update the form field value
    * @since 1.0.0
    */
   public function updated_category_image( $term_id, $tt_id ) {
     if( isset( $_POST['showcase-taxonomy-image-id'] ) && '' !== $_POST['showcase-taxonomy-image-id'] ){
       update_term_meta( $term_id, 'showcase-taxonomy-image-id', absint( $_POST['showcase-taxonomy-image-id'] ) );
     } else {
       update_term_meta( $term_id, 'showcase-taxonomy-image-id', '' );
     }
   }
 
   /**
    * Enqueue styles and scripts
    * @since 1.0.0
    */
   public function add_script() {
     if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != 'product_cat' ) {
       return;
     } ?>
     <script> jQuery(document).ready( function($) {
       _wpMediaViewsL10n.insertIntoPost = '<?php _e( "Insert", "showcase" ); ?>';
       function ct_media_upload(button_class) {
         var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;
         $('body').on('click', button_class, function(e) {
           var button_id = '#'+$(this).attr('id');
           var send_attachment_bkp = wp.media.editor.send.attachment;
           var button = $(button_id);
           _custom_media = true;
           wp.media.editor.send.attachment = function(props, attachment){
             if( _custom_media ) {
               $('#showcase-taxonomy-image-id').val(attachment.id);
               $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
               $( '#category-image-wrapper .custom_media_image' ).attr( 'src',attachment.url ).css( 'display','block' );
             } else {
               return _orig_send_attachment.apply( button_id, [props, attachment] );
             }
           }
           wp.media.editor.open(button); return false;
         });
       }
       ct_media_upload('.showcase_tax_media_button.button');
       $('body').on('click','.showcase_tax_media_remove',function(){
         $('#showcase-taxonomy-image-id').val('');
         $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
       });
      
       $(document).ajaxComplete(function(event, xhr, settings) {
         var queryStringArr = settings.data.split('&');
         if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
           var xml = xhr.responseXML;
           $response = $(xml).find('term_id').text();
           if($response!=""){
             // Clear the thumb image
             $('#category-image-wrapper').html('');
           }
          }
        });
      });
    </script>
   <?php }
  }
$Showcase_Taxonomy_Images = new Showcase_Taxonomy_Images();
$Showcase_Taxonomy_Images->init(); }








// Display image and adjust css style of category description 


function robomx_category_description_image() {
  $term_id = get_queried_object_id();
  $image_id = get_term_meta( $term_id, 'showcase-taxonomy-image-id', true );
  if (is_product_category() && $image_id) {
    $image_url = wp_get_attachment_image_src( $image_id, 'full' )[0];
    ?>
    <style type="text/css">
      .woocommerce-products-header {
        background-image: url(<?php echo $image_url; ?>);
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        text-align: center;        
        padding: 50px 0;
      }
      .woocommerce-products-header h1 {
        margin-top: 0;
        font-size: 48px;
        font-weight: bold;
      }
	  
	  .term-description h2 {
    font-size: 15px;
}
	  
	  
      .woocommerce-archive-description {
        background-image: url(<?php echo $image_url; ?>);
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        padding: 50px 0;
      }
    </style>
    <?php
  }
}
add_action('woocommerce_archive_description', 'robomx_category_description_image');















// Add 	ColorPicker fields in Product Categories & Edit Category dashboard pages

add_action( 'product_cat_add_form_fields', 'robomx_woo_add_category_color_fields' );
add_action( 'product_cat_edit_form_fields', 'robomx_woo_edit_category_color_fields', 10, 2 );

function robomx_woo_add_category_color_fields() {
    ?>
    <div class="form-field">
        <label for="term_meta[title_color]"><?php _e( 'Title Color', 'robomx' ); ?></label>
        <input type="text" name="term_meta[title_color]" id="term_meta[title_color]" value="" class="robomx-color-picker" />
    </div>
    <div class="form-field">
        <label for="term_meta[subtitle_color]"><?php _e( 'Subtitle Color', 'robomx' ); ?></label>
        <input type="text" name="term_meta[subtitle_color]" id="term_meta[subtitle_color]" value="" class="robomx-color-picker" />
    </div>
    <script>
        jQuery(document).ready(function($){
            $('.robomx-color-picker').wpColorPicker();
        });
    </script>
    <?php
}



function robomx_woo_edit_category_color_fields( $term, $taxonomy ) {
    $term_id = $term->term_id;
    $term_meta = get_option( "taxonomy_$term_id" );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[title_color]"><?php _e( 'Title Color', 'robomx' ); ?></label></th>
        <td>
            <input type="text" name="term_meta[title_color]" id="term_meta[title_color]" value="<?php echo esc_attr( $term_meta['title_color'] ) ? esc_attr( $term_meta['title_color'] ) : ''; ?>" class="robomx-color-picker" />
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[subtitle_color]"><?php _e( 'Subtitle Color', 'robomx' ); ?></label></th>
        <td>
            <input type="text" name="term_meta[subtitle_color]" id="term_meta[subtitle_color]" value="<?php echo esc_attr( $term_meta['subtitle_color'] ) ? esc_attr( $term_meta['subtitle_color'] ) : ''; ?>" class="robomx-color-picker" />
        </td>
    </tr>
    <script>
        jQuery(document).ready(function($){
            $('.robomx-color-picker').wpColorPicker();
        });
    </script>
    <?php
}



add_action( 'edited_product_cat', 'robomx_woo_save_category_color_fields', 10, 2 );
add_action( 'create_product_cat', 'robomx_woo_save_category_color_fields', 10, 2 );

function robomx_woo_save_category_color_fields( $term_id, $tt_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $term_meta = get_option( "taxonomy_$term_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        update_option( "taxonomy_$term_id", $term_meta );
    }
}











// Add conexion between ColorPicker fields and Category Description Title/Subtitle in Product Categories front page

add_action( 'woocommerce_archive_description', 'robomx_woo_category_description', 2 );
function robomx_woo_category_description() {
    if ( is_product_category() ) {
        $term_id = get_queried_object_id();
        $term_meta = get_option( "taxonomy_$term_id" );
        $title_color = isset( $term_meta['title_color'] ) ? $term_meta['title_color'] : '#000000';
        $subtitle_color = isset( $term_meta['subtitle_color'] ) ? $term_meta['subtitle_color'] : '#000000';
        $term = get_queried_object();
        if ( $term && $term->description ) { ?>
            <div class="term-description">
                <h1 style="color: <?php echo $title_color; ?>"><?php echo $term->name; ?></h1>
                <?php if ( $term->description ) { ?>
                    <h2 style="color: <?php echo $subtitle_color; ?>"><?php echo $term->description; ?></h2>
                <?php } ?>
            </div>
        <?php }
    }
}















// Override the archive-product.php file

add_filter('woocommerce_locate_template', 'omx_custom_woocommerce_template', 999, 3);

function omx_custom_woocommerce_template($template, $template_name, $template_path) {
    $plugin_path = plugin_dir_path(__FILE__) . 'woocommerce/templates/';

    // Override archive-product.php
    if ($template_name == 'archive-product.php' && file_exists($plugin_path . $template_name)) {
        $template = $plugin_path . $template_name;
    }

    return $template;
}


// Alias the conflicting function
if (!function_exists('omx_wc_template_redirect')) {
    function omx_wc_template_redirect() {
        wc_template_redirect();
    }
    add_action('template_redirect', 'omx_wc_template_redirect');
}











// Remove Category Description 

function remove_term_description_class() {
    remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
}
add_action( 'woocommerce_archive_description', 'remove_term_description_class', 5 );





