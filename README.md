# kv-php

PHP module for abstracting key-value stores.

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/kv
```

Or download the source from GitHub: https://github.com/jdwx/kv-php.git

## Requirements

This module requires PHP 8.3 or later.

There are some optional dependencies. The PdoKV class requires the PDO extension. The SqliteKV class also requires pdo_sqlite. The JsonWrapper class requires jdwx/json and the json extension. The CacheInterfaceKV class requires the CacheInterface PSR.

These are not listed as dependencies in composer.json because there are very few cases where you need them all at once. If you need them, you can require them directly.

## Usage

This module is designed to provide a simple interface for key-value stores, both persistent and in-memory. For example, it provides the solution to "I really need a simple persistent key-value store here, but I don't want to mess with the SQLite API."

The key-value interface is provided (almost) entirely through the ArrayAccess interface. I.e., you can just treat your key-value store like an array. And you're done.

The basic classes (PdoKV, SqliteKV, ArrayKV) provide a key-value store for strings only. The JsonWrapper and SerializeWrapper classes are provided to support more complex datatypes. The TTLWrapper class provides a key-value store with time-to-live support.

The CacheInterfaceKV class is provided to allow you to use any PSR-16 cache as a key-value store. It's useful if you've already got one. ("I told them we've already got one!")

## Stability

This module is relatively new and has not been extensively deployed in production yet. That said, its reliance on the ArrayAccess interface should make it incredibly stable, at least for all but the most exotic functionality.

## History

This module was created in early 2025 to replace several ad-hoc implementations. (Now there are 15 competing standards...)
