<?php
/**
 * The MIT License
 * Copyright (c) 2007 Andy Smith
 */
namespace Abraham\TwitterOAuth;

class Consumer
{
    /** @var string  */
    public $key;
    /** @var string  */
    public $secret;
    /** @var string|null  */
    public $callbackUrl;

    /**
     * @param string $key
     * @param string $secret
     * @param null $callbackUrl
     */
    public function __construct($key, $secret, $callbackUrl = null)
    {
        $this->key = sanitize_text_field($key);
        $this->secret = sanitize_text_field($secret);
        $this->callbackUrl = sanitize_text_field($callbackUrl);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Consumer[key=$this->key,secret=$this->secret]";
    }
}
