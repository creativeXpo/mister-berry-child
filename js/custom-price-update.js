jQuery(document).ready(function($) {
    let originalPriceText;
    let originalPriceElement;
    let originalPrice;

    function storeOriginalPrice() {
        originalPriceElement = $('.price .woocommerce-Price-amount.amount bdi').first();
        if (originalPriceElement.length > 0) {
            originalPriceText = originalPriceElement.text().trim();
            originalPrice = parseFloat(originalPriceText.replace(/[^0-9.-]/g, '')); // Remove everything except numbers, dots, and minus sign
            if (isNaN(originalPrice)) {
                console.error("Original price could not be parsed!");
                originalPrice = 0; // Fallback to 0 if the original price can't be parsed
            }
        } else {
            console.error("Original price element not found!");
            return false;
        }
        return true;
    }

    function updatePriceWithOptions() {
        let updatedPrice = originalPrice;

        const creamBasedOption = $('#cream_based').val();
        const fondantOption = $('#fondant').val();
        const pictureCakeOption = $('#picture_cake').val();
        const weightOption = $('#product_weight').val();

        if (creamBasedOption) {
            const additionalCreamPrice = parseFloat(creamBasedOption);
            if (isNaN(additionalCreamPrice)) {
                console.error("Selected cream based option value is not a valid number!");
                return;
            }
            updatedPrice += additionalCreamPrice;
        }

        if (fondantOption) {
            const additionalFondantPrice = parseFloat(fondantOption);
            if (isNaN(additionalFondantPrice)) {
                console.error("Selected fondant option value is not a valid number!");
                return;
            }
            updatedPrice += additionalFondantPrice;
        }

        if (pictureCakeOption) {
            const additionalPictureCakePrice = parseFloat(pictureCakeOption);
            if (isNaN(additionalPictureCakePrice)) {
                console.error("Selected picture cake option value is not a valid number!");
                return;
            }
            updatedPrice += additionalPictureCakePrice;
        }

        if (weightOption) {
            const weight = parseFloat(weightOption);
            if (isNaN(weight) || weight < 1) {
                console.error("Selected weight option value is not a valid number!");
                return;
            }
            updatedPrice = originalPrice * weight + (updatedPrice - originalPrice);
        }

        originalPriceElement.text(wc_price_format(updatedPrice));
    }

    function wc_price_format(price) {
        const currencySymbol = $('.woocommerce-Price-currencySymbol').first().text() || 'AED';
        return currencySymbol + ' ' + price.toFixed(2); // Removed comma formatting for thousands
    }

    if (!storeOriginalPrice()) {
        return;
    }

    $('#cream_based, #fondant, #picture_cake, #product_weight').change(function() {
        console.log("Option change detected.");
        updatePriceWithOptions();
    });

    $('form.variations_form').on('woocommerce_variation_has_changed', function() {
        console.log("Variation change detected.");
        if (storeOriginalPrice()) {
            updatePriceWithOptions();
        }
    });
});