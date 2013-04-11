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

use ICanBoogie\Core;
use ICanBoogie\HTTP\Dispatcher;
use ICanBoogie\HTTP\RedirectResponse;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Routing;

class Hooks
{
	/**
	 * Initializes the {@link Core::$site}, {@link Core::$locale} and {@link Core::$timezone}
	 * properties of the core object. The {@link Core::$timezone} property is only initialized is
	 * it is defined be the site.
	 *
	 * If the current site has a path, the {@link Routing\contextualize()} and
	 * {@link Routing\decontextualize()} helpers are patched.
	 *
	 * @param Core\RunEvent $event
	 * @param Core $target
	 */
	static public function on_core_run(Core\RunEvent $event, Core $target)
	{
		$target->site = $site = Model::find_by_request($event->request);
		$target->locale = $site->language;

		if ($site->timezone)
		{
			$target->timezone = $site->timezone;
		}

		$path = $site->path;

		if ($path)
		{
			Routing\Helpers::patch('contextualize', function ($str) use ($path)
			{
				return $path . $str;
			});

			Routing\Helpers::patch('decontextualize', function ($str) use ($path)
			{
				if (strpos($str, $path . '/') === 0)
				{
					$str = substr($str, strlen($path));
				}

				return $str;
			});
		}
	}

	/**
	 * Redirects the request to the first available website to the user if the request matches
	 * none.
	 *
	 * Only online websites are used if the user is a guest or a member.
	 *
	 * @param Dispatcher\BeforeDispatchEvent $event
	 * @param Dispatcher $target
	 */
	static public function before_http_dispatcher_dispatch(Dispatcher\BeforeDispatchEvent $event, Dispatcher $target)
	{
		global $core;

		if ($core->site_id)
		{
			return;
		}

		$request = $event->request;

		if (!in_array($request->method, array(Request::METHOD_ANY, Request::METHOD_GET, Request::METHOD_HEAD)))
		{
			return;
		}

		$path = \ICanBoogie\normalize_url_path(\ICanBoogie\Routing\decontextualize($request->path));

		if (strpos($path, '/api/') === 0)
		{
			return;
		}

		try
		{
			$query = $core->models['sites']->order('weight');
			$user = $core->user;

			if ($user->is_guest || $user instanceof \Icybee\Modules\Members\Member)
			{
				$query->filter_by_status(Site::STATUS_OK);
			}

			$site = $query->one;

			if ($site)
			{
				$request_url = \ICanBoogie\normalize_url_path($core->site->url . $request->path);
				$location = \ICanBoogie\normalize_url_path($site->url . $path);

				#
				# we don't redirect if the redirect location is the same as the request URL.
				#

				if ($request_url != $location)
				{
					$query_string = $request->query_string;

					if ($query_string)
					{
						$location .= '?' . $query_string;
					}

					$event->response = new RedirectResponse
					(
						$location, 302, array
						(
							'Icybee-Redirected-By' => __CLASS__ . '::' . __FUNCTION__
						)
					);

					return;
				}
			}
		}
		catch (\Exception $e) { }

		\ICanBoogie\log_error('You are on a dummy website. You should check which websites are available or create one if none are.');
	}

	/**
	 * Returns the site active record associated to the node.
	 *
	 * This is the getter for the nodes' `site` magic property.
	 *
	 * <pre>
	 * <?php
	 *
	 * $core->models['nodes']->one->site;
	 * </pre>
	 *
	 * @param \Icybee\Modules\Nodes\Node $node
	 *
	 * @return Site|null The site active record associate with the node,
	 * or null if the node is not associated to a specific site.
	 */
	static public function get_node_site(\Icybee\Modules\Nodes\Node $node)
	{
		global $core;

		if (!$node->siteid)
		{
			return;
		}

		return $core->site_id == $node->siteid ? $core->site : $core->models['sites'][$node->siteid];
	}

	/**
	 * Returns the active record for the current site.
	 *
	 * This is the getter for the core's {@link Core::$site} magic property.
	 *
	 * <pre>
	 * <?php
	 *
	 * $core->site;
	 * </pre>
	 *
	 * @return Site
	 */
	static public function get_core_site(Core $core)
	{
		return Model::find_by_request($core->request ?: $core->initial_request);
	}

	/**
	 * Returns the key of the current site.
	 *
	 * This is the getter for the core's {@link Site::site_id} magic
	 * property.
	 *
	 * <pre>
	 * <?php
	 *
	 * $core->site_id;
	 * </pre>
	 *
	 * @param Core $core
	 *
	 * @return int
	 */
	static public function get_core_site_id(Core $core)
	{
		$site = self::get_core_site($core);

		return $site ? $site->siteid : null;
	}

	/**
	 * Returns the site active record for a request.
	 *
	 * This is the getter for the {@link \ICanBoogie\HTTP\Request\Context::site} magic property.
	 *
	 * <pre>
	 * <?php
	 *
	 * $core->request->context->site;
	 * </pre>
	 *
	 * @return Site
	 */
	static public function get_site_for_request_context(Request\Context $context)
	{
		return Model::find_by_request($context->request);
	}

	/**
	 * Returns the identifier of the site for a request.
	 *
	 * This is the getter for the {@link \ICanBoogie\HTTP\Request\Context::site_id} magic property.
	 *
	 * <pre>
	 * <?php
	 *
	 * $core->request->context->site_id;
	 * </pre>
	 *
	 * @return int
	 */
	static public function get_site_id_for_request_context(Request\Context $context)
	{
		return $context->site ? $context->site->siteid : null;
	}
}