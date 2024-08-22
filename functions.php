<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );

//Display Cream Based Fondant, and Picture Cake fields conditionally

function custom_product_options() {
    global $product;
    $show_cream_based = get_post_meta($product->get_id(), '_show_cream_based', true);
    $show_fondant = get_post_meta($product->get_id(), '_show_fondant', true);
    $show_picture_cake = get_post_meta($product->get_id(), '_show_picture_cake', true);
    $show_weight_field = get_post_meta($product->get_id(), '_show_weight_field', true);
    $show_flavour = get_post_meta($product->get_id(), '_show_flavour', true);

    if ($show_cream_based === 'yes') {
        echo '<div class="cream-based">';
        echo '<label for="cream_based">Cream Based:</label>';
        echo '<select name="cream_based" id="cream_based">';
        echo '<option value="">Select an option</option>';
        echo '<option value="70">Cream Based + 70 AED</option>';
        echo '</select>';
        echo '</div>';
    }

    if ($show_fondant === 'yes') {
        echo '<div class="fondant">';
        echo '<label for="fondant">Fondant:</label>';
        echo '<select name="fondant" id="fondant">';
        echo '<option value="">Select an option</option>';
        echo '<option value="90">Semi Fondant + 90 AED</option>';
        echo '<option value="110">Full Fondant + 110 AED</option>';
        echo '</select>';
        echo '</div>';
    }

    if ($show_picture_cake === 'yes') {
        echo '<div class="picture-cake">';
        echo '<label for="picture_cake">Picture Cake:</label>';
        echo '<select name="picture_cake" id="picture_cake">';
        echo '<option value="">Select an option</option>';
        echo '<option value="60">Picture Cake + 60 AED</option>';
        echo '</select>';
        echo '</div>';
    }

    if ($show_weight_field === 'yes') {
        echo '<div class="weight-field">';
        echo '<label for="product_weight">' . __('Weight (kg):', 'woodmart') . '</label>';
        echo '<input type="number" id="product_weight" name="product_weight" value="1" min="1" step="1">';
        echo '</div>';
    }

    if ($show_flavour === 'yes') {
        echo '<div class="flavour-field">';
        echo '<label for="flavour">' . __('Flavour:', 'woodmart') . '</label>';
        echo '<select name="flavour" id="flavour">';
        echo '<option value="">Select a flavour</option>';
        echo '<option value="Black Forest">Black Forest</option>';
        echo '<option value="Strawberry Vanilla">Strawberry Vanilla</option>';
        echo '<option value="Pineapple Vanilla">Pineapple Vanilla</option>';
        echo '<option value="Choco Truffle">Choco Truffle</option>';
        echo '<option value="Swiss Chocolate">Swiss Chocolate</option>';
        echo '<option value="Vanilla Marble">Vanilla Marble</option>';
        echo '<option value="Choco Vanilla">Choco Vanilla</option>';
        echo '</select>';
        echo '</div>';
    }
}
add_action('woocommerce_before_add_to_cart_button', 'custom_product_options');

function add_custom_cart_item_data($cart_item_data, $product_id, $variation_id) {
    if (isset($_POST['product_weight']) && $_POST['product_weight'] !== '') {
        $cart_item_data['product_weight'] = intval($_POST['product_weight']);
    }
    if (isset($_POST['cream_based']) && $_POST['cream_based'] !== '') {
        $cart_item_data['cream_based'] = sanitize_text_field($_POST['cream_based']);
    }
    if (isset($_POST['fondant']) && $_POST['fondant'] !== '') {
        $cart_item_data['fondant'] = sanitize_text_field($_POST['fondant']);
    }
    if (isset($_POST['picture_cake']) && $_POST['picture_cake'] !== '') {
        $cart_item_data['picture_cake'] = sanitize_text_field($_POST['picture_cake']);
    }
    if (isset($_POST['flavour']) && $_POST['flavour'] !== '') {
        $cart_item_data['flavour'] = sanitize_text_field($_POST['flavour']);
    }
    $cart_item_data['unique_key'] = md5(microtime().rand()); // Unique key to prevent merging
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'add_custom_cart_item_data', 10, 3);

