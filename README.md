# WP Admin Notices

[![Latest Stable Version](https://poser.pugx.org/typisttech/wp-admin-notices/v/stable)](https://packagist.org/packages/typisttech/wp-admin-notices)
[![Total Downloads](https://poser.pugx.org/typisttech/wp-admin-notices/downloads)](https://packagist.org/packages/typisttech/wp-admin-notices)
[![Build Status](https://travis-ci.org/TypistTech/wp-admin-notices.svg?branch=master)](https://travis-ci.org/TypistTech/wp-admin-notices)
[![codecov](https://codecov.io/gh/TypistTech/wp-admin-notices/branch/master/graph/badge.svg)](https://codecov.io/gh/TypistTech/wp-admin-notices)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TypistTech/wp-admin-notices/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TypistTech/wp-admin-notices/?branch=master)
[![PHP Versions Tested](http://php-eye.com/badge/typisttech/wp-admin-notices/tested.svg)](https://travis-ci.org/TypistTech/wp-admin-notices)
[![StyleCI](https://styleci.io/repos/105561450/shield?branch=master)](https://styleci.io/repos/105561450)
[![Dependency Status](https://gemnasium.com/badges/github.com/TypistTech/wp-admin-notices.svg)](https://gemnasium.com/github.com/TypistTech/wp-admin-notices)
[![License](https://poser.pugx.org/typisttech/wp-admin-notices/license)](https://packagist.org/packages/typisttech/wp-admin-notices)
[![Donate via PayPal](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.typist.tech/donate/wp-admin-notices/)
[![Hire Typist Tech](https://img.shields.io/badge/Hire-Typist%20Tech-ff69b4.svg)](https://www.typist.tech/contact/)

A simplified OOP implementation of the WordPress admin notices.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [The Goals, or What This Package Does?](#the-goals-or-what-this-package-does)
- [Install](#install)
- [Usage](#usage)
  - [Example](#example)
  - [Notice](#notice)
    - [`__construct(string $handle, string $content, string $type = null)`](#__constructstring-handle-string-content-string-type--null)
  - [StickyNotice](#stickynotice)
    - [`__construct(string $handle, string $content, string $type = null)`](#__constructstring-handle-string-content-string-type--null-1)
  - [Store](#store)
    - [`__construct(string $optionKey)`](#__constructstring-optionkey)
    - [`add(NoticeInterface ...$notices)`](#addnoticeinterface-notices)
    - [`delete(string $handle)`](#deletestring-handle)
  - [Notifier](#notifier)
    - [`__construct(string $action, StoreInterface $store)`](#__constructstring-action-storeinterface-store)
  - [Factory](#factory)
    - [`build(string $optionKey, string $action): Store`](#buildstring-optionkey-string-action-store)
- [Frequently Asked Questions](#frequently-asked-questions)
  - [Can I implement my own notice classes?](#can-i-implement-my-own-notice-classes)
  - [Can I use a different storage scheme other than `wp_option` table?](#can-i-use-a-different-storage-scheme-other-than-wp_option-table)
  - [Can two different plugins use this package at the same time?](#can-two-different-plugins-use-this-package-at-the-same-time)
  - [Do you have a demo plugin that use this package?](#do-you-have-a-demo-plugin-that-use-this-package)
  - [Do you have real life examples that use this package?](#do-you-have-real-life-examples-that-use-this-package)
- [Support!](#support)
  - [Donate via PayPal *](#donate-via-paypal-)
  - [Why don't you hire me?](#why-dont-you-hire-me)
  - [Want to help in other way? Want to be a sponsor?](#want-to-help-in-other-way-want-to-be-a-sponsor)
- [Developing](#developing)
- [Running the Tests](#running-the-tests)
- [Feedback](#feedback)
- [Change log](#change-log)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## The Goals, or What This Package Does?

## Install

Installation should be done via composer, details of how to install composer can be found at [https://getcomposer.org/](https://getcomposer.org/).

``` bash
$ composer require typisttech/wp-admin-notices
```

You should put all `WP Admin Notices` classes under your own namespace to avoid class name conflicts.

- [imposter-plugin](https://github.com/Typisttech/imposter-plugin)
- [mozart](https://github.com/coenjacobs/mozart)

## Usage

### Example

```php
use TypistTech\WPAdminNotices\Factory;
use TypistTech\WPAdminNotices\Notice;
use TypistTech\WPAdminNotices\StickyNotice;

$store = Factory::build('my_unique_demo_option', 'my_unique_demo_action');

add_action('admin_init', function () use ($store) {
        $notice = new Notice('example-notice-1', 'my notice message');
        $store->add($notice);
    }
);

add_action('post_updated', function ($post_id) use ($store) {
        $notices[] = new Notice(
            'example-notice-2',
            "<p><strong>WPAdminNotices</strong>: Post ID: $post_id has been updated.</p>",
            Notice::SUCCESS
        );
        $notices[] = new StickyNotice(
            'example-notice-3',
            '<p><strong>WPAdminNotices</strong>: StickyNotice persists in database until user clicks to dismiss it.</p>'
        );
        $store->add(...$notices);
    }
);
```

### Notice

One-off notice that guaranteed to be shown once and once only.

#### `__construct(string $handle, string $content, string $type = null)`

`Notice` constructor.

 * @param string      $handle  The notice's unique identifier.
 * @param string      $content The HTML content of the notice.
 * @param string|null $type    The notice's type. Expecting one of `Notice::UPDATE_NAG`, `Notice::ERROR`, `Notice::WARNING`, `Notice::INFO`, `Notice::SUCCESS`. Default is `Notice::INFO`.

`Notice::UPDATE_NAG` is not suitable for regular admin notices. See [WordPress codex](https://cnhv.co/5497).

```php
$notice = new Notice('example-notice', '<strong>Hello</strong> World!', Notice::SUCCESS);
```

### StickyNotice

`StickyNotice` persists in database until user clicks to dismiss it.

#### `__construct(string $handle, string $content, string $type = null)`

`StickyNotice` constructor.

 * @param string      $handle  The notice's unique identifier. Also used to permanently dismiss a sticky notice.
 * @param string      $content The HTML content of the notice.
 * @param string|null $type    The notice's type. Expecting one of `StickyNotice::ERROR`, `StickyNotice::WARNING`, `StickyNotice::INFO`, `StickyNotice::SUCCESS`. Default is `StickyNotice::INFO`.

`UPDATE_NAG` is not available for `StickyNotice`.

```php
$stickyNotice = new StickyNotice('example-sticky-notice', 'I wont go away until users click on me.', StickyNotice::WARNING);
```

### Store

By default, `WP Admin Notices` stores notices in [WordPress' `wp_option`](https://cnhv.co/565f) table via `Store`.
If you want to use an alternative store, see [FAQ](#can-i-use-a-different-storage-scheme-other-than-wp_option-table).

#### `__construct(string $optionKey)`

`Store` constructor.

* @param string $optionKey Key in options table that holds all enqueued notices.

```php
$store = new Store('my_unique_option_key');
```

#### `add(NoticeInterface ...$notices)`

Enqueue admin notices to database.

Not limited to `Notice` and `StickyNotice` only, any instance of `NoticeInterface` is accepted. See [FAQ](#can-i-implement-my-own-notice-classes).

 * @param NoticeInterface[] ...$notices Notices to be enqueued.

```php
$store->add($notice1, $notice2);

// To update a notice, re-add with the same handle.
$oldNotice = new Notice('i-am-unique', "Chaos isn't a pit.");
$store->add($oldNotice);

$newNotice = new Notice('i-am-unique', 'Chaos is a ladder.');
$store->add($newNotice);
```

#### `delete(string $handle)`

Delete an enqueued notice.

 * @param string $handle Handle of the notice to be deleted.

```php
$store->delete('i-am-unique');
```

### Notifier

`Notifier` handles all interactions between WordPress and this package via action hooks. You have to hook it into WordPress via `add_action` unless you use [`Factory`](#factory).

#### `__construct(string $action, StoreInterface $store)`

Notifier constructor.

 * @param string         $action AJAX request's 'action' property for sticky notices.
 * @param StoreInterface $store  Connector to notice storage.

```php
$store = new Store('my_unique_option_key');
$notifier = new Notifier('my_unique_action', $store);

add_action('admin_notices', [$notifier, 'renderNotices']);
add_action("wp_ajax_my_unique_action", [$notifier, 'dismissNotice']);
add_action('admin_footer', [$notifier, 'renderScript']);
```

### Factory

`Factory` is a helper class to reduce boilerplate code for those who use default `Store` class. If you use a [custom store](#can-i-use-a-different-storage-scheme-other-than-wp_option-table), don't use this class.

#### `build(string $optionKey, string $action): Store`

 * @param string $optionKey Key in options table that holds all enqueued notices.
 * @param string $action    AJAX request's 'action' property for sticky notices.
 * @return Store

```php
$store = Factory::build('my_unique_option_key', 'my_unique_action');
```

## Frequently Asked Questions

### Can I implement my own notice classes?

Of course! Just implements the `NoticeInterface`.

Take a look at classes `Notice` and `StickyNotice` as well as their tests for example implementations of `StoreInterface`.

If you'd like to create a open-source package to do this to help others, [open a new issue](https://github.com/TypistTech/wp-contained-hook/issues/new) to let us know, we'd love to help you with it.

### Can I use a different storage scheme other than `wp_option` table?

Of course! `WP Admin Notices` data store is completely swappable, and always has been.

To implement a custom store:
 1. Implement `StoreInterface`
 2. Pass you custom store to `Notifier`

```php
class MyCustomStore implements StoreInterface
{
  // Implements all the required methods.
}

$store = new MyCustomStore;
$action = 'my_unique_action';
$notifier = new Notifier($action, $store);

add_action('admin_notices', [$notifier, 'renderNotices']);
add_action("wp_ajax_$action", [$notifier, 'dismissNotice']);
add_action('admin_footer', [$notifier, 'renderScript']);
```

Take a look at the `Store` class and `StoreTest` for an example implementation of `StoreInterface`.

If you'd like to create a open-source package to do this to help others, [open a new issue](https://github.com/TypistTech/wp-contained-hook/issues/new) to let us know, we'd love to help you with it.

### Can two different plugins use this package at the same time?

Yes, if put all `WP Admin Notices` classes under your own namespace to avoid class name conflicts.

- [imposter-plugin](https://github.com/Typisttech/imposter-plugin)
- [mozart](https://github.com/coenjacobs/mozart)

### Do you have a demo plugin that use this package?

You can install this demo plugin by
```bash
$ wp plugin install https://github.com/TypistTech/wp-admin-notices/archive/nightly.zip --activate
```

Check out [`wp-admin-notices.php`](./wp-admin-notices.php). We use it for acceptance tests.

### Do you have real life examples that use this package?

Here you go:

 * [Sunny](https://github.com/Typisttech/sunny)
 * [WP Cloudflare Guard](https://github.com/TypistTech/wp-cloudflare-guard)

*Add your own plugin [here](https://github.com/TypistTech/wp-admin-notices/edit/master/README.md)*

## Support!

### Donate via PayPal [![Donate via PayPal](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.typist.tech/donate/wp-admin-notices/)

Love WP Admin Notices? Help me maintain WP Admin Notices, a [donation here](https://www.typist.tech/donate/wp-admin-notices/) can help with it.

### Why don't you hire me?
Ready to take freelance WordPress jobs. Contact me via the contact form [here](https://www.typist.tech/contact/) or, via email info@typist.tech

### Want to help in other way? Want to be a sponsor?
Contact: [Tang Rufus](mailto:tangrufus@gmail.com)

## Developing

To setup a developer workable version you should run these commands:

```bash
$ composer create-project --keep-vcs --no-install typisttech/wp-admin-notices:dev-master
$ cd wp-admin-notices
$ composer install
```

## Running the Tests

[WP Admin Notices](https://github.com/TypistTech/wp-admin-notices) run tests on [Codeception](http://codeception.com/) and relies [wp-browser](https://github.com/lucatume/wp-browser) to provide WordPress integration.
Before testing, you have to install WordPress locally and add a [codeception.yml](http://codeception.com/docs/reference/Configuration) file.
See [*.suite.example.yml](./tests/) for [Local by Flywheel](https://share.getf.ly/v20q1y) configuration examples.

Actually run the tests:

``` bash
$ composer test
```

We also test all PHP files against [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/) and part of the [WordPress coding standard](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).

Check the code style with ``$ composer check-style`` and fix it with ``$ composer fix-style``.

## Feedback

**Please provide feedback!** We want to make this package useful in as many projects as possible.
Please submit an [issue](https://github.com/TypistTech/wp-admin-notices/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email wp-admin-notices@typist.tech instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](./CODE_OF_CONDUCT.md) for details.

## Credits

[WP Admin Notices](https://github.com/TypistTech/wp-admin-notices) is a [Typist Tech](https://www.typist.tech) project and maintained by [Tang Rufus](https://twitter.com/Tangrufus), freelance developer for [hire](https://www.typist.tech/contact/).

Full list of contributors can be found [here](https://github.com/TypistTech/wp-admin-notices/graphs/contributors).

## License

[WP Admin Notices](https://github.com/TypistTech/wp-admin-notices) is licensed under the GPLv2 (or later) from the [Free Software Foundation](http://www.fsf.org/).
Please see [License File](LICENSE) for more information.
