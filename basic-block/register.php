<?php
/**
 * Basic Block Registration
 *
 * Demonstrates pure PHP-only block registration with:
 * - autoRegister support
 * - Custom attributes with auto-generated controls
 * - Block supports (color, typography, spacing)
 * - Scoped CSS via wp_enqueue_block_style()
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		// Register assets.
		wp_register_style(
			'epb-hello-style',
			EPB_URL . 'basic-block/style.css',
			array(),
			EPB_VERSION
		);

		register_block_type(
			'example-php-block/hello',
			array(
				'title'           => 'Hello Pattern',
				'description'     => 'A simple hello world block with custom styling.',
				'category'        => 'widgets',
				'icon'            => 'smiley',
				'version'         => EPB_VERSION,

				// Assets - handle passed here auto-enqueues when block is used.
				'style'           => 'epb-hello-style', // Frontend + Editor.

				'attributes'      => array(
					'heading'      => array(
						'type'    => 'string',
						'default' => 'Hello, World!',
						'label'   => 'Heading Text',
					),
					'headingLevel' => array(
						'type'    => 'string',
						'enum'    => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
						'default' => 'h2',
						'label'   => 'Heading Level',
					),
					'message'      => array(
						'type'    => 'string',
						'default' => 'This block is registered entirely in PHP.',
						'label'   => 'Message',
					),
				),
				'supports'        => array(
					'autoRegister' => true,
					'color'        => array(
						'text'       => true,
						'background' => true,
					),
					'spacing'      => array(
						'padding' => true,
						'margin'  => true,
					),
					'typography'   => array(
						'fontSize' => true,
					),
				),
				'render_callback' => function ( $attributes ) {
					$wrapper = get_block_wrapper_attributes(
						array(
							'class' => 'epb-hello',
						)
					);

					$tag = in_array( $attributes['headingLevel'], array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true )
						? $attributes['headingLevel']
						: 'h2';

					return sprintf(
						'<div %s>
						<%s class="epb-hello__heading">%s</%s>
						<p class="epb-hello__message">%s</p>
					</div>',
						$wrapper,
						$tag,
						esc_html( $attributes['heading'] ),
						$tag,
						esc_html( $attributes['message'] )
					);
				},
			)
		);
	}
);

