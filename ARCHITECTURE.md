# ARCHITECTURE

## Module Parts

* Checks - true/false checks, normally (but not exclusively) against Data objects
* DataBuilders - return objects that ultimately implement the Data interface
* DataTypes - any Data classes defined by the module
* FactBuilders - return objects that implement the Fact interface
* Facts - any Fact classes defined by the module
* ValueBuilders - return data in the form of PHP scalar types

## What A Module Does

1. Listen for its own data types
1. Listen for any facts that it is interested in
1. Build facts - must be classes that are defined by the module
1. Build data - normally classes defined by other modules

To illustrate, Module A can:

* listen for data types defined by Module A
* listen for facts defined by any module
* build facts defined by Module A, but not Modules B, C, and so on
* build data defined by any module

Why? I'm looking for a disciplined approach to help guide the development of modules. Too soon to say whether this is the right approach.