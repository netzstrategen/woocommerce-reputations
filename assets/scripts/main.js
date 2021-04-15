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
   * Adds @ID to Trusted Shops structured data type AggregateRating.
   *
   * Waits for the presence of the DOM element #trustedshops-productreviews-sticker-wrapper
   * which is added by Trusted Shops via JavaScript.
   *
   * @param {string} selector The DOM element that must be waited for.
   * @return {string} When DOM element is available.
   */
  function waitForElm(selector) {
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

  waitForElm('#trustedshops-productreviews-sticker-wrapper').then(() => {
    const TargetScripts = document.querySelectorAll('head > script[type="application/ld+json"]');
    TargetScripts.forEach((TargetScript) => {
      if (!TargetScript.innerText.includes('"@id":')) {
        TargetScript.innerText = TargetScript.innerText.replace(',"@type":"Product","name"', `,"@id":"${location.protocol}//${location.host}${location.pathname}#product","@type":"Product","name"`);
      }
    });
  });
}(jQuery));
