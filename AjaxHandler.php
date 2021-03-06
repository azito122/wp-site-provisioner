<?php

namespace WPSP;

use \WPSP\render\Renderer as Renderer;

abstract class AjaxHandler {
    public static function makeGroup() {
        global $Store;

        $grouptypeid = $_REQUEST[ 'grouptypeid' ];
        $metaid = $_REQUEST[ 'metaid' ];

        $grouptype = $Store->unstoreEntity( 'GroupType', $grouptypeid );
        $metas = $grouptype->generatePossibleMetas();
        $group = $grouptype->makeGroup( $metas[ $metaid ] );
        $Store->storeEntity( $group );
        $Store->addUserGroupId( $group->uid );
        die();
    }

    public static function render() {
        global $Store;

        $type = $_REQUEST[ 'rendertype' ];
        if ( $type == 'template' ) {
            // echo Renderer::renderTemplate( $template );
        } else if ( $type == 'entity' ) {
            $name = Renderer::classnameFrontToBack( $_REQUEST[ 'entity' ] );
            $classname = resolve_classname( $name );
            $id = array_key_exists( 'entityid', $_REQUEST ) ? $_REQUEST[ 'entityid' ] : false;
            if ( $id ) {
                $object = $Store->unstoreEntity( $name, $id );
            } else {
                $object = new $classname();
            }
            echo Renderer::renderEntity( $object );
        }
        die();
    }

    public static function store() {
        global $Store;
        header('Content-Type: application/json');

        $type = Renderer::classnameFrontToBack( $_REQUEST[ 'type' ] );
        $data = json_decode(stripslashes($_REQUEST[ 'data' ]), true);

        $derendered = Renderer::derenderEntity( $data, $type );

        if ( ! $derendered ) {
            die();
        }

        $stored = $Store->storeEntity( $derendered );
        $object = $stored['object'];

        $return = array(
            'id' => $stored['id'],
        );

        $rerenderid = $_REQUEST[ 'rerenderid' ];

        if ( ! empty( $rerenderid ) ) {
            $return[ 'rerenderid' ] = $rerenderid;
            $return[ 'rerendered' ] = Renderer::renderEntity( $object );
        }
        echo json_encode( $return );

        die();
    }

    public static function addSingleSiteEngine() {
        global $Store;

        header( 'Content-Type: application/json' );

        $data = json_decode(stripslashes($_REQUEST[ 'data' ]), true);

        $object = $Store->unstoreEntity( 'Group', $data[ 'uid' ] );
        $object->addSingleSiteEngine();
        $Store->storeEntity( $object );

        $rerenderid = $_REQUEST[ 'rerenderid' ];

        if ( ! empty( $rerenderid ) ) {
            $return[ 'rerenderid' ] = $rerenderid;
            $return[ 'rerendered' ] = Renderer::renderEntity( $object );
        }
        echo json_encode( $return );

        die();
    }

    public static function removeSiteEngine() {
        global $Store;

        header( 'Content-Type: application/json' );

        $data = json_decode(stripslashes($_REQUEST[ 'data' ]), true);
        $siteengineid = $_REQUEST[ 'siteengineid' ];

        $object = $Store->unstoreEntity( 'Group', $data[ 'storeid' ] )[0];
        $object->removeSiteEngine( $siteengineid );
        $Store->storeEntity( $object );

        $rerenderid = $_REQUEST[ 'rerenderid' ];

        if ( ! empty( $rerenderid ) ) {
            $return[ 'rerenderid' ] = $rerenderid;
            $return[ 'rerendered' ] = Renderer::renderEntity( $object );
        }
        echo json_encode( $return );

        die();
    }
}