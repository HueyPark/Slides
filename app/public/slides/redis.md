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

* Data structure
* Replication
* LRU eviction
* Transactions
* On-disk persistence
* Best practice

---

## Data structure

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

Since Redis 2.6, slaves supprot a read-only mode that is enabled by default. This behavior is controlled by the slave-read-only option in the redis.conf file, and can be enabled and disabled at runtime using CONFIG SET.

Read-only slaves will reject all write commands, so that it is not possible to write to a slave because of a mistake. This does not mean that the feature is intended to expose a slave instance to the internet or more generally to a network where untrusted clients exist, because administrative commands like DEBUG or CONFIG are still enabled. However, security of read-only instances can be improved by disabling commands in redis.conf using the rename-command directive.

You may wonder why it is possible to revert the read-only setting and have slave instances that can be target of write operations. While those writes will be discarded if the slave and the master resynchronize or if the slave is restarted, there are a few legitimate use case for sorting ephemeral data in writable slaves. However in the future it is possible that this feature will be dropped.

---

## LRU eviction

When Redis is used as a cache, sometimes it is handy to let it automatically evict old data as you add new one. This behavior is very well known in the community of developers, since it is default behavior of the popular memcached system.

LRU is actually only one of the supported eviction methods. This page covers the more gemeral topic of the Redis maxmemory directive that is used in order to limit the memory usage to a fixed amount, and it also covers in depth the LRU algorithm used by Redis, that is actually an approximationm of exact LRU.

---

### Maxmemory configuration directive

The maxmemory configuration directive is used in order to configure Redis to use a specified amount of memory for the data set. It is possible to set the configuration directive using the redis.conf file, or later using the CONFIG SET command at runtime.

Setting maxmemory to zero results into no memory limits. This is default behavior for 64 bit systems, while 32 bit systems use an implicit memory limit of 3GB.

When the specified amount of memory reached, it is posiible to select among different behaviors, called policies. Redis can just return errors for commands that could result in more memory being used, or it can evict some old data in order to return back to the specified limit every time new data is added.

---

### Eviction policies

The exact behavior Redis follows when the maxmemory limit is reached is configured using the maxmeory-policy configuration directive.

The following policies are available:

* noeviction: return errors when the memory limit was reached and the client is trying to execute commands that could result in more memory to used (most write commands, but DEL and a few more exceptions).
* allkeys-lru: evict keys trying to remove the less recently used (LRU) keys first, in order to make space for the new data added.
* volatile-lru: evict keys trying to remove the less recently used (LRU) keys first, but only among keys that have an expire set, in order to make space for the new data added.
* allkeys-random: evict random keys in order to make space for the new data added.
* volatile-random: evict random keys in order to make space for the new data added, but only evict keys with an expire set.
* volatile-ttl: In order to make space for the new data, evict only keys with an expire set, and try to evict keys with a shorter time to live (TTL) first.

The policies volitale-lru, volatile-random and volatile-ttl behave like noeviction if there are no keys to evict matching the prerequisites.

To pick the right eviction policy is important depending on the access pattern of your application, however you can reconfigure the policy at runtime while the application is running, and monitor the number of cache misses and hits using the Redis INFO output in order to tune your setup.

In general as a rule of thumb:

* Use allkleys-lru policy when you expect a power-law distribution in the popularity of your requests, that is, you expect that a subset of elements will be accessed far more often tha the rest. This is a good pick if you are unsure.
* Use the allkeys-random if you have a cyclic access where all the keys are scanned continuously, or when you expect the distribution to be uniform (all elements likely accessed with the same probability).
* Use volatile-ttl if you want to be able to provide hints to Redis about what are good candidate for expiration by using different TTL values when you create your cache objects.

The allkeys-lru and volatile-random policies are mainly useful when you want to use a single instance for both caching and to have a set of persistent keys. However it is usually a better idea to run two Redis instances to solve such a problem.

It is also worth to note that setting an expire to a key costs memory, so using a policy allkeys-lru is more memory efficient since there is no need to set an expire for the key to be evicted under memory pressure.

---

### How the eviction process works

It is important to understand that the eviction process works like this:

* A client runs a new command resulting in more data added.
* Redis checks the memory usage, and if it is greater than the maxmemory limit, it evicts keys according the policy.
* A new command is executed, and so forth.

So we continuously cross the boundaries of the memoey limit, by going over it, and then by evicting keys to return back under the limit.

If a command results in a lot of memory being used (like a big set intersection stored into a new key) for some time the memory limit can be surpassed by a noticeable amount.

---

### Approximated LRU algorithm

Redis LRU algorithm is not an exact implementation. This means that Redis is not able to pick the best candidate for eviction, that is, the access that was accessed the most in the past. instead it will try to run an approximation of the LRU algolithm, by sampling a small number of keys, and evicting the one that is the best (with the oldest access time) among the sampled keys.

However since Redis 3.0 the algorithm was improved to also take a pool of good candidates for eviction. This improved the performance, making it able to approximate more closely the behavior of a real LRU algorithm.

