# DkplusBase

Utility for other zf2 modules

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