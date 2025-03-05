jQuery(document).ready(function(){
  
    jQuery(document.body).on("updated_checkout", async function () {
      console.clear();
      await getDhlRatesRequestLogs();
    });
    
    jQuery(document.body).on("updated_cart_totals", async function () {
      console.clear();
      await getDhlRatesRequestLogs();
    });
    });
    async function getDhlRatesRequestLogs() {
        const response = await jQuery.ajax({
          type: "get",
          url: dhl_cart_checkout.ajax_url,
          data: {
            action: "dhl_get_rates_request_logs",
            _ajax_nonce: dhl_cart_checkout.nonce,
          },
          dataType: "json",
        });
        if (response?.success && response?.data) {
          const responseData = response?.data;		
      
          if (responseData?.dhl_rates_logs) {
            console.log(
              responseData?.dhl_rates_logs
                ?.replaceAll("%colored_text_start%", "%c")
                ?.replaceAll("%colored_text_end%", "%c"),
              responseData?.dhl_rates_logs?.includes("%colored_text_start%")
                ? "color: red"
                : "",
              responseData?.dhl_rates_logs?.includes("%colored_text_end%")
                ? "color: initial"
                : ""
            );
          }
        }
      }
      // WOOCOMMERCE BLOCKS - CART AND CHECKOUT COMPATIBILITY.
    jQuery(document).ready(function () {
      // Check if the method getCartData is available in the window object.
      if (window?.wp?.data?.select('wc/store/cart')?.getCartData()) {
          // Set Initial cart content.
          localStorage.setItem('elex-dhl-cart-data', JSON.stringify(wp.data.select('wc/store/cart').getCartData()));
    
          // Subscribe to cart changes.
          wp.data.subscribe(async () => {
              // This condition will be true only if the quantity or address is updated on the cart/checkout page.
              if (
                  localStorage.getItem('elex-dhl-cart-data') !==
                  JSON.stringify(wp.data.select('wc/store/cart').getCartData())
              ) {
                  // FIRE CART/CHECKOUT UPDATED CALLBACK EVENTS HERE.
                  // Fetching request log/debug log after cart/checkout is updated.
                  await getDhlRatesRequestLogs();
              }
    
              // Set Updated cart content.
              localStorage.setItem(
                  'elex-dhl-cart-data',
                  JSON.stringify(wp.data.select('wc/store/cart').getCartData())
              );
          });
      }
    });
    