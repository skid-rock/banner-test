# Banner test

composer install

```shell
docker-compose exec app composer install
```

консольный клиент

```shell
docker-compose exec app ./bin/console.php
```

## Дизайн

### Требования к системе

* лимит показов банера в месяц задаётся при регистрации банера в админке
* лимит показов банера на пользователя для начала максимально 2 показа всего / затем максимально 2 показа в день
* одинаковое время ответа api на запрос банеров затем сократить время ответа api до 50мс
* равномерный показ банеров в течение месяца
* погрешность

### Верхнеуровнево модули системы

1. api выдачи банеров
2. постоянное хранилище
3. in-memory хранилище
4. админка для добавления банеров
5. хранилище для файлов банеров

### Технологии

1. Любой фреймворк для RESTful api для банеров
2. Любой фреймворк для RESTful api для админ панели, либо готовая админ панель
3. Облачное S3 хранилище для банеров + система оптимизации изображений
4. SQL база данных
5. Redis для in-memory хранилища

### Ожидаемое количество пользователей к системе запроса банеров

* 70 мил / мес
* 2,33 мил / сутки
* дневной трафик составляет 3/4 от общего количества Количество запросов 1,7 мил
* ночной трафик составляет 1/4 от общего количества
* день принимаем равным 10 часам
* Итого: RPS днём составляет 49

### Размер таблицы из одного поля INT под количество записей, согласно объёму пользователей в месяц

* TINYINT: 1 байта * 70 000 000 польз * 100 баннеров / 1024 / 1024 / 1024 = 6,519 Гб
* INT: 4 байта * 70 000 000 польз * 100 баннеров / 1024 / 1024 / 1024 = 26,077 Гб

### Логика обработки запроса api банеров

1. Клиент заходит на страницу
2. На бекенд прилетает запрос с id страницы и с id пользователя если есть
3. Бизнес логика обработки и выдачи данных
4. Клиенту возвращается страница с банерами, либо массив ссылок на баннеры для интеграции, отправляем id пользователя в
   куки

### Бизнес логика обработки и выдачи данных

Проверяем если нет id пользователя, то генерируем id, (используем например UUID)