<?php
/**
 * WooCommerce Block Registration
 *
 * Demonstrates PHP-only block registration with:
 * - autoRegister support
 * - Server-side product rendering with WooCommerce functions
 * - Frontend JavaScript for cart status via Store API
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		// Register assets.
		wp_register_script(
			'epb-woo-view',
			EPB_URL . 'woo-block/view.js',
			array(),
			EPB_VERSION,
			array( 'in_footer' => true )
		);

		wp_register_style(
			'epb-woo-style',
			EPB_URL . 'woo-block/style.css',
			array(),
			EPB_VERSION
		);

		register_block_type(
			'example-php-block/woo-products',
			array(
				'title'           => 'Store Products Preview',
				'description'     => 'Displays products with cart status from Store API.',
				'category'        => 'woocommerce',
				'icon'            => 'cart',
				'version'         => EPB_VERSION,

				// Style loads in editor and frontend.
				'style'           => 'epb-woo-style',

				'attributes'      => array(
					'count'      => array(
						'type'    => 'integer',
						'default' => 3,
						'label'   => 'Number of Products',
					),
					'showPrices' => array(
						'type'    => 'boolean',
						'default' => true,
						'label'   => 'Show Prices',
					),
					'showButton' => array(
						'type'    => 'boolean',
						'default' => true,
						'label'   => 'Show View Product Button',
					),
				),
				'supports'        => array(
					'autoRegister' => true,
					'align'        => array( 'wide', 'full' ),
					'color'        => array(
						'background' => true,
					),
					'spacing'      => array(
						'padding' => true,
					),
				),
				'render_callback' => function ( $attributes ) {
					// Enqueue frontend script.
					wp_enqueue_script( 'epb-woo-view' );

					return epb_render_woo_products_block( $attributes );
				},
			)
		);
	}
);

/**
 * Render the WooCommerce products block.
 *
 * @param array $attributes Block attributes.
 * @return string Block HTML.
 */
function epb_render_woo_products_block( $attributes ) {
	// Check if WooCommerce is active.
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '<p>WooCommerce is required for this block.</p>';
	}

	$count       = intval( $attributes['count'] ?? 3 );
	$show_prices = $attributes['showPrices'] ?? true;
	$show_button = $attributes['showButton'] ?? true;

	// Get products using WooCommerce functions.
	$products = wc_get_products(
		array(
			'status' => 'publish',
			'limit'  => $count,
		)
	);

	if ( empty( $products ) ) {
		return '<p>No products found.</p>';
	}

	$wrapper = get_block_wrapper_attributes(
		array(
			'class' => 'epb-woo-products',
		)
	);

	ob_start();
	?>
	<div <?php echo $wrapper; ?>>
		<div class="epb-woo-products__grid">
			<?php foreach ( $products as $product ) : ?>
				<?php
				$image_id  = $product->get_image_id();
				$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();
				$image_alt = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : $product->get_name();
				?>
				<div class="epb-woo-product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<img
						class="epb-woo-product__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $image_alt ); ?>"
					>
					<div class="epb-woo-product__content">
						<h3 class="epb-woo-product__name">
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo esc_html( $product->get_name() ); ?>
							</a>
						</h3>
						<?php if ( $show_prices ) : ?>
							<p class="epb-woo-product__price">
								<?php echo $product->get_price_html(); ?>
							</p>
						<?php endif; ?>
						<?php if ( $show_button ) : ?>
							<div class="epb-woo-product__actions">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="epb-woo-product__button">
									View Product
								</a>
								<span class="epb-woo-product__cart-status" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
