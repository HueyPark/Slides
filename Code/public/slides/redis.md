# Redis

---

## Contents

* Redis란?
* Data structures
* Replication
* LRU eviction
* Transaction
* On-disk persistence
* Best practice

---

## Redis란?

In-memory data structure store <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

Database, Cahce, Message broker로 사용 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

Open source <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### 기능

* Replication <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Lua Scripting, Transaction <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* LRU eviction <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* On-disk persistence <!-- .element: class="fragment fade-in" data-fragment-index="4" -->
* High availability via Redis Sentinel <!-- .element: class="fragment fade-in" data-fragment-index="5" -->
* Automatic partitioning with Redis Cluster <!-- .element: class="fragment fade-in" data-fragment-index="6" -->

---

### 특징

* ANSI C로 작성 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Works in most POSIX systems like Linux, BSD, OS X <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* Windows 에 대한 공식 지원은 없음, 하지만 Win-64 port를 Microsoft에서 유지 중 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

## Data structures

Redis is not a plain key-value store
Actually it is a data structures server, supproting different kind of values <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### 종류

* String <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* List <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* Set <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* Sorted set <!-- .element: class="fragment fade-in" data-fragment-index="4" -->
* Hash <!-- .element: class="fragment fade-in" data-fragment-index="5" -->

---

### Redis Key

Binary safe (문자열 뿐만 아니라 JPEG 같은 이미지도 사용가능)

empty key 허용됨

---

#### Key 사용시 주의사항

* 매우 긴 Key는 나쁘다, 용량 뿐만 아니라 성능에도 영향을 줌
* 매우 짧은 Key도 나쁘다, 키는 데이터를 설명할 수 있어야 함
* 좋은 예: "user:1000", "comment:1234:reply.to" 또는 "comment:1234:reply-to"
* 최대 허용 욜량: 512 MB

---

### Redis String

Key와 함께 사용할 수 있는 간단한 타입
Memcached를 사용해 보았으면 아주 익숙하게 사용가능
Binary safe

Key가 String이기 때문에 String을 다른 String에 mapping 가능
최대 허용 욜량: 512 MB

---

### Redis String Tutorial

```
> SET mykey somevalue
OK
> GET mykey
"somevalue"
```

---

#### String이 Redis 기본 값이지만 추가적으로 흥미로운 COMMAND가 지원됨

```
> SET counter 100
OK
> INCR counter
(integer) 101
> INCR counter
(integer) 102
> INCRBY counter 50
(integer) 152
```

---

#### MSET and MGET과 같은 COMMAND가 준비되어 있음

Latency 감소에 효과적

```
> MSET a 10 b 20 c 30
OK
> MGET a b c
1) "10"
2) "20"
3) "30"
```

MGET 이 사용되면 value의 배열을 return

---

#### Altering and querying the key space

EXISTS, DEL과 같은
모든 타입에 대한 변경 또는 질의를 위한 COMMAND가 제공됨

```
> SET mykey hello
OK
> TYPE mykey
// TODO: 타입 확인
> EXISTS mykey
(integer) 1
> DEL mykey
(integer) 1
> EXISTS mykey
(integer) 0
```

---

#### Redis Expire: keys with limited time to live

Key에 timeout을 설정하여 일정 기간동안만 사용가능하게 설정 가능

---

#### Redis Expire 특징

* seconds, millisecons 두 종류의 정밀도 사용가능
* On-disk persistence를 사용할 경우 Redis server가 중지되어 있던 시간은 계산되지 않음 (Redis가 유효기간이 지난 Key를 가지고 있을 수 있음)

---

#### Redis Expire Tutorial (1/2)

```
> SET key some-value
OK
> expire key 5
(integer) 1
> GET key (immediately)
"some-value"
> GET key (after sime time)
(nil)
```

---

#### Redis Expire Tutorial (2/2)

```
> SET key 100 EX 10
OK
> TTL key
(integer) 9
```

---

### Redis List

