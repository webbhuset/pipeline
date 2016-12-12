# Product Import using Job Factory

* The job factory class is responsable of creating a job object.
* Dependency injection is used to add all project specific configuration.
* The job factory uses task factories to create tasks.

## Example factory classes

**Note!** To make this example more readable, the class names are written in their full canonical path.
Normally you would put several `use` statements in the top of the file.

* [Acme\Bifrost\Import\Product](./acme/Bifrost/Import/Product.php) is the job factory.
* [Acme\Bifrost\Import\Product\Task\Simple](./acme/Bifrost/Import/Product/Task/Simple.php) is the task factory for simple products.
* [Acme\Bifrost\Import\Product\Task\Configurable](./acme/Bifrost/Import/Product/Task/Configurable.php) is the task factory for configurable products.
