FROM ubuntu:18.04
RUN apt-get -y update
RUN apt-get -y install xinetd vim net-tools
RUN useradd challenge
RUN mkdir /app
COPY ./level1 /app/level1
COPY ./config /etc/xinetd.d/level1
COPY ./flag /flag
CMD ["/usr/sbin/xinetd", "-dontfork"]