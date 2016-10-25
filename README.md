### bootapp install
```sh
wget https://github.com/yejune/bootapp/raw/0.2.21/bootapp.phar
sudo mv bootapp.phar /usr/local/bin/bootapp
sudo chmod 755 /usr/local/bin/bootapp
```

### skeleton setup
```sh
cd [workspace]
git clone https://github.com/yejune/skeleton.git .
```

### 개발환경 start
```sh
bootapp up
bootapp task composer install
#키체인 접근 > 로그인 > local.com > 정보 가져오기 > 신뢰: 항상신뢰 적용.
bootapp up
```

### 브라우저 확인
- https://local.com