Redis List는 Linked List를 기반으로 구현되어 있음
따라서 Linked List의 특성을 동일하게 가짐
* element를 앞 또는 뒤에 추가할 경우 유리
* index를 data에 접근할 경우 불리

---

#### Redis List Tutorial (1/3)

```
> RPUSH myslist A
(integer) 1
> RPUSH mylist B
(integer) 2
> LPUSH mylist first
(integer) 3
> LRANGE mylist 0 - 1
1) "first"
2) "A"
3) "B"
```

Note that LRANGE takes two indexws, the first element of the range to return. Both the indexes can be negative, telling Redis to start counting from the end: so - 1 is the last element, -2 is the penultimate element of the list, an so forth

---

#### Redis List Tutorial (2/3)

```
> RPUSH mylist 1 2 3 4 5 "foo bar"
(integer) 9
> LRANGE mylist 0 - 1
1) "first"
2) "A"
3) "B"
4) "1"
5) "2"
6) "3"
7) "4"
8) "5"
9) "foo bar"
```

---

#### Redis List Tutorial (3/3)

```
> RPUSH mylist a b c
(integer) 3
> RPOP mylist
"c"
> RPOP mylist
"c"
> RPOP mylist
"a"
> RPOP mylist
(nil)
```

---

#### Redis List를 사용하면 좋은 경우

* 유저의 마지막 행동을 알아야 할 필요가 있을 때
* consumer-producer pattern을 사용해서 Process 간의 통신힐 때

* Twitter에서 최근 Twitt을 가져오기 위해 사용 중

---

#### Capped list

최근 몇 개의 element만 남기고 나머지를 list에서 제거하고 싶을 때 사용

---

#### Capped list Tutorial (1/2)

```
> RPUSH mylist 1 2 3 4 5
(integer) 5
> LTRIM mylist 0 2
OK
> LRANGE mylist 0 -1
1) "1"
2) "2"
3) "3"
```

---

#### Capped list Tutorial (2/2)

```
LPUSH mylist elements...
LTRIM mylist 0 999
```

---

#### Blocking operations on lists

List의 blocking opertation을 이용해서 producer-consumer pattern을 구현할 수 있음

---

##### producer-consumer polling을 통한 구현

1. To push items into the list, producers call LPUSH
2. To extract / process items from the list, consumers call RPOP
3. RPOP 시점에 data가 없으면 일정시간 대기후 재요청함

---

##### producer-consumer polling을 통한 구현의 문제점

1. Redis와 Client에게 불필요한 요청을 강제함
2. 요청 시점에 데이터가 없으면 일정시간 대기 후 재요청하는 방식이므로 요청간 딜레이가 생김

---

##### Blocking operations on lists

BRPOP, BLPOP을 사용하여 POP COMMAND를 blocking 하게 사용할 수 있음

---

##### Blocking operations on lists Tutorial

```
> BRPOP tasks 5
```

element가 없어도 5초간 대기하겠음

만약 timeout을 0으로 입력하면 영원히 대기함

---

##### Blocking operations 특징

* 여러 Client가 요청했을 경우 먼저 요청한 Client가 먼저 응답을 받음
* return value에 key가 포함되어 있음
* timeout이 되면 NULL이 반환됨

RPOPLPUSH, BRPOPLPUSH 이런 친구들도 있습니다

---

### Key의 생성과 삭제의 자동화

빈 Key에 대해 PUSH 또는 POP을 명령하면 Key가 자동으로 생성 및 삭제됨

이는 List 뿐만 아니라 Set, Sorted Set, Hash에도 동일하게 적용되며 아래의 규칙을 따름
* Element를 추가할 때 아직 target key가 없으면 해당 data type을 생성함
* Element를 삭제할 때 data 모두 없어지면 자동으로 data type을 삭제함
* Read-only COMMAND를 사용하거나 없는 Key에 대해 삭제 명령을 내리면 비어있는 data type에 적용한 것과 동일한 행동을 함 !!확인 필요!!

---

