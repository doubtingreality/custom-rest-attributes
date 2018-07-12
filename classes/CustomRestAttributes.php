<?php

declare(strict_types = 1);

namespace CustomRestAttributes;

use stdClass;
use ErrorException;
use RuntimeException;
use UnexpectedValueException;

class CustomRestAttributes
{
	protected $routes;
	protected $callbacks;
	protected $callbacksMap;

	/**
	 * Constructor
	 *
	 * @throws UnexpectedValueException If the callbacks map is not a valid
	 *                                  stdClass iterable object
	 *
	 * @throws UnexpectedValueException If the map cannot be encoded to JSON
	 */
	function __construct()
	{
		$this->callbacks = new Callbacks();

		if (!is_array($this->callbacks::$map)
		    || !is_iterable($this->callbacks::$map)) {
			throw new UnexpectedValueException(
				sprintf(
					'Callbacks map is not a valid iterable array. ' .
					'Current type is: %s. Refer to README.md for an example.',
					gettype($this->callbacks::$map)
				)
			);
		}

		$map = json_encode($this->callbacks::$map);

		if ($map === null && json_last_error() !== JSON_ERROR_NONE) {
			throw new UnexpectedValueException(
				sprintf(
					'Callbacks map cannot be encoded to JSON. ' .
					'Error: %s',
					$this->configPath
				)
			);
		}

		$this->callbacksMap = (object) json_decode(json_encode($this->callbacks::$map));
		$this->registerRoutes();
	}

	/**
	 * Loops through the callbacks map and calls the individual registration
	 * function for each object and attribute.
	 *
	 * @throws UnexpectedValueException
	 */
	private function registerRoutes()
	{
		add_action('rest_api_init', function() {
			// Loop through each route and attribute to bind the callback to the attribute names
			foreach ($this->callbacksMap as $route) {
				foreach ($route->attributes as $attribute) {
					$this->registerAttribute($route, $attribute);
				}
			}
		});
	}

	/**
	 * Registers the attributes and callbacks to the REST API
	 *
	 * @throws RuntimeException If a callback does not exist or is not callable
	 *
	 * @throws UnexpectedValueException If an attribute method is not empty and
	 *                                  does not equal "get" or "update"
	 */
	private function registerAttribute(
		stdClass $route,
		stdClass $attribute
	) {
		// Check if the callback exists and is callable
		if (!method_exists($this->callbacks, $attribute->callback)
		    || !is_callable([$this->callbacks, $attribute->callback])) {
			throw new RuntimeException(
				sprintf(
					'Callback function "%1$s" does not exist ' .
					'or is not callable for attribute "%2$s".',
					$attribute->callback,
					$attribute->name
				)
			);
		}

		if (isset($attribute->method)
		    && ($attribute->method !== 'get'
		        || $attribute->method !== 'update')) {
			throw new UnexpectedValueException(
				sprintf(
					'Attribute method "%1$s" is not supported. ' .
					'Use either "update" or "get"(default) for '.
					'object: "%2$s" with attribute "%3$s".' ,
					$attribute->method,
					$route->object_type,
					$attribute->name
				)
			);
		}

		$method = ($attribute->method ?: 'get') . '_callback';

		// Register the custom attributes with accompanying callback
		$registered = register_rest_field(
			$route->object_type,
			$attribute->name,
			[
				$method => [$this->callbacks, $attribute->callback]
			]
		);
	}
}
