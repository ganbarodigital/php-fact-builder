# ARCHITECTURE

## Module Parts

* Checks - true/false checks, normally (but not exclusively) against Data objects
* DataBuilders - return objects that ultimately implement the Data interface
* DataTypes - any Data classes defined by the module
* FactBuilders - return objects that implement the Fact interface
* Facts - any Fact classes defined by the module
* ValueBuilders - return data in the form of PHP scalar types