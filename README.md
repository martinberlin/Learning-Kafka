# Learning-Kafka
My steps and documentation learning event streaming Kafka

## Installation with Docker and DDEV

This git repository will be mostly for documentation purpouses with just a few code examples mostly in PHP using librdkafka-dev PHP 8 extension. This was my first docker standalone example. With DDEV comes just after, so just scroll down:

```
# FILE:  compose.yaml

services:
  kafka:
    image: 'bitnami/kafka:latest'
    ports:
      - '9094:9094'

    environment:
      - KAFKA_ENABLE_KRAFT=yes
      - KAFKA_CFG_BROKER_ID=1
      - KAFKA_CFG_NODE_ID=1
      - KAFKA_CFG_PROCESS_ROLES=broker,controller
      - KAFKA_CFG_CONTROLLER_LISTENER_NAMES=CONTROLLER
      - KAFKA_CFG_LISTENERS=PLAINTEXT://:9092,CONTROLLER://:9093,EXTERNAL://:9094
      - KAFKA_CFG_LISTENER_SECURITY_PROTOCOL_MAP=CONTROLLER:PLAINTEXT,PLAINTEXT:PLAINTEXT,EXTERNAL:PLAINTEXT
      - KAFKA_CFG_ADVERTISED_LISTENERS=PLAINTEXT://kafka:9092,EXTERNAL://localhost:9094
      - KAFKA_CFG_CONTROLLER_QUORUM_VOTERS=1@:9093
      - ALLOW_PLAINTEXT_LISTENER=yes
```

NOTE: This should not be used in production since uses a PLAINTEXT protocol for messages. Doing it with this configuration you will be able to access the KAFKA server using port 9092 from inside the container. And using port 9094 from outside.

     $ docker compose up

Should return: INFO [KafkaRaftServer nodeId=1] Kafka Server started (kafka.server.KafkaRaftServer)

## DDEV

All your ddev configurations for a website should be in **.ddev** folder

```
# FILE PATH: .ddev/docker-compose.kafka.yaml
services:
  kafka:
    image: 'bitnami/kafka:latest'
    ports:
      - '9094:9094'
    container_name: ddev-${DDEV_SITENAME}-kafka
    labels:
      com.ddev.site-name: ${DDEV_SITENAME}
      com.ddev.approot: ${DDEV_APPROOT}

    environment:
      - KAFKA_ENABLE_KRAFT=yes
      - KAFKA_CFG_BROKER_ID=1
      - KAFKA_CFG_NODE_ID=1
      - KAFKA_CFG_PROCESS_ROLES=broker,controller
      - KAFKA_CFG_CONTROLLER_LISTENER_NAMES=CONTROLLER
      - KAFKA_CFG_LISTENERS=PLAINTEXT://:9092,CONTROLLER://:9093,EXTERNAL://:9094
      - KAFKA_CFG_LISTENER_SECURITY_PROTOCOL_MAP=CONTROLLER:PLAINTEXT,PLAINTEXT:PLAINTEXT,EXTERNAL:PLAINTEXT
      - KAFKA_CFG_ADVERTISED_LISTENERS=PLAINTEXT://kafka:9092,EXTERNAL://localhost:9094
      - KAFKA_CFG_CONTROLLER_QUORUM_VOTERS=1@:9093
      - ALLOW_PLAINTEXT_LISTENER=yes
```

Doing a: ddev describe
![grafik](https://github.com/user-attachments/assets/db122b51-8ab1-41bf-9229-5463f814bbb9)


## Let's create a topic

```
bin/kafka-topics.sh --create --partitions 1 --replication-factor 1 --topic orderevent --bootstrap-server localhost:9094
# This should return:
Created topic orderevent.
```

## Produce an event into a topic

```
bin/kafka-console-producer.sh --bootstrap-server localhost:9094 --topic orderevent
>{"key":1}

Just write any message and hit ENTER, CTRL+C to get out
```

## Consume an event from a topic

Just to see how it works in real-time the best is to open a second window and just "hear" on the topic you just created

```
$ bin/kafka-console-consumer.sh --bootstrap-server localhost:9094 --topic orderevent --from-beginning
{"key":1}
^CProcessed a total of 1 messages
```

Further documentation will be added in the WiKi. The goal is to produce and consume this messages in PHP so we can use this in a real life application server.
