<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Sites;

use ICanBoogie\I18n\FormattedString;

/**
 * Creates or updates a website.
 */
class SaveOperation extends \ICanBoogie\SaveOperation
{
	protected function process()
	{
		global $core;

		$rc = parent::process();

		unset($core->vars['cached_sites']);

		$record = $this->module->model[$rc['key']];

		$this->response->message = new FormattedString
		(
			$rc['mode'] == 'update' ? '%title has been updated in %module.' : '%title has been created in %module.', array
			(
				'title' => \ICanBoogie\shorten($record->title),
				'module' => $this->module->title
			)
		);

		return $rc;
	}
}