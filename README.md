# oxid-logstash-component
Demo Component to redirect oxideshop log entries to a logstash instance

![image](https://github.com/user-attachments/assets/63b3c563-ffe1-4a11-a218-703d33357dcc)

For this setup I used the default docker-eshop-sdk, but added an external network ```docker network create oxid-elk``` and the docker-compose-elk-stack.yml to create a default elk-stack that OXID can send log messages to.
Remember you need to connect your docker-eshop-sdk to this network as well. Either by re-creating it with the network defined in docker-compose-elk-stack.yml as well or by connecting it 1 by 1 with commands like this:
```docker network connect oxid-elk docker-eshop-sdk-php-1``` for every container that should send logs to logstash. (This demo only covers OXID, but you also might want to send MySQL logs etc.)

For my tests I slightly adapted the logstash.conf in the logstash_pipeline volume to contain this:
```
input {
  syslog {
    port => 1514
  }
  tcp {
    port => 5000
    codec => json_lines
    type => "jsonlog"
  }
}

output {
  stdout {
    codec => rubydebug
  }
  elasticsearch {
    hosts => ["http://elasticsearch:9200"]
    index => "syslog-%{+yyyy.MM.dd}"
  }
}
```

The stdout/rubydebug is only so you can see every log message in the logstash container log as well. It is not necessary to collect the logs from OXID.
The index name is up to you. It was called syslog before because the plan was to first have OXID log to syslog and then use rsyslog to push all logs from the php container from rsyslog to logstash.
(Therefore the syslog input. This is now also obsolete, but kept for a reference. You can change the SocketHandler to a SyslogHandler to try this, but that would of course require you to also set up rsyslog)

You might also want to define a data view in Kibana to actually see the indexed log lines. That is done via Management->Stack Management->Kibana->Data View in http://localhost.local:5601/ (http://localhost.local:5601/app/management/kibana/dataViews)
Just Create a Data View with a name of your preference, add the indexed defined above as pattern (e.g. syslog-*) and use the @timestamp field for timestamps.

If you can't see anything or the data view can't be created, because there is no index, something probably went wrong and logstash didn't get any data yet.
(you can check that with ```GET _cat/indices?v``` in the Dev Tools>Console
There should be one entry like:
```yellow open   syslog-2025.05.21                                                  A7MEV-R7Qda2EeWqIbCUwg   1   1         11            0       65kb           65kb         65kb```
(The "yellow" means that the setup does not have a fallback node. That should be fine for a demo installation.)
