# Redis

---

## Contents

* 들어가기 전에
* Redis?
* 주요기능
* 활용사례
* 주의사항

---

## 들어가기 전에

---

## Redis?

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

## 몇가지 상황

---

### 상황 1

* 유저에게 공성전 정보 보여주어야 함 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 공성전 정보에는 점령 현황, 각 길드정보, 길드의 유저 정보등이 있음  <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* 또 이 정보들을 조합해서 전투현황 정보를 생성함  <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* 모든 요청마다 데이터베이스에서 정보를 가져와서 서버가 조합한다면? <!-- .element: class="fragment fade-in" data-fragment-index="4" -->

---

### 상황 2

* 공성전 결과에 따른 길드랭킹을 보여주어야 함 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

## Redis 주요기능

* Data type <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Replication <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* LRU eviction <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* Transaction <!-- .element: class="fragment fade-in" data-fragment-index="4" -->
* On-disk persistance <!-- .element: class="fragment fade-in" data-fragment-index="5" -->
* Cluster <!-- .element: class="fragment fade-in" data-fragment-index="5" -->

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

* Cache로 사용될 때 오래된 데이터를 삭제하는 방법 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* LRU 알고리즘의 근사를 사용함 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### Eviction 설정

* noeviction: 메모리가 초과할 경우 error
* allkeys-lru: 모든 Key에 대해 LRU 알고리즘 사용
* volatile-lru: expire값이 설정된 key중 LRU 알고리즘 사용
* allkeys-random: 무작위 key 삭제
* volatile-random: expire값이 설정된 무작위 key 삭제
* volatile-ttl: expire값이 설정된 key 중 가장 짧은 생존시간을 가진 key를 삭제

---

## Transaction

* Transaction을 지원함

---

## Transaction 특징

* roll back 기능을 지원하지 않음 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Lua script를 통한 Transaction도 지원함 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

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

---

### Cluster란 무엇인가?

여러 대의 컴퓨터를 연결하여 마치 하나의 컴퓨터처럼 사용하는 기술 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

---

### 상황

A. 데이터베이스가 힘들어합니다 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

B. 서버 스펙을 올립시다 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

A. 더 못 올리겠는데요 or 싼 거 여러개가 더 좋을 거 같아요 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

B. 클러스터를 도입합시다 <!-- .element: class="fragment fade-in" data-fragment-index="4" -->

---

### Redis Cluster 101

* 오늘은 간단한 설명만 합니다 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* Redis Cluster의 분산 시스템 컨셉을 이해하는 것이 목적 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* 진지하게 프로젝트에 적용하려한다면 정확한 작동방식 이해가 필요함 <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### Redis Cluster 제공 기능

* 자동으로 여러 노드들에 데이터가 샤딩되어 저장 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 몇 개 노드 fail시에도 전체적인 시스템이 동작 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

## 다시 몇가지 상황

---

### 상황 1

* 유저에게 공성전 정보 보여주어야 함 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 공성전 정보에는 점령 현황, 각 길드정보, 길드의 유저 정보등이 있음  <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* 또 이 정보들을 조합해서 전투현황 정보를 생성함  <!-- .element: class="fragment fade-in" data-fragment-index="3" -->
* 모든 요청마다 데이터베이스에서 정보를 가져와서 서버가 조합한다면? <!-- .element: class="fragment fade-in" data-fragment-index="4" -->

---

### Redis String을 캐시로 사용

* 데이터를 가져올 때 Redis에 저장해 놓고 캐시로 사용 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 만약 데이터에 변동이 발생하면 Key를 이용해 캐시 삭제 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

---

### 상황 2

* 공성전 결과에 따른 길드랭킹을 보여주어야 함 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->

### 해법 3

_Sorted set!_ <!-- .element: class="fragment fade-in" data-fragment-index="2" -->

* ZRANK: Sorted set내 데이터의 rank를 반환, O(log(N)) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

## 주의사항

---

### Redis는 싱글 쓰레드로 작동함

* Redis는 대부분 싱글 쓰레드로 디자인되어 있음 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 대부분의 경우에 단일 요청에 대한 응답이 매우 빠르므로 전체 성능에 영향을 주지 않음 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
* 새로운 명령어를 사용하게 된다면 시간 복잡도 확인 및 검증과정이 필요함 (KEYS, SORT, LREM, SUNION 등 주의) <!-- .element: class="fragment fade-in" data-fragment-index="3" -->

---

### 하나의 서버를 두가지 용도로 사용할때 (캐시, 데이터베이스)

* 왠만하면... 안하는 것이... <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
* 최대 메모리 사용량을 초과하였을 때 어떤 키가 삭제될 것인가에 대한 별도의 설정이 필요함 <!-- .element: class="fragment fade-in" data-fragment-index="2" -->
    * volatile-lru, allkeys-random, volatile-random

---

## 참고자료

* http://redis.io