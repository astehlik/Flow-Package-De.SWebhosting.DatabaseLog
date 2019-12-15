[![Build Status](https://travis-ci.com/astehlik/Flow-Package-De.SWebhosting.DatabaseLog.svg?branch=develop)](https://travis-ci.com/astehlik/Flow-Package-De.SWebhosting.DatabaseLog)

# De.SWebhosting.DatabaseLog

This is a [Flow framework](https://flow.neos.io/) package that brings you some logging features:

1. You can store log messages in the database.
2. You can search for log entries using a repository.
3. You can store account / user information in the log entries and filter for them.


## Install

If you want to use this package, you simply need to add a require statement to your `composer.json` file:

```json
{
    "require": {
        "de-swebhosting-flow-package/databaselog": "~6.1"
    }
}
```

**Hint!** This package was tested with TYPO3 Flow Version 6.1 only. If you are having trouble with older versions
please open an issue.

## How to use it

### Database backend

There are two possibilities to use the database backend for logging.

#### Inject `databaseLogger` in your objects

This package comes with a preconfigured PSR log identifier called `databaseLogger`. This logger **only** logs to
the database.

You can inject it by adding the related config in your `Objects.yaml`:

```yaml
My\Vendor\My\Class:
  properties:
    logger:
      object:
        factoryObjectName: Neos\Flow\Log\PsrLoggerFactoryInterface
        factoryMethodName: get
        arguments:
          1:
            value: databaseLogger
```

With this configuration the `$logger` poperty in your class will use the `databseLogger` log.

#### Configure backend in existing loggers

You can also add the database logger as an additional backend to existing logs or replace the default
backend by ajusting the configuration in `Settings.yaml`:

This overwrites the i18nLogger backend with the database backend.

```yaml
Neos:
  Flow:
    log:
      psr3:
        'Neos\Flow\Log\PsrLoggerFactory':
          i18nLogger:
            default:
              class: De\SWebhosting\DatabaseLog\Log\DatabaseBackend
              options:
                severityThreshold: '%LOG_INFO%'
                logIpAddress: true
```

To add it as additional backend, simply use another key than `default`:

```yaml
Neos:
  Flow:
    log:
      psr3:
        'Neos\Flow\Log\PsrLoggerFactory':
          i18nLogger:
            database:
              class: De\SWebhosting\DatabaseLog\Log\DatabaseBackend
              options:
                severityThreshold: '%LOG_INFO%'
                logIpAddress: true
```

### Log repository

You can use the `\De\SWebhosting\DatabaseLog\Domain\Repository\LogEntryRepository` like any other Flow repository
to search for log entries. Inject it in your class and start querying.

It currently comes with a minimal selection of query methods. Please let me know if you need more.

### Account action logger

A special feature of this package is the account action logger. It allows you to store log entries that are
connected to an account or to a party (if the party framework is installed).

The account is passed to the logging backend in the additional data array in a parameter called
`De.SWebhosting.DatabaseLog.Account`. If a normal backend is used, this parameter will simply be stored as a
readable var dump.

If the DatabaseBackend of this package is used, the parameter will be interpreted and the account identifer and the
authentication provider name will be stored in properties of the log entry model. This allows you to filter for log
messages of a dedicated account.

If the party framework is installed and a party is connected to the provided account, the object identifier and the
full name of the user will also be stored in the database.
