<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceReputations\TrustedShops.
 */

namespace Netzstrategen\WooCommerceReputations;

class TrustedShops {

  /**
   * Cache expiration time.
   *
   * @var int
   */
  const CACHE_DURATION = 43200;

  /**
   * Cache expiration time for errors.
   *
   * @var int
   */
  const CACHE_DURATION_ERROR = 3600;

  /**
   * Adds Trusted Shops rich snippet markup to product pages.
   *
   * @implements woocommerce_after_single_product
   */
  public static function woocommerce_after_single_product() {
    $transient_id = Plugin::PREFIX  . 'trustedshops_reviews';
    if ($transient = get_transient($transient_id)) {
      echo $transient;
      return;
    }
    if (!$shop_id = Settings::getOption('trusted_shops/id')) {
      return;
    }
    $api_url = 'http://api.trustedshops.com/rest/public/v2/shops/' . $shop_id . '/quality/reviews.json';
    $response = wp_remote_get($api_url);
    if ($response instanceof \WP_Error) {
      trigger_error($response->get_error_message(), E_USER_ERROR);
      set_transient($transient_id, '', static::CACHE_DURATION_ERROR);
      return;
    }
    elseif (empty($response['body']) || !($response = json_decode($response['body'], TRUE)) || empty($response = $response['response'])) {
      trigger_error('Unable to retrieve Trusted Shops data', E_USER_ERROR);
      set_transient($transient_id, '', static::CACHE_DURATION_ERROR);
      return;
    }
    $reviewIndicator = $response['data']['shop']['qualityIndicators']['reviewIndicator'];
    $ts_snippet = [
      '@context' => 'http://schema.org',
      '@type' => 'Organization',
      'name' => $response['data']['shop']['name'],
      'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => $reviewIndicator['overallMark'],
        'bestRating' => '5.00',
        'ratingCount' => $reviewIndicator['activeReviewCount'],
      ],
    ];
    $transient = '<script type="application/ld+json">' . json_encode($ts_snippet) . '</script>';
    echo $transient;
    set_transient($transient_id, $transient, self::CACHE_DURATION);
  }

  /**
   * Adds Trusted Shops integrations HTML output to order confirmation page.
   *
   * @implements woocommerce_thankyou_order_received_text
   */
  public static function woocommerce_thankyou_order_received_text($text, $order) {
    if (empty($order)) {
      return $text;
    }
    $text .= <<<EOD
<span id="trustedShopsCheckout" style="display: none;">
  <span id="tsCheckoutOrderNr">{$order->get_id()}</span>
  <span id="tsCheckoutBuyerEmail">{$order->get_billing_email()}</span>
  <span id="tsCheckoutOrderAmount">{$order->get_total()}</span>
  <span id="tsCheckoutOrderCurrency">{$order->get_currency()}</span>
  <span id="tsCheckoutOrderPaymentType">{$order->get_payment_method()}</span>
EOD;
    foreach ($order->get_items() as $item) {
      $product = $item->get_product();
      $product_id = $product->get_id();
      $product_url = get_permalink($product_id);
      $product_image = has_post_thumbnail($product_id) ? wp_get_attachment_url($product->get_data()['image_id']) : '';
      $product_sku = $product->get_sku();
      $product_gtin = get_post_meta($product_id, '_custom_gtin', TRUE);
      $brands = wp_get_post_terms($product_id, 'product_brand', ['orderby' => 'name', 'fields' => 'names']);
      $product_brand = $brands && !is_wp_error($brands) ? implode(' | ', $brands) : '';
      $text .= <<<EOD
  <span class="tsCheckoutProductItem">
    <span class="tsCheckoutProductUrl">{$product_url}</span>
    <span class="tsCheckoutProductImageUrl">{$product_image}</span>
    <span class="tsCheckoutProductName">{$item->get_name()}</span>
    <span class="tsCheckoutProductSKU">{$product_sku}</span>
    <span class="tsCheckoutProductGTIN">{$product_gtin}</span>
    <span class="tsCheckoutProductBrand">{$product_brand}</span>
  </span>
EOD;
    }
    $text .= <<<EOD
</span>
EOD;
    return $text;
  }

  /**
   * Adds Trusted Shops Badge script to footer and thank-you page.
   *
   * @implements wp_footer
   */
  public static function wp_footer() {
    if (!$shop_id = Settings::getOption('trusted_shops/id')) {
      return;
    }
    ?>
    <script>
      (function () {
        var _tsid = '<?= $shop_id ?>';
        _tsConfig = {
          'yOffset': '0',
          'variant': 'custom',
          'customElementId': '<?= Plugin::PREFIX . '-trusted-shops-badge' ?>',
          'trustcardDirection': 'topRight',
          'customBadgeWidth': '40',
          'customBadgeHeight': '40',
          'disableResponsive': 'false',
          'disableTrustbadge': 'false',
          'customCheckoutElementId': '<?= Plugin::PREFIX . '-trusted-shops-buyer-protection' ?>'
        };
        var _ts = document.createElement('script');
        _ts.type = 'text/javascript';
        _ts.charset = 'utf-8';
        _ts.async = true;
        _ts.src = '//widgets.trustedshops.com/js/' + _tsid + '.js';
        var __ts = document.getElementsByTagName('script')[0];
        __ts.parentNode.insertBefore(_ts, __ts);
      })();
    </script>
    <?php
  }

  /**
   * Renders Trusted Shops Badge.
   */
  public static function renderBadge() {
    echo '<div id="' . Plugin::PREFIX . '-trusted-shops-badge"></div>';
  }

  /**
   * Adds trusted-shop-badge to the thank-you page.
   *
   * @implements woocommerce_order_items_table
   */
  public static function woocommerce_order_items_table() {
    echo '<div id="' . Plugin::PREFIX . '-trusted-shops-buyer-protection"></div>';
  }

}
