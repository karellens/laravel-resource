<?php

namespace Karellens\Resource;
use Illuminate\Support\Facades\Cache;

/**
 * Helper for Eloquent Model reverse engineering
 *
 * Class ReflectionModel
 * @package Karellens\Resource
 */
class ReflectionModel
{
    private static $RELATIONS = [
        // Define a one-to-one relationship.
        'hasOne'            => '\Illuminate\Database\Eloquent\Relations\HasOne',
        // Define a polymorphic one-to-one relationship.
        'morphOne'          => '\Illuminate\Database\Eloquent\Relations\MorphOne',
        // Define an inverse one-to-one or many relationship.
        'belongsTo'         => '\Illuminate\Database\Eloquent\Relations\BelongsTo',
        // Define a polymorphic, inverse one-to-one or many relationship.
        'morphTo'           => '\Illuminate\Database\Eloquent\Relations\MorphTo',
        // Define a one-to-many relationship.
        'hasMany'           => '\Illuminate\Database\Eloquent\Relations\HasMany',
        // Define a has-many-through relationship.
        'hasManyThrough'    => '\Illuminate\Database\Eloquent\Relations\HasManyThrough',
        // Define a polymorphic one-to-many relationship.
        'morphMany'         => '\Illuminate\Database\Eloquent\Relations\MorphMany',
        // Define a many-to-many relationship.
        'belongsToMany'     => '\Illuminate\Database\Eloquent\Relations\BelongsToMany',
        // Define a polymorphic many-to-many relationship.
        'morphToMany'       => '\Illuminate\Database\Eloquent\Relations\MorphToMany',
        // Define a polymorphic, inverse many-to-many relationship.
        'morphedByMany'     => '\Illuminate\Database\Eloquent\Relations\MorphToMany',
    ];

    protected $modelName;

    protected $modelClass;

    protected $relations;

    /**
     * @return string Class
     */
    public function getClassByAlias($alias)
    {
        $modelClass = null;
        $modelName = str_singular(studly_case($alias));

        foreach (config('api.models_namespaces') as $ns) {
            if(class_exists($ns.$modelName))
            {
                $modelClass = $ns.$modelName;
                break;
            }
        }

        if(!$modelClass)
        {
            // throw 404
        }

        return $modelClass;
    }

    /**
     * Get table linked to model
     *
     * @param string $modelClass
     * @return string
     */
    public function getTable($modelClass = null)
    {
        return with(new $modelClass)->getTable();
    }

    /**
     * @return array
     */
    public function getSupportedRelations($modelClass = null)
    {
        $relations = [];

        if(Cache::has($modelClass.'_relations')) {
            $relations = Cache::get($modelClass.'_relations');
            return $relations;
        }

        $rc = new \ReflectionClass($modelClass);

        foreach($rc->getMethods() as $method)
        {
            $doc = $method->getDocComment();

            if($doc && self::strposa($doc, self::$RELATIONS) !== false)
            {
                // except definitions of the relations
                if(!in_array($method->getName(), array_keys(self::$RELATIONS))) {
                    $relations[$method->getName()] = null;

                    // check for pivot columns
                    preg_match_all('#@pivotColumns (.*?)\n#s', $doc, $pivotColumnsString);
                    if(count($pivotColumnsString[1])) {
                        // remove spaces and split columns by commas
                        $pivotColumns = explode(',', preg_replace('/\s+/', '', $pivotColumnsString[1][0]));
                        $relations[$method->getName()] = $pivotColumns;
                    }
                }
            }
        }

        Cache::put($modelClass.'_relations', $relations);
        return $relations;
    }

    private static function strposa($haystack, $needle, $offset=0) {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $query) {
            if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
        }
        return false;
    }
}