What is important about the Redis LRU algorithm is that you are able to tune the precision of the algorithm by changing the number of samples to check for every eviction. This parameter is controlled by the following configuration directive:

The reason why Redis does not use a true LRU implementation is because it costs more memory. However the approximation is virtually equivalent for the application using Redis. The following is a graphical comparison of how the LRU approximation used by Redis compares with true LRU.

---

## Transactions

---

## On-disk persistence

---

## Redis Cluster

This document is a gentle introduction to Redis Cluster, that does not use complext to understand distributed systems concepts. It provides instructions about how to setup a cluster, test and operate it, without going into the details that are covered in the Redis Cluster specification but just describing how the system behaves from the point of viwe of the user.

However this tutorial tries to provide information about availability and consistency characteristics of Redis Cluster from the point of view of the final user, stated in a simple to understand way.

Note this tutorial requires Redis version 3.0 or higher.

If you plan to run a serious Redis Cluster deployment, the more formal specification is a suggested reading, even if not strictly required. However it is a good idea to start from this document, play with Redis Cluster some time, and only later read the specification.

### Redis Cluster 101

Redis Cluster provides a way to run Redis installation where data is automatically sharded across multiple Redis nodes.

Redis Cluster also provides some degree of availability during partitions, that is in practical terms the ability to continue the operations when some nodes fail or are not able to communicate. However the cluster stops to operate in the event of larger failures (for example when the majority of masters are unavailable).

So in practical terms, what you get with Redis Cluster?

* The ability to automatically split your dataset among multiple nodes.
* The ability to continue operations when a subset of the nodes are experiencing failures or are unable to communicate with the rest of the cluster.

### Redis Cluster TCP ports

Every Redis Cluster node requires two TCP connections open. The normal Redis TCP port uset to serve clients, for example 6379, plus the port obtained by adding 10000 to the data port, so 16379 in the example.

This second high port is used for the Cluster bus, that is a node-to-node communication channel using a binary protocol. The Cluster bus is used by nodes for failure detection, configuration update, failover authorization and so forth. Clients should never try to communicate with the cluster bus port, but always with the normal Redis command port, however make sure you open both ports in your firewall, otherwise Redis cluster nodes will be not able to communicate.

The command port and cluster bus port offset is fixed and is always 10000.

Note that for a Redis Cluster to work properly you need, for each node:

1. The noraml client communication port (usually 6379) used to communicate with clients to be open to all the clients that need to reach the cluster, plus all the othe cluster nodes (that use the client port for keys migrations).

2. The cluster bus port (the client prot + 10000) must be reachable from all the other cluster nodes.

If you don't open both TCP ports, your cluster will not work as expected.

The cluster bus uses a different, binary protocol, for node to node data exchange, which is more suited to exchange information between nodes using little bandwidth and processing time.

### Redis Cluster data sharding

Redis Cluster does not use consistent hashing, but a different form of sharding where every key is conceptually part of what we call an hash slot.

There are 16384 hash slots in Redis Cluster, and to compute what is the hash slot of a given key, we simply take the CRC16 of the key modulo 16384.

Every node in a Redis Cluster is reponsible for a subset of the hash slots, so for example you may have a cluster with 3 nodes, where:

* Node A contains hash slots from 0 to 5500.
* Node B contains hash slots from 5501 to 11000.
* Node C conatins hash slots from 11001 to 16384.

This allows to add and remove nodes in the cluster easily. For example if I want to add a new node D, I need to move some hash slot from nodes A, B, C to D. Similarly if I want to remove node A from the cluster I can just move the hash slots served by A to B and C. When the node A will be empty I can remove it from the cluster completely.

Because moving hash slots from a node to another does not require to stop operations, adding and removing nodes, or changin the percentage of hash slots hold my nodes, does not require any downtime.

Redis Cluster supports multiple key operations as long as all the keys invilved into a single command execution (or whole transaction, or Lua script execution) all belong to the same hash slot. The user can force multiple keys to be part of the same hash slot by using a concept called hash tags.

Hash tags are documented in the Redis Cluster specification, but the gist is that if there is a substring between {} brackets in a key, only what is inside the string is hashed, so for example this {foo} key and another guranteed to be in the same hash slot, and can be used together in a command with multiple keys as arguments.

### Redis Cluster master-slave model

In order to remain available when a subset of master nodes are failing or are not able to communicate with the majority of nodes, Redis Cluster uses a master-slave model where every hash slot has from 1 (the master itself) to N replicas (N-1 additional slaves nodes).

In our example cluster with nodes A, B, C if node B fails the cluster is not able to continue, since we no longer have a way to serve hash slots in the range 5501-11000.

However when the cluster is created (or at a latter time) we add a slave node to every master, so that the final cluster is composed of A, B, C that are masters nodes, and A1, B1, C1 that are slaves nodes, the system is able to continue if node B fails.

Node B1 replicates B, and B fails, the cluster will promote node B1 as the new master and will continue to operate correctly.

