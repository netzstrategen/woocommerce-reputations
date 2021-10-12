<?php

/*
  Plugin Name: WooCommerce Reputations
  Plugin URI: https://github.com/netzstrategen/woocommerce-reputations
  Version: 1.7.3
  Text Domain: woocommerce-reputations
  Description: Integrate Trusted Shops and Google Trusted Stores into WooCommerce.
  Author: netzstrategen
  Author URI: http://www.netzstrategen.com/
  License: GPL-2.0+
  License URI: http://www.gnu.org/licenses/gpl-2.0
*/

namespace Netzstrategen\WooCommerceReputations;

if (!defined('ABSPATH')) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
  exit;
}

/**
 * Loads PSR-4-style plugin classes.
 */
function classloader($class) {
  static $ns_offset;
  if (strpos($class, __NAMESPACE__ . '\\') === 0) {
    if ($ns_offset === NULL) {
      $ns_offset = strlen(__NAMESPACE__) + 1;
    }
    include __DIR__ . '/src/' . strtr(substr($class, $ns_offset), '\\', '/') . '.php';
  }
}
spl_autoload_register(__NAMESPACE__ . '\classloader');

add_action('plugins_loaded', __NAMESPACE__ . '\Plugin::loadTextdomain');
add_action('plugins_loaded', __NAMESPACE__ . '\Plugin::plugins_loaded');
add_action('admin_init', __NAMESPACE__ . '\Plugin::admin_init');
add_action('init', __NAMESPACE__ . '\Plugin::init');
