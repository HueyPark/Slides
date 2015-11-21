# Redis

---

## Contents

* Redis란?
* Data type
* Replication
* Cluster
* LRU eviction
* On-disk persistence
* Lua Scripting & Transaction
* 활용사례

---

## Redis란?

In-memory data structure store <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

Database, Cahce, Message broker로 사용가능 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

Open source <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### 기능

* Lua Scripting, Transaction <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* LRU eviction <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* On-disk persistence <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* Replication <!-- .element: class="fragment fade-in" data-fragment-index="4" -->
* Automatic partitioning with Redis Cluster <!-- .element: class="fragment fade-in" data-fragment-index="5" -->

---

### 특징

* ANSI C로 작성 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Linux, OS X, BSD를 지원함 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* Windows는 지원하지 않으나 Microsoft에서 유지중 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

## Data type

Redis는 여러 종류의 data type을 지원함 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### Data type 종류

* String <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* List <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* Set <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* Hash <!-- .element: class="fragment fade-in" data-fragment-index="4" -->
* Sorted set <!-- .element: class="fragment fade-in" data-fragment-index="5" -->
<!-- * bitmaps, hyperloglogs (String을 사용하며, 관련 명령어를 지원) <!-- .element: class="fragment fade-in" data-fragment-index="6" -->
<!-- * geospatial data (String을 사용하며, 관련 명령어를 지원, beta) <!-- .element: class="fragment fade-in" data-fragment-index="7" -->

---

### String

* 간단한 문자열 data <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Binary safe (문자열이 아닌 data도 저장가능) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* 최대 허용 용량: 512 MB <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### String 기본 명령어
* GET : data를 읽음, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* SET: data를 저장, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### String 기본 명령어

``` 
> SET mykey somevalue
OK

> GET mykey
"somevalue"
```

---

### String 추가 명령어 (1/2)

* INCR : integer 1 증가, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* DECR : integer 1 감소, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* INCRBY : integer 값만큼 증가, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* DECRBY : integer 값만큼 감소, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### String 추가 명령어 (1/2)

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

### String 추가 명령어 (2/2)

* MSET : 여러 data를 동시에 저장, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* MGET : 여러 데이터를 동시에 가져옴, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

_Latency 감소에 효과적_ <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### String 추가 명령어 (2/2)

```
> MSET a 10 b 20 c 30
OK

> MGET a b c
1) "10"
2) "20"
3) "30"
```

_MGET 이 사용되면 value의 배열을 반환_ <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### 공용 명령어

_모든 타입에 공용으로 사용되는 명령어_ <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

* EXISTS : 데이터 존재여부 확인, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* DEL : 여러 데이터를 동시에 가져옴, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* TYPE : 데이터 타입 확인, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="4" -->

---

### 공용 명령어

```
> SET mykey hello
OK

> TYPE mykey
string

> EXISTS mykey
(integer) 1

> DEL mykey
(integer) 1

> EXISTS mykey
(integer) 0
```

---

### 공용 명령어

* EXPIRE : 만료시간 설정, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

###  공용 명령어

```
> SET key some-value
OK

> EXPIRE key 5
(integer) 1

> GET key (immediately)
"some-value"

> GET key (after sime time)
(nil)
```

---

### 공용 명령어

```
> SET key 100 EX 10
OK

> TTL key
(integer) 9
```

---

### EXPIRE 특징

* seconds, millisecons 두 종류의 정밀도 사용 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* On-disk persistence를 사용할 경우 서버가 중지되어 있던 시간은 계산되지 않음 (Redis가 유효기간이 지난 Key를 가지고 있을 수 있음) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### List

_List는 Linked List의 특징을 동일하게 가짐_ <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

* 데이터를 앞 또는 뒤에 추가할 경우 유리 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* index로 데이터에 접근할 경우 불리 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### List 명령어
* LPUSH : List 왼쪽에 데이터 추가, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* LPOP : List 왼쪽의 데이터를 반환하고 삭제, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* LRANGE : List의 데이터를 반환, O(S+N) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
    * S는 List 시작지점으로부터의 offset

---


### List 명령어

```
> RPUSH myslist A
(integer) 1

> RPUSH mylist B
(integer) 2

> LPUSH mylist first
(integer) 3

> LRANGE mylist 0 -1
1) "first"
2) "A"
3) "B"
```
_LRANGE의 인자가 음수로 사용되면 마지막 인자로부터의 offset을 나타냄_ <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### List 명령어

```
> RPUSH mylist a b c
(integer) 3

> RPOP mylist
"c"

> RPOP mylist
"b"

> RPOP mylist
"a"

> RPOP mylist
(nil)
```

---

### List 명령어

* LTRIM : 선택된 인덱스 범위를를 제외한 데이터 삭제, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

_최근 몇 개의 데이터만 남기고 싶을 때 사용_ <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### List 명령어

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

### List 명령어

* BLPOP : blocking으로 동작하는 LPOP, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

