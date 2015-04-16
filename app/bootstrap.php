<?php
use \Nette\Application\Routers\Route as Route;
require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode(array('23.75.345.200', )); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

/*$configurator->onCompile[] = function ($configurator, $compiler) {
    $compiler->addExtension('formDateTime', new IPub\FormDateTime\DI\FormDateTimeExtension);
};
 $configurator->onCompile[] = function ($configurator, $compiler) {
    $compiler->addExtension('addTbDateTimePicker', new RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker);
};*/

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();


Nette\Forms\Container::extensionMethod('addTbDateTimePicker', function(Nette\Forms\Form $_this, $name, $label, $cols = NULL, $maxLength = NULL) {
  return $_this[$name] = new RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker($label, $cols, $maxLength);
});

$container->router[] = new Route('produkty/<id>', array(
			'id' => array(
				Route::FILTER_IN => function ($str) use ($container) {
					if(is_numeric($str)) {
						return $str;
					} else {
						/** @var products Nette\Database\Table\Selection */
						$products = $container->productsService->getAll();
						return $products->where('uri', $str)->fetch()->id;
					}
				},
				Route::FILTER_OUT => function ($id) use ($container) {
					if (!is_numeric($id)) {
						return $id;
					} else {
						return Nette\Utils\Strings::webalize($container->productsService->getProduct($id)->name);
					}
				}
			),
			'presenter' => 'Products',
			'action' => 'product'
		));
$container->router[] = new Route('kategorie/<cat>', array(
			'cat' => array(
				Route::FILTER_IN => function ($str) use ($container) {
					if(is_numeric($str)) {
						return $str;
					} else {
						/** @var products Nette\Database\Table\Selection */
						$categories = $container->categoriesService->getAll();
						return $categories->where('uri', $str)->fetch()->id;
					}
				},
				Route::FILTER_OUT => function ($id) use ($container) {
					if (!is_numeric($id)) {
						return $id;
					} else {
						return Nette\Utils\Strings::webalize($container->categoriesService->get($id)->name);
					}
				}
			),
			'presenter' => 'Products',
			'action' => 'default'
		));
/*$container->router[] = new Route('//[!<mobile (www|m)>.]%domain%/<presenter>/<action>/[</id>]', array(
                'mobile' => 'm',
                'presenter' => 'Register',
                'action' => 'default',
));*/
$container->router[] = new Route('produkty', array(
	'presenter' => 'Products',
	'action' => 'default'
));
$container->router[] = new Route('kategorie', array(
	'presenter' => 'Categories',
	'action' => 'default'
));
$container->router[] = new Route('rezervace/pridat', array(
	'presenter' => 'Bookings',
	'action' => 'add'
));
$container->router[] = new Route('rezervace/[<id>]', array(
	'presenter' => 'Bookings',
	'action' => 'default'
));
$container->router[] = new Route('objednavky/[<id>]', array(
	'presenter' => 'Orders',
	'action' => 'default'
));
$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
$container->router[] = new Route('<presenter>/<action>/<id>', 'Homepage:default');

return $container;