### Key의 생성과 삭제의 자동화 Tutorial (1/4)

```
> DEL mylist
(integer) 1
> LPUSH mylist 1 2 3
(integer) 3
```
---

### Key의 생성과 삭제의 자동화 Tutorial (2/4)

다른 타입에 대한 시도는 error를 발생시킴

```
> SET foo bar
OK
> LPUSH foo 1 2 3
(error) WRONGTYPE Operation against a key holding the wrong kind of value
> TYPE foo
string
```

---

### Key의 생성과 삭제의 자동화 Tutorial (3/4)

```
> LPUSH mylist 1 2 3
(integer) 3
> EXISTS mylist
(integer) 1
> LPOP mylist
"3"
> LPOP mylist
"2"
> LPOP mylist
"1"
> EXISTS mylist
(integer) 0
```

---

### Key의 생성과 삭제의 자동화 Tutorial (4/4)

```
> DEL mylist
(integer) 0
> LLEN mylist
(integer) 0
> LPOP mylist
(nil)
```

---

## Redis Hash

Redis hash는 당신이 생각하는 그 "hash"임
field-value pair로 구성되어 있음

---

### Redis Hash Tutorial 특징

Redis Hash는 오브젝트를 나타내기 편하며, Hash에 저장할수 있는 인자의 수에는 제한이 없음

---

### Redis Hash Tutorial

```
> HMSET user:1000 username antirez birthyear 1977 verified 1
OK
> HGET user:1000 username
"antirez"
> HGET user:1000 birthyear
"1977"
> HGETALL user:1000
1) "username"
2) "antirez"
3) "birthyear"
4) "1977"
5) "verified"
6) "1"
```

---

## Redis Set

정렬되지 않은 Redis String의 집합
Set 안의 인자가 하나임을 보장함

---

### Redis Set Tutorial

```
> SADD myset 1 2 3
(integer) 3
> SMEMBERS myset
1. 3
2. 1
3. 2
```

---

### Redis Set Tutorial

```
> SISMEMBER myset 3
(integer) 1
> SISMEMBER myset 30
(integer) 0
```

---

### Redis Set Tutorial

```
> SADD deck C1 C2 C3 C4 C5 C6 C7 C8 C9 C10 CJ CQ CK
  D1 D2 D3 D4 D5 D6 D7 D8 D9 D10 DJ DQ DK H1 H2 H3
  H4 H5 H6 H7 H8 H9 H10 HJ HQ HK S1 S2 S3 S4 S5 S6
  S7 S8 S9 S10 SJ SQ SK
  (integer) 52
```

---

### Redis Set Tutorial

```
> SUNIONSTORE game:1:deck deck
(integer) 52

```

---

### Redis Set Tutorial

```
> SPOP game:1:deck
"C6"
> SPOP game:1:deck
"CQ"
> SPOP game:1:deck
"D1"
> SPOP game:1:deck
"CJ"
> SPOP game:1:deck
"SJ"
```

---

### Redis Set Tutorial

```
> SCARD game:1:deck
(integer) 47
```

---

## Redis Sorted set

Sorted set 정렬이 되어있는 Set임
인자들은 score라고 불리는 부동소수점 값을 가지며 이를 기준으로 정렬됨

정렬 기준
* A와 B의 score가 다르면 score 순으로 정렬
* A와 B의 score가 동일하면 key string 기준으로 정렬

---

### Redis Sorted set Tutorial

```
> ZADD hackers 1940 "Alan Kay"
(integer) 1
> ZADD hackers 1957 "Sophie Wilson"
(integer 1)
> ZADD hackers 1953 "Richard Stallman"
(integer) 1
> ZADD hackers 1949 "Anita Borg"
(integer) 1
> ZADD hackers 1965 "Yukihiro Matsumoto"
(integer) 1
> ZADD hackers 1914 "Hedy Lamarr"
(integer) 1
> ZADD hackers 1916 "Claude Shannon"
(integer) 1
> ZADD hackers 1969 "Linus Torvalds"
(integer) 1
> ZADD hackers 1912 "Alan Turing"
(integer) 1
```

