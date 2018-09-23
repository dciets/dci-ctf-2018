FROM node:8.11.4-alpine

ADD ./app /opt/app

WORKDIR /opt/app

RUN npm install

RUN chmod a-r get_flag
RUN chmod a-w get_flag

USER node

CMD npx websocketify node server