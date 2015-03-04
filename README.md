
# De.SWebhosting.DatabaseLog

This is a [TYPO3 Flow](http://flow.typo3.org) package that brings you some logging features:
  
1. You can store log messages in the database.
2. You can search for log entries using a repository.
3. You can store account / user information in the log entries and filter for them.


## Install

If you want to use this package, you simply need to add a require statement to your `composer.json` file:

```json
{
    "require": {
        "de-swebhosting-flow-package/databaselog": "dev-master"
    }
}
```

## How to use it

### Database backend

You can configure any log to use the `\De\SWebhosting\DatabaseLog\Log\DatabaseBackend` class.

**An example:** if you want Flow to write its security logs to the database, you can put this in your `Settings.yaml` file:

```yaml
TYPO3:
  Flow:
    log:
      securityLogger:
        backend: De\SWebhosting\DatabaseLog\Log\DatabaseBackend
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
