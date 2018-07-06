<?php
/*
Plugin Name: Custom Rest Attributes
Description: Allows addition of custom attributes with custom callbacks for the WordPress REST API
Author: Murtada al Mousawy
Version: 0.1.0
Author URI: https://murtada.nl
*/

require 'classes/Core.php';
require 'classes/Callbacks.php';

new CustomRestAttributes\Core('example.config.json');
