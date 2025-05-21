# oxid-logstash-component
Demo Component to redirect oxideshop log entries to a logstash instance

![image](https://github.com/user-attachments/assets/63b3c563-ffe1-4a11-a218-703d33357dcc)

For this setup I used the default docker-eshop-sdk, but added an external network ```docker network create oxid-elk``` and the docker-compose-elk-stack.yml to create a default elk-stack that OXID can send log messages to.
Remember you need to connect your docker-eshop-sdk to this network as well. Either by re-creating it with the network defined in docker-compose-elk-stack.yml as well or by connecting it 1 by 1 with commands like this:
```docker network connect oxid-elk docker-eshop-sdk-php-1``` for every container that should send logs to logstash. (This demo only covers OXID, but you also might want to send MySQL logs etc.)
