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

use ICanBoogie\ActiveRecord\CreatedAtProperty;
use ICanBoogie\ActiveRecord\UpdatedAtProperty;
use ICanBoogie\Debug;

/**
 * Representation of a website.
 *
 * @property array $translations Translations for the site.
 *
 * @method \Icybee\Modules\Pages\Page|null resolve_view_target() resolve_view_target(string $view)
 * Return the page on which the view is displayed, or null if the view is not displayed.
 *
 * This method is injected by the "pages" module.
 *
 * @method string resolve_view_url() resolve_view_url(string $view) Return the URL of the view.
 *
 * This method is injected by the "pages" module.
 *
 * @property \ICanBoogie\DateTime $created_at Date and time at which the site was created.
 * @property \ICanBoogie\DateTime $updated_at Date and time at which the site was updated.
 * @property-read string $url The URL of the website.
 * @property-read string $native The native source of the site.
 */
class Site extends \ICanBoogie\ActiveRecord
{
	const MODEL_ID = 'sites';

	const SITEID = 'siteid';
	const SUBDOMAIN = 'subdomain';
	const DOMAIN = 'domain';
	const PATH = 'path';
	const TLD = 'tld';
	const TITLE = 'title';
	const ADMIN_TITLE = 'admin_title';
	const LANGUAGE = 'language';
	const TIMEZONE = 'tmezone';
	const NATIVEID = 'nativeid';
	const STATUS = 'status';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	const BASE = '/protected/';

	const STATUS_OK = 200;
	const STATUS_UNAUTHORIZED = 401;
	const STATUS_NOT_FOUND = 404;
	const STATUS_UNAVAILABLE = 503;

	public $siteid;
	public $path = '';
	public $tld = '';
	public $domain = '';
	public $subdomain = '';
	public $title;
	public $admin_title = '';
	public $weight = 0;
	public $language = '';
	public $nativeid = 0;
	public $timezone = '';
	public $email = '';
	public $status = 0;

	use CreatedAtProperty;
	use UpdatedAtProperty;

	/**
	 * Clears the sites cache.
	 */
	public function save()
	{
		unset($this->app->vars['cached_sites']);

		return parent::save();
	}

	/**
	 * Returns the URL of the website.
	 *
	 * @return string
	 */
	protected function get_url()
	{
		$parts = explode('.', $_SERVER['SERVER_NAME']);
		$parts = array_reverse($parts);

		if ($this->tld)
		{
			$parts[0] = $this->tld;
		}

		if ($this->domain)
		{
			$parts[1] = $this->domain;
		}

		if ($this->subdomain)
		{
			$parts[2] = $this->subdomain;
		}
		else if (empty($parts[2]))
		{
			unset($parts[2]);
		}

		return 'http://' . implode('.', array_reverse($parts)) . $this->path;
	}

	/**
	 * Returns the available templates for the site
	 */
	protected function lazy_get_templates()
	{
		$templates = [];
		$root = \ICanBoogie\DOCUMENT_ROOT;

		$models = [ 'default', 'all' ];

		foreach ($models as $model)
		{
			$path = self::BASE . $model . '/templates';

			if (!is_dir($root . $path))
			{
				continue;
			}

			$dh = opendir($root . $path);

			if (!$dh)
			{
				Debug::trigger('Unable to open directory %path', [ '%path' => $path ]);

				continue;
			}

			while (($file = readdir($dh)) !== false)
			{
				if ($file{0} == '.')
				{
					continue;
				}

				$pos = strrpos($file, '.');

				if (!$pos)
				{
					continue;
				}

				$templates[$file] = $file;
			}

			closedir($dh);
		}

		sort($templates);

		return $templates;
	}

	protected function lazy_get_partial_templates()
	{
		$templates = [];
		$root = \ICanBoogie\DOCUMENT_ROOT;

		$models = [ 'default', 'all' ];

		foreach ($models as $model)
		{
			$path = self::BASE . $model . '/templates/partials';

			if (!is_dir($root . $path))
			{
				continue;
			}

			$dh = opendir($root . $path);

			if (!$dh)
			{
				Debug::trigger('Unable to open directory %path', [ '%path' => $path ]);

				continue;
			}

			while (($file = readdir($dh)) !== false)
			{
				if ($file{0} == '.')
				{
					continue;
				}

				$pos = strrpos($file, '.');

				if (!$pos)
				{
					continue;
				}

				$id = preg_replace('#\.(php|html)$#', '', $file);
				$templates[$id] = $root . $path . '/' . $file;
			}

			closedir($dh);
		}

		return $templates;
	}

	/**
	 * Resolve the location of a relative path according site inheritance.
	 *
	 * @param string $relative The path to the file to locate.
	 */

	public function resolve_path($relative)
	{
		$root = $_SERVER['DOCUMENT_ROOT'];

		$try = self::BASE . 'default/' . $relative;

		if (file_exists($root . $try))
		{
			return $try;
		}

		$try = self::BASE . 'all/' . $relative;

		if (file_exists($root . $try))
		{
			return $try;
		}
	}

	protected function get_native()
	{
		$native_id = $this->nativeid;

		return $native_id ? $this->model[$native_id] : $this;
	}

	/**
	 * Returns the translations for this site.
	 *
	 * @return array
	 */
	protected function lazy_get_translations()
	{
		if ($this->nativeid)
		{
			return $this->model->where('siteid != ? AND (siteid = ? OR nativeid = ?)', $this->siteid, $this->nativeid, $this->nativeid)->order('language')->all;
		}
		else
		{
			return $this->model->where('nativeid = ?', $this->siteid)->order('language')->all;
		}
	}

	private $_server_name;

	protected function get_server_name()
	{
		if ($this->_server_name)
		{
			return $this->_server_name;
		}

		$parts = explode('.', $_SERVER['SERVER_NAME']);
		$parts = array_reverse($parts);

		if (count($parts) > 3)
		{
			$parts[2] = implode('.', array_reverse(array_slice($parts, 2)));
		}

		$parts += [ 2 => '' ];

		if ($this->tld)
		{
			$parts[0] = $this->tld;
		}

		if ($this->domain)
		{
			$parts[1] = $this->domain;
		}

		if ($this->subdomain)
		{
			$parts[2] = $this->subdomain;
		}

		return $this->_server_name = new ServerName([ $parts[2], $parts[1], $parts[0] ]);
	}

	protected function set_server_name($server_name)
	{
		if (!($server_name instanceof ServerName))
		{
			$server_name = new ServerName($server_name);
		}

		$this->subdomain = $server_name->subdomain;
		$this->domain = $server_name->domain;
		$this->tld = $server_name->tld;

		$this->_server_name = $server_name;
	}
}
