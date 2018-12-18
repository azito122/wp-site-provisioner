<?php

namespace WPSP;

class Group {

    private $typemeta;
    private $userlist;
    private $userprovider;
    private $siteengines;

    public function __construct__( $typemeta ) {
        $this->typemeta = $typemeta;
    }

    public function addMember(User $user) {
        $this->userlist->add($user);
    }

    public function updateMembers() {
        $this->userlist = $this->userprovider->getUsers();
        foreach( $this->siteengines as $se) {
            $se->updateSites( $this->userlist );
        }
    }

}