---

### Redis Sorted set Tutorial

```
> ZRANGE hackers 0 -1
1) "Alan Turing"
2) "Hedy Lamarr"
3) "Claude Shannon"
4) "Alan Kay"
5) "Anita Borg"
6) "Richard Stallman"
7) "Sophie Wilson"
8) "Yukihiro Matsumoto"
9) "Linus Torvalds"
```

---

### Redis Sorted set Tutorial

```
> ZREVRANGE hackers 0 -1
1) "Linus Torvalds"
2) "Yukihiro Matsumoto"
3) "Sophie Wilson"
4) "Richard Stallman"
5) "Anita Borg"
6) "Alan Kay"
7) "Claude Shannon"
8) "Hedy Lamarr"
9) "Alan Turing"
```

---

### Redis Sorted set Tutorial

```
> ZRANGE hackers 0 -1 withscores
1) "Alan Turing"
2) "1912"
3) "Hedy Lamarr"
4) "1914"
5) "Claude Shannon"
6) "1916"
7) "Alan Kay"
8) "1940"
9) "Anita Borg"
10) "1949"
11) "Richard Stallman"
12) "1953"
13) "Sophie Wilson"
14) "1957"
15) "Yukihiro Matsumoto"
16) "1965"
17) "Linus Torvalds"
18) "1969"
```

---

### Operating on ranges

Sorted Set은 range로 탐색이 가능이라는
막강한 기능을 보유하고 있음

---

### Redis Sorted set Tutorial

```
> ZRANGEBYSCORE hackers -inf 1950
1) "Alan Turing"
2) "Hedy Lamarr"
3) "Claude Shannon"
4) "Alan Kay"
5) "Anita Borg"
```

---

### Redis Sorted set Tutorial

```
> ZREMRANGEBYSCORE hackers 1940 1960
(integer) 4
```

---

### Redis Sorted set Tutorial

```
> ZRANK hackers "Anita Borg"
(integer) 4
```

---

### Redis Sorted set으로 랭킹 구현하기

Sorted set의 score는 ZADD를 통해 언제나 수정가능함
이런 특성으로 인해 Sorted set은 랭킹에 매우 적합함

---

## Bitmaps

Bitmaps는 별도의 data type은 아니고 Redis String을 사용한다 다만 여러 명령어를 통해 bit연산을 지원한다

---

## HyperLogLogs

A HyperLogLog is a probablilstic data structure used in order to count unique things (technically this is refereed to estimating the cardinality of a set). Usually counting unique items requires using an amount of memory proportional to the number of items you want to count, because you need to remember the elements you have already seen in the past in order to avoid counting them multiple times.

---

## Replication

Redis는 쉽고 간단하게 설정할수 있는 master-slave replication을 지원함

---

## Replication 특징

* 2.8버전부터 비동기 replication을 지원
* master는 복수의 slave를 가질 수 있음
* slave는 다른 slave와 통신할 수 있음
* replication이 master의 작업을 blocking하지 않음
* slave도 replication작업에 의해 blocking되지 않음, 하지만 최초 동기화 이후 데이터 동기화 시간에 들어온 요청을 blocking할 수 있음
* replication은 read-only query를 사용한 scalability와 data 이중화를 위해 사용할 수 있음
* replication을 통해 slave만 on-disk persistance를 지원하게 설정할 수 있음

---

### Replication은 어떻게 동작하는가?

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

Redis가 cache로 사용될 때 오래된 data가 새로운 데이터에 의해 삭제될 수 있음

기본적으로 LRU 알고리즘의 approximation을 이요함

---

### 최대 메모리 설정

최대 메모리 설정은 Redis에게 최대 가용 메모리의 값을 설정하게 도와줌

`redis.conf` 파일을 수정하거나 `CONFIG SET` COMMAND를 이용해서 설정가능

