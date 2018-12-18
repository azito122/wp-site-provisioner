<?php

namespace WPSP;

class Group {

    private $typemeta;
    private $userlist;
    private $query;
    private $siteengines;

    public function __construct__( $typemeta ) {
        $this->typemeta = $typemeta;
    }

    public function addMember(User $user) {
        $this->userlist->add($user);
    }

    public function updateMembers() {
        $userdata = $this->query->run;
        $this->userlist->update($userdata);
        foreach( $this->siteengines as $se) {
            $se->updateSites( $this->userlist );
        }
    }

}