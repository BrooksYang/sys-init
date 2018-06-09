# ex-admin
dcu token exchange admin

### 服务器和数据库信息

```
IP: 103.240.181.123
USER: root
PASS: 联系brooks
```

mysql数据库及用户信息
```
DB_HOST=103.240.181.123
DB_PORT=3306
DB_DATABASE=dcuex
DB_USERNAME=dcu
DB_PASSWORD=dcu@2018!)

```
mongodb数据库及用户信息 -用于记录系统日志
```
MONGO_HOST=103.240.181.123
MONGO_PORT=27017
MONGO_DATABASE=dcuex
MONGO_USERNAME=dcu
MONGO_PASSWORD=dcu@2018!)

```

### 安装部署事项mongodb扩展安装

安装项目前需要提前安装mongodb扩展

```
sudo apt-get -y install php7.2-dev php-mongodb
sudo apt-get -y install libcurl4-openssl-dev pkg-config libssl-dev libsslcommon2-dev
sudo pecl install mongodb
```

### mongodb数据库服务的安装及数据库创建并授权用户

安装mongo数据库

```
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6
echo "deb [ arch=amd64,arm64 ] http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.4 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.4.list
sudo apt-get update
sudo apt-get install -y mongodb-org
```
查看mongodb是否启动
```
mongo 
```
停止-启动或重启mongodb服务命令
```
service mongod stop
service mongod start
service mongod restart
```

mongo数据库的创建并授权用户
```
use  dcuex
db.createUser({ user: "dcu", pwd: "dcu@2018!)", roles: [{ role: "dbOwner", db: "dcuex" }] })
db.auth("dcu", "dcu@2018!)")
```



### 日志记录的使用

系统默认仅在生产环境下记录日志-请根据需要修改 .env 环境配置并安装mongodb

```
APP_ENV=local
 或者
APP_ENV=production
```