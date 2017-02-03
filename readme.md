#swalker2 - Cpanel

The simplest PHP implementation of the cpanel version 2 api for Laravel

At the moment suporting only the Zone Edit and Email modules 

You can write your own modules if you dig into the [Guide to cPanel API 2](https://documentation.cpanel.net/display/SDK/Guide+to+cPanel+API+2)
 
 
 ### Install
 
 Require this package with composer using the following command:
 ```bash
 composer require swalker2/cpanel
 ```
 
 After updating composer, add the service provider to the `providers` array in `config/app.php`
 ```php
 Swalker2\Cpanel\CpanelServiceProvider::class,
 ```
 
 Also, publish the configuration file using the following command:
 ```bash
 php artisan vendor:publish --tag=swalker2.cpanel
 ```

 Finally, add the .env variables:
 ```
CPANEL_HOST=https://domain.com
CPANEL_PORT=2083
CPANEL_USERNAME=yourname
CPANEL_PASSWORD=yourpass
 ```

### But how do I use it?
After completing the instalation steps, you simply make a cpanel instance, like so:
```php
	$cpanel = app()->make(Cpanel::class);
```


And then you can call the module implementations
```php
	dd(
		$cpanel->zoneEdit('mydomain.com')->fetch()
	);
```


Note that the Modules that you create can be instantiated individually:
```php
	$mymodule = new MyCpanelModule();
	dd(
		$mymodule->doSomething()
	);
```


### Writing a module
To write a module you need to extend the class ``Swalker2\CpanelFunction``
like so: 
```php
namespace App;


use Swalker2\Cpanel\CpanelFunction;

class CpanelModule extends CpanelBaseModule
{
    
    function __construct()
    {
        parent::__construct();
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_module' => 'ModuleName', //reference this from the Guide to cPanel API 2
        ]);
    }
    
    public function someAction()
    {
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'some_action',
        ]);
                
        $response = $this->getApiData();
        
        //do something with the response
    }
}
```

### Contributing
Feel free to send pull requests, not just bug reports.

"Bug reports" may also be sent in the form of a pull request containing a failing test. 

However, if you file a bug report, your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue. The goal of a bug report is to make it easy for yourself - and others - to replicate the bug and develop a fix.
