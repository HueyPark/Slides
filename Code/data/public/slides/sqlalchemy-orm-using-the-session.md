# Using the Session

The `orm.mapper()` function and `declarative` extensions are the primary configurational interface for the ORM. Once mappings are configured, the primary usage interface for `persistence operation`s is the `Session`.

## Session Basics

### What does the Session do ?
### Getting a Session
### Session Frequently Asked Questions
### Basics of Using a Session

## State Management

### Quickie Intro to Object States
### Session Attributes
### Merging
### Expunging
### Refreshing / Expiring

## Cascades

### save-update
### delete
### delete-orphan
### merge
### refresh-expire
### expunge
### Controlling Cascade on Backrefs

## Transactions and Connection Management

### Managing Transactions
### Joining a Session into an External Transaction (such as for test suites)

## Additional Persistence Techniques

### Embedding SQL Insert/Update Expressions into a Flush
### Using SQL Expressions with Sessions
### Partitioning Strategies
### Bulk Operations

## Contextual/Thread-local Sessions

### Implicit Method Access
### Thread-Local Scope
### Using Thread-Local Scope with Web Applications
### Using Custom Created Scopes
### Contextual Session API

## Tracking Object and Session Changes with Events

### Persistence Events
### Object Lifecycle Events
### Transaction Events
### Attribute Change Events

## Session API

### Session and sessionmaker()
### Session Utilites
### Attribute and State Management Utilities
