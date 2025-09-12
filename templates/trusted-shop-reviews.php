<?php
/**
 * @file
 * Template for Trusted Shop Reviews endpoint.
 */

namespace Netzstrategen\WooCommerceReputations;

if (!defined('ABSPATH')) {
  exit;
}

// Get parameters from URL
$order_nr = isset($_GET['order_nr']) ? sanitize_text_field($_GET['order_nr']) : '';
$buyer_email = isset($_GET['buyer_email']) ? sanitize_email($_GET['buyer_email']) : '';
$order_amount = isset($_GET['order_amount']) ? sanitize_text_field($_GET['order_amount']) : '';
$order_currency = isset($_GET['order_currency']) ? sanitize_text_field($_GET['order_currency']) : '';
$payment_type = isset($_GET['payment_type']) ? sanitize_text_field($_GET['payment_type']) : '';


// Example: products[0][name]=Product1&products[0][sku]=SKU1&products[1][name]=Product2
$products = [];

if (isset($_GET['products']) && is_array($_GET['products'])) {
  foreach ($_GET['products'] as $product_data) {
    $products[] = [
      'url' => isset($product_data['url']) ? esc_url_raw($product_data['url']) : '',
      'image_url' => isset($product_data['image_url']) ? esc_url_raw($product_data['image_url']) : '',
      'name' => isset($product_data['name']) ? sanitize_text_field($product_data['name']) : '',
      'sku' => isset($product_data['sku']) ? sanitize_text_field($product_data['sku']) : '',
      'gtin' => isset($product_data['gtin']) ? sanitize_text_field($product_data['gtin']) : '',
      'brand' => isset($product_data['brand']) ? sanitize_text_field($product_data['brand']) : '',
    ];
  }
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trusted Shop Reviews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .widget-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Trusted Shop Reviews</h1>
        <div id="woocommerce-reputations-trusted-shops-badge"></div>

        <div class="widget-container">
            <!-- Buyer protection widget container -->
            <div id="woocommerce-reputations-trusted-shops-buyer-protection"></div>
        </div>

        <!-- Hidden checkout data with values from GET parameters -->
        <div id="trustedShopsCheckout" style="display: none;">
            <span id="tsCheckoutOrderNr"><?php echo esc_html($order_nr); ?></span>
            <span id="tsCheckoutBuyerEmail"><?php echo esc_html($buyer_email); ?></span>
            <span id="tsCheckoutOrderAmount"><?php echo esc_html($order_amount); ?></span>
            <span id="tsCheckoutOrderCurrency"><?php echo esc_html($order_currency); ?></span>
            <span id="tsCheckoutOrderPaymentType"><?php echo esc_html($payment_type); ?></span>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <span class="tsCheckoutProductItem">
                    <span class="tsCheckoutProductUrl"><?php echo esc_html($product['url']); ?></span>
                    <span class="tsCheckoutProductImageUrl"><?php echo esc_html($product['image_url']); ?></span>
                    <span class="tsCheckoutProductName"><?php echo esc_html($product['name']); ?></span>
                    <span class="tsCheckoutProductSKU"><?php echo esc_html($product['sku']); ?></span>
                    <span class="tsCheckoutProductGTIN"><?php echo esc_html($product['gtin']); ?></span>
                    <span class="tsCheckoutProductBrand"><?php echo esc_html($product['brand']); ?></span>
                </span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $trusted_shops_id = Settings::getOption('trusted_shops/id');
    if ($trusted_shops_id):
    ?>
    <script>
    (function () {
      var _tsid = '<?php echo esc_js($trusted_shops_id); ?>';
      _tsConfig = {
        'yOffset': '-40',
        'variant': 'custom',
        'customElementId': 'woocommerce-reputations-trusted-shops-badge',
        'trustcardDirection': 'topRight',
        'customBadgeWidth': '40',
        'customBadgeHeight': '40',
        'disableTrustbadge': false,
        'customCheckoutElementId': 'woocommerce-reputations-trusted-shops-buyer-protection'
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
    <?php else: ?>
    <div class="notice">
        <p><strong>Notice:</strong> Trusted Shops ID is not configured. Please configure it in WooCommerce → Settings → Integrations → Reputations.</p>
    </div>
    <?php endif; ?>
</body>
</html>