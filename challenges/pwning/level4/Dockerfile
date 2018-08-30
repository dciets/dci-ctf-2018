FROM ubuntu:18.04
RUN apt-get -y update
RUN apt-get -y install xinetd vim net-tools
RUN useradd challenge
RUN mkdir /app
COPY ./level4 /app/level4
COPY ./config /etc/xinetd.d/level4
COPY ./flag /flag
CMD ["/usr/sbin/xinetd", "-dontfork"]