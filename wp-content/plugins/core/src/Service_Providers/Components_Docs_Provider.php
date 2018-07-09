<?php


namespace Tribe\Project\Service_Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tribe\Project\Components_Docs\Ajax;
use Tribe\Project\Components_Docs\Component_Item;
use Tribe\Project\Components_Docs\Registry;
use Tribe\Project\Components_Docs\Router;
use Tribe\Project\Components_Docs\Templates\Component_Docs;
use Tribe\Project\Components_Docs\Theme\Assets;
use Tribe\Project\Templates\Components\Accordion;
use Tribe\Project\Templates\Components\Button;
use Tribe\Project\Templates\Components\Card;
use Tribe\Project\Templates\Components\Content_Block;
use Tribe\Project\Templates\Components\Quote;

class Components_Docs_Provider implements ServiceProviderInterface {

	protected $panels     = [];
	protected $components = [
		Accordion::class,
		Button::class,
		Card::class,
		Content_Block::class,
		Quote::class,
	];

	public function register( Container $container ) {

		$this->add_template_paths( $container );

		$container['components_docs.router'] = function () {
			return new Router();
		};

		$container['components_docs.registry'] = function () {
			return new Registry();
		};

		$container['components_docs.assets'] = function ( $container ) {
			return new Assets( $container['components_docs.router'] );
		};

		$container['components_docs.ajax'] = function ( $container ) {
			return new Ajax( $container['components_docs.registry'] );
		};

		foreach ( $this->components as $component ) {
			$component_item = new Component_Item( $component );
			$container['components_docs.registry']->add_item( $component_item->get_slug(), $component_item );
		}

		$container['component_docs.template'] = function ( $container ) {
			$twig_path = 'main.twig';
			return new Component_Docs( $twig_path, null, $container['components_docs.registry'] );
		};

		add_action( 'init', function () use ( $container ) {
			$container['components_docs.router']->add_rewrite_rule();
			$container['components_docs.ajax']->add_ajax_actions();
		}, 10, 0 );

		add_filter( 'core_js_config', function ( $data ) use ( $container ) {
			return $container['components_docs.ajax']->add_config_items( $data );
		}, 10, 1 );

		add_filter( 'template_include', function ( $template ) use ( $container ) {
			return $container['components_docs.router']->show_components_docs_page( $template );
		}, 10, 1 );

		add_action( 'wp_enqueue_scripts', function () use ( $container ) {
			$container['components_docs.assets']->enqueue_scripts();
			$container['components_docs.assets']->enqueue_styles();
		}, 10, 0 );

	}

	protected function add_template_paths( Container $container ) {
		add_filter( 'tribe/project/twig', function ( $twig ) use ( $container ) {
			$template_path = dirname( dirname( __FILE__ ) ) . '/Components_Docs/Twig/';
			$container['twig.loader']->addPath( $template_path );

			$twig = new \Twig_Environment( $container['twig.loader'], $container['twig.options'] );
			$twig->addExtension( $container['twig.extension'] );

			return $twig;
		}, 10, 1 );
	}
}