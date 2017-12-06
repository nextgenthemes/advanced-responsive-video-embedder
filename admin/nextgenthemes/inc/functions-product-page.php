<?php
namespace nextgenthemes\admin;

function ads_page() {

	wp_enqueue_style(
		'nextgenthemes-product-page',
		URL . 'css/product-page.css',
		array(),
		filemtime( dirname( dirname( __FILE__ ) ) . '/css/product-page.css' )
	);

	echo '<div id="nextgenthemes-ads">';
	products_html();
	echo '</div>';
}

function products_html() {

	$data = remote_get_cached( array(
		'url' => 'https://nextgenthemes.com/edd-api/products/',
	) );

	if ( is_wp_error( $data ) ) {

		printf(
			'<div class="error"><p>%s</p></div>',
			// @codingStandardsIgnoreLine
			$data->get_error_message()
		);
		return;
	}

	foreach ( $data->products as $product ) :

		if ( defined( 'ARVE_VERSION' ) && 'arve' === $product->info->slug ) {
			continue;
		}
		if ( defined( 'ARVE_PRO_VERSION' ) && 'arve-pro' === $product->info->slug ) {
			continue;
		}
		if ( defined( 'ARVE_AMP_VERSION' ) && 'arve-amp' === $product->info->slug ) {
			continue;
		}
		?>
		<a href="<?php product_link( $product ); ?>">
			<?php if ( ! empty( $product->info->thumbnail ) ) : ?>
				<figure><img src="<?php echo esc_attr( $product->info->thumbnail ); ?>"></figure>
			<?php endif; ?>
			<h2><?php echo esc_html( $product->info->title ); ?></h2>
			<?php
			// @codingStandardsIgnoreLine
			echo filter_product_html( $product->info->content );
			?>
			<?php if ( ! empty( $product->pricing->amount ) && '0.00' === $product->pricing->amount ) : ?>
				<span>Free</span>
			<?php else : ?>
				<span>More Info</span>
			<?php endif; ?>
		</a>
		<?php

	endforeach;
}

function filter_product_html( $content ) {

	$allowed_tags = array(
		#'a'       => array( 'href' => true, 'title' => true ),
		'code'    => array(),
		'em'      => array(),
		'h2'      => array(),
		'li'      => array(),
		'ol'      => array(),
		'p'       => array(),
		'section' => array(),
		'span'    => array(),
		'strong'  => array(),
		'ul'      => array(),
	);

	return wp_kses( $content, $allowed_tags );
}

function product_link( $product ) {
	echo esc_url( sprintf( 'https://nextgenthemes.com/%s/%s/', $product->info->category[0]->slug, $product->info->slug ) );
}
