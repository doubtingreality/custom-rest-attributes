<?php
/**
 * Plugin Name: Custom Rest Attributes
 * Description: Allows addition of custom attributes with custom callbacks for the WordPress REST API
 * Author:      Murtada al Mousawy
 * Version:     0.2.0
 * Author URI:  https://murtada.nl
 * License:     GPLv3
 */

if (!class_exists('CustomRestAttributes')) {
	// Require the main and callbacks class
	require 'classes/CustomRestAttributes.php';
	require 'classes/Callbacks.php';

	new CustomRestAttributes\CustomRestAttributes();
}
