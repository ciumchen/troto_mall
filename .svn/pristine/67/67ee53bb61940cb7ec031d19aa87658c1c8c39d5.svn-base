#!/bin/bash

cd /data/wwwroot/proj_ichibanv2/ichibanv2
svn update

rsync -aP \
	--exclude=.svn \
	--exclude=attachment \
	--exclude=data \
	--exclude=nbproject \
	--exclude=tools/include.sh \
	* /data/publish/proj_ichibanv2/ichibanv2

cd /data/publish/proj_ichibanv2/ichibanv2
grep \#{{{define_in_ -R * | awk -F':' '{print $1}'|xargs sed -i '/#{{{define_in_devel/,/#}}}/c \'
