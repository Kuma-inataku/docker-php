# 環境
- WSL2
- Docker
- Apache(php:apache)
- PHP(php:8.2.1)

## 参考先
- https://idealump.com/service/lab/95
    - Dockerの初歩の初歩. hello worldするだけ
↓
- https://qiita.com/ryamate/items/74c57a290b78c812f089
    - xdebugを導入する環境

### dockerhub
- https://hub.docker.com/_/php/tags

## 動作確認URL
http://localhost:8080/

# schema spy使用
## メモ
### `Connection to localhost:5432 refused`になる
```
inataku@Takuya1790:~/docker-php$ docker run -v "$PWD/output:/output" --net="docker-php_default" -v "$PWD/schemaspy/schemaspy.properties:/schemaspy.properties" schemaspy/schemaspy:snapshot
Using drivers:jtds-1.3.1.jar, mariadb-java-client-1.1.10.jar
mysql-connector-java-8.0.28.jar, postgresql-42.3.5.jar
  ____       _                          ____
 / ___|  ___| |__   ___ _ __ ___   __ _/ ___| _ __  _   _
 \___ \ / __| '_ \ / _ \ '_ ` _ \ / _` \___ \| '_ \| | | |
  ___) | (__| | | |  __/ | | | | | (_| |___) | |_) | |_| |
 |____/ \___|_| |_|\___|_| |_| |_|\__,_|____/| .__/ \__, |
                                             |_|    |___/

                                              6.1.1-SNAPSHOT

SchemaSpy generates an HTML representation of a database schema's relationships.
SchemaSpy comes with ABSOLUTELY NO WARRANTY.
SchemaSpy is free software and can be redistributed under the conditions of LGPL version 3 or later.
http://www.gnu.org/licenses/

INFO  - Starting Main v6.1.1-SNAPSHOT on ac035303fcff with PID 1 (/usr/local/lib/schemaspy/schemaspy-6.1.1-SNAPSHOT.jar started by java in /)
INFO  - The following profiles are active: default
INFO  - Found configuration file: schemaspy.properties
INFO  - Started Main in 1.817 seconds (JVM running for 2.757)
INFO  - Loaded configuration from schemaspy.properties
INFO  - Starting schema analysis
WARN  - Connection Failure
Failed to connect to database URL [jdbc:postgresql://localhost:5432/mydb] Connection to localhost:5432 refused. Check that the hostname and port are correct and that the postmaster is accepting TCP/IP connections.
INFO  - StackTraces have been omitted, use `-debug` when executing SchemaSpy to see them
```
- `Connection to localhost:5432 refused.`なので「`localhost:5432`への接続に失敗」とのこと。
- netstatを見てもpostgresのポート5432は動いているのになんで？
```
inataku@Takuya1790:~/docker-php$ NETSTAT.EXE -an | grep 5432
  TCP    0.0.0.0:5432           0.0.0.0:0              LISTENING
  TCP    [::]:5432              [::]:0                 LISTENING
  TCP    [::1]:5432             [::]:0                 LISTENING
```
- 参考: https://zenn.dev/ryo_t/articles/3be7a5ca39d496
  - schemaspy/schemaspy.propertiesの`schemaspy.host`を`localhost` -> `postgres`(コンテナ名)で解決
  - 確かにDockerコンテナ上のpostgresはlocalhost(hostOS上)ではないな。。。

### `Unable to create directory /output/tables`になる
```
inataku@Takuya1790:~/docker-php$ docker run -v "$PWD/output:/output" --net="docker-php_default" -v "$PWD/schemaspy/schemaspy.properties:/schemaspy.properties" schemaspy/schemaspy:snapshot
Using drivers:jtds-1.3.1.jar, mariadb-java-client-1.1.10.jar
mysql-connector-java-8.0.28.jar, postgresql-42.3.5.jar
  ____       _                          ____
 / ___|  ___| |__   ___ _ __ ___   __ _/ ___| _ __  _   _
 \___ \ / __| '_ \ / _ \ '_ ` _ \ / _` \___ \| '_ \| | | |
  ___) | (__| | | |  __/ | | | | | (_| |___) | |_) | |_| |
 |____/ \___|_| |_|\___|_| |_| |_|\__,_|____/| .__/ \__, |
                                             |_|    |___/

                                              6.1.1-SNAPSHOT

SchemaSpy generates an HTML representation of a database schema's relationships.
SchemaSpy comes with ABSOLUTELY NO WARRANTY.
SchemaSpy is free software and can be redistributed under the conditions of LGPL version 3 or later.
http://www.gnu.org/licenses/

INFO  - Starting Main v6.1.1-SNAPSHOT on 5326b5225095 with PID 1 (/usr/local/lib/schemaspy/schemaspy-6.1.1-SNAPSHOT.jar started by java in /)
INFO  - The following profiles are active: default
INFO  - Found configuration file: schemaspy.properties
INFO  - Started Main in 1.821 seconds (JVM running for 2.87)
INFO  - Loaded configuration from schemaspy.properties
INFO  - Starting schema analysis
ERROR - IOException
Unable to create directory /output/tables
INFO  - StackTraces have been omitted, use `-debug` when executing SchemaSpy to see them
```
- schemaspy は一般ユーザーで実行されるようにイメージが作成されているため
  - 参考: https://qiita.com/ngyuki/items/4efa0734e8d8582bfc16
- 出力先の書き込み権限を変更(rootで実行は現実的ではないので…)
```
sudo chmod 777 output
```
