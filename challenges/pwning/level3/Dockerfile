FROM ubuntu:18.04
RUN apt-get -y update
RUN apt-get -y install xinetd vim net-tools
RUN useradd challenge
RUN mkdir /app
COPY ./level3 /app/level3
COPY ./config /etc/xinetd.d/level3
COPY ./flag /flag
CMD ["/usr/sbin/xinetd", "-dontfork"]