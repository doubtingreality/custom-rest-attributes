<?php

declare(strict_types = 1);

namespace CustomRestAttributes;

/**
 * Modify this class with your own attributes and callbacks.
 * Below is an example for a custom post type "member" with custom attributes
 * "tags", "biography" and "featured_work" that refer to their own callbacks.
 * An attribute can have an optional property "method" with values "update" or
 * "get" (default).
 */
class Callbacks
{
	public static $map = [
		[
			'object_type' => 'member',
			'attributes' => [
				[
					'name' => 'tags',
					'callback' => 'getMemberTags'
				],
				[
					'name' => 'biography',
					'callback' => 'getMemberBiography'
				],
				[
					'name' => 'featured_work',
					'callback' => 'getMemberFeaturedWork'
				]
			]
		]
	];

	public static function getMemberTags($object) {
		return get_the_tags($object['id']);
	}

	public static function getMemberBiography($object) {
		$objectMeta = get_post_meta($object['id']);

		return $objectMeta['biography'];
	}

	public static function getMemberFeaturedWork($object) {
		$objectMeta = get_post_meta($object['id']);

		return $objectMeta['featured_work'];
	}
}
