create database if not exists logs;

CREATE TABLE if not exists logs.kafka_seaslog
(
    `appname`        String,
    `datetime`       DateTime,
    `level`          String,
    `request_uri`    String,
    `request_method` String,
    `clientip`       String,
    `requestid`      String,
    `filename`       String,
    `memoryusage`    UInt64,
    `message`        String
) ENGINE = Kafka SETTINGS kafka_broker_list = 'kafka:29092',
    kafka_topic_list = 'seaslog',
    kafka_group_name = 'clickhouse',
    kafka_format = 'JSONEachRow',
    kafka_skip_broken_messages = 1,
    kafka_num_consumers = 1;
--消费者数量根据情况自定义

CREATE TABLE if not exists logs.seaslog
(
    `appname`        String,
    `datetime`       DateTime,
    `level`          String,
    `request_uri`    String,
    `request_method` String,
    `clientip`       String,
    `requestid`      String,
    `filename`       String,
    `memoryusage`    UInt64,
    `message`        String
) ENGINE = MergeTree PARTITION BY toYYYYMM(datetime)
      ORDER BY
          (datetime,
           appname) SETTINGS index_granularity = 8192;

CREATE MATERIALIZED VIEW if not exists logs.consumer_seaslog TO logs.seaslog AS
SELECT appname,
       datetime,
       level,
       request_uri,
       request_method,
       clientip,
       requestid,
       filename,
       memoryusage,
       message
FROM logs.kafka_seaslog;