최대 메모리를 0으로 설정하면 64bit system에서는 무한대, 32 bit에서는 3GB로 설정됨

최대 메모리에 도착하면 설정된 policy에 따라 예전 data가 삭제됨

---

### Eviction policies

* noeviction: 메모리가 초과할 경우 error를 반환함, eviction을 일어나지 않음
* allkeys-lru: LRU 알고리즘을 사용해 삭제
* volatile-lru: expire값이 설정된 key 중 LRU 알고리즘을 사용해 삭제
* allkeys-random: 랜덤한 key를 삭제
* volatile-random: expire값이 설정된 key 중 랜덤하게 삭제
* volatile-ttl: expire값이 설정된 key 중 가장 짧은 생존시간을 가진 key를 삭제

---

### 어떻게 Eviction은 작동하는가?

1. 클라이언트가 새로운 data를 추가하는 COMMAND를 실행
2. Redis가 메모리를 확인하고 maxmemory 한계를 초과하면 policy에 따라 key를 evict함
3. 새로운 COMMAND가 실행됨

---

### Approximated LRU algorithm

Redis LRU 알고리즘은 정확한 구현이 아님
Redis는 정확한 LRU 알고리즘을 구현하는 대신 적절한 근사값을 얻었음
3.0 버전부터는 그 근사치의 정밀도가 더 증가하였음
Redis가 근사값을 사용하는 이유는 메모리 사용량을 줄이기 위함임
하지만 이 근사치는 LRU와 동등한 성능을 가지고 있음

---

### Redis scripting and transactions

A Redis script is transactional by definition, so everything you can do with a Redis transaction, you can also do with a script, and usually the script will be both simpler and faster.

This duplication is due to the fact that scripting was introduced in Redis 2.6 while transactions already existed long before. However we are unlikely to remove the support for transactions in the short time because it seems semantically opportune that even without resorting to Redis scripting it is still possible to avoid race conditions, especially since the implementation complexity of Redis transactions is minimal.

However it is not impossible that in a non immediate future we'll see that the whole user base is just using scripts. If this happens we may deprecate and finally remove transactions.

---

## On-disk persistence

Redis provides a different range of persistence options:

* The RDB persistence performs point-in-time snapshots of your dataset at specified intervals.
* The AOF persistence logs every write operation received by the server, that will be played again at server startup, reconstructing the original dataset. Commands are logged using the same format as the Redis protocol itself, in an append-only fashion. Redis is able to rewrite the log on background when it gets too big.
* If you wish, you can disable persistence at all, if you want your data to just exist as long as the server is running.
* It is possible to combine both AOF and RDB in the same instance. Notice that, in this case, when Redis restarts the AOF file will be used to reconstruct the original dataset since it is guaranteed to be the most complete.

The most important thing to understand is the different trade-offs between the RDB and AOF persistence. Let's start with RDB:

### RDB advantages

* RDB is a very compact single-file point-in-time representation of your Redis data. RDB instance you may want to archive your RDB files every hour for the latest 24 hours, and to save an RDB snapshot every day for 30 days. This allows you easily restore different versions of the data set in case of disasters.
* RDB is very good for disaster recovery, being a single compact file can be transferred to far data centers, or on Amazon S3 (possibly encrypted).
* RDB maximizes Redis performances since the only work the Redis parent process needs to do in order to persist is forking a child that will do all the rest. The parent instance will never perform disk I/O or alike.
* RDB allows faster restarts with big datasets compared to AOF.

### RDB disadvantages

* RDB is NOT good if you need to minimize the chance of data loss in case Redis stops working (for example after a power outage). You can configure different save points where an RDB is produced (for instance after at least five minutes and 100 writes against the data set, but you can have multiple save points). However you'll usually create an RDB snapshot every five minutes or more, so in case of Redis stopping working without a correct shutdown for any reason you should be prepared to lose the latest minutes of data.
* RDB needs to fork() often in order to persist on disk using a child process. Fork() can be time consuming if the dataset is big, and may result in Redis to stop serving clients for some millisecond or even for one second if the dataset is very big ans the CPU performance not great AOF also needs to fork() but you can tune how often you want to rewrite your logs without any trade-off durability.