_blocking을 이용해 producer-consumer pattern을 쉽게 구현_ <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### List 명령어

```
> BRPOP tasks 5
```

_데이터가 없어도 5초간 대기_ <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### List blocking 명령어 특징

* 여러 Client가 요청했을 경우 먼저 요청한 Client가 먼저 응답을 받음 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* return value에 key가 포함되어 있음 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* timeout이 되면 NULL이 반환됨 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

## Hash

* Redis hash는 자료구조 hash를 기반으로 구현 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* field-value pair로 구성되어 있음 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* 오브젝트를 나타내기 편하며, Hash에 저장할수 있는 인자의 수에는 제한이 없음 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### Hash 명령어

* HSET : Hash에 데이터를 저장, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* HGET : Hash에서 데이터를 가져옴, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* HMSET : Hash에 여러 데이터를 저장, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* HGETALL: Hash의 모든 데이터를 가져옴, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="4" -->

---

### Hash 명령어

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

## Set

* 정렬되지 않은 Redis String의 집합 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Set 안의 인자가 하나임을 보장함 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### Set 명령어

* SADD : Set에 데이터를 저장, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* SMEMBERS : Set에서 모든 데이터를 가져옴, O(N) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* SISMEMBER : Set의 데이터 존재유무 확인, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* SPOP: Set에서 무작위 데이터를 반환하고 지움, O(1) <!-- .element: class="fragment fade-in" data-fragment-index="4" -->

---

### Set 명령어

```
> SADD myset 1 2 3
(integer) 3

> SMEMBERS myset
1. 3
2. 1
3. 2
```

---

### Set 명령어

```
> SISMEMBER myset 3
(integer) 1

> SISMEMBER myset 30
(integer) 0
```

---

### Set 명령어

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
> SPOP deck
"C6"

> SPOP deck
"CQ"

> SPOP deck
"D1"
```

---

## Sorted set

* Sorted set 정렬이 되어있는 Set임 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 인자들은 score라고 불리는 부동소수점 값을 가지며 이를 기준으로 정렬됨 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### Sorted set 명령어

* ZADD : Sorted set에 score와 데이터로 이루어진 데이터를 저장, O(log(N)) <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* ZRANK: Sorted set내 데이터의 rank를 반환, O(log(N)) <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* ZRANGE : 인덱스를 이용하여 score 오름차순으로 데이터 반환, O(log(N)+M) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### Sorted set 명령어

```
> ZADD hackers 1940 "Alan Kay"
(integer) 1

> ZADD hackers 1957 "Sophie Wilson"
(integer 1)

