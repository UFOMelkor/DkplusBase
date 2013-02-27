# DkplusBase [![Build Status](https://secure.travis-ci.org/UFOMelkor/DkplusBase.png?branch=master)](http://travis-ci.org/UFOMelkor/DkplusBase)

Utility for other zf2 modules

## ControllerPlugins

### NotFoundForward

When a user looks for an item that is not in your database you might want to show him not the default 404 not found page but a specific page.
The NotFoundForward-Plugin might help you in this case because it extends the original Forward-Plugin by 2 points.
First it sets a 404 status code, so you don't need to bother about this, since if a 404 status code appears the RouteNotFoundStrategy it will be triggered, the plugin also sets the template of the RouteNotFoundStrategy to the one of your viewModel so your template will be rendered anyway.
Furthermore it can help you when need a MatchedRouteName inside your view, e.g. when you use a paginator. You can give the MatchedRouteName as third parameter to the plugin and it will put it into the RouteMatch.

One example

```php
class MyController
{
    // […]

    public function editAction()
    {
        $entity = $this->service->find($this->params()->fromRoute('id'));
        if (!$entity) {
            return $this->notFoundForward('MyController', array('action' => 'index'), 'my-route');
        }

        // […]

    }
}
```

## ViewHelpers

### FlashMessenger

The FlashMessenger-ViewHelper provides reading-access to the FlashMessenger-Controller-Plugin.

Some examples:

Displaying every messages that has been set within the current request and belongs to the `404-not-found` namespace:
```php
foreach ($this->flashMessenger('404-not-found')->getCurrentMessages() as $message) {
    print $this->escapeHtml($message) . '<br>';
}
```

Displaying every messages that belongs to the `error` namespace:
```php
foreach ($this->flashMessenger('error')->getMessages() as $message) {
    print $this->escapeHtml($message) . '<br>';
}
```