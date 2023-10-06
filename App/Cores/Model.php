<?php
namespace App\Cores;

class Model
{
    public static $table;

    public static $callRelations;
    public static $callerRelations;

    public static function query(callable $callback)
    {
        if (is_callable($callback)) {

            $modelClass = get_called_class();

            $db = new DB();
            $table = $modelClass::$table ?? Model::$table;
            if($table) {
                return $callback($db, $modelClass::$table);
            }

            $classParts = explode('\\', $modelClass);
            $modelName = end($classParts);
            throw new \BadFunctionCallException('$table'." property must defined in $modelName or Model.");

        } else {
            throw new \InvalidArgumentException('Parameter must be a callable function.');
        }
    }

    protected function loadModule($moduleName)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $modelClass = $backtrace[1]['class'];

        $classParts = explode('\\', $modelClass);
        $modelName = end($classParts);

        $modulePath = __DIR__ . "/../model/$modelName/$moduleName.php";

        if (file_exists($modulePath)) {
            $data = new \stdClass();
            require $modulePath;
            return $data;
        } else {
            throw new Exception("Model's module '$moduleName' not found.");
        }
    }

    public static function with(callable $callRelations)
    {
        static::$callRelations = $callRelations;
        static::$callerRelations = static::class;
    }

    protected static function castRow($row)
    {
        if(!is_array($row)) return null;
        if(static::class != static::$callerRelations || !static::$callRelations) return $row;
        $formatRow = static::$callRelations;
        return $formatRow($row);
    }

    protected static function castRows($data)
    {
        foreach($data as &$row) {
            $row = static::castRow($row);
        }
        return $row;
    }

    public static function find($id)
    {
        $row = static::query(function ($db, $table) use ($id) {
            return $db->queryFirstRow("SELECT * FROM $table WHERE id=%i", $id);
        });
        return $row;
    }

    public static function getAll()
    {
        $rows = static::query(function ($db, $table) {
            return $db->query("SELECT * FROM $table");
        });
        return $rows;
    }

    public static function create(array $data)
    {
        return static::query(function ($db, $table) use ($data) {
            $db->insert($table, $data);
            $id = $db->insertId();
            return $id ? static::find($id) : null;
        });
    }
}