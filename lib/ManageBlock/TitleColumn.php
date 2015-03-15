<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Sites\ManageBlock;

use Icybee\ManageBlock\Column;
use Icybee\ManageBlock\EditDecorator;
use Icybee\Modules\Sites\Site;

/**
 * Column for the `title` property.
 */
class TitleColumn extends Column
{
	/**
	 * @param Site $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		return new EditDecorator($record->title, $record);
	}
}
