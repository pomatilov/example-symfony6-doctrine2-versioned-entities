# example-symfony6-doctrine2-versioned-entities

Пример проекта на PHP 8 + Symfony 6 + PostgreSQL, описывающий логику работы с версионными таблицами через Doctrine

При инициации запроса, с помощью SetTransactionIdentifierListener, создается запись в таблице Transaction. По окончании запроса статус записи в Transaction обновляется в соответствии с HTTP кодом ответа (при успешном выполнении запроса - success, в случае ошибки - failed)

При создании нового объекта версионной таблицы, автоматически, через VersionedEntityListener, проставляется CAX (идентификатор транзакции, в рамках которой была создана запись). Аналогично, через VersionedEntityListener, при изменении или инвалидации записи, проставляются EAX и IAX (идентификатор транзакции обновления и идентификатор транзакции инвалидации записи соответственно).

Все запросы, формируемые через Doctrine, будут автоматически исключать невалидные записи, благодаря VersionedEntityValidFilter, который включается при помощи BeforeRequestListener.

VersionedEntityVersionFilter в свою очередь позволяет фильтровать записи по дате версии записи. Он также включается по умолчанию в BeforeRequestListener, но для некоторых запросов необходимо предусмотреть его отключение (например, при необходимости получить все версии)

Репозиторий версионной сущности должен реализовывать интерфейс VersionEntityRepositoryInterface.

Для реализации запросов к версионной сущности также предусмотрены общие методы, размещенные в VersionEntityRepositoryTrait.
Пример репозитория версионной сущности - CompanyRepository