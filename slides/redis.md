# Redis

---

## Introduction

Redis is an open source, In-memory data structure store, used as database, cache and message broker. It supports data structures such as strings, hashes, lists, sets, sorted sets with range queries, bitmaps, hyperloglogs and geospatial indexes with radius queries. Redis has built-in replication, Lua scripting, LRU eviction, transactions and different levels of on-disk persistence, and provides high availability via Redis Sentinel and automatic partitioning with Redis Cluster.

---

You can run atomic operations on these types, like appending to a string; increamenting the value in a hash; pushing an element to a list; computing set intersection, union and difference; or getting the member with highest ranking in a sorted set.

---

In order to achieve its outstanding performance, Redis works with an in-memory dataset. Depending on your use case, you can persist it either by dumping th dataset to disk every once in a while, or by appending each command to a log. Persistence can be optionally disabled, if you just need a feature-rich, networked, in-memory cache.

---

Redis also supprots trivial-to-setup marster-slave asynchronous replication, with very fast non-blocking first synchronization, auto-reconnection with partial resynchronization on net split.

---

## Other features include:

* Transactions
* Pub/Sub
* Lua scripting
* Keys with a limited time-to-live
* LRU eviction of keys
* Automatic failover

---

You can use Redis from most programming languages out there.

---

Redis is written in ANSI C and works in monst POSIX systems like Linux, *BSD, OS X without external dependencies. Linux and OS X are the two operating systems where Redis is developed and more tested, and we recommend using Linux for deploying. Redis may work in Solaris-derived systems like SmartOS, but suport is best effort. There is no official support for Windows builds, but Microsoft develops and maintains a Win-64 port of Redis.

---

## Contents

* Data structure (with range queries)
* Replication
* LRU eviction
* Transactions
* On-disk persistence
* Best practice

---

## Data structure (with range queries)

* strings
* hashes
* lists
* sets
* sorted sets

---

## Replication

Redis replication is a very simple to use and configure master-slave replication that allows slave Redis servers to be exact copies of master servers. The following are very important facts about Redis replication:

* Redis uses asynchrounous replication. Starting with Redis 2.8, however, slaves will periodically acknowledge the amount of data processed from the replication stream.
* A master can have multiple slaves.
* Slaves are able to accept connections from other slaves. Aside from connecting a number of slaves to the same master, slaves can also be connected to other slaves in a graph-like structure.
* Redis replication is non-blocking on the master side. This mean that master will continue to handle queries when one or more slaves perform the initial synchronization.
* Replication is also non-blocking on the slave side. While the slave is performing the initila synchronization, it can handle queries using the old version of the dataset, assuming you  configured Redis to do so in redis.conf. Otherwise, you can configure Redis slaves to return an error to clients if the replication stream is down. However after the initial sync, the old dataset must be deleted and the new one must be loaded. The slave will block incoming connections during this bried window.
* Replication can be used both for scalability, in order to have multiple slaves for read-only queries (for example, heavy SORT operations can be offloaded to slaves), or simply for data redundancy.
* It is possible to use replication to avoid the cost of having the master write full dataset to disk: just configure your master redis.conf to avoid savinf (just comment all the "save" directives), then connect a slave configured to save from time to time. However in this setup make sure masters don't restart automatically (please read the next section for more information).

---

### Safety of replication when master has persistence turned off

In setups where Redis replication is used, it is strongly advised to have persistence turned on in the master, or when this is not possible, for example because of latency concerns, instances should be configured to avoid restarting automatically.

To better understand why masters with persistence turned off configured to auto restart are dangerous, check the following failure mode where data is wiped from the master and all its slaves:

1. We have a setup with node A acting as master, with persistence turned down, and nodes B and C replication from node A.
2. A crashes, however it has some auto-restart system, that restarts the process. However since persistence is turned off, the node restarts with an empty data set.
3. Nodes B and C will replicate from A, which is empty, so they'll effectively destroy therir copy of the data.

When Redis Sentinel is used for high availability, als turning off persistence on the master, together with auto restart of the process, is dangerous. For example the master can restart fast enough for Sentinel to don't detect a failure, so that the failure mode described above happens.

Every time data safetu is important, and replication is used with master configured without persistence, auto restart of instances should be disabled.

---

### How Redis replication works

If you set up a slave upon connection it sends a SYNC command. It doesn't matter if it's the first time it has connected or it it's a reconnection.

The master then starts background saving, ans starts to buffer all new commands received that will modify the dataset. When the background saving is complete, the master transfers the database file to the slave, which saves it on disk, and then loads it into memory. The master will then send to the slave all buffered commands. This is done as a stream of commands and is in the same format of the Redis protocol itself.

You can try it yourself via telnet. Connect to the Redis port while the server is doing some work and issue the SYNC command. You'll see a bulk transfer and then every command recerived by the master will be re-issued int the telnet session.

Slaves are able to automatically reconnect when the master <-> slave link goes down for some reason. If the master receives multiple concurrent slave synchronization requests, if performs a single background save in order to serve all of them.

When a master and a slave reconnects after the link went down, a full resync is always performed. However, starting with Redis 2.8, a partial resynchronization is also possible.

---

### Paritial resynchronization

Starting with Redis 2.8, master and slave are usually able to continue the replication process without requiring a full resynchronizatopm after replication link went down.

This works by creatinf an in-memory backlog of the replication stream on the master side. Ther master and all the slaves agree on a replication offset and a master run id, so wher the link goies down, the slave will reconnect and ask the master to continer the replication. Assimong the master run id is still the same, and that the offset specified is available in the replication backlog, replication will resume from the point where it left off. If either of these conditions are unmet, a full resynchronization is performed (which is the normal pre-2.8 behavior). As the run id of the connected master is not persisted to disk, a full resynchronization is needed when the slave restarts.

The new partial resynchronization feature uses the PSYNC command internally, while the old implementaion uses the SYNC command. Note that a Redis 2.8 slave is able to detect if the server it it talking with does not support PSYNC, and will use SYNC instead.

---

### Diskless replication

Normally a fulll resynchronization requires to create an RDB file on disk, then reload same RDB from disk in order to feed slaves with the data.

With slow disks this can be a very stressing operation for the master. Redis version 2.8.18 will be the firest version to have experimental supprot for diskless replication. In this setup the child process directly sends the RDB over the wire to slaves, without using the disk as intermediate storage.

The feature is currently considered experimental.

---

### Read-only slave

Since Redis 2.6, slaves supprot a read-only mode that is enabled by default. This behavior is controlled by 

---

## LRU eviction

---

## Transactions

---

## On-disk persistence

---

## Best practice