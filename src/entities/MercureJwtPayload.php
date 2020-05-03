<?php

namespace Entities;

class MercureJwtPayload extends AbstractJwtPayload {
    protected array $subscribe = array();
    protected array $publish = array();

    /**
     *
     * @return multitype:
     */
    public function getSubscribe(): array {
        return $this->subscribe;
    }

    /**
     *
     * @param multitype: $subscribe
     */
    public function setSubscribe(array $subscribe): void {
        $this->subscribe = $subscribe;
    }

    /**
     *
     * @return multitype:
     */
    public function getPublish(): array {
        return $this->publish;
    }

    /**
     *
     * @param multitype: $publish
     */
    public function setPublish(array $publish): void {
        $this->publish = $publish;
    }

    public function addSubscribe(String $subscribe): void {
        $this->subscribe[] = $subscribe;
    }

    public function addPublish(String $publish): void {
        $this->publish[] = $publish;
    }

    public function jsonSerialize(): array {
        return array(
            'mercure' => array(
                'publish' => array_values(array_unique($this->publish)),
                'subscribe' => array_values(array_unique($this->subscribe))
            )
        );
    }

    public function fromStdClass(\stdClass $stdClass): void {
        $this->publish = json_decode(json_encode($stdClass->mercure->publish), true);
        $this->subscribe = json_decode(json_encode($stdClass->mercure->subscribe), true);
    }
}