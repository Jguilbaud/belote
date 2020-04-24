<?php

namespace Entities;

class MercureEventBelotePayload extends AbstractJwtPayload {
    protected String $action = '';
    protected array $data = array();

    public function jsonSerialize() {
        return array(

            'action' => $this->action,
            'data' => $this->data
        );
    }

    /**
     *
     * @return mixed
     */
    public function getAction() {
        return $this->action;
    }

    /**
     *
     * @param mixed $action
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     *
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     *
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     *
     * @param mixed $data
     */
    public function addData(String $key, String $value) {
        $this->data[$key] = $value;
    }
}