## Pug Renderer

This is a (heavily based on the [PhpRenderer](https://github.com/slimphp/PHP-View/blob/master/src/PhpRenderer.php)) renderer for rendering Pug view scripts into a PSR-7 Response object. It works well with Slim Framework 3.

### Security risks

Pug Renderer uses [Pug.php](https://github.com/pug-php/pug) under the hood. Pug Renderer is merely a thin layer to Pug.php work with Slim 3. We will not keep track of vulnerabilities. Please refer to the original project to find out more how to prevent issues and to report vulnerabilities.

## Installation

Install with [Composer](http://getcomposer.org):

```bash
$ composer require md/pug-slim
```

## Usage with Slim 3

```php
use Slim\Views\PugRenderer;

include "vendor/autoload.php";

$app = new Slim\App();
$container = $app->getContainer();
$container['renderer'] = new PugRenderer("./templates");

$app->get('/hello/{name}', function ($request, $response, $args) {
    return $this->renderer->render($response, "/hello.pug", $args);
});

$app->run();
```

## Usage with any PSR-7 Project
```php
//Construct the View
$pugView = new PugRenderer("./path/to/templates");

//Render a Template
$response = $pugView->render(new Response(), "/path/to/template.pug", $yourData);
```

## Template Variables

You can add variables to your renderer that will be available to all templates you render.

```php
// via the constructor
$templateVariables = [
    "title" => "Title"
];
$pugView = new PugRenderer("./path/to/templates", $templateVariables);

// or setter
$pugView->setAttributes($templateVariables);

// or individually
$pugView->addAttribute($key, $value);
```

Data passed in via `->render()` takes precedence over attributes.
```php
$templateVariables = [
    "title" => "Title"
];
$pugView = new PhpRenderer("./path/to/templates", $templateVariables);

//...

$pugView->render($response, $template, [
    "title" => "My Title"
]);
// In the view above, the $title will be "My Title" and not "Title"
```

## Exceptions
`\RuntimeException` - if template does not exist

`\InvalidArgumentException` - if $data contains 'template'

## References

 * To learn more about Pug go to [https://pugjs.org](https://pugjs.org)
 * Here is an online HTML to Pug converter [https://html2pug.herokuapp.com/](https://html2pug.herokuapp.com/)