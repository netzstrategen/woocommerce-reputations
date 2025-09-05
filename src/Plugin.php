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
   * @implements admin_init
   */
  public static function admin_init() {
    // Adds former SKUs field to woocommerce product backend inventory tab.
    add_action('woocommerce_product_options_sku', __NAMESPACE__ . '\TrustedShops::woocommerce_product_options_sku');
  }

  /**
   * @implements init
   */
  public static function init() {
    // Add rewrite rule for trusted-shop-reviews endpoint
    add_filter('query_vars', __CLASS__ . '::add_query_vars');
    add_action('template_redirect', __CLASS__ . '::handle_trusted_shop_reviews_template');

    // Saves custom fields for simple products.
    add_action('woocommerce_process_product_meta', __NAMESPACE__ . '\TrustedShops::woocommerce_process_product_meta');

    // Enqueue Trusted Shops product reviews related scripts.
    add_action('wp_enqueue_scripts', __CLASS__ . '::enqueueProductReviewsScripts');

    // Displays product rating stars after product title on product detail page.
    add_action('woocommerce_single_product_summary', __NAMESPACE__ . '\TrustedShops::woocommerce_single_product_summary', 6);
    // Displays product reviews on product detail page.
    add_action('woocommerce_after_single_product_summary', __NAMESPACE__ . '\TrustedShops::woocommerce_after_single_product_summary');
    add_action('woocommerce_after_single_product', __NAMESPACE__ . '\TrustedShops::woocommerce_after_single_product');
    add_filter('woocommerce_thankyou_order_received_text', __NAMESPACE__ . '\TrustedShops::woocommerce_thankyou_order_received_text', 100, 2);
    // Shows etrusted Shop widget in cart page
    add_action('woocommerce_before_proceed_to_checkout', __NAMESPACE__ . '\TrustedShops::displayETrustedWidget');
    if (defined('WC_VERSION') && WC_VERSION < '3.3.0') {
      add_action('woocommerce_order_items_table', __NAMESPACE__ . '\TrustedShops::addsTrustedShopsBuyerProtection');
    }
    else {
      add_action('woocommerce_order_details_after_order_table_items', __NAMESPACE__ . '\TrustedShops::addsTrustedShopsBuyerProtection');
    }
    add_action('wp_footer', __NAMESPACE__ . '\TrustedShops::wp_footer');
    add_action(Plugin::PREFIX . '/badge/trusted-shops', __NAMESPACE__ . '\TrustedShops::renderBadge');

    add_filter('woocommerce_thankyou_order_received_text', __NAMESPACE__ . '\GoogleTrustedStores::woocommerce_thankyou_order_received_text', 100, 2);
    // Adds Trusted Shops aggregate schema to GraphQL.
    add_action('graphql_register_types',  __NAMESPACE__ . '\GraphQL::graphql_register_types');
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

    if (is_cart()) {
      wp_enqueue_script(
        'etrusted-widget',
        'https://integrations.etrusted.com/applications/widget.js/v2',
        [],
        null,
        [
          "in_footer" => true,
          "strategy" => "defer"
        ]
      );
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

  /**
   * Add query vars for trusted-shop-reviews endpoint.
   *
   * @param array $vars
   * @return array
   */
  public static function add_query_vars($vars) {
    $vars[] = 'trusted_shop_reviews';
    return $vars;
  }

  /**
   * Handle the trusted-shop-reviews template.
   */
  public static function handle_trusted_shop_reviews_template() {
    if (get_query_var('trusted_shop_reviews')) {
      // Load the template file
      $template_path = self::getBasePath() . '/templates/trusted-shop-reviews.php';
      if (file_exists($template_path)) {
        include $template_path;
        exit;
      }
    }
  }

}
