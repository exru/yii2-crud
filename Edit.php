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
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

class Edit extends CrudAction{

    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;
            
    public function process() {
        $model = $this->getModel();
        if (isset($this->attr)) {
            $model->load($this->attr);
        }
        if(Yii::$app->request->getQueryParam('_pjax')){
            return $this->render();
        }        
        $this->performAjaxValidation();
        if ($this->loadAndValidate()) {     
            if ($this->save()){
                if ($this->returnView && $this->view) {
                    $this->params = ArrayHelper::merge($this->params, [$this->delegationModelName => $this->getModel()]);
                    return $this->controller->renderPartial($this->view, $this->params);
                }else{
                    if(is_callable($this->afterSave)){
                        call_user_func($this->afterSave, $this->getModel());
                    }
                }
                $this->setFlash('success');
                if($this->renderOnPjax && Yii::$app->request->isPjax){
                    return $this->render();
                }
                return $this->controller->redirect($this->routeTo([$this->queryParam => $this->getModel()->id]));
            }else{
                $this->setFlash('error');
                $this->riseException('save', $this->getModel()->errors);
            }
        }elseif(Yii::$app->request->isPost){
            $this->setFlash('error');
        }
        if (!Yii::$app->request->isAjax) {
            return $this->render();
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function render() {
        if(is_null($this->view)){
            $this->view = $this->id;
        }
        $this->params = ArrayHelper::merge($this->params, [$this->delegationModelName => $this->getModel()]);
        return $this->controller->render($this->view, $this->params);
    }

}
