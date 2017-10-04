<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceReputations\GoogleTrustedStores.
 */

namespace Netzstrategen\WooCommerceReputations;

class GoogleTrustedStores {

  /**
   * Adds Google Trusted Store integrations HTML output to order confirmation page.
   *
   * @implements woocommerce_thankyou_order_received_text
   */
  public static function woocommerce_thankyou_order_received_text($text, $order) {
    $merchant_id = Settings::getOption('google/merchant_id');
    $delivery_time = Settings::getOption('google/delivery_time');
    if (!$merchant_id || !$delivery_time || empty($order)) {
      return $text;
    }
    $delivery_date = date('Y-m-d', strtotime($order->get_date_created()->date('c') . ' + ' . $delivery_time . ' days'));
    $js_snippet = [
      'merchant_id' => $merchant_id,
      'order_id' => $order->get_id(),
      'email' => $order->get_billing_email(),
      'delivery_country' => $order->get_shipping_country(),
      'estimated_delivery_date' => $delivery_date,
    ];
    $json_output = json_encode($js_snippet);
    $text .= <<<EOD
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
<script>
  window.renderOptIn = function() {
    window.gapi.load('surveyoptin', function() {
      window.gapi.surveyoptin.render($json_output);
    });
  }
</script>
EOD;
    return $text;
  }

}
