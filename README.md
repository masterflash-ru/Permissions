 ##Единый сервис контроля доступа к ресурсам Simba по подобию UNIX.

для установки используйте composer require masterflash-ru/permissions
После установки загрузите дамп из папки data


Три варианта записи прав пользователя (все в разработке!)

двоичная | восьмеричная | символьная | права на файл | права на директорию
---------|--------------|------------|---------------|---------------------
000 | 0 | --- | нет | нет  
001 | 1 | --x | выполнение | чтение файлов и их свойств
010 | 2 | -w- | запись | нет
011 | 3 | -wx | запись и выполнение | всё, кроме чтения списка файлов
100 | 4 | r-- | чтение | Можно прочитать содержимое папки
101 | 5 | r-x | чтение и выполнение | Можно зайти в каталог и прочитать его содержимое, удалять или добавлять файлы нельзя.
110 | 6 | rw- | чтение и запись | Можно добавить, удалить, изменить файл папки
111 | 7 | rwx | все права |	все права 

Биты установлены в 1: 
SUID, SGID - при создании новых записей права унаследуются от родительского элемента, иначе от текущего пользователя
Sticky - можно удалить записи только принадлежащие владельцу

Разрешения установленные для папки действуют только на вложения в эту папку. На саму папку действуют разрешения, установленные на папку уровнем выше.

Битовая структура кода доступа:

Имя | Вес бита | описание
----|----------|---------
SUID | 2048 | бит SUID (При создании записи ID юзера наследуются от родительской)
SGID | 1024 | бит SGID (При создании записи ID группы наследуются от родительской)
Sticky | 512 | бит Sticky (Запрещает удалять не владельцу)
r | 256 | чтение для владельца
w | 128 | запись для владельца
x | 64 | запуск/поиск для владельца
r | 32 | чтение для группы
w | 16 | запись для группы
x | 8 | запуск/поиск для группы
r | 4 | чтение для других
w | 2 | запись для других
x | 1 | запуск/поиск для других

в систему пишется простая сумма весов.

Все доступы хранятся в плоской таблице, идентификаторы 1-100 резервированы для админпанели.

В конфиге приложения должны быть настройки кэша:
```php

    'caches' => [
        'DefaultSystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    'cache_dir' => './data/cache',
                    'ttl' => 60*60*2 
                ],
            ],
            'plugins' => [
                [
                    'name' => Serializer::class,
                    'options' => [
                    ],
                ],
            ],
        ],
    ],
```
Для работы с базой в конфиге приложения должно быть объявлено DefaultSystemDb:
```php
......
    "databases"=>[
        //соединение с базой + имя драйвера
        'DefaultSystemDb' => [
            'driver'=>'MysqlPdo',
            //"unix_socket"=>"/tmp/mysql.sock",
            "host"=>"localhost",
            'login'=>"root",
            "password"=>"**********",
            "database"=>"simba4",
            "locale"=>"ru_RU",
            "character"=>"utf8"
        ],
    ],
.....
```

