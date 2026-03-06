/**
 * WooCommerce Block Frontend Script
 *
 * Fetches cart from Store API and displays "N in cart" for products.
 * Products are rendered server-side; this just adds cart status.
 */

document.addEventListener( 'DOMContentLoaded', () => {
	const cartStatusElements = document.querySelectorAll( '.epb-woo-product__cart-status' );

	if ( ! cartStatusElements.length ) {
		return;
	}

	fetchCartStatus();
} );

async function fetchCartStatus() {
	try {
		const response = await fetch( '/wp-json/wc/store/v1/cart' );

		if ( ! response.ok ) {
			return;
		}

		const cart = await response.json();
		updateCartStatus( cart.items );
	} catch ( error ) {
		console.error( 'EPB: Failed to fetch cart', error );
	}
}

function updateCartStatus( cartItems ) {
	// Build a map of product ID to quantity.
	const cartQuantities = {};

	cartItems.forEach( ( item ) => {
		const productId = item.id;
		if ( cartQuantities[ productId ] ) {
			cartQuantities[ productId ] += item.quantity;
		} else {
			cartQuantities[ productId ] = item.quantity;
		}
	} );

	// Update all cart status elements.
	document.querySelectorAll( '.epb-woo-product__cart-status' ).forEach( ( element ) => {
		const productId = element.dataset.productId;
		const quantity = cartQuantities[ productId ];

		if ( quantity ) {
			element.textContent = `${ quantity } in cart`;
			element.classList.add( 'has-items' );
		} else {
			element.textContent = '';
			element.classList.remove( 'has-items' );
		}
	} );
}
