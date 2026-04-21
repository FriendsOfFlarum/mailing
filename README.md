# Mailing by FriendsOfFlarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/fof/mailing.svg)](https://packagist.org/packages/fof/mailing) [![Total Downloads](https://img.shields.io/packagist/dt/fof/mailing.svg)](https://packagist.org/packages/fof/mailing) [![OpenCollective](https://img.shields.io/badge/opencollective-fof-blue.svg)](https://opencollective.com/fof/donate)

A [Flarum](http://flarum.org) extension. Send individual or mass mailing to Flarum users.

## Installation

```sh
composer require fof/mailing
```

## Updating

```sh
composer update fof/mailing
php flarum cache:clear
```

## Documentation

The individual user mailing button can be found in the user card dropdown menu, including the user profile page and while hovering a username in a discussion.

The mass mailing button can be found in the session dropdown menu under the link to the admin panel.

If the FriendsOfFlarum User Directory extension is enabled, the buttons for both features can be found on that page as well.

Two permissions allow you to select which groups can send individual emails and emails to all users.

**Send emails to individual users**: allows to select one or multiple Flarum users. Also allows to enter email addresses not connected to any user.

**Send emails to groups or all users**: allows to select groups, including "Members" to send to all registered users.

## Credits

This extension was originally developed by [Clark Winkelmann](https://clarkwinkelmann.com/) for a client and released as open-source. It has since been adopted and is now maintained by [FriendsOfFlarum](https://friendsofflarum.org/) for Flarum 1.x and 2.x.

**Original sponsors**: [Glowing Blue](https://glowingblue.com/), [Andrew MacLean](https://andrewdmaclean.com/).

## Links

[![OpenCollective](https://img.shields.io/badge/donate-friendsofflarum-44AEE5?style=for-the-badge&logo=open-collective)](https://opencollective.com/fof/donate)

- [Packagist](https://packagist.org/packages/fof/mailing)
- [GitHub](https://github.com/FriendsOfFlarum/mailing)
- [Discuss](https://discuss.flarum.org/d/39128)

An extension by [FriendsOfFlarum](https://github.com/FriendsOfFlarum).
