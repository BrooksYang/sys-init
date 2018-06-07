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

### 日志记录的使用

系统默认仅在生产环境下记录日志-请根据需要修改 .env 环境配置并安装mongodb

```
APP_ENV=local
 或者
APP_ENV=production
```