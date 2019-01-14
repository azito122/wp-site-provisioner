<?php

namespace WPSP;

use WPSP\siteengine\SingleSiteEngine as SingleSiteEngine;
use WPSP\siteengine\MultiSiteEngine as MultiSiteEngine;

class Group {

    public $storeid;

    protected $meta;
    protected $queryid;
    protected $siteengines;

    protected $query;
    protected $members;

    public function __sleep() {
        return array(
            'meta',
            'queryid',
            'siteengines',
        );
    }

    public function __wakeup() {
        global $Store;
        $this->query = $Store->unstoreEntity( 'Query', $this->queryid );
    }

    public function __construct( $meta, $queryid ) {
        $this->meta = $meta;
        $this->queryid = $queryid;
    }

    public function update() {
        $this->members = $this->loadUsers();

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

    private function loadUsers() {
        return $this->query->run();
    }
}