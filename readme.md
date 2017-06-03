Yii2 simple CRUD
================

ENG
---
#### What is it for:
- Replace the standard CRUD generator, with more advanced functionality.
- Rather, it's not a Create-Read-Update-Delete extension, but a simpler for the Edit-Delete analog
- For more flexible programming

####How it works:
- Create a form tied to our model
- We supplement the controller with a small number of settings (see the example below)
- We get the full cycle of creating / editing and deleting data
- If you just need to output data, just output it (see the example below)
- If you want to save / edit data, just save it (see the example below)

#### What works out of the box:
- ListView \ GridView simply pick up $ dataProvider and output data for the desired model
- ListView \ GridView also picks up $ model for filtering \ pagination \ sorting
- Validation before saving data
- Save a new record to the database when submitting a completed form
- Edit \ Save the saved record to the database
- Deleting an entry (including AJAX)
- Convenient restrictions on editing / creating records

#### Things to know before using:
- Action "/ edit", just create a new entry in the database
- Action "/ edit? Id = 2", will receive data from the database. And in the case of conservation, they will be updated
- The parameter "returnParams" will supply additional URLs to the URL, for example
If there was an action "/ edit" and the parameter is set, the form will be perezagruzitsya to "/ edit? Id = ID",
Where ID is the identifier of the newly saved model (or old one).
- You can not set the "view" option if there is a view in the folder with the same name of the action file

#### What you need to do:
- The code requires more detailed documentation
- The code does not pretend to be the "best", but there is a place to be
- Refactoring is needed, because Works in the production of many projects
- Auto-test coverage

RUS
---
####Для чего это надо:
- Заменить стандартный генератор CRUD, более продвинутым функционалом.
- Скорее, это не Create-Read-Update-Delete расширение, a более простой для Edit-Delete аналог 
- Для более гибкого программирования

####Как это работает:
- Создаем форму привязанную к нашей модели
- Дополняем контроллер небольшим количеством настроек (см.далее пример)
- Получем полный цикл создания\редактрования и удаления данных
- Если нужно просто вывести данные просто их выводим (см.далее пример)
- Если нужно сохранить\редактировать данные просто сохраняем (см.далее пример)

####Что работает из коробки:
- ListView\GridView просто подхватывают $dataProvider и выводят данные для нужной модели
- ListView\GridView также подхватывают $model для фильтрации\пагинации\сортировки
- Валидация перед сохранением данных
- Сохранение новой записи в БД при сабмите заполненной формы
- Правка\сохранение сохраненной записи в БД
- Удаление записи (в том числе AJAX)
- Удобные ограничения на редактирование\создание записей

####Что нужно знать перед использованием:
- Экшн "/edit" , просто создаст новю запись в БД
- Экшн "/edit?id=2" , получит данные из БД. И в случее сохранения их обновит
- Параметр "returnParams" поставлят к URL дополнительные данне например
если был экшн "/edit" и параметр установлен, форма перазагрузится на "/edit?id=ID",
где ID - идентификатор вновь сохранённой модели (или старой). 
- Параметр "view" можно не задавать, если в папке есть вьюшка с одноименным файлом экшена

####Что нужно  нужно сделать:
- Код требует более детальной документации
- Код не претендует на звание "лучшего", но имеет место быть
- Нужен рефакторинг, т.к. работает в продакшене многих проектов
- Покрытие автотестами


Installation
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

Usage example
------
```php


//ENG: This is example of controller from some-real project
//RUS: Экземпляр контроллера из одного проекта

class ClientsController extends Controller {

    public function actions() {
        return [
            //Select all Users from db and show             
            //Экшен выводит всех пользоватлей из БД             
            'list'=>[
                'class'=>View::className(),
                'model'=>Users::className(),
                'pagination'=>[ //if false  - all records
                    'pageSize'=>8,
                ]
            ],
            //
            'edit'=>[
                'class'=>Edit::className(),
                'model'=>Users::className(),
                'scenario'=>'editclients',
                'view'=>'edit',
                'returnParams'=>true,
                'successRoute'=>'/manager/clients/edit',
                'errorRoute'=>'/manager/clients/list',
                'successMessage'=>'Профиль сохранен',
                'errorMessage'=>'Ошибка сохранения профиля',
            ],
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
                'successMessage'=>'Клиент удален',
                'errorMessage'=>'Ошибка удаления клиента',
            ],
        ];
    }

}

//ENG: somewhere in view "list"
//RUS: пример вьюшки для "list"

<?= GridView::widget([
        'dataProvider'=>$dataProvider,
        'filterModel'=>$model,        
])?>


[
    'class'=>ActionColumn::className(),
    'header'=>Html::a('Add', ['/manager/clients/edit']),
    'template'=>'{update} {delete}',
    'urlCreator'=>function($action, $model){
        if($action == 'update'){
            return ['/manager/clients/edit', 'id'=>$model->id];
        }
        elseif($action == 'delete'){
            return ['/manager/clients/delete', 'id'=>$model->id];
        }
    }
]


//ENG: somewhere in view "edit"
//RUS: пример вьюшки для "edit"

<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'lastname')->textInput() ?>
    <?= $form->field($model, 'firstname')->textInput() ?>
    <?= $form->field($model, 'middlename')->textInput() ?>
    <?= Html::submitButton('Save') ?>
<?php ActiveForm::end(); ?>

```


