# The `sites` module [![Build Status](https://travis-ci.org/Icybee/module-sites.png?branch=2.0)](https://travis-ci.org/Icybee/module-sites)

Manages websites.





### Event hooks





#### `ICanBoogie\Core::run`

Initializes the `site`, `locale` and `timezone` properties of the core object. If the website has
a path, the `contextualize()` and `decontextualize()` helpers of the [Routing](https://github.com/ICanBoogie/Routing)
package are patched.





#### `ICanBoogie\HTTP\Dispatcher::dispatch:before`

Redirects the request to the first available website to the user if the request matches none.





### Prototype methods





#### `Icybee\Modules\Nodes\Node::get_site`

Returns the website the node belongs to.

```php
<?php

$core->models['nodes']->one->site;
```





#### `ICanBoogie\Core::get_site`

Returns the website matching the request or the initial request.

```php
<?php

$core->site;
```





#### `ICanBoogie\Core::get_site_id`

Returns the identifier of the website matching the request or the initial request.

```php
<?php

$core->site_id;
```





#### `ICanBoogie\HTTP\Request\Context::get_site`

Returns the website matching the request context.

```php
<?php

$core->request->context->site;
```





#### `ICanBoogie\HTTP\Request\Context::get_site_id`

Returns the identifier of the website matching the request context.

```php
<?php

$core->request->context->site_id;
```





## Requirement

The package requires PHP 5.4 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

```json
{
	"minimum-stability": "dev",
	"require":
	{
		"icybee/module-sites": "2.x"
	}
}
```





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-sites), its repository can be
cloned with the following command line:

	$ git clone git://github.com/Icybee/module-sites.git sites





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later be
cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://travis-ci.org/Icybee/module-sites.png?branch=2.0)](https://travis-ci.org/Icybee/module-sites)





## License

The module is licensed under the New BSD License - See the LICENSE file for details.