FROM ubuntu:18.04
RUN apt-get -y update
RUN apt-get -y install xinetd vim net-tools
RUN useradd challenge
RUN mkdir /app
COPY ./echo_server /app/echo_server
COPY ./config /etc/xinetd.d/echo-server
COPY ./flag /flag
CMD ["/usr/sbin/xinetd", "-dontfork"]