> ZADD hackers 1953 "Richard Stallman"
(integer) 1
```

---

### Sorted set 명령어

```
> ZRANGE hackers 0 -1
1) "Alan Kay"
2) "Richard Stallman"
3) "Sophie Wilson"
```

---

### Sorted set 명령어

```
> ZRANK hackers "Richard Stallman"
(integer) 2
```

---

## Key

* Binary safe한 데이터 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
    * (문자열 뿐만 아니라 JPEG 같은 이미지도 사용가능)
* empty key 허용 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

#### Key 특징

* 최대 허용 욜량: 512 MB <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Key는 자동으로 생성 및 삭제됨 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
    * 추가할 때 아직 key가 없으면 생성
    * 삭제할 때 데이터가 모두 없어지면 삭제
    * Read-only 명령어를 사용하거나 없는 Key에 대해 삭제 명령을 내리면 비어있는 key에 적용한 것과 동일하게 작동
    * 다른 타입에 대한 시도는 error를 발생

---

## 기타 명령어

* Bitmaps <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
    * bit 연산을 지원하는 명령어 집합
    * String 자료구조 사용
* HyperLogLogs <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
    * 매우 적은 메모리로 집합의 원소 개수를 추정하기 위한 명령어 집합
    * String 자료구조 사용

---

## Replication

Master-Slave replication 지원 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### Master-Slave replication 목적

* 데이터베이스 읽기 Scale out <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 데이터베이스 이중화 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### Replication 특징

* 2.8버전부터는 비동기 replication을 지원 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Master는 복수의 Slave를 가질 수 있음 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* Slave는 다른 Slave를 가질 수 있음 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* Replication이 Master의 작업을 Blocking하지 않음 <!-- .element: class="fragment fade-in" data-fragment-index="4" -->
* Slave도 Replication작업에 의해 Blocking되지 않음, 하지만 최초 동기화 이후 데이터 동기화 시간에 들어온 요청을 Blocking할 수 있음 <!-- .element: class="fragment fade-in" data-fragment-index="5" -->
* Read-only query를 사용한 Scalability와 Data 이중화를 위해 사용할 수 있음 <!-- .element: class="fragment fade-in" data-fragment-index="6" -->
* Slave만 on-disk persistance를 지원하게 설정할 수 있음 <!-- .element: class="fragment fade-in" data-fragment-index="7" -->

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

Redis persistence는 두가지 옵션을 제공함

---

## On-disk persistence 특징

* RDB: 일정 시간 간격으로 데이터의 snapshot을 만드는 방식
* AOF: 모든 쓰기 operation을 저장해 놓았다가 서버가 재시작할 때 데이터를 새로 만드는 방식

---

### RDB의 장점

* RDB는 장애 복구에 유리함, 하나의 파일을 저장해 놓는 것만으로 데이터 백업이 가능
* RDB는 Redis의 performance를 최대로 이끌어 냄
* 서버 재시작이 빠름

---

### RDB의 단점

* RDB는 snapshot 사이의 데이터를 보존할 수 없음
* 만약 이 문제를 없애려고 snapshot 간격을 줄이면 performance 문제가 발생함

---

### AOF의 장점

* 설정에 따라 데이터를 쿼리 단위 또는 초 단위로 보존할 수 있음
* 모든 operation을 저장하기 때문에 이해하기 쉬운 데이터가 저장됨

---

### AOF의 단점

* AOF 파일은 일반적으로 RDB 파일에 비해 큼
* RDB 방식에 비해 실행 중 performance가 떨어짐

---

## Redis Cluster

Redis가 제공하는 Cluster에 대한 간단한 설명
간단하게 Redis Cluster의 분산 시스템 컨셉을 이해하는 것이 목적
Redis 3.0 이상에서 적용됨
진지하게 프로젝트에 적용하려한다면 세부적인 검토가 별도로 필요함

### Cluster란 무엇인가?


### Redis Cluster란

자동으로 여러 Redis node들에 데이터가 샤딩되어 저장되는 기능을 제공함
또 몇 개의 node가 fail 되더라도 전체적인 시스템이 동작하는 기능을 지원함

### Redis Cluster TCP ports

모든 Redis node는 2개의 TCP connection 포트를 필요로 함

* 하나는 클라이언트와의 통신을 위해 사용
* 두번째는 node와 node간의 통신을 위해 사용하는 bus port
** 장애 감지, 설정 변경, 장애 복구 권한 부여등

사용자는 bus 포트를 이용해 통신할 필요가 없으며 Redis 명령어만 사용하면 됨

### Redis Cluster data sharding

Redis Cluster는 consistent hashing을 사용하지 않고 hash slot이라고 부르는 방식을 사용함

Redis Cluster에는 16384개의 hash slot이 있고 이를 지정된 slot의 키를 계산해서 우리는 CRC16의 검증된 키를 받을 수 있음

예를 들면
* Node A contains hash slots from 0 to 5500.
* Node B contains hash slots from 5501 to 11000.
* Node C conatins hash slots from 11001 to 16384.

`consistent hashing: 메타 정보를 조회하지 않아도 클러스터에서 키가 저장된 노드를 바로 찾아갈 수 있는 방법 (memcached가 이용)`

### Redis Cluster master-slave model

Master 노드에 장애가 생기거나 통신이 불가능해 지는 상황에서 상황에서의 처리를 위해 master-slave 모델을 사용함

예에서 Node B에 장애가 발생하면 hash slot 5501에서 11000은 사용 불가

하지만 우리가 마스터 노드에 슬레이브 노드를 모두 추가했다면 (Node A, B, C에 A1, B1, C1 추가) B에 문제가 생겨도 서비스 가능

Node sB가 B를 복제 중이기 때문에 B에 장애가 생겼을 때 sB가 B로 승격됨

### Redis Cluster consistency guarantees

Redis Cluster는 강한 consistency (일관성)을 보장하지않음
이는 특정한 조건에서 Redis Cluster가 클라이언트가 쓴 데이터를 잃을 수 있음을 말함

첫번째 이유: Redis Cluster가 비동기로 replication을 하기 때문임

1. 클라이언트가 master B에 기록함
2. master B가 클라이언트에 OK를 반환
3. master B가 슬레이브 노드에게 데이터 복제

이것은 이상하게 보일 수 있지만 일반적인 성능과 일관성의 trade-off임

만약 꼭 필요하다면 `WAIT` 명령어를 이용해 동기쓰기를 할 수도 있음

두번째 이유: 데이터베이스간 통신 장애가 발생할 수 있음 

예)
가정:
    서버: 마스터 A, B, C와 슬레이브 A1, B1, C1으로 이루어진 서버,
    클라이언트: Z1,
    A, C, A1, B1, C1 그룹과 B, Z1그룹이 물리적으로 분리

만약 두 그룹간 네트워크가 오랜기간 불안정해진다면 B1이 마스터로 B는 커넥션을 잃어버림

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

## 활용 사례

* 캐시
** 느린 데이터베이스의 병목 테이블 cache 
** 계산량이 많은 서버 사이드 작업결과에 대한 cache
* 쉬운 정렬
** 유저 랭킹
* 대량 데이터 입력(로그 등) 의 버퍼