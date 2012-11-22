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

$app['feather']['path.extensions'] = __DIR__.'/Feather/Extensions';
$app['feather']['path.themes'] = __DIR__.'/Feather/Themes';

/*
|--------------------------------------------------------------------------
| Feather Configuration
|--------------------------------------------------------------------------
|
| Register the Feather database connection and the configuration.
|
*/

$app['config']->package('feather/feather', __DIR__);

$app['config']['database.connections.'.FEATHER_DATABASE] = $app['config']['feather::database'];

// TODO: Remove after development.
if ($app['cache']->has('config')) $app['cache']->forget('config');

foreach (Models\Config::everything() as $item)
{
	$app['config']["feather::{$item->name}"] = $item->value;
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
| Prepare the theme to be used by Feather and register some view paths.
|
*/

$app['feather']['presenter']->prepare(array(
	'path'		  => $app['feather']['path'],
	'path.themes' => $app['feather']['path.themes']
));

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