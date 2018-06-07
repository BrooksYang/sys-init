# ex-admin
dcu token exchange admin

### mongodb服务器和数据库信息

```
IP: 103.240.181.123
USER: root
PASS: 联系brooks
```

mongodb数据库信息
```
MONGODB 用于记录系统日志
DB: dcuex
USER: dcu
PASS: dcu@2018!)
```

### 安装部署事项mongodb扩展安装


安装项目项目前需要提前安装mongodb扩展
```
sudo apt-get -y install php7.1-dev php-mongodb
sudo apt-get -y install libcurl4-openssl-dev pkg-config libssl-dev libsslcommon2-dev
sudo pecl install mongodb
```

### 日志记录的使用

```
系统默认仅在生产环境下记录日志-请根据需要修改 .env 环境配置
APP_ENV=local
 或者
APP_ENV=production
```