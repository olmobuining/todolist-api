FROM mysql:5.7.22

# COPY ./sql-scripts/ /docker-entrypoint-initdb.d/
COPY my.cnf /etc/mysql/my.cnf

RUN echo "USE mysql;" > /docker-entrypoint-initdb.d/1-timezones.sql &&  mysql_tzinfo_to_sql /usr/share/zoneinfo >> /docker-entrypoint-initdb.d/1-timezones.sql