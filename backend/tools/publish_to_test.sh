#!/bin/bash

cd /data/publish/proj_ichibanv2/ichibanv2

rsync -aP \
	--exclude=.svn \
	--exclude=advanced/frontend/runtime \
	--exclude=advanced/frontend/web/assets \
	--exclude=advanced/backend/runtime \
	--exclude=advanced/backend/web/assets \
	--exclude=tools/include.sh \
	* root@120.25.210.235:/home/wwwroot/ichibanv2
