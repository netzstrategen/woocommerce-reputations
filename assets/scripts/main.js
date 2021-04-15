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

  /**
   * Fixes a missing '@id' of the product in the AggregateRating schema.org data
   * injected by Trusted Shops.
   *
   * Waits for the presence of the element #trustedshops-productreviews-sticker-wrapper
   * in the body, which is added by the Trusted Shops via JavaScript.
   *
   * The schema.org script element itself is added to the HTML head by the Trusted Shops JavaScript.
   *
   * @param {string} selector The DOM element to wait for.
   * @return {string} The DOM element when it is available.
   */
  function waitForElement(selector) {
    return new Promise((resolve) => {
      if (document.querySelector(selector)) {
        return resolve(document.querySelector(selector));
      }
      const observer = new MutationObserver(() => {
        if (document.querySelector(selector)) {
          resolve(document.querySelector(selector));
          observer.disconnect();
        }
      });
      observer.observe(document.body, {
        childList: true,
        subtree: true,
      });
    });
  }

  waitForElement('#trustedshops-productreviews-sticker-wrapper').then(() => {
    const scripts = document.querySelectorAll('head > script[type="application/ld+json"]');
    scripts.forEach((script) => {
      if (!script.innerText.includes('@id')) {
        script.innerText = script.innerText.replace(',"@type":"Product","name"', `,"@id":"${location.protocol}//${location.host}${location.pathname}#product","@type":"Product","name"`);
      }
    });
  });
}(jQuery));
