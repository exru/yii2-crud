<?php

/*
 * Copyright (C) 2017 exru.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace exru\crud;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

abstract class CrudAction extends \yii\base\Action {

    /**
     * Model
     * Модель
     * @var ActiveRecord;
     */
    private $_model = null;
    
    /**
     * Primary key \ ID
     * @var string 
     */
    private $_id = null;
    
    /**
     * Extra attributes to be loaded
     * Дополнительно устанавливаемые атрибуты модели   
     * @var mixed 
     */
    public $attr = null;
    
    /**
     * Name of class of model
     * Имя класса модели
     * @var mixed 
     */
    public $model = '';
    
    /**
     * Disable ability to create new records
     * Запретить содавать новые записи
     * @var boolean 
     */
    public $createable = true;
    
    /**
     * Disable ability to edit records
     * Запретить редактировать записи
     * @var boolean 
     */
    public $editable = true;
    
    /**
     * Disable ability to delete records
     * Запретить удалять записи
     * @var boolean 
     */
    public $deleteable = true;
    
    /**
     * Greedy loading:  Model::find()->joinWith(['relModel']);
     * Жадная загрузка: Model::find()->joinWith(['relModel']);
     * @var mixed 
     */
    public $relationModels = [];
    
    /**
     * Model scenario
     * Сценарий модели
     * @var string
     */
    public $scenario = null;
    
    /**
     * Success URL where to be return to
     * URL при успешной операции на 
     * null      - previous URL; //предыдущий URL
     * string    - '/site/index' will be eval as Url::to('/site/index');
     * array     - ['/site/index', 'id'=>1] will be eval as Url::to(['/site/index', 'id'=>1]);
     * callable  - function(){}; must be return an URL //функция должна возвратить правильный URL 
     * @var null|string|array|callable 
     */
    public $successRoute;
    
    /**
     * URL to be returned when error, @see $successRoute
     * URL при ошибке, см. описание $successRoute
     * If value is 'true' then $errorRoute take value of $successRoute //берет значение из $successRoute
     * @var null|string|array|callable|boolean
     */
    public $errorRoute;
    
    /**
     * View for render
     * Вью для рендеринга
     * @var string 
     */
    public $view;
    
    /**
     * Parametrs passed to view
     * Доп.параметры передаваемые во вью
     * @var mixed 
     */
    public $params = [];
    
    /**
     * Name of model in view 
     * Имя модели во вью
     * @var string 
     */
    public $delegationModelName = 'model'; //
    
    /**
     * Name of dataProvider in view 
     * Имя провайдера данных во вью
     * @var string 
     */
    public $delegationDataProvider = 'dataProvider';
    
    /**
     * Pagination
     * Пагинация
     * @var mixed 
     */
    public $pagination = false;
    
    /**
     * Sorting
     * Сортировка
     * @var mixed 
     */
    public $sort = null;
    
    /**
     * Order by for a query
     * Сортировка в запросе
     * @var mixed 
     */
    public $order = null;
    
    /**
     * Extra params for dataProvider
     * Параметры для поиска в датапровадере
     * @var array 
     */
    public $queryParams = [];
    
    /**
     * Extra params for searching in model
     * Параметры для поиска в модели
     * @var array 
     */
    public $searchParams = [];
    
    /**
     * If model has $linkMethod then it be called;
     * Функция из модели для линковки данных 
     * @var string 
     */
    public $linkMethod = false;
    
    /**
     * Disable load data to model before validation
     * Не загружать модель перед валидацией
     * @var type 
     */
    public $noLoad = false;
    
    /**
     * Exit silently when it's ok. (Useful with AJAX)
     * Только сохранить\удалить модель. (исп. AJAX)
     * @var boolean 
     */
    public $silentMode = false;
    
    /**
     * Add params to URL after operation (like ID)
     * Добавлять параметры к URL при успешной операции (например ID)
     * @var boolean 
     */
    public $returnParams = false;
    
    /**
     * Render instead of return
     * Вместо редиректа возвращать вью
     * @var boolean 
     */
    public $returnView = false;
    
    /**
     * Debug mode
     * Режим отладки
     * @var boolean 
     */
    public $debug = false;
    
    /**
     * Mode of debug
     * Тип отладки
     * 0 - var_dump
     * 1 - log_file
     * @var integer
     */
    public $debug_mode = 0;
    
    /**
     * Message if success
     * Сообщение при успешной операции
     * @var string 
     */
    public $successMessage = 'Данные сохранены';
    
    /**
     * Message if error
     * Сообщение при успешной операции
     * @var string 
     */
    public $errorMessage = 'Ошибка сохранения данных';
    
    /**
     * Message if data not found
     * Сообщение если не удалось загрузить данные
     * @var string 
     */
    public $findMessage = 'Данные не загружены';
    
    /**
     * Message if action is forbidden
     * Сообщение если действие запрещено
     * @var string 
     */
    public $forbiddenMessage = 'Дествие запрещено';
    
    /**
     * Closure after find data
     * Функция после выборки
     * @var collable 
     */
    public $afterFind = null;
    
    /**
     * Closure before save
     * Функция перед сохранением
     * @var collable 
     */
    public $beforeSave = null;
    
    /**
     * Closure after save
     * Функция после сохранения
     * @var collable 
     */
    public $afterSave = null;
    
    /**
     * Closure after delete
     * Функция после удаления
     * @var collable 
     */
    public $afterDelete = null; //после удаления модели
    
    /**
     * Render on PJAX
     * Рендерить при PJAX запросе
     * @var type 
     */
    public $renderOnPjax = true; 
    
    /**
     * Don't flash messages
     * Не добавлять flash сообщения при операциях
     * @var type 
     */
    public $disableFlash = false;
    
    /**
     * URL ID parametr
     * Параметр из URL для ID модели
     * @var type 
     */
    public $queryParam = 'id';
    

    abstract public function process();

   
    public function routeTo($params = [], $error = false) {
        if(is_bool($this->errorRoute) && $this->errorRoute){
            $this->errorRoute = $this->successRoute;
        }
        $route = $error ? $this->errorRoute : $this->successRoute;
        $params = $this->returnParams ? $params : [];        
        if(is_string($route)){
            $url = ArrayHelper::merge([$route], $params);            
            return Url::to($url);
        }
        elseif(is_array($route)){
            $url = ArrayHelper::merge($route, $params);            
            return Url::to($url);
        }
        elseif(is_callable($route)){
            return call_user_func($route, $this->getModel(), $this);
        }
        elseif(is_null($route)){
            return Yii::$app->request->referrer;
        }
    }
    
    /**
     * Сurrent info about action
     * Текущая информация о экшене
     * @return Object $currentInfo
     */
    public function getCurrentInfo() {        
        return (object)[
            'module'=>$this->controller->module->id,
            'controller'=>$this->controller->id,
            'action'=>$this->id,
        ];
    }
    
    /**
     * Info about previous action
     * Информация о предыдущем экшене
     * @return Object $currentInfo
     */
    public function getLastInfo() {        
        $last = parse_url(Yii::$app->request->referrer, PHP_URL_PATH);
        list($controller, $action) = Yii::$app->createController($last);
        return (object)[
            'module'=>$controller->module->id,
            'controller'=>$controller->id,
            'action'=>$action,
        ];
    }

    public function getID() {
        return (isset($this->_id)) ? $this->_id : null;
    }

    public function setID($id) {
        if(is_numeric($id)){
            $this->_id = intval($id);            
        }elseif(is_string($id)){
            $this->_id = $id;                        
        }else{
            $this->_id = null;            
        }        
    }

    /**
     * 
     * @return ActiveRecord;
     */
    public function getModel() {
        if (empty($this->model)) {
            $this->riseException('find');
        }

        if ($this->_model) {
            $this->_model->attributes = $this->attr;
            if(is_callable($this->afterFind)){
                $this->_model = call_user_func($this->afterFind, $this->_model, $this);
            }
            return $this->_model;
        }
        if (is_null($this->_model)){            
            $this->_model = (new $this->model);
        }
        
        $model = $this->_model;        
        
        if($id = $this->getID()){
            if(!$this->editable  || !$this->deleteable){
                $this->riseException('forbidden');
            }
            $query = $model::find()->where([$this->queryParam => $this->_id]);

            $this->setRelationModels($query);

            $this->setSearchParams($query);

            $this->setOrderParams($query);

            try{
                $this->_model = $query->one();
            }
            catch (\Exception $e){
               $this->riseException('find');
            }
        }else{
            if(!$this->createable){
                $this->riseException('forbidden');
            }
        }

        if (empty($this->_model)) {
            $this->riseException('find');
        }
        
        
        
        if(isset($this->scenario)){
            $this->_model->scenario = $this->scenario;
        }
        
        $this->_model->attributes = $this->attr;
        
        if(is_callable($this->afterFind)){
            $this->_model = call_user_func($this->afterFind, $this->_model, $this);
        }
        return $this->_model;
    }
    
    
    /**
     * 
     * @return ActiveDataProvider
     */
    public function getData() {

        $model = $this->getModel();
        
        if (method_exists($model, 'search')) { //custom search            
            $data = call_user_func([$model, 'search'], $this->queryParams, $this);
        } else {//default search
            $model->load($this->queryParams);

            $query = $model::find();
            
            $this->setRelationModels($query);
            
            $this->setSearchParams($query);
            
            $this->setOrderParams($query);
            
            $query->andFilterWhere($model->attributes);

            $data = new ActiveDataProvider([
                'query' => $query,
                'pagination'=>$this->pagination,
                'sort'=>isset($this->sort)?$this->sort:(new \yii\data\Sort()),
            ]);
        }
        
        
        return $data;
    }

    public function setOrderParams($query) {
        if (!empty($this->order)) {
            $query->orderBy($this->order);
        }
    }
    
    public function setSearchParams($query) {
        if (!empty($this->searchParams)) {
            $query->where($this->searchParams);
        }
    }

    public function setRelationModels($query) {
        if (!empty($this->relationModels)) {
            $query->joinWith($this->relationModels);
        }
    }

    public function render() {
        $this->params = ArrayHelper::merge($this->params, [
            $this->delegationModelName => $this->getModel(), 
            $this->delegationDataProvider => $this->getData()
        ]);
        if(is_null($this->view)){
            $this->view = $this->id;
        }
        return $this->controller->render($this->view, $this->params);
    }

    public function riseException($key, $params = null) {
        if($key == 'find'){
            throw new yii\web\NotFoundHttpException($this->findMessage, 404);
        }
        if($key == 'forbidden'){
            throw new \yii\web\ForbiddenHttpException($this->forbiddenMessage, 403);
        }
        $this->controller->redirect($this->routeTo([], true));       
        Yii::$app->end();
    }

    public function run() {
        $id = Yii::$app->request->getQueryParam($this->queryParam);
        $this->setID($id); //safe init        
        $this->queryParams = Yii::$app->request->queryParams; // init query params
        return $this->process();
    }
    
    public function performAjaxValidation(){
        if (Yii::$app->request->isAjax && Yii::$app->request->getBodyParam('ajax')){
            $model = $this->getModel();
            if(!$model->load(Yii::$app->request->post())){
                $model->attributes = Yii::$app->request->post();
            }
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if(!$model->hasErrors() && $this->silentMode){
                return;
            }
            echo \yii\helpers\Json::encode(ActiveForm::validate($model));
            Yii::$app->end();
        }
    }

    public function loadAndValidate() {
        if (!$this->getModel()) {
            $this->riseException('find');
        }
        $this->debug();
        $test = $this->noLoad?true:$this->getModel()->load(Yii::$app->request->post());
        return ($test && $this->getModel()->validate());
    }

    public function setFlash($category = 'success') {
        if (!$this->disableFlash && !Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            Yii::$app->session->addFlash($category, Yii::t('app', (($category == 'success')?$this->successMessage:$this->errorMessage)));
        }
    }
    
    public function save() {
        if (!$this->createable && $this->getModel()->isNewRecord) {
            $this->setFlash('error');
            $this->riseException('forbidden');
        }
        if (!$this->editable && !$this->getModel()->isNewRecord) {
           $this->setFlash('error');
            $this->riseException('forbidden');
        }
        
        if($this->linkMethod && method_exists($this->getModel(), $this->linkMethod)){
            $model = $this->getModel();
            $model->{$this->linkMethod}($model);
        }        
        if(is_callable($this->beforeSave)){
            call_user_func($this->beforeSave, $this->getModel());
        }
        $save = $this->getModel()->save();     
        if(is_callable($this->afterSave)){
            call_user_func($this->afterSave, $this->getModel(), $save);
        }
        if($this->silentMode){    
            exit;
        }
        return $save;
    }

    public function delete() {
        if (!$this->deleteable) {
            $this->setFlash('error');
            return $this->riseException('forbidden');
        }
        $model = $this->getModel();
        if($delete = $model->delete()){
            $this->setFlash('success');
        }else{
            $this->setFlash('error');
        }
        
        if(is_callable($this->afterDelete)){
            call_user_func($this->afterDelete, $model, $this);
        }
        
        if($this->silentMode){
            exit;
        } 
        
        return (bool)$delete;
    }
    
    public function debug(){
        if(!$this->debug){            
            return;
        }
        $data = [
            'isLoad'=>$this->getModel()->load(Yii::$app->request->post()),
            'post'=>Yii::$app->request->post(),
            'model'=>$this->getModel()->attributes,
            'isValidate'=>$this->getModel()->validate(),
            'errors'=>$this->getModel()->errors,
            '$_FILES'=>$_FILES
        ];
        if($this->debug_mode == 1){
            Yii::warning(print_r($data, true), ' debug CRUD loadAndValidate method');
            return;
        }
        \yii\helpers\VarDumper::dump($data, 10, true); exit;
    }

}
