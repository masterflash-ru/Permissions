 Единый сервис контроля доступа к ресурсам Simba по подобию UNIX.

Три варианта записи прав пользователя 

двоичная | восьмеричная | символьная | права на файл | права на директорию
---------|--------------|------------|---------------|---------------------
000 | 0 | --- | нет | нет  
001 |	1 |	--x |	выполнение | чтение файлов и их свойств
010 |	2 |	-w- |	запись | нет
011 |	3 |	-wx |	запись и выполнение |	всё, кроме чтения списка файлов
100 |	4 |	r-- |	чтение | чтение имён файлов
101 |	5 |	r-x |	чтение и выполнение | доступ на чтение,  свойств файлов
110 |	6 |	rw- |	чтение и запись | чтение имён файлов
111 |	7 |	rwx |	все права |	все права 

Биты установлены в 1: 
SUID, SGID - при создании новых записей права унаследуются от родительского элемента, иначе от текущего пользователя
Sticky - можно удалить записи только принадлежащие владельцу

Битовая структура кода доступа:

Имя | описание
----|---------
SUID | бит SUID (При создании записи ID юзера наследуются от родительской)
SGID | бит SGID (При создании записи ID группы наследуются от родительской)
Sticky | бит Sticky
r | чтение для владельца
w | запись для владельца
x | запуск/поиск для владельца
r | чтение для группы
w | запись для группы
x | запуск/поиск для группы
r | чтение для других
w | запись для других
x | запуск/поиск для других

