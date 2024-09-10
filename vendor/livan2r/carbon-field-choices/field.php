<?php
use Carbon_Fields\Carbon_Fields;
use Carbon_Field_Choices\Choices_Field;

define( 'Carbon_Field_Choices\\DIR', __DIR__ );

$loaded = defined('ABSPATH');
if ($loaded) {
    Carbon_Fields::extend(Choices_Field::class, function ($container) {
        return new Choices_Field($container['arguments']['type'], $container['arguments']['name'], $container['arguments']['label']);
    });
}
