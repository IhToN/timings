<?php
/**
 * Copyright (c) (2016) - Aikar's Minecraft Timings Parser
 *
 *  Written by Aikar <aikar@aikar.co>
 *    + Contributors (See AUTHORS)
 *
 *  http://aikar.co
 *  http://starlis.com
 *
 * @license MIT
 *
 */

namespace Starlis\Timings;

class Cache {
	/**
	 * Get Cached Data
	 *
	 * @param        $key
	 * @param string $type
	 *
	 * @return null|string
	 */
	public static function get($key, $type = 'timings') {
		$file = self::getFile($key, $type, $_SERVER['DOCUMENT_ROOT']);
		if (!file_exists($file)) {
			$file = self::getFile($key, $type, $_SERVER['DOCUMENT_ROOT']);
		}
		if (file_exists($file)) {
			if (is_writable($file)) {
				touch($file);
			}

			return trim(gzdecode(file_get_contents($file)));
		}

		return null;
	}


	/**
	 * Put data into cache
	 *
	 * @param        $key
	 * @param        $data
	 * @param string $type
	 */
	public static function put($key, $data, $type = 'timings') {
		$file = self::getFile($key, $type, $_SERVER['DOCUMENT_ROOT']);
		file_put_contents($file, gzencode($data));
	}


	/**
	 * Retrieves a cached object
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function getObject($key) {
		global $ini;
		return unserialize(self::get($key, "objectcache" . $ini['cache_ver']));
	}

	/**
	 * Saves serialized data for fast loading of parsed data.
	 *
	 * @param $key
	 * @param $data
	 */
	public static function putObject($key, $data) {
		$data = serialize($data);
		if ($data) {
			global $ini;
			self::put($key, $data, "objectcache" . $ini['cache_ver']);
		}
	}

	/**
	 * Sanitizes a key name and returns a cache file name for it.
	 *
	 * @param $key
	 * @param $type
	 *
	 * @return string
	 */
	public static function getFile($key, $type = 'timings', $dir = null) {
		$key = preg_replace('/[^a-zA-Z0-9-_]/ms', '', $key);

		global $ini;
		if (\is_null($dir)) {
			$dir = $_SERVER['DOCUMENT_ROOT'];
		}
		return $dir . "/${type}_${key}.gz";
	}
}
