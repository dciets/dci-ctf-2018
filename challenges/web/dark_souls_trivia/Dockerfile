FROM python:3.6
RUN pip3 install flask
RUN mkdir -p /app/templates
WORKDIR /app
COPY ./app.py ./UserData.py ./ItemList.py /app/
COPY ./templates /app/templates/
COPY ./flag /in_awe_at_the_size_of_this_flag_absolute_unit
USER 1000:1000
CMD ["flask", "run", "--host", "0.0.0.0"]