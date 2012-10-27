<?php

if (!@include __DIR__ . '/../vendor/autoload.php') {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}

/*
 * Generate Mandango model.
 */
$configClasses = array(
    'Model\Mandango\Article' => array(
        'fields' => array(
            'title' => array('type' => 'string'),
        ),
    ),
);

use Mandango\Mondator\Mondator;

$mondator = new Mondator();
$mondator->setConfigClasses($configClasses);
$mondator->setExtensions(array(
    new Mandango\Extension\Core(array(
        'metadata_factory_class'  => 'Model\Mandango\Mapping\Metadata',
        'metadata_factory_output' => __DIR__.'/Model/Mandango/Mapping',
        'default_output'          => __DIR__.'/Model/Mandango',
    )),
));
$mondator->process();
