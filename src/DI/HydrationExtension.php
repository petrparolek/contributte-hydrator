<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineHydration\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\DoctrineHydration\Adapters\IArrayAdapter;
use WebChemistry\DoctrineHydration\Adapters\IFieldAdapter;
use WebChemistry\DoctrineHydration\Factories\IMetadataFactory;
use WebChemistry\DoctrineHydration\Factories\MetadataFactory;
use WebChemistry\DoctrineHydration\Hydration;
use WebChemistry\DoctrineHydration\IHydration;

class HydrationExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'adapters' => [
			'fields' => [],
			'array' => [],
		]
	];

	public function loadConfiguration(): void {
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$hydration = $builder->addDefinition($this->prefix('hydration'))
			->setType(IHydration::class)
			->setFactory(Hydration::class);

		$builder->addDefinition($this->prefix('metadataFactory'))
			->setType(IMetadataFactory::class)
			->setFactory(MetadataFactory::class);

		foreach ($config['adapters']['fields'] as $name => $adapter) {
			if (class_exists($adapter)) {
				$def = $builder->addDefinition($this->prefix('fieldAdapter.' . $name))
					->setType(IFieldAdapter::class)
					->setFactory($adapter);
			} else {
				$def = $adapter;
			}

			$hydration->addSetup('addFieldAdapter', [$def]);
		}

		foreach ($config['adapters']['array'] as $name => $adapter) {
			if (class_exists($adapter)) {
				$def = $builder->addDefinition($this->prefix('arrayAdapter.' . $name))
					->setType(IArrayAdapter::class)
					->setFactory($adapter);
			} else {
				$def = $adapter;
			}

			$hydration->addSetup('addArrayAdapter', [$def]);
		}
	}

}
