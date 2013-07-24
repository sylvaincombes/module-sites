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
 * An element to manage websites.
 */
class ManageBlock extends \Icybee\ManageBlock
{
	static public function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/admin.css');
		$document->js->add(DIR . 'public/admin.js');
	}

	public function __construct($module, array $attributes=array())
	{
		global $core;

		parent::__construct
		(
			$module, $attributes + array
			(
				self::T_ORDER_BY => array('updated_at', 'desc'),
				self::T_COLUMNS_ORDER => array('title', 'url', 'language', 'timezone', 'updated_at', 'status')
			)
		);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `title`: An instance of {@link ManageBlock\TitleColumn}.
	 * - `url`: An instance of {@link ManageBlock\URLColumn}.
	 * - `language`: An instance of {@link ManageBlock\LanguageColumn}.
	 * - `status`: An instance of {@link ManageBlock\StatusColumn}.
	 * - `timezone`: An instance of {@link ManageBlock\TimezoneColumn}.
	 * - `update_at`: An instance of {@link \Icybee\ManageBlock\DateTimeColumn}.
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), array
		(
			'title' => __CLASS__ . '\TitleColumn',
			'url' => __CLASS__ . '\URLColumn',
			'language' => __CLASS__ . '\LanguageColumn',
			'status' => __CLASS__ . '\StatusColumn',
			'timezone' => __CLASS__ . '\TimezoneColumn',
			'updated_at' => 'Icybee\ManageBlock\DateTimeColumn'
		));
	}
}

namespace Icybee\Modules\Sites\ManageBlock;

use Brickrouge\DropdownMenu;

use Icybee\ManageBlock\Column;
use Icybee\Modules\Sites\Site;
use Icybee\ManageBlock\FilterDecorator;
use Icybee\ManageBlock\EditDecorator;

/* @var $record \Icybee\Modules\Sites\Site */

class TitleColumn extends Column
{
	public function render_cell($record)
	{
		return new EditDecorator($record->title, $record);
	}
}

/**
 * Representation of the `status` column.
 */
class StatusColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'title' => 'Status'
			)
		);
	}

	public function render_cell($record)
	{
		static $labels = array
		(
			Site::STATUS_OK => 'Ok (online)',
			Site::STATUS_UNAUTHORIZED => 'Unauthorized',
			Site::STATUS_NOT_FOUND => 'Not found (offline)',
			Site::STATUS_UNAVAILABLE => 'Unavailable'
		);

		static $classes = array
		(
			Site::STATUS_OK => 'btn-success',
			Site::STATUS_UNAUTHORIZED => 'btn-warning',
			Site::STATUS_NOT_FOUND => 'btn-danger',
			Site::STATUS_UNAVAILABLE => 'btn-warning'
		);

		$status = $record->status;
		$status_label = isset($labels[$status]) ? $labels[$status] : "<em>Invalid status code: $status</em>";
		$status_class = isset($classes[$status]) ? $classes[$status] : 'btn-danger';
		$site_id = $record->siteid;

		$menu = new DropdownMenu
		(
			array
			(
				DropdownMenu::OPTIONS => $labels,

				'value' => $status
			)
		);

		$classes_json = \Brickrouge\escape(json_encode($classes));

		return <<<EOT
<div class="btn-group" data-property="status" data-site-id="$site_id" data-classes="$classes_json">
	<span class="btn $status_class dropdown-toggle" data-toggle="dropdown"><span class="text">$status_label</span> <span class="caret"></span></span>
	$menu
</div>
EOT;
	}
}

/**
 * Representation of the `url` column.
 */
class URLColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'orderable' => false
			)
		);
	}

	public function render_cell($record)
	{
		$parts = explode('.', $_SERVER['SERVER_NAME']);
		$parts = array_reverse($parts);

		if ($record->tld)
		{
			$parts[0] = '<strong>' . $record->tld . '</strong>';
		}

		if ($record->domain)
		{
			$parts[1] = '<strong>' . $record->domain . '</strong>';
		}

		if ($record->subdomain)
		{
			$parts[2] = '<strong>' . $record->subdomain . '</strong>';
		}
		else if (empty($parts[2]))
		{
			unset($parts[2]);
		}

		$label = 'http://' . implode('.', array_reverse($parts)) . ($record->path ? '<strong>' . $record->path . '</strong>' : '');

		return '<a href="' . $record->url . '">' . $label . '</a>';
	}
}

/**
 * Representation of the `timezone` column.
 */
class TimezoneColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'discreet' => true
			)
		);
	}

	public function render_cell($record)
	{
		$timezone = $record->timezone;

		if (!$timezone)
		{
			return '<em title="Inherited from the server\'s configuration" class="light">' . date_default_timezone_get() . '</em>';
		}

		return new FilterDecorator($record, $this->id, $this->manager->is_filtering($this->id));
	}
}

/**
 * Representation of the `language` column.
 */
class LanguageColumn extends Column
{
	public function __construct(\Icybee\ManageBlock $manager, $id, array $options=array())
	{
		parent::__construct
		(
			$manager, $id, $options + array
			(
				'discreet' => true
			)
		);
	}

	public function render_cell($record)
	{
		global $core;

		$property = $this->id;

		return new FilterDecorator
		(
			$record,
			$property,
			$this->manager->is_filtering($property),
			\ICanBoogie\capitalize($core->locale->conventions['localeDisplayNames']['languages'][$record->$property])
		);
	}
}