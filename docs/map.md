# Map Command (WIP Documentation)


```bash
map

App/Thing/Blabla.php Blabla2.php
./SubThing2/Blabla.php
-/SubThing/Blabla.php
-/SubThing3/Blabla.php
```

Execute multiple actions:
- Creates `App/Thing` directory
- Creates `App/Thing/Blabla.php` file
- Creates `App/Thing/Blabla2.php` file
- Sets `App/Thing` as 'last directory'
- Creates `App/Thing/SubThing2` directory
- The command map lines starting with '.' to the 'last directory'
- Also, these lines don't change the 'last directory'
- Creates `App/Thing/SubThing2/Blabla.php` file
- Creates `App/Thing/SubThing` directory
- The command map lines starting with '-' to the 'last directory' AND change 'last directory' values (like a `cd` command)
- Creates `App/Thing/SubThing/Blabla.php`
- Creates `App/Thing/SubThing/SubThing3` directory
- Creates `App/Thing/SubThing/SubThing3/Blabla.php`

Every `*.php` files has a basic content containing a namespace and a class 

`App/Thing/SubThing/SubThing3/Blabla.php`:
```php
namespace App\Thing\SubThing\SubThing3;

class Blabla {

}
```

Also, the command can detect in which namespace the command is launched in, for instance,
if you're launching `map` in `app/Http/Controllers` and creates `MyFeat/Controller.php`, `map`
will set the namespace to `App\Http\Controllers\MyFeat` !