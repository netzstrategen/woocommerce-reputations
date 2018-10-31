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
    // Enqueue Trusted Shops product reviews related scripts.
    add_action('wp_enqueue_scripts', __CLASS__ . '::enqueueProductReviewsScripts');

    // Displays product rating stars after product title on product detail page.
    add_action('woocommerce_single_product_summary', __NAMESPACE__ . '\TrustedShops::woocommerce_single_product_summary', 6);
    // Displays product reviews on product detail page.
    add_action('woocommerce_after_single_product_summary', __NAMESPACE__ . '\TrustedShops::woocommerce_after_single_product_summary');
    add_action('woocommerce_after_single_product', __NAMESPACE__ . '\TrustedShops::woocommerce_after_single_product');
    add_filter('woocommerce_thankyou_order_received_text', __NAMESPACE__ . '\TrustedShops::woocommerce_thankyou_order_received_text', 100, 2);
    if (WC_VERSION < '3.3.0') {
      add_action('woocommerce_order_items_table', __NAMESPACE__ . '\TrustedShops::addsTrustedShopsBuyerProtection');
    }
    else {
      add_action('woocommerce_order_details_after_order_table_items', __NAMESPACE__ . '\TrustedShops::addsTrustedShopsBuyerProtection');
    }
    add_action('wp_footer', __NAMESPACE__ . '\TrustedShops::wp_footer');
    add_action(Plugin::PREFIX . '/badge/trusted-shops', __NAMESPACE__ . '\TrustedShops::renderBadge');

    add_filter('woocommerce_thankyou_order_received_text', __NAMESPACE__ . '\GoogleTrustedStores::woocommerce_thankyou_order_received_text', 100, 2);
  }

  /**
   * Enqueues Trusted Shops product reviews related scripts.
   *
   * @implements wp_enqueue_scripts.
   */
  public static function enqueueProductReviewsScripts() {
    $git_version = static::getGitVersion();

    if (is_product()) {
      wp_enqueue_script(static::PREFIX, Plugin::getBaseUrl() . '/dist/scripts/main.min.js', ['jquery'], $git_version, TRUE);
    }
  }

  /**
   * Generates a version out of the current commit hash.
   *
   * @return string
   */
  public static function getGitVersion() {
    $git_version = NULL;
    if (is_dir(ABSPATH . '.git')) {
      $ref = trim(file_get_contents(ABSPATH . '.git/HEAD'));
      if (strpos($ref, 'ref:') === 0) {
        $ref = substr($ref, 5);
        if (file_exists(ABSPATH . '.git/' . $ref)) {
          $ref = trim(file_get_contents(ABSPATH . '.git/' . $ref));
        }
        else {
          $ref = substr($ref, 11);
        }
      }
      $git_version = substr($ref, 0, 8);
    }
    return $git_version;
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
