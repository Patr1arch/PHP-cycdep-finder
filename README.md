Cyclic Dependencies Finder
==========

Requirements
--------
* PHP v8.*=>
* Composer v2.*=>

Usage
--------
    vendor/bin/cycdep.php <directories, .php or composer.json files>
optional you can pass some options:
* -v to view elements that form cyclic dependency
* -vv to view php AST's, composer json array with -v output 
