(function pageLoad($) {
  const $reviewsBox = $('#woocommerce-reputations-trusted-shops-product-reviews');

  /**
   * Hides Trusted Shops product reviews box if it does not contain reviews.
   *
   * @param {Array} mutations Changes to Trusted Shops product reviews box.
   */
  function MutationObserverCallBack(mutations) {
    mutations.forEach((mutation) => {
      if (mutation.addedNodes !== null && $reviewsBox.find('.ts-no-reviews').length) {
        $reviewsBox.css('display', 'none');
      }
    });
  }

  /**
   * Starts a mutation observer on the product container element
   * to hide the Trusted Shops products reviews box if it does not
   * contain reviews.
   *
   * @return {Object} Mutation observer
   */
  function observeProductReviewsBox() {
    const observer = new MutationObserver(MutationObserverCallBack);

    return observer.observe($reviewsBox[0], { childList: true });
  }

  if ($reviewsBox.length) {
    observeProductReviewsBox();
  }
}(jQuery));
