#!/bin/bash
echo ""
echo "Проверка минимальных требований к системе..."

if [ "$(which sudo | wc -l)" -eq 0 ]; then
    notice "Устанавливаем sudo";
	apt-get update
	apt-get -y install sudo
	    if [ $? -ne 0 ]; then
		    error "sudo не был установлен"
		    exit 1
	    fi
fi

if  [ "$(which docker | wc -l)" -eq 0 ]; then
    notice "Устанавливаем docker";
    sudo apt-get -y install \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg2 \
    software-properties-common ;
    curl -fsSL "https://download.docker.com/linux/$(. /etc/os-release; echo "$ID")/gpg" | sudo apt-key add - ;
    sudo add-apt-repository \
    "deb [arch=amd64] https://download.docker.com/linux/$(. /etc/os-release; echo "$ID") \
    $(lsb_release -cs) \
    stable" ;
    sudo apt-get update ;
    sudo apt-get install docker-ce ;
  	   if [ $? -ne 0 ]; then
		   error "docker не был установлен"
		   exit 1
	   fi
fi

if [ "$(which curl | wc -l)" -eq 0 ]; then
    notice "Устанавливаем curl";
        apt-get update
        apt-get -y install curl
            if [ $? -ne 0 ]; then
                    error "curl не был установлен"
                    exit 1
            fi
fi

if [ "$(which make | wc -l)" -eq 0 ]; then
    notice "Устанавливаем make";
        apt-get update
        apt-get -y install make
            if [ $? -ne 0 ]; then
                    error "make не был установлен"
                    exit 1
            fi
fi

echo "...Прошла успешно"
echo ""
