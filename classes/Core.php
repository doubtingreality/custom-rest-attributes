<?php

namespace CustomRestAttributes;

use RuntimeException;
use UnexpectedValueException;

class Core
{
	protected $routes;
	protected $configPath;

	function __construct(
		string $configPath
	) {
		$this->loadRoutes($configPath);
		$this->registerRoutes();
	}

	function loadRoutes(
		string $configPath
	) {
		if (!isset($configPath)) {
			throw new RuntimeException(
				'No config path provided'
			);
		}

		$this->configPath = dirname(__FILE__) . '/../' . $configPath;

		if (!file_exists($this->configPath)) {
			throw new RuntimeException(
				sprintf(
					'Config file not found at path "%s"',
					$this->configPath
				)
			);
		}

		$routes = json_decode(file_get_contents($this->configPath));

		if ($routes === null && json_last_error() !== JSON_ERROR_NONE) {
			throw new UnexpectedValueException(
				sprintf(
					'Invalid JSON config file provided at path "%s"',
					$this->configPath
				)
			);
		}

		$this->routes = (object)$routes;
	}

	function registerRoutes()
	{
		$callbacks = new Callbacks();

		add_action('rest_api_init', function() use ($callbacks) {
			// Loop through each route and attribute to bind the callback to the attribute names
			foreach ($this->routes as $route) {
				foreach ($route->attributes as $attribute) {
					if (!method_exists($callbacks, $attribute->callback)
					    || !is_callable([$callbacks, 'getMemberTags'])) {
						throw new RuntimeException(
							sprintf(
								'Callback method "%1$s" does not exist or is not callable for attribute "%2$s"',
								$attribute->callback,
								$attribute->name
							)
						);
					}

					register_rest_field(
						$route->object_type,
						$attribute->name,
						[
							'get_callback' => [$callbacks, $attribute->callback]
						]
					);
				}
			}
		});
	}
}
