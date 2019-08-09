<?php


namespace Tribe\Project\Service_Providers;

use Pimple\Container;
use Tribe\Project\CLI\Container_Export;
use Tribe\Project\Container\Service_Provider;
use Tribe\Project\CLI\CLI_Generator;
use Tribe\Project\CLI\Settings_Generator;
use Tribe\Project\CLI\CPT_Generator;
use Tribe\Project\CLI\File_System;
use Tribe\Project\CLI\Meta\Importer;
use Tribe\Project\CLI\Queues\Add_Tasks;
use Tribe\Project\CLI\Queues\Cleanup;
use Tribe\Project\CLI\Queues\Process;
use Tribe\Project\CLI\Taxonomy_Generator;
use Tribe\Project\CLI\Cache_Prime;
use Tribe\Project\CLI\Queues\List_Queues;
use Tribe\Project\CLI\Queues\MySQL_Table;

class CLI_Provider extends Service_Provider {
	const CACHE_PRIME      = 'cli.cache-prime';
	const CONTAINER_EXPORT = 'cli.container_export';
	const FILE_SYSTEM      = 'cli.file_system';
	const GENERATE_CPT     = 'cli.generator.cpt';
	const GENERATE_TAX     = 'cli.generator.taxonomy';
	const GENERATE_CLI     = 'cli.generator.cli';
	const GENERATE_SETTING = 'cli.generator.setting';
	const GENERATE_META    = 'cli.generator.meta';
	const QUEUES_LIST      = 'cli.queues.list';
	const QUEUES_ADD_TABLE = 'cli.queues.add_table';
	const QUEUES_CLEANUP   = 'cli.queues.cleanup';
	const QUEUES_PROCESS   = 'cli.queues.process';
	const QUEUES_ADD_TASK  = 'cli.queues.add_tasks';

	public function register( Container $container ) {
		$this->generators( $container );
		$this->queues( $container );

		$container[ self::CONTAINER_EXPORT ] = function ( $container ) {
			return new Container_Export( tribe_project() );
		};

		$container[ self::CACHE_PRIME ] = function () {
			return new Cache_Prime();
		};

		add_action( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}

			$container[ self::CONTAINER_EXPORT ]->register();
			$container[ self::CACHE_PRIME ]->register();
			$container[ self::GENERATE_CPT ]->register();
			$container[ self::GENERATE_TAX ]->register();
			$container[ self::GENERATE_CLI ]->register();
			$container[ self::GENERATE_SETTING ]->register();
			$container[ self::QUEUES_LIST ]->register();
			$container[ self::QUEUES_ADD_TABLE ]->register();
			$container[ self::QUEUES_CLEANUP ]->register();
			$container[ self::QUEUES_PROCESS ]->register();
			$container[ self::QUEUES_ADD_TASK ]->register();
			$container[ self::GENERATE_META ]->register();
		}, 0, 0 );
	}

	private function queues( Container $container ) {
		$container[ self::QUEUES_LIST ] = function ( $container ) {
			return new List_Queues( $container['queues.collection'] );
		};

		$container[ self::QUEUES_ADD_TABLE ] = function ( $container ) {
			return new MySQL_Table( $container['queues.backend.mysql'] );
		};

		$container[ self::QUEUES_CLEANUP ] = function ( $container ) {
			return new Cleanup( $container['queues.collection'] );
		};

		$container[ self::QUEUES_PROCESS ] = function ( $container ) {
			return new Process( $container['queues.collection'] );
		};

		$container[ self::QUEUES_ADD_TASK ] = function ( $container ) {
			return new Add_Tasks( $container['queues.collection'] );
		};
	}

	private function generators( Container $container ) {

		$container[ self::FILE_SYSTEM ] = function ( $container ) {
			return new File_System();
		};

		$container[ self::GENERATE_CPT ] = function ( $container ) {
			return new CPT_Generator( $container[ self::FILE_SYSTEM ] );
		};

		$container[ self::GENERATE_TAX ] = function ( $container ) {
			return new Taxonomy_Generator( $container[ self::FILE_SYSTEM ] );
		};

		$container[ self::GENERATE_CLI ]     = function ( $container ) {
			return new CLI_Generator( $container[ self::FILE_SYSTEM ] );
		};
		$container[ self::GENERATE_SETTING ] = function ( $container ) {
			return new Settings_Generator( $container[ self::FILE_SYSTEM ] );
		};

		$container[ self::GENERATE_META ] = function ( $container ) {
			return new Importer( $container[ self::FILE_SYSTEM ] );
		};
	}
}
