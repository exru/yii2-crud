Yii2 simple CRUD
================

#### What is it for:
- Replace the standard CRUD generator, with more advanced functionality.
- Rather, it's not a Create-Read-Update-Delete extension, but a simpler for the Edit-Delete analog
- For more flexible programming

#### How it works:
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

#### TODO:
- The code requires more detailed documentation
- The code does not pretend to be the "best", but there is a place to be
- Refactoring is needed, because Works in the production of many projects
- Auto-test coverage


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


//This is example of controller from some-real project

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

            //The example is using various configuration
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

            //Deleting of user with complex success redirect
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
//somewhere in view "list"

<?= GridView::widget([
        'dataProvider'=>$dataProvider,
        'filterModel'=>$model,        
]);

//Example of GridView buttons for edit models
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
//somewhere in view "edit", $model - passed autamatically

<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'lastname')->textInput() ?>
    <?= $form->field($model, 'firstname')->textInput() ?>
    <?= $form->field($model, 'middlename')->textInput() ?>
    <?= Html::submitButton('Save') ?>
<?php ActiveForm::end(); ?>

```


