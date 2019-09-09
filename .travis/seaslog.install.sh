#!/usr/bin/env bash
wget https://github.com/SeasX/SeasLog/archive/${SL_VERSION}.tar.gz
mkdir -p SeasLog
tar -xf SeasLog.tar.gz -C SeasLog --strip-components=1
rm SeasLog.tar.gz
cd SeasLog
phpize
./configure
make -j$(nproc)
make install