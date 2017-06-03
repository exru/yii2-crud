<?php

/*
 * Copyright (C) 2017 exru.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
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
use yii;

class Delete extends CrudAction {

    private $_status = false;
    
    public $ajaxOnly = false;
    
    public $noLoad = true;
    
    public $successMessage = 'Удаление завершилось успешно';
    
    public $errorMessage = 'Ошибка удаления';
    
    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;

    public function process() { 
        $this->performAjaxValidation();
        if($this->loadAndValidate()){
            if (!$this->delete()){
                return;
            }
            $this->_status = true;
        }
        if($this->getModel()->hasErrors()){
            $this->setFlash('error'); 
        }
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return yii\helpers\Json::encode(['success' => $this->_status?self::STATUS_SUCCESS:self::STATUS_ERROR]);
        }
        if(!$this->ajaxOnly){
            return $this->controller->redirect($this->routeTo([$this->queryParam => $this->getModel()->id]));
        }
    }

}