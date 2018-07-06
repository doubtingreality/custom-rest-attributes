<?php

namespace CustomRestAttributes;

class Callbacks
{
	function getMemberTags($object) {
		return get_the_tags($object['id']);
	}

	function getMemberBiography($object) {
		$objectMeta = get_post_meta($object['id']);

		return $objectMeta['biography'];
	}

	function getMemberFeaturedWork($object) {
		$objectMeta = get_post_meta($object['id']);

		return $objectMeta['featured_work'];
	}
}
