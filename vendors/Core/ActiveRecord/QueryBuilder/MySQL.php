<?php

namespace Core\ActiveRecord\QueryBuilder;

use \Core\ActiveRecord\QueryBuilder;

class MySQL extends QueryBuilder {

    /**
     * <p>
     * Builds insert query (prepared statement).
     * http://dev.mysql.com/doc/refman/5.0/en/insert.html.
     * </p>
     *
     * <p>
     * The most complex query for $options could be somewhat like this:
     * </p>
     * <code>
     * $options = array(
     *      "select" => MySQL::selectQuery("Model", array(
     *          "fields" => array("Model.foo", "Model.bar", "Model.hello"),
     *          "conditions" => array(
     *              Operator::boolOr(
     *                  Operator::boolAnd(
     *                      Operator::equals("foo", "bar"),
     *                      Operator::notEquals("baz", "blah"),
     *                      Operator::notEquals("hello", "world"),
     *                  ),
     *                  Operator::equals("ThisModel.field", "1")
     *              )
     *          )
     *      )),
     *      "on duplicate key update" => array("foo='bar'", "baz='blah'"),
     * );
     * </code>
     *
     * @param $data     Data to insert. Array of key => value pairs.
     * @param $options  Options. See example above.
     * @return string   SQL Query (prepared statement).
     */
    public function insert($data = array(), $options = array()) {
        // Lowercase all $options keys then find for "select" option.
        if(isset($options["select"]) == false) {
            // First case: Standard insert query
            // 1st %s => table name
            // 2nd %s => fields
            // 3rd %s => Binding parameters (series of question marks "?")
            // 4th %s => INSERT ... ON DUPLICATE KEY UPDATE syntax. See link to manual above.
            $query = "INSERT INTO `%s`(%s) VALUES(%s) %s";
        }
        else {
            // Second case: Insert query with select.
            // If "INSERT ... SELECT" is meant to be executed, then query is as following:
            // http://dev.mysql.com/doc/refman/5.5/en/insert-select.html
            // 1st %s => table name
            // 2nd %s => fields
            // 3rd %s => SELECT statement
            $query = "INSERT INTO `%s`(%s) %s";
        }

        // Build field names based on $data keys
        $fields = "";
        $binds = "";
        if(count($data)) {
            $keys = array_keys($data);
            $fields = implode(", ", $keys);
            $fields = strtolower($fields);
            $fields = preg_replace("/([\w0-9_]+)/", "`$1`", $fields);

            // Statements are bound with bind variables
            $binds = implode(", ", $keys);
            $binds = preg_replace("/([\w0-9_]+)/", ":$1", $binds);
        }

        // Default query parts
        $onDuplicateKeyUpdate = "";
        $select = "";

        // Check for "INSERT ... SELECT"
        if(isset($options["select"])) {
            $select = $options["select"];
        }

        if(isset($options["on duplicate key update"])) {
            // Check for "INSERT ... ON DUPLICATE KEY UPDATE"
            // If value is an array, then build: col_name=expr, col_name2=expr2, ...
            // Otherwise just append the value.
            $onDuplicateKeyUpdate = "ON DUPLICATE KEY UPDATE ";
            if(is_array($options["on duplicate key update"])) {
                $onDuplicateKeyUpdate .= implode(", ", $options["on duplicate key update"]);
            }
            else {
                $onDuplicateKeyUpdate .= $options["on duplicate key update"];
            }
        }

        // Refer to first and second cases above.
        if(isset($options["select"]) == false) {
            return trim(sprintf($query, $this->tableName, $fields, $binds, $onDuplicateKeyUpdate));
        }
        else {
            return trim(sprintf($query, $this->tableName, $select));
        }
    }