However note that if nodes B and B1 fail at the same time Redis Cluster is not able to continue to operate.

### Redis Cluster consistency guarantees

Redis CLuster is not able to guarantee strong consistency. In practical terms this means that under certain conditions it is possible that Redis Cluster will lose writes that were acknowledged by the system to the client.

The first reason why Redis Cluster can lose writes is because it uses asynchronous replication. This means that during writes the following happens:

* Your client writes to the master B
* The master B replies OK to your client.
* The master B propagates the write to its slaves B1, B2 and B3.

As you can see B does not wait for an acknowledge from B1, B2, B3 before replying to the client, since this would be a prohibitive latency penalty for Redis, so if your client writes something, B acknowledges the write, but crashes before being able to send the write to its slaves, one of the slaves (that did not received the write) can be promoted to master, losing the write forever.

This is very similar to what happens woth most databases that are configured to flush data to disk every second, so it is a scenario you are already able to reason about because of past experiences with traditional database systems not involving distributed systems. Similarly you can improve consistency by forcing the database to flush data on disk before replying to the client, but thos usually results into prohibitively low performance. That would be the equivalent of synchronous replication in the case of Redis Cluster.

Basically there is a trade-off to take between performance and consistency.

Redis Cluster has support for synchronous writes when absolutely needed, implemented via the WAIT command, this makes losing writes a lot less likely, however note that Redis Cluster does not implement strong consistency even when synchronous replication is used: it is always possible under more complex failure scenarios that a slave that was not able to receive the write is elected as master.

There is another notable scenario where Redis Cluster will lose writes, that happens during a network partition where a client is isolated with a minority of instances including at least a master.

Take as an example our 6 nodes cluster composed of A, B, C, A1, B1, C1, with 3 masters and 3 slaves. There is also a client, that we will call Z1.

After a partition occurs, it is possible that in one side of the partition we have A, C, A1, B1, C1 and in the other side we have B and Z1.

Z1 is still able to write to B, that will accept its writes. If the partition heals in a very short time, the cluster will continue normally. However if the partition lasts enough time for B1 to be promoted to master in the majority side of the partition, the writes that Z1 is sending to B will be lost.

Note that there is a maximum window to the amount of writes Z1 will be able to send to B: if enough time has elapsed for the majority side of the partition to elect a slave as master, every master node in the minority side stops accepting writes.

This amount of time is a very important configuration directive of Redis Cluster, and is called the node timeout.

After node timeout has elapsed, a master node is considered to be failing, and can be replaced by one of its replicas. Similarly after node timeout has elapsed without a master node to be able to sense the majority of the other master nodes, it enters an error state and stops accepting writes.

### Redis Cluster configuration parameters

We are about to create an example cluster deployment. Before to continue let's introduce the configuration parameters that Redis Cluster introduces in the redis.conf file. Some will be obvious, others will be more clear as you continue reading.

* cluster-enabled <yes/no/>: If yes enables Redis Cluster support in a specific Redis instance. Otherwise the instance starts as a stand alone instance as usually.
* cluster-config-file <filename>: Note that despite the name of this option, this is not an user editable configuration file, but the file where a Redis Cluster node automatically persists the cluster configuration (the state, basically) every time there is a change, in order to be able to re-read it at statup. The file lists things like the ohter nodes in the cluster, their state, persistent variables, and so forth. Ofren this file is rewritten and flushed on disk as a result of some message reception.
* cluster-node-timeout <milliseconds>: The maximum amount of time a Redis Cluster node can be unavailable, without it being considered as failing. if a master node is not reachable for more than the specified amount of time, it will be failed over by its slaves. This parameter controls other important things in Redis Cluster. Notably, every node that can't reach the majority of master nodes for the specified amount of time, will stop accepting queries.
* cluster-salve-validity-factor <factor>: If set to zero, a slave will always try to failover a master, regardless of the amount of time the link between the master and the slave remained disconnected. If the value is positive, a maximum disconnection time is calculated as the node timeout value multiplied by the factor provided with this option, and if the node is a slave, it will not try to start a failover if the master link was disconnected for mote than the specified amount of time. For example if the node timeout is set to 5 seconds, and the validity factor is set to 10, a slave diconnected from the master for more than 50 secons, and the validity factor is set to 10, a slave disconnected from the master for more than 50 seconds will not try to failover its master. Note that any value different than zero may result in Redis Cluster to be not available after a master failure if there is no slave able to failover it. In that case the cluster will return back available only when the original master rejoins the cluster.
* cluster-migration-barrier <count>: Minimum number of slaves a master will remain connected with, for another slaver to migrate to a master which is no longer covered by any slave. See the appropriate section about replica migration in this tutorial for more information.
* cluster-require-full-coverage <yes/no>: If this is set to yes, as it is by default, the cluster stops accepting writes if some percentage of the key space is not covered by any node. If the option is set to no, the cluster will still serve queries even if only requests abouyt a subset of keys can be processed.

---

## Best practice