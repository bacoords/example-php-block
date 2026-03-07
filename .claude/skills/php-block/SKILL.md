# PHP-Only Block Development

Create WordPress blocks using PHP-only registration (WordPress 7.0+ `autoRegister` feature).

More information: https://make.wordpress.org/core/2026/03/03/php-only-block-registration/

## When to Use

Use this skill when the user wants to:

- Create a block without build tools (no npm, no webpack)
- Register a block entirely in PHP
- Add a simple server-rendered block

## Block Structure

```
my-block/
├── register.php    # Block registration and render callback
└── style.css       # Block styles (frontend + editor)
```

For blocks with frontend interactivity:

```
my-block/
├── register.php
├── style.css
└── view.js         # Frontend-only JavaScript
```

## Registration Pattern

```php
add_action( 'init', function () {
    // 1. Register assets first
    wp_register_style(
        'my-block-style',
        plugins_url( 'style.css', __FILE__ ),
        array(),
        '1.0.0'
    );

    // 2. Register the block
    register_block_type(
        'my-plugin/my-block',
        array(
            'title'           => 'My Block',
            'description'     => 'A PHP-only block.',
            'category'        => 'widgets',
            'icon'            => 'smiley',

            // Pass registered style handle
            'style'           => 'my-block-style',

            'attributes'      => array( /* ... */ ),
            'supports'        => array(
                'autoRegister' => true,  // Required for PHP-only
                // ... other supports
            ),
            'render_callback' => 'my_render_function',
        )
    );
} );
```

Refer to the official documentation for supported attributes: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/

## Critical: JavaScript Loading

**JavaScript CANNOT run in the block editor for autoRegister blocks.** The editor uses ServerSideRender, which only displays the PHP output.

For frontend JavaScript:

1. Register the script on `init`
2. Enqueue it inside the `render_callback`

```php
add_action( 'init', function () {
    // Register (not enqueue) the script
    wp_register_script(
        'my-block-view',
        plugins_url( 'view.js', __FILE__ ),
        array(),
        '1.0.0',
        array( 'in_footer' => true )
    );

    register_block_type( 'my-plugin/my-block', array(
        // ... other settings
        'render_callback' => function ( $attributes ) {
            // Enqueue here - only runs on frontend
            wp_enqueue_script( 'my-block-view' );

            return my_render_block( $attributes );
        },
    ) );
} );
```

## Attributes

Attributes auto-generate editor controls based on type:

| Type                 | Control       |
| -------------------- | ------------- |
| `string`             | TextControl   |
| `integer` / `number` | NumberControl |
| `boolean`            | ToggleControl |
| `string` + `enum`    | SelectControl |

```php
'attributes' => array(
    'title' => array(
        'type'    => 'string',
        'default' => 'Hello',
        'label'   => 'Title Text',  // Shows in sidebar
    ),
    'count' => array(
        'type'    => 'integer',
        'default' => 3,
        'label'   => 'Number of Items',
    ),
    'showBorder' => array(
        'type'    => 'boolean',
        'default' => true,
        'label'   => 'Show Border',
    ),
    'size' => array(
        'type'    => 'string',
        'enum'    => array( 'small', 'medium', 'large' ),
        'default' => 'medium',
        'label'   => 'Size',
    ),
),
```

**Important:** Never name an attribute `style` - it conflicts with WordPress internals.

## Block Supports

Supports auto-generate sidebar panels:

```php
'supports' => array(
    'autoRegister' => true,  // Required
    'align'        => array( 'wide', 'full' ),
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
```

## Render Callback

Always use `get_block_wrapper_attributes()` to apply block supports:

```php
function my_render_block( $attributes ) {
    $wrapper = get_block_wrapper_attributes( array(
        'class' => 'my-block',
    ) );

    return sprintf(
        '<div %s>%s</div>',
        $wrapper,
        esc_html( $attributes['title'] )
    );
}
```

## CSS Loading

Use the `style` key to load CSS in both editor and frontend:

```php
wp_register_style( 'my-block-style', ... );

register_block_type( '...', array(
    'style' => 'my-block-style',  // Loads everywhere block is used
) );
```

## Limitations

- No InnerBlocks support (ServerSideRender limitation)
- No client-side interactivity in editor
- JavaScript only works on frontend
- Requires WordPress 7.0+

## Examples in This Plugin

- `basic-block/` - Simple block with attributes and CSS
- `woo-block/` - Server-rendered products with frontend JS for cart status
