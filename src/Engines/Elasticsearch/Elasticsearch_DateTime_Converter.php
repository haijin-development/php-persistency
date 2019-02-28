<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

class Elasticsearch_DateTime_Converter
{
    public function to_database($date_time)
    {
        return $date_time;
    }

    public function from_database($array)
    {
        return new \DateTime(
            $array[ 'date' ],
            new \DateTimeZone( $array[ 'timezone' ] )
        );
    }
}