### AOF advantages

* Using AOF Redis is much more durable: you can have different fsync policies: no fsync every second, fsync at every query. With the default policy of fsync every second write performances are still great (fsync is performed using a background thread and the main thread will try hard to perform writes when no fsync is in progress.) but you can only lose one second worth of writes.
* The AOF log is an append only log, so ther are no seeks, nor corruption problems if there is a power outage. Even if the log ends with an half-written command for some reason (disk full or other reasons) the redis-checj-aof tool is able to fix it easily.
* Redis is able to automatically rewrite the AOF in background when it gets too big. The rewrite is completely safe as while Redis continues appending to the old file, a completely new one is produced with the minimal set of operations needed to create the current data set, and once this second file is ready Redis switches the two and starts appending to the new one.
* AOF contains a log of all operations one after the other in an easy to understand and parse format. You can even easily export an AOF file. For instance even if you flushed everything for an error using a FLUSHALL command, if no rewrite of the log was performed in the meantime you can still save your data set just stopping the server, removing the latest command, and restarting Redis again.

### AOF disadvantages

* AOF diles are usually bigger than the equivalent RDB files for the same dataset.
* AOF can be slower than RDB depending on the exact fsync policy. In general with fsync set to every second performances are stil very high, and with fsync disabled it should be exactly as fast as RDB even under high load. Still RDB is able to provide more guarantees about the maximum latency even in the case of an huge write load.
* In the past we experenced rare bugs in specific commands (for instance ther was on involving blocking commands like BRPOPLPUSH) causing the AOF produced to not reproduce exactly the same dataset on reloading. This bugs are rare and we have tests in the test suite creating random complex datasets automatically and reloading them to check everythong is ok, but this kind of bugs are almost impossible with RDB persistence. To make this point more clear: the Redis AOF works incrementally updating an existing state, like MySQL or MongoDB does, while the RDB snapshotting creates everything from scratch again and again, that is conceptually more robust. However - 1) It should be noted that every time the AOF is rewritten by Redis it is recreated from scratch starting from the actual data contained in the data set, making resistance to bugs stringer compared to an always appending AOF file (or one rewritten reading the old AOF instead of reading the data in memory). 2) We never had a single report from users abour an AOF corruption that was detected in the real world.

### Ok, so what should I use?

The general indication is that you should use both persistence methods if you want a degree of data safety comparable to what PostgreSQL can provide you.

If you care a lot about your data, but still can live with a few minutes of data loss in case of disasters, you can simply use RDB alone.

There are many users using AOF alone, but we dicourage it since to have an RDB snapshot from time to time is a great idea for doing database backups, for faster restarts, and int the event of bugs in the AOF engine.

Note: for all these reasons we'll likely end up unifying AOF and RDB into a single persistence model in the future (long term plan).

The following sections will illustrate a few more defails about the two persistence models.

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

## Redis latency problems troubleshooting

This document will help you understand what the problem could be if you are experiencing latency problems with Redis.

In this context latency is the maximum delay between the time a client issues a command and the time the reply to the command is received by the client. Usually Redis processing time is extremely low, in the sub microsecond range, but there are certain conditions leading to higher latency figures.

### I've little time, give me the checklist

The following documentation is very important in order to run Redis in a low latency fashion. However I understand that we are busy people, so let's start with a quick checklist. If you fail following these steps, please return here to read the full documentation.

1. Make sure you are not running slow commands that are blocking the server. Use the Redis Slow Log feature to check this.
2. For EC2 users, make sure you use HVM based modern EC2 instances, like m3.medium. Otherwise fork() is too slow.
3. Transparent huge pages must be disabled from your kernel. Use echo never >/sys/kernel/mm/transparent_hugepage/enabled to disable them, and restart your Redis process.
4. If you are using a virtual machine, it is possible that you have an intrinsic latency that has nothing to do with Redis. Check the minimum latency you can expect from your runtime environment using ./redis-cli --intrinsic-latency 100. Note: you need to run this command in the server not in the client.
5. Enable and use the Latency monitor feature of Redis in order to get a human readable description of the latency events and causes in yout Redis instance.

