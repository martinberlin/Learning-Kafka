<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conf = new RdKafka\Conf();
// Configure the group.id. All consumer with the same group.id will consume
// different partitions.
$conf->set('group.id', '1');
// Initial list of Kafka brokers
$conf->set('metadata.broker.list', '127.0.0.1:9094');

// false will not store the read position in the offset store
// true  does not read again messages
$conf->set('enable.auto.offset.store', 'false');
// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'earliest': start from the beginning
$conf->set('auto.offset.reset', 'earliest');

// Emit EOF event when reaching the end of a partition
$conf->set('enable.partition.eof', 'true');

$consumer = new RdKafka\KafkaConsumer($conf);
ob_start();
$myTopic = "test";
// Subscribe to topic 'test'
echo "subscribing to topic <b>$myTopic</b><br>";
ob_flush();
$consumer->subscribe([$myTopic]);
ob_flush();

$loop =0;
// Consume some messages
while ($loop<10) {
    $message = $consumer->consume(3500);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
		    echo($message->offset.":".$message->payload."<br>");
		    ob_flush();
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more<br>";
            ob_flush();
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out $loop<br>";
            ob_flush();
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
            ob_flush();
            break;
    }
    $loop++;
}
