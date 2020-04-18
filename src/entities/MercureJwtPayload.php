<?php

namespace Entities;

class MercureJwtPayload implements \JsonSerializable {
    protected $subscribe = array();
    protected $publish = array();

    /**
     *
     * @return multitype:
     */
    public function getSubscribe() {
        return $this->subscribe;
    }

    /**
     *
     * @param multitype: $subscribe
     */
    public function setSubscribe($subscribe) {
        $this->subscribe = $subscribe;
    }

    /**
     *
     * @return multitype:
     */
    public function getPublish() {
        return $this->publish;
    }

    /**
     *
     * @param multitype: $publish
     */
    public function setPublish($publish) {
        $this->publish = $publish;
    }

    public function addSubscribe($subscribe) {
        $this->subscribe[] = $subscribe;
    }

    public function addPublish($publish) {
        $this->publish[] = $publish;
    }

    public function jsonSerialize() {
        return array(
            'mercure' => array(
                'publish' => $this->publish,
                'subscribe' => $this->subscribe
            )
        );
    }
}