In general, use the following table for durability VS latency/performance tradeoffs, orderd from stronger safety to better latency.

1. AOF + fsync always: this is very slow, you should use it only if you know what you are doing.
2. AOF + fsync every second: this is a good compromise.
3. AOF + fsync every second + no-appendfsync-on-rewrite option set to yes: this is as the above, but avoids to fsync during rewrites to lower the disk pressure.
4. AOF + fsync never. Fsyncing is up to the kernel in this setup, even less disk pressure and risk of letency spikes.
5. RDB. Here you have a vast spectrum of tradeoffs depending on the save triggers you configure.

And now for people with 15 minutes to spend, the details...

### Measuring latency

If you experiencing latency problems, probably you know how to measure it in context of your application, or maybe your latency problem is very evident even macroscopically. However redis-cli can be used to measure the latency of a Redis server in milliseconds, just try:

redis-cli --latency -h `host` -p `port`

### Using the internal Redis latency monitoring subsystem

Since Redis 2.8.13, Redis provides latency monitoring capabilities that are able to sample different execution paths to understand where the server is blocking. This makes debugging of the problems illustrated in this documentation much simpler, so we suggest to enable latency monitoring ASAP. Please refer to the Latency monitor documentation.

While the latency monitoring sampling and reporing capabilities will make simpler to understand the source of latency in your Redis systme, it is still advised that you read this documentation extensively to better understand the topic of Redis and latency spikes.

### Latency baseline

There is a kind of latency that is inherently part of the environment where you run Redis, that is the latency provided by your operating system kernel and, if you are using virtualization, by the hypervisor you are using.

While this latency can't removed it is important to study it because it is the baseline, or in other words, you'll not be able to achieve a Redis latency that is better than the latency that every process running in your enviornment will experience because of the kernel or hypervisor implementation or setup.

We call this kind of latency intrinsic latency, and redis-cli starting from Redis version 2.8.7 is able to measure it. This is an example run under Linux 3.11.0 running on an entry level server.

Note: the argument 100 is the number of seconds the test will be executed. The more time we run the test, the more likely we'll be able to spot latency spikes. 100 seconds is usually appropriate, however you may want to perform a few runs at different times. Please note that the test is CPU intensuve and will likely saturate a single core in your system.

```
$ ./redis-cli --intrinsic-latency 100
Max latency so far: 1 microseconds.
Max latency so far: 16 microseconds.
Max latency so far: 50 microseconds.
Max latency so far: 53 microseconds.
Max latency so far: 83 microseconds.
Max latency so far: 115 microseconds.
```

Note: redis-cli in this special case needs to run in the server where you run or plan to run Redis, not in the client. In this special mode redis-cli does no connect to a Redis server at all: it will just try to measure the largest time the kernel does not provide CPU time to run to the redis-cli process itself.

In the above example, the intrinsic latency of the system is just 0.115 milliseconds (or 115 microsecondes), which is a good news, however keep in mind that the intrinsic latency may change over time depending on the load of the system.

Virtualized environments will not show so good numbers, especially with high load or if there are noisy neighbors. The following is a run on a Linode 4096 instance running Redis and Apache:

```
$ ./redis-cli -- intrinsic-latency 100
Max latency so far: 573 microseconds.
Max latency so far: 695 microseconds.
Max latency so far: 919 microseconds.
Max latency so far: 1606 microseconds.
Max latency so far: 3191 microseconds.
Max latency so far: 9243 microseconds.
Max latency so far: 9671 microseconds.
```

