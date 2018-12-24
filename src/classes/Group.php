<?php

namespace WPSP;

class Group {

    private $typemeta;
    private $userlist;
    private $userprovider;
    private $siteengines;

    public function __construct( $typemeta ) {
        $this->typemeta = $typemeta;
    }

    // public function addMember(User $user) {
    //     $this->userlist->add($user);
    // }

    public function update() {
        // $this->userlist = $this->userprovider->getUsers();

        $grouptype = Store::unstore( 'GroupType', $this->grouptypeid );
        $query = $grouptype->makeQuery( $this->typemeta );
        $this->userlist = $query->run();

        foreach( $this->siteengines as $se ) {
            $se->update( $this->userlist );
        }
    }

    public function addSiteEngine( $type, $filter = null ) {
        if ( $type = ADDSE_SINGLE ) {
            $newse = new SingleSiteEngine();
        } else if ( $type = ADDSE_MULTI ) {
            $newse = new MultiSiteEngine( $filter );
        }
        $newse->update( $this->userlist );
        $this->siteengines[] = $newse;
    }

}