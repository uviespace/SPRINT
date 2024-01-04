#!/bin/bash

WEBAPP_PATH=../src/webapp
TEMP_PATH=/tmp
SSH_USER=u\\johanng84
REMOTE_PATH=user/html/SPRINT_NEXT

echo "Creating deployment archive"
tar --exclude=db_config.php \
	--exclude=config.js \
	--exclude=*~ \
	--exclude=python \
	--exclude=python/* \
	--exclude=uploads \
	--exclude=uploads/* \
	-cf $TEMP_PATH/web_app_deploy.tar -C $WEBAPP_PATH .

if [ -d "$TEMP_PATH/local_deployment" ]; then
	rm -rf $TEMP_PATH/local_deployment
fi
mkdir -p $TEMP_PATH/local_deployment

echo "Extracting archive locally and prepare copying"
tar -xf $TEMP_PATH/web_app_deploy.tar -C $TEMP_PATH/local_deployment


echo "Deploying to webspace"
scp -r $TEMP_PATH/local_deployment/* $SSH_USER@webspace-access:$REMOTE_PATH