Here we have an intrinsic latency of 9.7 milliseconds: this means that we can't ask better than that to Redis. However other runs at different times in different virtualization environments with higher load or with noisy neighbors can easily show eben worse values. We were able to measured up to 40 milliseconds in systems otherwise apparently running normally.

### Latency induced by network and communication

Clients connect to Redis using a TCP/IP connection or a Unix domain connection. The typical latency of a 1 Gbit/s network is about 2000 us, while the latency with a Unix domain socket can be as low as 30 us. It actually depends on your network and system hardware. On top of the communication itself, the system adds some more latency (due to thread scheduling, CPU caches, NUMA placement, etc ...). System induced latencies are significantly higher on virtualized environment than on a physical machine.

The consequence is even if Redis processes most commands in sub microsecond range, a client performing many roundtrips to the server will have to pay for these network and system related latencies.

An efficient client will therefore try to limit the number of roundtrips by pipelining several commands together. This is fully supported by the servers and most clients. Aggregated command like MSET, MGET can be also used for that purpose. Starting with Redis 2.4, a number of commands also support variadic parameters for all data types.

Here are some guidelines:

* If you can afford it, prefer a physical machine over a VM to host the server.
* Do not systematically connect/disconnect to the server (especially true for web based applications). Keep your connections as long lived as possible.
* If your client is on the same host than the server, use Unix domain sockets.
* Prefer to use aggregated command (MSET/MGET), or commands with variadic parameteres (if possible) over pipelining.
* Prefer to use pipelining (if possible) over sequence of roundtrips.
* Redis supports Lua server-side svripting to cover cases that are not suitable for raw pipelining (for instance when the result of a command is an input for the following commands).

On Linux, some people can achieve better latencies by playing with process placement (taskset), cgroups, real-time priorities (chrt), NUMA configuration (numactk), or by using a low-latency kernel, Please note vanilla Redis is not really suitable to be bound on a single CPU core. Redsi can fork background tasks that can be extremely CPU consuming like bgsave or AOF rewrite. These taks must never run on the smae core as the main event loop.

In most situations, these kind of system level oprimizations are not needed. Only do them if you require them, and if you are familiar with them.

### Single threaded nature of Redis

Redis uses a mostly single threaded design. This means that a single process serves all the client requests, using a technique called multiplexing. This means that Redis can serve a single request in every given moment, so all the requests are served sequentially. This is very similar to how Node.js works as well.

However, both products are often not perceived as being slow. This is caused in part by the small amount of time to complete a single request, but primarily because these products are designed to not block on system calls, such as reading data from or writing data to a socket.

I said that Redis is mostly single threaded since actually from Redis 2.4 we use threads in Redis in order to perform some slow I/O operations in the background, mainly related to disk I/O, but this does not change the fact that Redis serves all the requests using a single thread.

### Latency generated by slow commands

A consequence of being single thread is that when a request is slow to serve all the other clients will wait for this request to be served. when executing normal commands, like GET or SET or LPUSH this it not a problem at all since this commands are executed in constant (and very small) time. However there are commands operating on many elements, like SORT, LREM, SUNION and others. For instance taking the intersection of two big sets can take a considerable amount of time.

The algorithmic complexity of all commands is documented. A good practice is to systematically check it when using commands you are not familiar with.

If you have latency concerns you should either not use slow commands against values composed of many elements, or you should run a replica using Redis replication where to run all your slow queries.

It is possible to monitor slow commands using the Redis Slow Log Feature.

Additionally, you can use your favorite per-process monitoring program (top, htop, prstat, etc ...) to quickly check the CPU consumption of the main Redis process. If it is high wile the traffic is not, it is usually a sign that slow commands are used.

IMPORTAN NOTE: a VERY common source of latency generated by execution of slow commands is the use of the KEYS command in production environments. KEYS, as documented in the Redis documentation, should only be used for debugging purposes. Since Redis 2.8 a new commands were introduced in order to iterate the key space and othe large collections incrementally, please check the SCAN , SSCAN, HSCAN and ZSCAN commands for more information.

---

## Best practice
