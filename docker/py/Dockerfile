FROM centos:centos7

# 各パッケージをインストール
RUN yum -y update
RUN yum -y groupinstall "Development Tools"
RUN yum -y install \ 
            gcc-c++ \
            automake \
            make \
            zip \
            wget \
            tar \
            gcc \
            zlib \
            zlib-devel \
            bzip2 \
            bzip2-devel \
            git \
            python-devel \
            python3-devel \
            mysql-devel \
            epel-release \
            python-pip

# sslエラー解消のためのパッケージ
RUN yum -y install zlib-devel libffi-devel bzip2-devel openssl-devel ncurses-devel sqlite-devel readline-devel tk-devel gdbm-devel libuuid-devel xz-devel

# playwright用にインストール
RUN yum -y install libappindicator-gtk3 liberation-fonts libXScrnSaver GConf2 alsa-lib

# Python3.9.4
WORKDIR /root
RUN wget https://www.python.org/ftp/python/3.9.4/Python-3.9.4.tgz
RUN tar xzvf Python-3.9.4.tgz

# makeでインストール
WORKDIR ./Python-3.9.4
RUN ./configure --with-threads
RUN make install

RUN pip3 install --upgrade pip
RUN pip3 install --upgrade setuptools

RUN mkdir -p /var/www/python
COPY docker/py/requirements.txt /var/www/python
WORKDIR /var/www/python

# requirements.txtのライブラリをインストール
RUN pip3 install -r requirements.txt

RUN pip3 install mysql-connector-python==8.0.31

RUN playwright install