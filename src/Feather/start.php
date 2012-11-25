<?php namespace Feather;

/*
|--------------------------------------------------------------------------
| Feather Definitions
|--------------------------------------------------------------------------
|
| Define some constants used by Feather.
|
*/

define('FEATHER_VERSION', '1.0.0');
define('FEATHER_DATABASE', 'feather');

/*
|--------------------------------------------------------------------------
| Path to Feather
|--------------------------------------------------------------------------
|
| Define some paths used by Feather.
|
*/

$app['feather']['path.extensions'] = $app['path.base'].'/feather/extensions';
$app['feather']['path.themes'] = $app['path.base'].'/feather/themes';

/*
|--------------------------------------------------------------------------
| Extension Class Loading
|--------------------------------------------------------------------------
|
| To keep extensions namespaced correctly we'll give the Composer
| autoloader the namespace and path to our extensions directory.
|
*/

$loader = require $app['path.base'].'/vendor/autoload.php';

$loader->add('Feather\\Extensions', $app['path.base']);

/*
|--------------------------------------------------------------------------
| Feather Configuration
|--------------------------------------------------------------------------
|
| Register some important configuration options that allows Feather to run
| smoothly.
|
*/

$app['config']->package('feather/feather', __DIR__);

$app['config']->set('database.connections.'.FEATHER_DATABASE, $app['config']['feather::database']);

$app['config']->set('database.migration.paths.feather', $app['feather']['path'].'/Migrations');

if ($app['cache']->has('config'))
{
	$app['cache']->forget('config');
}

foreach (Models\Config::everything() as $item)
{
	$app['config']->set("feather::{$item->name}", $item->value);
}

/*
|--------------------------------------------------------------------------
| Feather Components
|--------------------------------------------------------------------------
|
| Load in Feather's components.
|
*/

foreach ($app['config']['feather::providers'] as $provider)
{
	$app->register(new $provider);
}

/*
|--------------------------------------------------------------------------
| Feather Facades
|--------------------------------------------------------------------------
|
| Load in the facades used by Feather's components.
|
*/

require __DIR__.'/facades.php';

/*
|--------------------------------------------------------------------------
| Feather Theme
|--------------------------------------------------------------------------
|
| Prepare the theme for Feather and setup theme development mode.
|
*/

$app['feather']['presenter']->prepare();

/*
|--------------------------------------------------------------------------
| Feather Extensions
|--------------------------------------------------------------------------
|
| Register the activated extensions with Feather.
|
*/

$extensions = Models\Extension::activated();

$app['feather']['extensions']->registerExtensions($extensions);

/*
|--------------------------------------------------------------------------
| Feather Application Routes
|--------------------------------------------------------------------------
|
| Register application routes for Feather.
|
*/

$app['feather']->parseRoutes(require __DIR__.'/routes.php');