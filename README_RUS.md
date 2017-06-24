Yii2 simple CRUD
================

#### Для чего это надо:
- Заменить стандартный генератор CRUD, более продвинутым функционалом.
- Скорее, это не Create-Read-Update-Delete расширение, a более простой для Edit-Delete аналог 
- Для более гибкого программирования

#### Как это работает:
- Создаем форму привязанную к нашей модели
- Дополняем контроллер небольшим количеством настроек (см.далее пример)
- Получем полный цикл создания\редактрования и удаления данных
- Если нужно просто вывести данные просто их выводим (см.далее пример)
- Если нужно сохранить\редактировать данные просто сохраняем (см.далее пример)

#### Что работает из коробки:
- ListView\GridView просто подхватывают $dataProvider и выводят данные для нужной модели
- ListView\GridView также подхватывают $model для фильтрации\пагинации\сортировки
- Валидация перед сохранением данных
- Сохранение новой записи в БД при сабмите заполненной формы
- Правка\сохранение сохраненной записи в БД
- Удаление записи (в том числе AJAX)
- Удобные ограничения на редактирование\создание записей

#### Что нужно знать перед использованием:
- Экшн "/edit" , просто создаст новю запись в БД
- Экшн "/edit?id=2" , получит данные из БД. И в случее сохранения их обновит
- Параметр "returnParams" поставлят к URL дополнительные данне например
если был экшн "/edit" и параметр установлен, форма перазагрузится на "/edit?id=ID",
где ID - идентификатор вновь сохранённой модели (или старой). 
- Параметр "view" можно не задавать, если в папке есть вьюшка с одноименным файлом экшена

#### Что нужно  нужно сделать:
- Код требует более детальной документации
- Код не претендует на звание "лучшего", но имеет место быть
- Нужен рефакторинг, т.к. работает в продакшене многих проектов
- Покрытие автотестами


Установка
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
Either run
```
php composer.phar require "exru/yii2-crud" "*"
```
or add
```json
"exru/yii2-crud" : "*"
```
to the `require` section of your application's `composer.json` file.

Пример использования
------
```php


//Экземпляр контроллера из одного проекта

class ClientsController extends Controller {

    public function actions() {
        return [

            //Show all users from db
            //Экшен выводит всех пользоватлей из БД             
            'list'=>[
                'class'=>View::className(),
                'model'=>Users::className(),
                'pagination'=>[ //if false  - all records
                    'pageSize'=>8,
                ]
            ],

            //Комбинированное использование настроек
            'edit'=>[
                'class'=>Edit::className(),
                'model'=>Users::className(),
                'scenario'=>'editclients',
                'view'=>'edit',
                'returnParams'=>true,
                'successRoute'=>'/manager/clients/edit',
                'errorRoute'=>'/manager/clients/list',
                'successMessage'=>'Profile saved',
                'errorMessage'=>'Error occured',
            ],

            //Удаление пользователя с необычным редиректом
            'delete'=>[
                'class'=>Delete::className(),
                'model'=>Users::className(),
                'scenario'=>'delete',
                'successRoute'=>function($model, $action){
                    if($action->lastInfo->action == 'edit'){
                        return \yii\helpers\Url::to(['/manager/clients/list']);
                    }
                    return \Yii::$app->request->referrer;
                },
                'errorRoute'=>'/manager/clients/list',
                'successMessage'=>'Client deleted',
                'errorMessage'=>'Error occured',
            ],
        ];
    }

}
```

```php
//пример вьюшки для "list"

<?= GridView::widget([
        'dataProvider'=>$dataProvider,
        'filterModel'=>$model,        
]);

//Пример кнопок редактирования для ГРИДА
//[
//    'class'=>ActionColumn::className(),
//    'header'=>Html::a('Add', ['/manager/clients/edit']),
//    'template'=>'{update} {delete}',
//    'urlCreator'=>function($action, $model){
//        if($action == 'update'){
//            return ['/manager/clients/edit', 'id'=>$model->id];
//        }
//        elseif($action == 'delete'){
//            return ['/manager/clients/delete', 'id'=>$model->id];
//        }
//    }
//]


?>
```

```PHP
//пример вьюшки для "edit", переменная $model - передается автоматически

<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'lastname')->textInput() ?>
    <?= $form->field($model, 'firstname')->textInput() ?>
    <?= $form->field($model, 'middlename')->textInput() ?>
    <?= Html::submitButton('Save') ?>
<?php ActiveForm::end(); ?>

```


