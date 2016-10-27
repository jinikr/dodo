### bootapp install
```sh
wget https://github.com/yejune/bootapp/raw/0.2.21/bootapp.phar
sudo mv bootapp.phar /usr/local/bin/bootapp
sudo chmod 755 /usr/local/bin/bootapp
```

### skeleton setup
```sh
cd [workspace]
git clone https://github.com/yjkim0/dodo.git .
```

### 개발환경 start
```sh
bootapp up
#키체인 접근 > 로그인 > local.com > 정보 가져오기 > 신뢰: 항상신뢰 적용.
bootapp task composer install
bootapp up
```

### 브라우저 확인
- https://local.com
- https://local.com/info.php
- https://local.com/info

### docker container ssh 접속
```sh
bootapp ls
bootapp ssh webserver
bootapp ssh mysql
```

### Error 로그 확인
```sh
bootapp log webserver
bootapp log mysql
```

### 서버 중지
```sh
bootapp halt
```