function display_custom_cart($item_data, $cart_item) {
    if (isset($cart_item['product_weight'])) {
        $item_data[] = array(
            'key'   => __('Weight', 'woodmart'),
            'value' => $cart_item['product_weight'] . ' kg'
        );
    }
    if (isset($cart_item['cream_based'])) {
        $item_data[] = array(
            'key'   => 'Cream Based',
            'value' => wc_price($cart_item['cream_based'])
        );
    }
    if (isset($cart_item['fondant'])) {
        $item_data[] = array(
            'key'   => 'Fondant',
            'value' => wc_price($cart_item['fondant'])
        );
    }
    if (isset($cart_item['picture_cake'])) {
        $item_data[] = array(
            'key'   => 'Picture Cake',
            'value' => wc_price($cart_item['picture_cake'])
        );
    }
    if (isset($cart_item['flavour'])) {
        $item_data[] = array(
            'key'   => __('Flavour', 'woodmart'),
            'value' => ucfirst(sanitize_text_field($cart_item['flavour'])) // Capitalize first letter
        );
    }
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'display_custom_cart', 10, 2);

// Enqueue custom field price update script
function custom_enqueue_scripts() {
    wp_enqueue_script('custom-price-update', get_stylesheet_directory_uri() . '/js/custom-price-update.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

// Add custom options to product editor for both simple and variable products
function add_custom_options() {
    echo '<div class="options_group show_if_simple show_if_variable">';
    woocommerce_wp_checkbox(
        array(
            'id'            => '_show_cream_based',
            'label'         => __('Show Cream Based', 'woodmart'),
            'description'   => __('Enable Cream Based', 'woodmart'),
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id'            => '_show_fondant',
            'label'         => __('Show Fondant', 'woodmart'),
            'description'   => __('Enable Fondant', 'woodmart'),
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id'            => '_show_picture_cake',
            'label'         => __('Show Picture Cake', 'woodmart'),
            'description'   => __('Enable Picture Cake', 'woodmart'),
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id'            => '_show_weight_field',
            'label'         => __('Show Weight Field', 'woodmart'),
            'description'   => __('Enable weight field for this product', 'woodmart'),
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id'            => '_show_flavour',
            'label'         => __('Flavour', 'woodmart'),
            'description'   => __('Flavour Selection', 'woodmart'),
        )
    );
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'add_custom_options');

function save_custom_options($post_id) {
    $checkbox_fields = ['_show_cream_based', '_show_fondant', '_show_picture_cake', '_show_weight_field', '_show_flavour'];
    foreach ($checkbox_fields as $field) {
        $value = isset($_POST[$field]) ? 'yes' : 'no';
        update_post_meta($post_id, $field, $value);
    }
}

add_action('woocommerce_process_product_meta', 'save_custom_options');

function add_custom_price($cart_object) {
    if (!WC()->session->__isset("reload_checkout")) {
        foreach ($cart_object->get_cart() as $key => $value) {
            if (isset($value['variation_id']) && $value['variation_id'] > 0) {
                // If the product is a variation, use the variation ID
                $product = wc_get_product($value['variation_id']);
            } else {
                // Otherwise, use the product ID
                $product = wc_get_product($value['product_id']);
            }

            $base_price = $product->get_price(); // Base price of the product
            $weight = isset($value['product_weight']) ? intval($value['product_weight']) : 1;
            $additional_price = 0;

            if (isset($value['cream_based']) && $value['cream_based'] !== '') {
                $additional_price += floatval($value['cream_based']);
            }
            if (isset($value['fondant']) && $value['fondant'] !== '') {
                $additional_price += floatval($value['fondant']);
            }
            if (isset($value['picture_cake']) && $value['picture_cake'] !== '') {
                $additional_price += floatval($value['picture_cake']);
            }

            $price = ($base_price * $weight) + $additional_price;
            $value['data']->set_price($price);
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'add_custom_price', 10, 1);

// Add custom fields to order items

function add_custom_fields_to_order_items($item, $cart_item_key, $values, $order) {
    if (isset($values['product_weight'])) {
        $item->add_meta_data(__('Product Weight', 'woodmart'), $values['product_weight'] . ' kg');
    }
    if (isset($values['cream_based'])) {
        $item->add_meta_data(__('Cream Based', 'woodmart'), wc_price($values['cream_based']));
    }
    if (isset($values['fondant'])) {
        $item->add_meta_data(__('Fondant', 'woodmart'), wc_price($values['fondant']));
    }
    if (isset($values['picture_cake'])) {
        $item->add_meta_data(__('Picture Cake', 'woodmart'), wc_price($values['picture_cake']));
    }
    if (isset($values['flavour'])) {
        $item->add_meta_data(__('Flavour', 'woodmart'), ucfirst($values['flavour']));
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'add_custom_fields_to_order_items', 10, 4);


/**
* change currency symbol to AED
*/

add_filter( 'woocommerce_currency_symbol', 'wc_change_uae_currency_symbol', 10, 2 );

function wc_change_uae_currency_symbol( $currency_symbol, $currency ) {
    switch ( $currency ) {
    case 'AED':
    $currency_symbol = 'AED';
    break;
}

	return $currency_symbol;
}

// Change the "Billing details" heading to "Account details"
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields_label');

function custom_woocommerce_billing_fields_label($fields) {
    $fields['billing']['billing_first_name']['label'] = __('Name', 'woocommerce');
    // You can change other billing field labels here as well if needed

    return $fields;
}

add_filter('gettext', 'custom_billing_details_text', 20, 3);

function custom_billing_details_text($translated_text, $text, $domain) {
    if ($translated_text == 'Billing details' && $domain == 'woocommerce') {
        $translated_text = 'Account details';
    }
    return $translated_text;
}

// Shipping Address Remove Checkbox 
function custom_override_checkout_fields( $fields ) {
    // Remove the "Ship to a different address?" checkbox
    unset( $fields['shipping']['ship_to_different_address'] );

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

function custom_ship_to_different_address_text() {
    echo '<h3 class="mb-shipping-hd">' . __( 'Delivery Address', 'woodmart' ) . '</h3>';
}
add_action( 'woocommerce_before_checkout_shipping_form', 'custom_ship_to_different_address_text', 5 );


function custom_woocommerce_text_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Create an account?' :
            $translated_text = __( 'Create an account (optional)', 'woodmart' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'custom_woocommerce_text_strings', 20, 3 );


// Whatsapp link
function whatsapp_order_button_shortcode() {
    ob_start();
    ?>
    <div class="wd-button-wrapper text-left">
        <a class="btn btn-style-default btn-style-semi-round btn-size-default btn-scheme-light btn-scheme-hover-inherit btn-full-width btn-icon-pos-right whatsapp_order_button"
            href="javascript:void(0);"
            onclick="sendWhatsAppMessage()"
            style="cursor: pointer;">
            <span class="wd-btn-text" data-elementor-setting-key="text">
                Ask Price
            </span>
        </a>
    </div>
        
    <script>
        
    function sendWhatsAppMessage() {
        var phoneNumber = '971566507389'; // Replace with your phone number
        var message = encodeURIComponent('I am interested in this cake <?php the_permalink()?>'); // URL encode the message

        var whatsappURL = 'https://api.whatsapp.com/send?phone=' + phoneNumber + '&text=' + message;
        
        // Open WhatsApp in a new tab/window
        window.open(whatsappURL, '_blank');
    }
    </script>
        
    <?php
        
    return ob_get_clean();
}

add_shortcode('whatsapp_order_button', 'whatsapp_order_button_shortcode');


/*Remove Checkout Fields*/
add_filter( 'woocommerce_billing_fields', 'remove_billing_fields' );
function remove_billing_fields( $fields ) {
    unset( $fields['billing_last_name'] );
    unset( $fields['billing_company'] );
    unset( $fields['billing_country'] );
    unset( $fields['billing_address_1'] );
    unset( $fields['billing_address_2'] );
    unset( $fields['billing_city'] );
    unset( $fields['billing_state'] );
    unset( $fields['billing_postcode'] );
    return $fields;
}

add_filter( 'woocommerce_shipping_fields', 'remove_shipping_fields' );
function remove_shipping_fields( $fields ) {
    unset( $fields['shipping_first_name'] );
    unset( $fields['shipping_last_name'] );
    unset( $fields['shipping_company'] );
    unset( $fields['shipping_address_2'] );
    unset( $fields['shipping_state'] );
    unset( $fields['shipping_postcode'] );
    return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'remove_order_notes_field' );
function remove_order_notes_field( $fields ) {
    unset( $fields['order']['order_comments'] );
    return $fields;
}


/****Checkout Addional Info****/

/*
* 1. Add the Date & Time Picker Fields
*/

add_filter( 'woocommerce_checkout_fields', 'add_additional_fields' );

function add_additional_fields( $fields ) {
    $fields['additional_info']['delivery_date'] = array(
        'type'        => 'text',
        'label'       => __('Delivery Date & Time', 'woodmart'),
        'placeholder' => _x('Select date', 'placeholder', 'woodmart'),
        'required'    => true,
        'class'       => array('delivery_date_row'),
        'priority'    => 30,
    );
    $fields['additional_info']['delivery_time'] = array(
        'type'        => 'text',
        'label'       => __(' ', 'woodmart'),
        'placeholder' => _x('Select time', 'placeholder', 'woodmart'),
        'required'    => true,
        'class'       => array('delivery_time_row'),
        'priority'    => 40,
    );
    $fields['additional_info']['egg_preference'] = array(
        'type'        => 'select',
        'label'       => __('Egg Preference', 'woodmart'),
        'placeholder' => _x('Select One', 'placeholder', 'woodmart'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'priority'    => 50,
        'options'     => array(
            ''         => __('Select One', 'woodmart'),
            'egg'      => __('Egg', 'woodmart'),
            'egg_free' => __('Egg Free', 'woodmart'),
        ),
    );
    $fields['additional_info']['cake_message'] = array(
        'type'        => 'textarea',
        'label'       => __('Message on the cake', 'woodmart'),
        'placeholder' => _x('Write your message', 'placeholder', 'woodmart'),
        'required'    => false,
        'class'       => array('form-row-wide'),
        'priority'    => 60,
    );
    return $fields;
}

/*
* 2. Display the Fields on the Checkout Page
*/

add_action( 'woocommerce_before_order_notes', 'display_additional_fields' );
function display_additional_fields( $checkout ) {

    echo '<div id="date_time_picker_checkout_fields"><h3 class="mb-shipping-hd">' . __('Additional Info') . '</h3>';

    woocommerce_form_field( 'delivery_date', $checkout->get_checkout_fields()['additional_info']['delivery_date'], $checkout->get_value( 'delivery_date' ) );
    woocommerce_form_field( 'delivery_time', $checkout->get_checkout_fields()['additional_info']['delivery_time'], $checkout->get_value( 'delivery_time' ) );
    woocommerce_form_field( 'egg_preference', $checkout->get_checkout_fields()['additional_info']['egg_preference'], $checkout->get_value( 'egg_preference' ) );
    woocommerce_form_field( 'cake_message', $checkout->get_checkout_fields()['additional_info']['cake_message'], $checkout->get_value( 'cake_message' ) );

    echo '</div>';
}

/*
* 3. Enqueue the Date & Time Picker Scripts
*/

add_action( 'wp_enqueue_scripts', 'enqueue_additional_scripts' );
function enqueue_additional_scripts() {
    if ( is_checkout() ) {
        // Enqueue jQuery UI Datepicker
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_style( 'jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

        // Enqueue jQuery Timepicker Addon
        wp_enqueue_script( 'jquery-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-datepicker' ), null, true );
        wp_enqueue_style( 'jquery-timepicker-addon-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css' );

        // Enqueue custom script to initialize the date and time pickers
        wp_enqueue_script( 'custom-date-time-picker', get_stylesheet_directory_uri() . '/js/custom-date-time-picker.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-timepicker-addon' ), null, true );
    }
}

/*
* 4. Save the Custom Fields
*/

add_action( 'woocommerce_checkout_update_order_meta', 'save_additional_fields' );
function save_additional_fields( $order_id ) {
    if ( ! empty( $_POST['delivery_date'] ) ) {
        update_post_meta( $order_id, '_delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
    }
    if ( ! empty( $_POST['delivery_time'] ) ) {
        update_post_meta( $order_id, '_delivery_time', sanitize_text_field( $_POST['delivery_time'] ) );
    }
    if ( ! empty( $_POST['egg_preference'] ) ) {
        update_post_meta( $order_id, '_egg_preference', sanitize_text_field( $_POST['egg_preference'] ) );
    }
    if ( ! empty( $_POST['cake_message'] ) ) {
        update_post_meta( $order_id, '_cake_message', sanitize_textarea_field( $_POST['cake_message'] ) );
    }
}

/*
* 5. Display the Fields in the Order Admin Panel
*/

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_additional_in_admin', 10, 1 );
function display_additional_in_admin( $order ) {
    echo '<h3>' . __('Additional Info', 'woodmart') . '</h3>';
    echo '<p><strong>' . __('Delivery Date', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_delivery_date', true ) . '</p>';
    echo '<p><strong>' . __('Delivery Time', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_delivery_time', true ) . '</p>';
    echo '<p><strong>' . __('Egg Preference', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_egg_preference', true ) . '</p>';
    echo '<p><strong>' . __('Message on the cake', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_cake_message', true ) . '</p>';
}

/*
* 6. Add Custom Fields to Order Emails
*/

add_action( 'woocommerce_email_order_meta', 'add_aditional_fields_to_email', 20, 3 );
function add_aditional_fields_to_email( $order, $sent_to_admin, $plain_text ) {
    $delivery_date = get_post_meta( $order->get_id(), '_delivery_date', true );
    $delivery_time = get_post_meta( $order->get_id(), '_delivery_time', true );
    $egg_preference = get_post_meta( $order->get_id(), '_egg_preference', true );
    $cake_message = get_post_meta( $order->get_id(), '_cake_message', true );

    if ( $plain_text ) {
        echo "Delivery Date: " . $delivery_date . "\n";
        echo "Delivery Time: " . $delivery_time . "\n";
        echo "Egg Preference: " . $egg_preference . "\n";
        echo "Message on the cake: " . $cake_message . "\n";
    } else {
        echo '<p><strong>' . __('Delivery Date', 'woodmart') . ':</strong> ' . $delivery_date . '</p>';
        echo '<p><strong>' . __('Delivery Time', 'woodmart') . ':</strong> ' . $delivery_time . '</p>';
        echo '<p><strong>' . __('Egg Preference', 'woodmart') . ':</strong> ' . $egg_preference . '</p>';
        echo '<p><strong>' . __('Message on the cake', 'woodmart') . ':</strong> ' . $cake_message . '</p>';
    }
}

/*Add the WhatsApp field to the checkout form*/

add_filter( 'woocommerce_billing_fields', 'add_whatsapp_billing_field' );
function add_whatsapp_billing_field( $fields ) {
    $fields['billing_whatsapp'] = array(
        'label'       => __( 'WhatsApp', 'woodmart' ),
        'required'    => false,
        'class'       => array( 'form-row-wide' ),
        'clear'       => true,
    );
    return $fields;
}


/*Save the WhatsApp field data*/

add_action( 'woocommerce_checkout_update_order_meta', 'save_whatsapp_billing_field' );
function save_whatsapp_billing_field( $order_id ) {
    if ( ! empty( $_POST['billing_whatsapp'] ) ) {
        update_post_meta( $order_id, '_billing_whatsapp', sanitize_text_field( $_POST['billing_whatsapp'] ) );
    }
}

/*Display the WhatsApp field in the admin order details page*/

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_whatsapp_in_admin_order', 10, 1 );
function display_whatsapp_in_admin_order( $order ) {
    $whatsapp_number = get_post_meta( $order->get_id(), '_billing_whatsapp', true );
    if ( ! empty( $whatsapp_number ) ) {
        echo '<p><strong>' . __( 'WhatsApp', 'woodmart' ) . ':</strong> ' . esc_html( $whatsapp_number ) . '</p>';
    }
}


/*Display the WhatsApp field in the order emails*/

add_filter( 'woocommerce_email_order_meta_fields', 'add_whatsapp_to_order_email', 10, 3 );
function add_whatsapp_to_order_email( $fields, $sent_to_admin, $order ) {
    $whatsapp_number = get_post_meta( $order->get_id(), '_billing_whatsapp', true );
    if ( ! empty( $whatsapp_number ) ) {
        $fields['whatsapp_number'] = array(
            'label' => __( 'WhatsApp', 'woodmart' ),
            'value' => $whatsapp_number,
        );
    }
    return $fields;
}

/*Email Template Shortcode*/

// Add this function to your theme's functions.php file
function site_url_shortcode() {
    $site_url = get_site_url();
    return '<a href="' . esc_url($site_url) . '">' . esc_html($site_url) . '</a>';
}
add_shortcode('site_url', 'site_url_shortcode');

//add_filter('woocommerce_email_content', 'do_shortcode');