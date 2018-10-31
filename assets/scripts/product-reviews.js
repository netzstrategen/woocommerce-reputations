(function ($) {
  var observer;

  /**
   * Starts a mutation observer on the product container element
   * to hide the Trusted Shops products reviews box if no reviews
   * have been added.
   */
  function observeProductReviewsBox() {
    var $reviewsBox = $('#woocommerce-reputations-trusted-shops-product-reviews');
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.addedNodes !== null && $reviewsBox.find('.ts-no-reviews').length) {
          $reviewsBox.css('display', 'none');
        }
      });
    });

    return observer.observe($reviewsBox[0], {childList: true});
  }

  observer = observeProductReviewsBox();
})(jQuery);
