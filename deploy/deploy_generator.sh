#!/bin/bash

GEN_PATH=../src/webapp/python
TEMP_PATH=/tmp
SSH_USER=u\\johanng84
REMOTE_PATH=spaceprojq75/html/SPRINT/python

echo "Creating deployment archive"
tar --exclude=settings.py \
	-cf $TEMP_PATH/code_gen_deploy.tar -C $GEN_PATH .

if [ -d "$TEMP_PATH/local_deployment" ]; then
	rm -rf $TEMP_PATH/local_deployment
fi
mkdir -p $TEMP_PATH/local_deployment

echo "Extracting archive locally and prepare copying"
tar -xf $TEMP_PATH/code_gen_deploy.tar -C $TEMP_PATH/local_deployment .

echo "Deploying to webspace"
scp -r $TEMP_PATH/local_deployment/* $SSH_USER@webspace-access.univie.ac.at:$REMOTE_PATH

