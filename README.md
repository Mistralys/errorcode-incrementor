# Error code / counter incrementor

Simple PHP script that increments a counter when accessed, and the displays the current count.

Can also be used programmatically when used as a project's composer dependency.

## Installation

- Check out in a folder of your choice in the document root of your webserver
- Rename `config-local.dist.php` to `config-local.php`
- Ensure that the `storage` folder is writable by the script
- Edit the required settings in the file, set up the counters you wish to use (see Configuration)

## Configuration

### Password

The counter is password protected to avoid someone hogging the server in a DOS attack, since it causes a write operation on the hard drive, and can thus create a heavy system load in such a context.

Even if this seems like a trivial operation, take care to choose a good password.

### The counters list

The counters array in `config-local.php` is very simple. Let's say you have two applications for which you want to maintain error code counters, called "Mistral" and "Tramontane" (Wind names). The counters list would look like this:

```php
<?php
$counters = array(
    'Mistral' => 0,
    'Tramontane' => 520 // start at #520
);
```

Note: There is no limitation on the characters used in the names - you can safely use UTF8 and special characters if you wish. 

### Update delay

See Security > Minimum update delays. 

## Quick start

To start checking counter values, point your browser to where the script is located, and use the following GET parameters:

- View a counter: `/path/?pw=****&counter=(name)`
- Increment a counter: `/path/?pw=****&counter=(name)&increment=yes`

Both will display the current value of the selected counter.

## Security

### Minimum update delays

To mitigate DOS attacks or avoid spamming the counters, an update delay is enforced between incrementing counters. By default, it is only possible to increment a counter every 10 seconds.

The absolute minimum is enforced at 1 second.

Note: The delay works separately for each counter, and is only used when incrementing.

### HTTPS recommended

Since the counter uses a password, it is a bad idea to use it over HTTP, as it is relatively easy to intercept the password in the requests. Use a webserver with an SSL certificate, and serve the script using an HTTPS connection.

## Error handling

The script will send an HTTP status code when something goes wrong, with a descriptive status text.

Possible errors:

- `401` Wrong or no password
- `400` No counter name specified
- `404` Specified counter does not exist
- `403` Minimum update delay not respected
- `500` Counter data cannot be saved/loaded

## Accessing counters programmatically

The `Counters` class can be used independently of serving the counters via HTTP requests. This means that you can simply require this package as a dependency in your project, and use the `Counters` class there.

### Getting a counter value

```php
<?php
// The list of counters
$counters = array(
    'Test' => 0
);

// Create the collection with the path to the folder
// where the files can be stored - must be writable.
$collection = new \Mistralys\Counters\Counters('./storage', $counters);

// To avoid an exception, always check first
if($collection->counterExists('Test'))
{
    $number = $collection->getByName('Test')->getNumber();
}  
```

### Incrementing a counter

```php
<?php
// The list of counters
$counters = array(
    'Test' => 0
);

// Create the collection with the path to the folder
// where the files can be stored - must be writable.
$collection = new \Mistralys\Counters\Counters('./storage', $counters);

// To avoid an exception, always check first
if($collection->counterExists('Test'))
{
    // Incrementing returns the new counter value
    $newNumber = $collection->getByName('Test')->increment();
}  
```

### Fetching available counters

```php
<?php
// The list of counters
$counters = array(
    'Test' => 0
);

// Create the collection with the path to the folder
// where the files can be stored - must be writable.
$collection = new \Mistralys\Counters\Counters('./storage', $counters);

$counters = $collection->getCounters();

// Display a list of counters and their values
foreach($counters as $counter)
{
    echo $counter->getName().' = '.$counter->getNumber().PHP_EOL;
}  
```