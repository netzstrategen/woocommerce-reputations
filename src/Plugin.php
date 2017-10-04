<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceReputations\Plugin.
 */

namespace Netzstrategen\WooCommerceReputations;

/**
 * Main front-end functionality.
 */
class Plugin {

  /**
   * Prefix for naming.
   *
   * @var string
   */
  const PREFIX = 'woocommerce-reputations';

  /**
   * Gettext localization domain.
   *
   * @var string
   */
  const L10N = self::PREFIX;

  /**
   * @var string
   */
  private static $baseUrl;

  /**
   * @implements plugins_loaded
   */
  public static function plugins_loaded() {
    add_filter('woocommerce_integrations', __NAMESPACE__ . '\Settings::woocommerce_integrations');
  }

  /**
   * @implements init
   */
  public static function init() {
    add_action('woocommerce_after_single_product', __NAMESPACE__ . '\TrustedShops::woocommerce_after_single_product');
    add_filter('woocommerce_thankyou_order_received_text', __NAMESPACE__ . '\TrustedShops::woocommerce_thankyou_order_received_text', 100, 2);
    add_action('woocommerce_order_items_table', __NAMESPACE__ . '\TrustedShops::woocommerce_order_items_table');
    add_action('wp_footer', __NAMESPACE__ . '\TrustedShops::wp_footer');
    add_action(Plugin::PREFIX . '/badge/trusted-shops', __NAMESPACE__ . '\TrustedShops::renderBadge');

    add_filter('woocommerce_thankyou_order_received_text', __NAMESPACE__ . '\GoogleTrustedStores::woocommerce_thankyou_order_received_text', 100, 2);
  }

  /**
   * Loads the plugin textdomain.
   */
  public static function loadTextdomain() {
    load_plugin_textdomain(static::L10N, FALSE, static::L10N . '/languages/');
  }

  /**
   * The base URL path to this plugin's folder.
   *
   * Uses plugins_url() instead of plugin_dir_url() to avoid a trailing slash.
   */
  public static function getBaseUrl() {
    if (!isset(self::$baseUrl)) {
      self::$baseUrl = plugins_url('', self::getBasePath() . '/plugin.php');
    }
    return self::$baseUrl;
  }

  /**
   * The absolute filesystem base path of this plugin.
   *
   * @return string
   */
  public static function getBasePath() {
    return dirname(__DIR__);
  }

}
