# Changelog

All notable changes to `livewire-page-group` will be documented in this file.

## Unreleased

- Add Laravel 13 support while retaining Laravel 10–12 constraints.
- Support Livewire `^3.8 || ^4.1` at runtime with stable component aliases across both release lines.
- Add functional coverage for provider booting, page groups, routes, middleware, rendering, discovery, and the page generator.
- Add a Laravel 10–13, Livewire 3/4, PHP 8.1–8.5, and Testbench 8–11 CI matrix.
- Fix nullable page-group lookup, safe group booting, string route middleware, page-generator paths and collisions, and missing-group handling.
