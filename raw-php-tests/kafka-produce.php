<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conf = new RdKafka\Conf();
// Initial list of Kafka brokers
$conf->set('metadata.broker.list', '127.0.0.1:9094');

//If you need to produce exactly once and want to keep the original produce order, uncomment the line below
$conf->set('enable.idempotence', 'true');
$producer = new RdKafka\Producer($conf);

$topic = $producer->newTopic("test");

for ($i = 0; $i < 10; $i++) {
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, "Message $i");
    $producer->poll(0);
}

for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
    $result = $producer->flush(10000);
    if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
        break;
    }
}

if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
    throw new \RuntimeException('Was unable to flush, messages might be lost!');
}
