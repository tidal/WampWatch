<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('tests')
    ->exclude('vendor')
    ->exclude('examples')
    ->in('src');

return Symfony\CS\Config\Config::create()
    ->finder($finder);