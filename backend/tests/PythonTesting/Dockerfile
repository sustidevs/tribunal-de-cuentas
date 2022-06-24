FROM python:3.9

COPY . /app
WORKDIR /app

RUN pip install --upgrade pip
RUN pip install -r req_bot.txt

CMD python /app/tribunal_service.py