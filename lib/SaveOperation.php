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

/**
 * Creates or updates a website.
 *
 * @property Site $record
 */
class SaveOperation extends \ICanBoogie\SaveOperation
{
	protected function process()
	{
		$rc = parent::process();

		unset($this->app->vars['cached_sites']);

		$this->response->message = $this->format($rc['mode'] == 'update' ? '%title has been updated in %module.' : '%title has been created in %module.', [

			'title' => \ICanBoogie\shorten($this->record->title),
			'module' => $this->module->title

		]);

		return $rc;
	}
}
