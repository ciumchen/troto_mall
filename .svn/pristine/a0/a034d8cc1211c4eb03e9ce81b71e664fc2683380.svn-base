#!/bin/bash
#使用说明：
#必须先执行(centos下) yum install ffmpeg -y，确保存在/usr/bin/ffmpeg 命令工具
# downAmr为下载的音频文件存放目录；/amr为转换成功存放原始文件目录；/mp3为转换后供调用目录
# 此脚本需要常驻运行，启动命令/bin/bash /home/audit_convet.sh >/dev/null 2>&1 &
ffmpeg="/usr/bin/ffmpeg"

cd /mnt/projs/ichibanv2/downAmr/


while true
do
    find ./ *.arm 
    find ./ -name "*.amr" -exec $ffmpeg -i {} {}.mp3 \;
    find ./ -name "*.amr.mp3" -exec rename .amr.mp3 .mp3 {} \;

    mv *.amr ../amr/
    mv *.mp3 ../mp3/
    sleep 0.1
done
