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
      'trusted_shops/disable_responsive' => [
        'title' => __('Disable responsive', Plugin::L10N),
        'type' => 'checkbox',
        'description' => __('If checked, the Trusted Shops responsive banner will be hidden on mobile devices.', Plugin::L10N),
        'desc_tip' => TRUE,
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
