# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 0.2.7 - 2026-11-27

- Added PHP 8.4 and PHP 8.5 support: `Container::set()` and `Collection::get()` parameters are now explicitly nullable, which removes the deprecation notices
- Removed the unused `PaymentCallbackValidationResponse::$refundTimestamp` property; the refund timestamp is available from `Refund::getRefundTimestamp()`
- Added array value types to `PaymentValidationResponseNormalizer` and removed the PHPStan baseline entries they replace
- Added PHP 8.4 and PHP 8.5 to the CI test matrix and quoted the PHP versions, so the `8.0` job runs on PHP 8.0
- Raised the minimum `mockery/mockery` dev dependency to `^1.6.12`, the first release whose mocks are PHP 8.4 compatible

## 0.2.6 - 2025-07-31

- Added refund data to the payment callback response: `PaymentCallbackValidationResponse::getRefund()` returns a `Refund` with the refund amount, currency, commission amount, commission currency and timestamp

## 0.2.5 - 2025-07-25

- Upgraded `webtopay/libwebtopay` to v3.1.3
- Added the `ss3` signature (RSA with SHA-256) to `PaymentCallbackValidationRequest`

## 0.2.4 - 2025-01-21

- Changed the `webtopay/libwebtopay` constraint to `^3.1`

## 0.2.3 - 2024-11-21

- Specify file and string where ProviderException is thrown

## 0.2.2 - 2024-11-21

- Added 'KO' and 'XK' country codes for 'Kosovo'

## 0.2.1 - 2024-10-11

- Added possibility to get raw data from Payment Redirect Response

## 0.1.2 - 2024-02-05

- Added information about PHP and CMS versions to payment request.
