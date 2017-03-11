<?php

namespace Medz\Wind\Upgrade;

use Guzzle\Http\Client;

class Repositorie
{
    /**
     * The repositorie url.
     *
     * @var string
     */
    protected $repositorie;
    protected $tag;

    protected $client;

    /**
     * create the rep.
     *
     * @param string $repositorie
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct($repositorie, $tag)
    {
        $this->repositorie = $repositorie;
        $this->tag = $tag;
        $this->createClient();
    }

    protected function createClient()
    {
        $this->client = new Client($this->repositorie);
    }

    public function check()
    {
        $request = $this->client->get("releases/download/{$this->tag}/upgrade.json");
        $response = $request->send();

        return $response;
    }
}