    /**
     * Builds select query.
     *
     * see http://dev.mysql.com/doc/refman/5.0/en/select.html
     * and http://dev.mysql.com/doc/refman/5.0/en/join.html
     *
     * SELECT
     * [ALL | DISTINCT | DISTINCTROW ]
     * select_expr [, select_expr ...]
     * [FROM table_references
     * [WHERE where_condition]
     * [GROUP BY {col_name | expr | position}
     * [ASC | DESC], ... [WITH ROLLUP]]
     * [HAVING where_condition]
     * [ORDER BY {col_name | expr | position}
     * [ASC | DESC], ...]
     * [LIMIT {[offset,] row_count | row_count OFFSET offset}]
     *
     * @param $data
     * @param $options
     * @return string
     */
    public function select($options = array()) {
        // Non join query
        // 1st %s: fields list
        // 2nd %s: table name
        // 3rd %s: where condition
        // 4th %s: grouping condition
        // 5th %s: having condition
        // 6th %s: ordering
        // 7th %s: limit
        // 8th %s: offset
        $query = "SELECT %s FROM `%s` %s%s%s%s%s%s";

        // Check for join key and build special query if it has been found.
        if(isset($options["join"])) {
            // 1st %s: fields list
            // 2nd %s: table name
            // 3rd %s: join statement
            // 4rd %s: where condition
            // 5th %s: grouping condition
            // 6th %s: having condition
            // 7th %s: ordering
            // 8th %s: limit
            // 9th %s: offset
            $query = "SELECT %s FROM `%s` %s%s%s%s%s%s%s";

            // Set join types, if not set then defaults to natural join
            if(isset($options["join"]["type"])) {
                $joinType = $options["join"]["type"]." ";
            }
            else {
                $joinType = "JOIN ";
            }

            // join tables (table factor). Should be inside an array.
            // build something like this (foo, bar, baz)
            if(is_array($options["join"]["tables"])) {
                $joinTables = "(".implode(", ", $options["join"]["tables"]).") ";
                $joinTables = strtolower($joinTables);
                $joinTables = preg_replace("/([\w0-9_]+)/", "`$1`", $joinTables);
            }
            else {
                // otherwise just use the value
                $joinTables = $options["join"]["tables"]." ";
            }

            // Join conditions
            if(isset($options["join"]["conditions"])) {
                $tmp = $options["join"]["conditions"];
                $joinConditions = "ON $tmp ";
            }
            else {
                $joinConditions = "";
            }

            // @todo: index hint
            if(isset($options["join"]["index"])) {
            }
            else {
            }

            $join = $joinType . $joinTables . $joinConditions;
        }

        // Build query
        // 1. Build (field1, field2, ..) and (?, ?, ..)
        if(isset($options["fields"])) {
            $fields = implode(", ", $options["fields"]);
            $fields = strtolower($fields);
            $fields = preg_replace("/([\w0-9_]+)/", "`$1`", $fields);
        }
        else {
            $fields = "*";
        }

        // Build WHERE condition
        if(isset($options["conditions"])) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build grouping
        if(isset($options["group"])) {
            $group = "GROUP BY `" . $options["group"] . "` ";
        }
        else {
            $group = "";
        }

        // Build having clause
        if(isset($options["having"])) {
            $having = "HAVING " . $options["having"] . " ";
        }
        else {
            $having = "";
        }

        // Build order
        if(isset($options["order"])) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        $limit = "";
        $offset = "";
        if(isset($options["limit"])) {
            $limit = "LIMIT :core_query_limit ";

            // Build offset. Offset won't work without limit.
            if(isset($options["offset"])) {
                $offset = "OFFSET :core_query_offset";
            }
        }

        if(isset($options["join"])) {
            // @todo
            return trim(sprintf($query, $fields, $this->tableName, $join,
                $conditions, $group, $having, $order, $limit, $offset));
        }
        else {
            return trim(sprintf($query, $fields, $this->tableName,
                $conditions, $group, $having, $order, $limit, $offset));
        }
    }


    /**
     * Builds update query. Only supports single table updates.
     *
     * Single-table syntax:
     * UPDATE [LOW_PRIORITY] [IGNORE] table_reference
     * SET col_name1={expr1|DEFAULT} [, col_name2={expr2|DEFAULT}] ...
     * [WHERE where_condition]
     * [ORDER BY ...]
     * [LIMIT row_count]
     *
     * see http://dev.mysql.com/doc/refman/5.0/en/update.html
     *
     * @param $data
     * @param $options
     * @return string
     */
    public function update($data, $options = array()) {
        // 1st %s : table name
        // 2nd %s : key-value pairs
        // 3rd %s : where conditions
        // 4th %s : order conditions
        // 5th %s : limit
        $query = "UPDATE `%s` SET %s%s%s%s";

        // Build key-value pairs
        $sets = "";
        $count = count($data);
        $i = 0;
        foreach((array) $data as $key=>$value) {
            if($i < $count-1) {
                $sets .= "`$key` = :$key, ";
            }
            else {
                $sets .= "`$key` = :$key ";
            }
            ++$i;
        }

        // Build condition
        if(isset($options["conditions"])) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build order
        if(isset($options["order"])) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if(isset($options["limit"])) {
            $limit = "LIMIT :core_query_limit";
        }
        else {
            $limit = "";
        }

        return trim(sprintf($query, $this->tableName, $sets, $conditions, $order, $limit));
    }


    /**
     * Builds delete query.
     *
     * Single-table syntax:
     * DELETE [LOW_PRIORITY] [QUICK] [IGNORE] FROM tbl_name
     * [WHERE where_condition]
     * [ORDER BY ...]
     * [LIMIT row_count]
     *
     * see http://dev.mysql.com/doc/refman/5.0/en/delete.html
     *
     * @param $options
     * @return string
     */
    public function delete($options = array()) {
        // [WHERE where_condition]
        // [ORDER BY ...]
        // [LIMIT row_count]
        //
        // 1st %s : Table name
        // 2nd %s : WHERE condition
        // 3rd %s : ORDER condition
        // 4st %s : LIMIT
        $query = "DELETE FROM `%s` %s%s%s";

        // Build condition
        if(isset($options["conditions"])) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build order
        if(isset($options["order"])) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if(isset($options["limit"])) {
            $limit = "LIMIT :core_query_limit";
        }
        else {
            $limit = "";
        }

        return trim(sprintf($query, $this->tableName, $conditions, $order, $limit));
    }
}

?>