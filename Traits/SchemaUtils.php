<?php namespace Modules\Thinking\Contracts\Traits;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

/**
 * Class SchemaUtils
 *
 * Se utiliza para evitar el duplicado de codigo
 *  utilizando la abstracción de codigo.
 *
 * @package Modules\Thinking\Tools\Extensions
 */
trait SchemaUtils
{
    /**
     * Función 'foreignTo'
     *
     * Establece el motor de la tabla.
     * El campo obtiene el resultado de si es un campo de lo contrario obtiene el arreglo
     *  de la tabla concatenada a su identificador unico.
     * Obtiene el resultado del entero de la tabla en el campo dado.
     * Establece los atributos dados a la tabla.
     * Retorna el resultado.
     *
     * @param  Blueprint  $table
     * @param  null  $asField
     * @param  string  $idField
     * @param  string  $deleteType
     *
     * @return \Illuminate\Support\Fluent
     */
    public static function foreignTo(Blueprint &$table, $asField = null, $idField = 'id', $deleteType = 'restrict')
    {
        $table->engine = 'InnoDB';
        $field = $asField ?: Str::singular(self::TABLE_NAME) . '_' . $idField;
        $result = $table->unsignedInteger($field)
            ->index();
        $table->foreign($field, self::fkName($table, $asField))
            ->references($idField)
            ->on(self::TABLE_NAME)
            ->onUpdate('restrict')
            ->onDelete($deleteType);

        return $result;
    }

    /**
     * Función 'fkName'
     *
     * Retorna un arreglo que contiene la descripción de la tabla y su nombre.
     *
     * @param Blueprint $table
     * @param null $asField
     *
     * @return string
     */
    public static function fkName(Blueprint $table, $asField = null)
    {
        return 'FK_' . Str::singular($table->getTable()) . '_' . ($asField ?: Str::singular(self::TABLE_NAME));
    }

    /**
     * Función 'removeForeign'
     *
     * Indica que la clave externa dada debe ser eliminada.
     *
     * @param Blueprint $table
     * @param null $asField
     *
     * @return void
     */
    public static function removeForeign(Blueprint &$table, $asField = null)
    {
        $table->dropForeign(self::fkName($table, $asField));
    }

    /**
     * Función 'setComment'
     *
     * Retorna el arreglo dado 'comment'
     *
     * @param $comment
     *
     * @return string
     */
    protected static function setComment($comment)
    {
        return addslashes(strstr(preg_replace('/\s+/', ' ', $comment), ', ej:', true));
    }

    /**
     * Función 'setTableComment'
     *
     * Establece el comentario de la tabla.
     *
     * @param $tableName
     * @param $comment
     *
     * @return void
     */
    protected static function setTableComment($tableName, $comment, $connection = null)
    {
        $sql = <<<SQL
ALTER TABLE $tableName COMMENT = "$comment";
SQL;
        if (is_null($connection)) {
            DB::statement($sql);
        } else {
            DB::connection($connection)
                ->statement($sql);
        }
    }

    /**
     * Función 'addVirtualField'
     *
     * Establece una columna a la tabla dada en la base de datos.
     *
     * @param        $table
     * @param        $column
     * @param string $sqlType
     * @param string $expression
     * @param string $virtualType
     * @param string $extras
     *
     * @return void
     */
    protected static function addVirtualField(
        $table,
        $column,
        $expression = 'id',
        $sqlType = 'int(10) unsigned',
        $virtualType = 'PERSISTENT UNIQUE KEY',
        $extras = ''
    ) {
        $sql = <<<SQL
ALTER TABLE $table ADD COLUMN `$column` $sqlType AS ($expression) $virtualType $extras;
SQL;
        try {
            DB::statement($sql);
        } catch (Exception $e) {
            print $e->getMessage();
        }
    }

    /**
     * Función 'foreignTables'
     *
     * Establece el motor de la tabla.
     * Obtiene el resultado del entero de la tabla en el campo dado.
     * El campo obtiene el resultado de si es un campo de lo contrario obtiene el arreglo
     *  de la tabla concatenada a su identificador unico.
     * Establece los atributos dados a la tabla.
     * Retorna el resultado.
     *
     * @param Blueprint $table
     * @param string $field
     * @param string $sourceTable
     * @param string $idField
     * @param string $deleteType
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function foreignTables(Blueprint $table, $field, $sourceTable, $idField = 'id', $deleteType = 'cascade')
    {
        $table->engine = 'InnoDB';
        $result = $table->unsignedInteger($field);
        $fkName = 'FK_' . Str::singular($table->getTable()) . '_' . Str::singular($sourceTable);
        $table->index($field);
        $table->foreign($field, $fkName)
            ->references($idField)
            ->on($sourceTable)
            ->onUpdate('restrict')
            ->onDelete($deleteType);

        return $result;
    }

    /**
     * Función 'removeTest'
     *
     * Si el entorno de la aplicación esta dado por 'local', 'test' entonces:
     * Elimina la tabla del esquema si existe dentro del sistema.
     *
     * @return void
     */
    protected function removeTest()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        if (env('APP_ENV', 'local')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->down();
        }
    }

    /**
     * Función 'implementTree'
     *
     * Implementa una estructura para manejo de arboles basada en BAUM.
     *
     * @param Blueprint $table
     *
     * @return Blueprint
     */
    protected function implementTree(Blueprint &$table)
    {
        $table->unsignedInteger('parent_id')
            ->nullable()
            ->index();
        $table->unsignedSmallInteger('order_at')
            ->nullable();
        $table->unsignedSmallInteger('left_at')
            ->nullable()
            ->index();
        $table->unsignedSmallInteger('right_at')
            ->nullable()
            ->index();
        $table->unsignedSmallInteger('depth')
            ->nullable();

        return $table;
    }
}
