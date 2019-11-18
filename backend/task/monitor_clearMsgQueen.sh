#!/bin/bash
##########################################
## 每分钟执行一次检查
## 如果发现处理消息队列的php程序挂了则重启
##########################################

#定义php执行文件
php_bin="/usr/local/php/bin/php"
#定义任务文件
task_script_file="/mnt/projs/ichibanv2/task/cli_clearMsgQueen.php"

execute_php_num=$(ps -ef | grep cli_clearMsgQueen | wc -l)

echo $execute_php_num

if [ $execute_php_num -lt 2 ]; then
  $php_bin $task_script_file >/dev/null 2>&1 &
fi