<?php

namespace MauticPlugin\BlocklistBundle\Model;
use \PDO;
# use Mautic\CoreBundle\Model\AbstractCommonModel;

Class ContactModel # extends AbstractCommonModel
{
    public $db;
    
    public function __construct()
    {
        $this->db = new PDO( "mysql:host={$_SERVER['MAUTIC_DB_HOST']}:{$_SERVER['MAUTIC_DB_PORT']};dbname={$_SERVER['MAUTIC_DB_NAME']}", $_SERVER['MAUTIC_DB_USER'], $_SERVER['MAUTIC_DB_PASSWORD'] );
        
        if( 0 == $this->query( "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'blocklist'", true ) )
        {
            print_r( $this->query( "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'blocklist'", true ) );
        }
    }
    
    function dostos()
    {
        return $this->query( "SELECT `id`, `email`, `firstname`, `lastname` FROM `leads`", true );
    }

    function getTables()
    {
        return $this->query( "SELECT TABLE_NAME from INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME like 'lead_id'", true );
    }

    function getLeads()
    {
        return $this->query( "SELECT `id`, `email` FROM `leads`", true );
    }

    function deleteLeads( $ids, $table )
    {
        if( is_string( $ids ) || is_int( $ids ) ):
            $this->query( "DELETE FROM {$table} WHERE `lead_id` = $ids " );
        elseif( is_array( $ids ) ):
            $this->query( "DELETE FROM {$table} WHERE `lead_id` = ". implode( " OR `lead_id` = ", $ids ) );
        endif;
    }

    function addToBlocklist( $email )
    {
        $bl = unserialize( $this->query( "SELECT * FROM `blocklist`" , true ) );
        $bl[ $email ] = $email;
        $bl = serialize( $bl );
        $this->query( "UPDATE `blocklist` SET `blocklist` = `{$bl}`" );
    }

    function query( $query, $return = false )
    {
        $sql = $this
               ->db
               ->prepare( $query );
        $sql->execute();
        
        if( true === $return ): return $sql->fetchAll( PDO::FETCH_ASSOC ); endif;
    }
}