# Системные требования 
- php 5.6.0 + (проверялось 7.3)
- PostgreSql 11.3
- PostGis 2.5.1
- Apache  2.4
- composer 2.0.7

# Установка

## Преднастройка

### Установка PostgreSql
> apt install postgresql

#### Подключение админом
> su postgres
> psql -p 5434

#### Создание базы и пользователя
``` sql
CREATE USER testgeo WITH CREATEDB PASSWORD 'pass' LOGIN;
CREATE DATABASE testgeo WITH OWNER testgeo ;
GRANT CONNECT ON DATABASE testgeo TO testgeo;
GRANT ALL ON DATABASE testgeo TO testgeo;
```
#### Подключение к базе созданым пользователем
> psql -d testgeo -p 5434 

#### Подключение PostGis
``` sql
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_sfcgal;
CREATE EXTENSION fuzzystrmatch; --needed for postgis_tiger_geocoder
--optional used by postgis_tiger_geocoder, or can be used standalone
CREATE EXTENSION address_standardizer;
CREATE EXTENSION address_standardizer_data_us;
CREATE EXTENSION postgis_tiger_geocoder;
CREATE EXTENSION postgis_topology;
```

### Настройка апача
В разделе добавить строку иначе не работает dotenv
> SetEnv PWD path/to/root/project

##  Настройка проекта

### Клонирование проекта
>git clone https://github.com/elemenarysan/elemenarysan.git

### Подтянуть библиотеки
> composer update

### Скопировать файл переменных
>cp .env.example .env

### Заполнить поля файла .env
 
## Миграция базы
>./yii migrate/up

# Использование

## Настройка данных
### Импортировать шейп
> ./yii geo/import-geo  -f Геозона.kml 

### Импортировать кадастры
> ./yii geo/import-cadastr -f Кадастровые\ номера.csv

## Получение сведений
### Получить кадастры входящие в зону
> ./yii geo/check-in-zone  "Untitled Polygon"

### Получить кадастры Не входящие в зону
> ./yii geo/check-out-zone  "Untitled Polygon"

### Получить GeoJson зоны и номера
> http://localhost/api/map?cadastr_number=1420988400:01:009:0027&zone_name=Untitled%20Polygon

#### Посмотрет полученый geojson
> https://geojsonlint.com/


