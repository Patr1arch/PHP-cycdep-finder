Cyclic Dependencies Finder
==========

Requirements
--------
* PHP v8.*=>
* Composer v2.*=>

Usage
--------
    php -d memory_limit=1024M vendor/bin/cycdep.php <directories, .php or composer.json files>
optional you can pass some options:
* -v to view elements that form cyclic dependency
* -vv to view php AST's, composer json array with -v output 

Highly recommended to run this script with custom memory_limit size
