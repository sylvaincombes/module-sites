<?php

namespace Icybee\Modules\Sites;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return array
(
	Descriptor::CATEGORY => 'site',
	Descriptor::MODELS => array
	(
		'primary' => array
		(
			Model::SCHEMA => array
			(
				'fields' => array
				(
					'siteid' => 'serial',
					'path' => array('varchar', 80),
					'tld' => array('varchar', 16),
					'domain' => array('varchar', 80),
					'subdomain' => array('varchar', 80),
					'title' => array('varchar', 80),
					'admin_title' => array('varchar', 80),
					'weight' => array('integer', 'unsigned' => true),
					'language' => array('varchar', 8),
					'nativeid' => 'foreign',
					'timezone' => array('varchar', 32), // widest is "America/Argentina/Buenos_Aires" with 30 characters
					'email' => 'varchar',
					'status' => array('integer', 'small'),
					'created_at' => 'datetime',
					'updated_at' => 'datetime'
				)
			)
		)
	),

	Descriptor::NS => __NAMESPACE__,
	Descriptor::REQUIRED => true,
	Descriptor::TITLE => 'Sites',
	Descriptor::VERSION => '1.0'
);