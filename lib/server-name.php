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
 * A representation of a server name.
 */
class ServerName
{
	public $subdomain;
	public $domain;
	public $tld;

	public function __construct($server_name)
	{
		$this->modify($server_name);
	}

	/**
	 * Returns a string representation of the server name.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$parts = [ $this->subdomain, $this->domain, $this->tld ];
		$parts = array_filter($parts);

		return implode('.', $parts);
	}

	/**
	 * Modifies the server name.
	 *
	 * The method updates the {@link $subdomain}, {@link $domain} and {@link $tld} properties.
	 *
	 * @param string|array $server_name
	 */
	public function modify($server_name)
	{
		$subdomain = null;
		$domain = null;
		$tld = null;

		if (is_array($server_name))
		{
			list($subdomain, $domain, $tld) = $server_name;
		}
		else
		{
			$parts = explode('.', $server_name);

			if (count($parts) > 1)
			{
				$tld = array_pop($parts);
			}

			if (count($parts) > 1)
			{
				$domain = array_pop($parts);
			}

			$subdomain = implode('.', $parts);
		}

		$this->subdomain = $subdomain;
		$this->domain = $domain;
		$this->tld = $tld;
	}
}
