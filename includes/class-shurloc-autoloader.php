<?php
/**
 * Plugin autoloader.
 *
 * Automatically loads classes, interfaces, and traits from the includes directory.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

/**
 * ShurLoc autoloader.
 */
final class Shurloc_Autoloader {

	/**
	 * Registered directories.
	 *
	 * @var array<int,string>
	 */
	private array $directories = array();

	/**
	 * Constructor.
	 *
	 * @param string $base_directory Base includes directory.
	 * @throws UnexpectedValueException When the base directory does not exist.
	 */
	public function __construct(
		string $base_directory
	) {

		if ( ! is_dir( $base_directory ) ) {
			throw new UnexpectedValueException(
				'Autoloader directory does not exist.'
			);
		}

		$this->directories = $this->scan_directories(
			$base_directory
		);
	}

	/**
	 * Register autoloader.
	 *
	 * @return void
	 */
	public function register(): void {

		spl_autoload_register(
			array(
				$this,
				'load',
			)
		);
	}

	/**
	 * Load class file.
	 *
	 * @param string $class_name Class name.
	 * @return void
	 */
	public function load(
		string $class_name
	): void {

		if (
			0 !== strpos(
				$class_name,
				'Shurloc_'
			)
		) {
			return;
		}

		$filename = $this->class_to_filename(
			$class_name
		);

		foreach ( $this->directories as $directory ) {

			$file = $directory . '/' . $filename;

			if ( file_exists( $file ) ) {

				require_once $file;

				return;
			}
		}
	}

	/**
	 * Convert class name to filename.
	 *
	 * Examples:
	 *
	 * Shurloc_Product_Service
	 * → class-shurloc-product-service.php
	 *
	 * Shurloc_Product_Service_Interface
	 * → interface-shurloc-product-service.php
	 *
	 * Shurloc_Product_Trait
	 * → trait-shurloc-product.php
	 *
	 * @param string $class_name Class name.
	 * @return string Filename.
	 */
	private function class_to_filename(
		string $class_name
	): string {

		$lower = strtolower(
			str_replace(
				'_',
				'-',
				$class_name
			)
		);

		if (
			str_ends_with(
				$lower,
				'-interface'
			)
		) {
			return 'interface-' .
				substr(
					$lower,
					0,
					-10
				) .
				'.php';
		}

		if (
			str_ends_with(
				$lower,
				'-trait'
			)
		) {
			return 'trait-' .
				substr(
					$lower,
					0,
					-6
				) .
				'.php';
		}

		return 'class-' . $lower . '.php';
	}

	/**
	 * Recursively scan include directories.
	 *
	 * @param string $directory Root directory.
	 * @return array<int,string>
	 */
	private function scan_directories(
		string $directory
	): array {

		$directories = array(
			$directory,
		);

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$directory,
				RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ( $iterator as $item ) {

			if ( $item->isDir() ) {

				$directories[] = $item->getPathname();
			}
		}

		return $directories;
	}
}
