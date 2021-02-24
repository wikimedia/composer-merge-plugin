<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		'vendor/composer/composer/src/Composer/',
		'vendor/composer/semver/src/',

	]
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		'vendor/composer/composer/src/Composer/',
		'vendor/composer/semver/src/',
	]
);

return $cfg;
