#!/bin/bash
#remote update с сервера gitlab, на сервер в задании из .gitlab-ci
set -e
DIR=$1
COMMIT=$2
BRANCH=$3
cd $DIR
dateUpdate=`date '+%F %X'`

echo "stash"
git stash
echo "fetch"
git fetch
echo "checkout to $COMMIT"
git checkout -f $COMMIT
echo "clean untracked"
#git clean -df


cat > $DIR/app/gitinfo.json <<EOL
{
    "branch" : "$BRANCH",
    "commit" : "${COMMIT:0:7}",
    "dateUpdate" : "$dateUpdate"
}
EOL

. $DIR/.env


#!/bin/bash
#remote update с сервера gitlab, на сервер в задании из .gitlab-ci
set -e
DIR=$1
COMMIT=$2
BRANCH=$3
cd $DIR
dateUpdate=`date '+%F %X'`

echo "stash"
git stash
echo "fetch"
git fetch
echo "checkout to $COMMIT"
git checkout -f $COMMIT
echo "clean untracked"
#git clean -df


cat > $DIR/app/gitinfo.json <<EOL
{
    "branch" : "$BRANCH",
    "commit" : "${COMMIT:0:7}",
    "dateUpdate" : "$dateUpdate"
}
EOL

. $DIR/.env


if [ `docker ps --format '{{.Names}}' --filter="name=$DOCKER_CONTAINER_NAME" --filter='status=running' | wc -l` -eq 0 ]; then
    echo "starting docker $DOCKER_CONTAINER_NAME"
        docker start $DOCKER_CONTAINER_NAME
        docker exec -i $DOCKER_CONTAINER_NAME /var/www/app/utils/service.sh start
fi
    echo "starting docker $DOCKER_CONTAINER_NAME"
        docker start $DOCKER_CONTAINER_NAME
        docker exec -i $DOCKER_CONTAINER_NAME /var/www/app/utils/service.sh start
fi