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

use Icybee\Modules\Sites\SiteTest\Extended;

use ICanBoogie\DateTime;

class SiteTest extends \PHPUnit_Framework_TestCase
{
	public function test_model_id()
	{
		$site = new Site;

		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);

		$this->assertEquals($site->_model_id, $unserialized_site->_model_id);
		$this->assertArrayNotHasKey('_model_id', $site->to_array());
	}

	public function test_created_at()
	{
		$site = new Site;
		$d = $site->created_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertTrue($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('0000-00-00 00:00:00', $d->as_db);

		$site->created_at = '2013-03-07 18:30:45';
		$d = $site->created_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$site->created_at = new DateTime('2013-03-07 18:30:45', 'utc');
		$d = $site->created_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$site->created_at = null;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);

		$site->created_at = DateTime::now();
		$properties = $site->__sleep();
		$this->assertArrayHasKey('created_at', $properties);
		$array = $site->to_array();
		$this->assertArrayHasKey('created_at', $array);

		$site = new Site();
		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);
		$this->assertInstanceOf('ICanBoogie\DateTime', $unserialized_site->created_at);
		$this->assertTrue($unserialized_site->created_at->is_empty);

		$site = new Site();
		$site->created_at = $time = new DateTime('now', 'utc');
		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);
		$this->assertEquals($time, $unserialized_site->created_at);

		$site = new Extended();
		$site->created_at = $time = new DateTime('now', 'utc');
		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);
		$this->assertEquals($time, $unserialized_site->created_at);
	}

	public function test_updated_at()
	{
		$site = new Site;
		$d = $site->updated_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertTrue($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('0000-00-00 00:00:00', $d->as_db);

		$site->updated_at = '2013-03-07 18:30:45';
		$d = $site->updated_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$site->updated_at = new DateTime('2013-03-07 18:30:45', 'utc');
		$d = $site->updated_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$site->updated_at = null;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);

		$site->updated_at = DateTime::now();
		$properties = $site->__sleep();
		$this->assertArrayHasKey('updated_at', $properties);
		$array = $site->to_array();
		$this->assertArrayHasKey('updated_at', $array);

		$site = new Site();
		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);
		$this->assertInstanceOf('ICanBoogie\DateTime', $unserialized_site->updated_at);
		$this->assertTrue($unserialized_site->updated_at->is_empty);

		$site = new Site();
		$site->updated_at = $time = new DateTime('now', 'utc');
		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);
		$this->assertEquals($time, $unserialized_site->updated_at);

		$site = new Extended();
		$site->updated_at = $time = new DateTime('now', 'utc');
		$serialized_site = serialize($site);
		$unserialized_site = unserialize($serialized_site);
		$this->assertEquals($time, $unserialized_site->updated_at);
	}
}

namespace Icybee\Modules\Sites\SiteTest;

class Extended extends \Icybee\Modules\Sites\Site
{

}