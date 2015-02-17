<?php
use \Nette\Application\Routers\Route as Route;
require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

$container->router[] = new \Nette\Application\Routers\Route('produkty/<id>', array(
			'id' => array(
				Route::FILTER_IN => function ($id) use ($container) {
					if(is_numeric($id)) {
						return $id;
					} else {
						/** @var $products Nette\Database\Table\Selection */
						$products = $container->createProducts();
						return $products->where('uri', $id)->fetch()->id;
					}
				},
				Route::FILTER_OUT => function ($id) use ($container) {
					if (!is_numeric($id)) {
						return $id;
					} else {
						/** @var $pages Nette\Database\Table\Selection */
						$pages = $container->createProducts();
					}
				}
			),
			'presenter' => 'Products',
			'action' => 'product'
		));
$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
$container->router[] = new Route('<presenter>/<action>/<id>', 'Homepage:default');

return $container;
