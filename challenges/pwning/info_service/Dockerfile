FROM ubuntu:16.04
RUN apt-get -y update
RUN apt-get -y install xinetd vim net-tools
RUN useradd challenge
RUN mkdir /app
COPY ./info_service /app/info_service
COPY ./config /etc/xinetd.d/info_service
COPY ./flag /flag
CMD ["/usr/sbin/xinetd", "-dontfork"]