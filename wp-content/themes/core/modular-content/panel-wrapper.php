<?php
/**
 * A wrapper around each modular panel.
 *
 * @var \ModularContent\Panel $panel
 * @var int $index 0-based count of panels rendered thus far
 * @var string $type panel type
 * @var string $html The rendered HTML of the panel
 */

$zebra = ( $index % 2 == 0 ) ? 'odd' : 'even';
$type  = esc_attr( $panel->get( 'type' ) );

// Child Panel
if( $panel->get_depth() >= 1 ) { ?>

	<article class="panel-child panel-type-<?php echo $type; ?>">

		<?php echo $html; ?>

	</article>

<?php }

// Parent Panel
else { ?>

	<section class="panel panel-count-<?php echo $index; ?> panel-type-<?php echo $type; ?> panel-<?php echo $zebra; ?>">

		<?php echo $html; ?>

	</section>

<?php } ?>