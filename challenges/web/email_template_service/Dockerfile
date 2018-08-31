FROM openjdk
RUN apt-get update
RUN apt-get -y install netcat
RUN mkdir /app
RUN mkdir /db
COPY ./ThymeServer/build/libs/ThymeServer.jar /app/app.jar
COPY ./templates.db /db/templates.db
COPY ./flag /this_is_a_long_flag_name_very_long
USER 1000:1000
ENTRYPOINT ["java", "-Djava.security.egd=file:/dev/./urandom", "-jar","/app/app.jar"]