define usage
Usage: make COMMAND

Commands:
  install    Create Docker container and install application dependencies
  start      Start container + start services
  uninstall  Remove container
  restart    Restart container + restart services
  status     Show container and services status
  test       Test installation

endef
export usage

help:
	@echo "$$usage"

start:
	@./docker/scripts/start.sh

install:
	@./docker/scripts/deps.sh
	@./docker/scripts/install.sh

uninstall:
	@./docker/scripts/uninstall.sh

restart:
	@./docker/scripts/restart.sh

test:
	cp .env.example .env
	@./docker/scripts/install.sh --test='1'

status:
	@./docker/scripts/status.sh
