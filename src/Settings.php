<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceReputations\Settings.
 */

namespace Netzstrategen\WooCommerceReputations;

class Settings extends \WC_Integration {

  public function __construct() {
    global $woocommerce;
    $this->id = Plugin::PREFIX;
    $this->method_title = __('Reputations', Plugin::L10N);
    $this->renderFormFields();
    $this->woocommerce_trusted_shops_id = $this->get_option('trusted_shops/id');
    $this->woocommerce_google_trusted_stores_merchant_id = $this->get_option('google/merchant_id');
    $this->woocommerce_google_trusted_stores_delivery_time = $this->get_option('google/delivery_time');
    add_action('woocommerce_update_options_integration_' . $this->id, [$this, 'process_admin_options']);
  }

  /**
   * @implements woocommerce_integrations
   */
  public static function woocommerce_integrations($integrations) {
    $integrations[] = __CLASS__;
    return $integrations;
  }

  /**
   * Renders woocommerce integration settings form fields.
   */
  public function renderFormFields() {
    $this->form_fields = [
      'trusted_shops/id' => [
        'title' => __('Trusted Shops ID', Plugin::L10N),
        'type' => 'text',
      ],
      'trusted_shops/yOffset' => [
        'title' => __('Offset from page bottom', Plugin::L10N),
        'type' => 'text',
      ],
      'trusted_shops/variant' => [
        'title' => __('Variant', Plugin::L10N),
        'type' => 'select',
        'options' => [
          'default' => 'default',
          'reviews' => 'reviews',
          'custom' => 'custom',
          'custom_reviews' => 'custom_reviews',
        ],
        'description' => __('Defines the badge position.', Plugin::L10N),
        'desc_tip' => TRUE,
      ],
      'trusted_shops/disable_responsive' => [
        'title' => __('Disable responsive', Plugin::L10N),
        'type' => 'checkbox',
        'description' => __('If checked, the Trusted Shops responsive banner will be hidden on mobile devices.', Plugin::L10N),
        'desc_tip' => TRUE,
      ],
      'trusted_shops/display_product_stars' => [
        'title' => __('Display product rating stars', Plugin::L10N),
        'type' => 'checkbox',
        'description' => __('If checked, Trusted Shops rating stars will be displayed in product pages.', Plugin::L10N),
        'desc_tip' => TRUE,
      ],
      'trusted_shops/display_product_reviews' => [
        'title' => __('Display product reviews', Plugin::L10N),
        'type' => 'checkbox',
        'description' => __('If checked, Trusted Shops reviews will be displayed in product pages.', Plugin::L10N),
        'desc_tip' => TRUE,
      ],
      'trusted_shops/product_review_box_bordercolor' => [
        'title' => __('Product reviews box border color', Plugin::L10N),
        'type' => 'color',
      ],
      'trusted_shops/product_review_box_backgroundcolor' => [
        'title' => __('Product reviews box background color', Plugin::L10N),
        'type' => 'color',
      ],
      'trusted_shops/product_review_comment_bordercolor' => [
        'title' => __('Product reviews comment border color', Plugin::L10N),
        'type' => 'color',
      ],
      'trusted_shops/product_stars_color' => [
        'title' => __('Product rating stars color', Plugin::L10N),
        'type' => 'color',
      ],
      'trusted_shops/product_review_box_backgroundcolor' => [
        'title' => __('Product reviews box background color', Plugin::L10N),
        'type' => 'color',
      ],
      'google/merchant_id' => [
        'title' => __('Google Merchant ID', Plugin::L10N),
        'type' => 'int',
      ],
      'google/delivery_time' => [
        'title' => __('Delivery time', Plugin::L10N),
        'type' => 'int',
        'description' => __('The delivery time will be added to the order date to calculate the estimated delivery data needed for Google Trusted Stores integrations.', Plugin::L10N),
        'desc_tip' => TRUE,
      ],
    ];
  }

  /**
   * Get WooCommerce integration option values.
   *
   * @param $key
   * @param null $default
   *
   * @return mixed
   */
  public static function getOption($key, $default = NULL) {
    $integrations = \WC()->integrations->get_integrations();
    $instance = $integrations[Plugin::PREFIX];
    return $instance->get_option($key, $default);
  }

}
