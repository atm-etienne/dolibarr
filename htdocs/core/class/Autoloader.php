<?php

namespace Dolibarr;

class Autoloader {

	public static function register(): void {
		spl_autoload_register([self::class, 'autoload']);
	}

	public static function autoload($class): void {
		// Check that namespace is in Dolibarr
		if (!str_starts_with($class, 'Dolibarr\\')) {
			return;
		}

		$relativeClass = substr($class, strlen('Dolibarr\\'));
		$relativeClassPath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);

        $classNamespaceParts = explode('\\', $class);
        $moduleName = (count($classNamespaceParts) < 3) ? '' : $classNamespaceParts[count($classNamespaceParts) - 2];

		$baseDirectories = [
			realpath(__DIR__) . DIRECTORY_SEPARATOR,
			realpath(__DIR__) . '/../' . strtolower($moduleName) . DIRECTORY_SEPARATOR,
			realpath(__DIR__ . '/../../custom/') . strtolower($moduleName) . DIRECTORY_SEPARATOR,
		];

		// We need to check all the possible class file names, until they all follow the correct syntax
		$filePatterns = [
			strtolower(basename($relativeClassPath)) . '.class.php',
			basename($relativeClassPath) . '.class.php',
			strtolower(basename($relativeClassPath)) . '.interface.php',
			basename($relativeClassPath) . '.interface.php',
			strtolower(dirname($relativeClassPath)) . '.' . strtolower(basename($relativeClassPath)) . '.class.php',
			basename($relativeClassPath) . '.php'
		];

		foreach ($baseDirectories as $baseDir) {
			foreach ($filePatterns as $pattern) {
				$possiblePath = $baseDir . $pattern;
				if (file_exists($possiblePath)) {
					require_once $possiblePath;
					return;
				}
			}
		}
	}

}

Autoloader::register();
