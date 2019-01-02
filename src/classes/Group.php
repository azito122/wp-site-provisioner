<?php

namespace WPSP;

include_once(__DIR__ . '/infrastructure/Store.php');

class Group {

    public $storeid;

    private $meta;
    private $queryid;
    private $siteengines;

    private $query;
    private $members;

    public function __sleep() {
        return array(
            'meta',
            'queryid',
            'siteengines',
        );
    }

    public function __wakeup() {
        global $Store;
        $this->query = $Store->unstore( 'Query', $this->queryid );
    }

    public function __construct( $meta, $queryid ) {
        $this->meta = $meta;
        $this->queryid = $queryid;
    }

    public function update() {
        $this->members = $this->getUsers();

        foreach( $this->siteengines as $se ) {
            $se->update( $this->members );
        }
    }

    public function addSiteEngine( $type, $filter = null ) {
        if ( $type = ADDSE_SINGLE ) {
            $newse = new SingleSiteEngine();
        } else if ( $type = ADDSE_MULTI ) {
            $newse = new MultiSiteEngine( $filter );
        }
        $newse->update( $this->members );
        $this->siteengines[] = $newse;
    }

    private function getUsers() {
        return $this->query->run();
    }
}