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