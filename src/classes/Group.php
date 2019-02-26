<?php

namespace WPSP;

use WPSP\siteengine\SingleSiteEngine as SingleSiteEngine;
use WPSP\siteengine\MultiSiteEngine as MultiSiteEngine;

class Group {

    use \WPSP\traits\GetterSetter;
    use \WPSP\traits\Storable;

    protected $label;
    protected $meta;
    protected $queryid;
    protected $siteengines;

    protected $query;
    protected $members;

    protected $sleeplist = [
        'label',
        'meta',
        'queryid',
        'siteengines',
    ];

    public function __wakeup() {
        global $Store;
        $this->query = $Store->unstoreEntity( 'Query', $this->queryid );
    }

    public function __construct( $meta = null, $queryid = null ) {
        $this->meta = $meta;
        $this->label = isset( $meta[ 'meta_displayname' ] ) ? $meta[ 'meta_displayname' ] : '';
        $this->queryid = $queryid;
        $this->siteengines = array();
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

    public function addSingleSiteEngine( $siteengine = null ) {
        $siteengine = $siteengine ? $siteengine : new SingleSiteEngine();
        array_push( $this->siteengines, $siteengine );
        return $siteengine;
    }

    public function loadUsers() {
        return $this->query->run( $this->meta );
    }
}