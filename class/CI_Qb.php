<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_mysqli_driver $masterDb
 */
class CI_Qb
{
    private $aseEncryptKey  = false;
    protected $database     = false;
    protected $masterDb     = false;
    protected $slaveDb      = false;
    protected $etcDb        = [];
    protected $qb;
    protected $queryType    = 'select';
    protected $table        = false;
    protected $subQryAlias  = '';
    protected $params;
    protected $totalRows    = 0;
    protected $insertId     = false;
    protected $lastQury;
    protected $enableMaster = false;
    public $total           = false;

    protected $subQuery;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->qb     = new NunaQb($params);
    }

    public function setAseEncryptKey($key)
    {
        $this->aseEncryptKey = $key;
        return $this;
    }

    public function setEnableMaster($enableMaster)
    {
        $this->enableMaster = $enableMaster;
        return $this;
    }

    public function platform()
    {
        return $this->qb->platform();
    }

    public function setDatabase($database, $useTryCatch = false)
    {
        if ($database === 'master') {
            $this->database = $this->getMasterDb();
        } elseif ($database === 'slave') {
            $this->database = $this->getSlaveDb();
        } elseif ($database === 'payment') {
            $this->database = $this->getMasterDb();
        } else {
            $this->database = $this->getEtcDb($database, $useTryCatch);
        }

        $this->qb->driver($this->database->dbdriver);

        return $this;
    }

    public function reConnect()
    {
        getForbiz()->load->closeAllDB();
    }

    public function resetDatabase()
    {
        $this->database = false;
        $this->qb->driver($this->getSlaveDb()->dbdriver);

        return $this;
    }

    public function getEtcDb($dbName, $useTryCatch)
    {
        // set etcDb
        if (isset($this->etcDb[$dbName]) === false) {
            $this->etcDb[$dbName] = getForbiz()->import('db.'.$dbName, ['useTryCatch' => $useTryCatch]);
        }

        return $this->etcDb[$dbName];
    }

    public function conn_id()
    {
        return $this->database->conn_id;
    }

    public function betweenDate($col, $startDate, $endDate, $type = 'and')
    {
        $startDate = (strlen($startDate) > 10 ? $startDate : ($startDate.' 00:00:00'));
        $endDate   = (strlen($endDate) > 10 ? $endDate : ($endDate.' 23:59:59'));
        if ($type == 'or') {
            $this->orWhere("{$col} BETWEEN '{$startDate}' AND '{$endDate}'", null, false);
        } else {
            $this->where("{$col} BETWEEN '{$startDate}' AND '{$endDate}'", null, false);
        }
        return $this;
    }

    public function betweenBasic($col, $startDate, $endDate, $type = 'and')
    {
        $startDate = fb_valid_date($startDate, 'Y-m-d H:i:s');
        $endDate = fb_valid_date($endDate, 'Y-m-d H:i:s');

        if ($type == 'or') {
            $this->orWhere("{$col} BETWEEN '{$startDate}' AND '{$endDate}'", null, false);
        } else {
            $this->where("{$col} BETWEEN '{$startDate}' AND '{$endDate}'", null, false);
        }
        return $this;
    }

    public function betweenColumn($date, $startColumn, $endColumn, $type = 'and')
    {
        if ($type == 'or') {
            $this->orWhere("{$date} BETWEEN $startColumn AND $endColumn", null, false);
        } else {
            $this->where("{$date} BETWEEN $startColumn AND $endColumn", null, false);
        }
        return $this;
    }

    public function orBetweenDate($col, $startDate, $endDate)
    {
        return $this->betweenDate($col, $startDate, $endDate, 'or');
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;

        return $this;
    }

    public function transStart($test_mode = false)
    {
        return $this->getMasterDb()->trans_start($test_mode);
    }

    public function transComplete()
    {
        return $this->getMasterDb()->trans_complete();
    }

    public function transStrict($mode = true)
    {
        return $this->getMasterDb()->trans_strict($mode);
    }

    public function transStatus()
    {
        return $this->getMasterDb()->trans_status();
    }

    public function transOff()
    {
        return $this->getMasterDb()->trans_off();
    }

    public function transBegin($test_mode = false)
    {
        return $this->getMasterDb()->trans_begin($test_mode);
    }

    public function transRollback()
    {
        return $this->getMasterDb()->trans_rollback();
    }

    public function transCommit()
    {
        return $this->getMasterDb()->trans_commit();
    }

    public function chkEnctyptKey()
    {
        if (!defined('FBEC4B0E1CFB328CE5CBE1EDC4B68C34')) {
            $this->getSlaveDb();
        }

        if ($this->aseEncryptKey === false) {
            $this->aseEncryptKey = FBEC4B0E1CFB328CE5CBE1EDC4B68C34;
        }
    }

    public function encrypt(string $val)
    {
        $this->chkEnctyptKey();

        return sprintf("HEX(AES_ENCRYPT('%s','%s'))", $val, $this->aseEncryptKey);
    }

    public function decrypt(string $key)
    {
        $this->chkEnctyptKey();

        return sprintf("AES_DECRYPT(UNHEX(%s),'%s')", $key, $this->aseEncryptKey);
    }

    public function encryptWhere($key, $value)
    {
        $this->where($key, $this->encrypt($value), false);
        return $this;
    }

    public function encryptOrWhere($key, $value)
    {
        $this->orWhere($key, $this->encrypt($value), false);
        return $this;
    }

    public function encryptLike($key, $match = '', $side = 'both')
    {
        $this->like($this->decrypt($key), $match, $side, false);
        return $this;
    }

    public function encryptOrLike($key, $match = '', $side = 'both')
    {
        $this->orLike($this->decrypt($key), $match, $side, false);
        return $this;
    }

    public function encryptSet(string $key, $value)
    {
        $this->set($key, $this->encrypt($value), false);
        return $this;
    }

    public function decryptSelect(string $key, $alias = false)
    {
        if ($alias === false) {
            if (strpos($key, '.') !== false) {
                $data  = explode('.', $key);
                $alias = array_pop($data);
            } else {
                $alias = $key;
            }
        }

        $this->select(sprintf("%s as %s", $this->decrypt($key), $alias), false);

        return $this;
    }

    public function encryptStr(String $value)
    {
        $dbSelect = new CI_Qb($this->params);

        $row =  $dbSelect
            ->select($dbSelect->encrypt($value).' AS enc', false)
            ->from('dual', false)
            ->exec()
            ->getRowArray();

        return ($row['enc'] ?? '');
    }

    public function pagination($cur_page = null, $per_page = null, $link_num = 10, $method = 'post')
    {
        $qry = $method == 'get' ? $_GET : $_POST;

        if(defined('PAGINATION_LINK_NUM') && PAGINATION_LINK_NUM > 0){
            $link_num = PAGINATION_LINK_NUM;
        }

        $qry_str = [];
        foreach ($qry as $key => $val) {
            $qry_str[xss_clean($key)] = xss_clean($val);
        }

        $cur_page = $cur_page === null ? (isset($qry_str['cur_page']) ? intval($qry_str['cur_page']) : 1) : $cur_page;
        $per_page = $per_page === null ? (isset($qry_str['per_page']) ? intval($qry_str['per_page']) : 20) : $per_page;

        unset($qry_str['cur_page']);
        unset($qry_str['per_page']);

        $cur_page  = $cur_page < 1 ? 1 : $cur_page;
        $per_page  = $per_page <= 0 ? 10 : $per_page;
        $per_page  = $per_page > 1000 ? 1000 : $per_page;
        $mid_range = intval(floor($link_num / 2));

        if ($this->totalRows > 0) {
            $total_rows = $this->totalRows;

            $last_page = intval(ceil($total_rows / $per_page));
            $cur_page  = $cur_page > $last_page ? $last_page : $cur_page;

            $mid_range = $mid_range > $last_page ? $last_page : $mid_range;

            $page_list = array();

            $start = $cur_page - $mid_range;
            $end   = $cur_page + $mid_range;

            if ($start <= 0) {
                $end   += abs($start) + 1;
                $start = 1;
            }

            if ($end > $last_page) {
                $start -= $end - $last_page;
                $start = $start < 1 ? 1 : $start;
                $end   = $last_page;
            }

            $prev_jump = $start - ($mid_range + 1);
            $prev_jump = $prev_jump < 1 ? 1 : $prev_jump;
            $next_jump = $end + $mid_range + 1;
            $next_jump = $next_jump > $last_page ? $last_page : $next_jump;

            for ($i = $start; $i <= $end; $i++) {
                $page_list[] = $i;
            }

            $offset = ($cur_page - 1) * $per_page;
            $offset = $offset <= 0 ? null : $offset;

            return array(
                'first_page' => 1,
                'prev_jump' => intval($prev_jump),
                'prev_page' => intval(($cur_page - 1 < 1 ? 1 : $cur_page - 1)),
                'cur_page' => intval($cur_page),
                'next_page' => intval($cur_page + 1 > $last_page ? $last_page : $cur_page + 1),
                'next_jump' => intval($next_jump),
                'last_page' => intval($last_page),
                'page_list' => $page_list,
                'per_page' => intval($per_page),
                'qry_str' => !empty($qry_str) ? ($method == 'get' ? http_build_query($qry_str) : $qry_str) : '',
                'offset' => intval($offset)
            );
        } else {
            return false;
        }
    }

    public function allowedFields($data, $allowedFields = [], $default = [])
    {
        if (!empty($allowedFields)) {
            $allowedData = [];

            foreach ($allowedFields as $fName) {
                if (array_key_exists($fName, $data)) {
                    $allowedData[$fName] = $data[$fName];
                } else if (!empty($default)) {
                    if (array_key_exists($fName, $default)) {
                        $allowedData[$fName] = $default[$fName];
                    }
                }
            }

            return $allowedData;
        } else {
            return $data;
        }
    }

    public function startCache()
    {
        $this->qb->start_cache();
        return $this;
    }

    public function stopCache()
    {
        $this->qb->stop_cache();
        return $this;
    }

    public function endCache()
    {
        $this->stopCache();
        return $this;
    }

    public function flushCache()
    {
        $this->qb->flush_cache();
        return $this;
    }

    public function getInsertId()
    {
        return $this->insertId;
    }

    public function getCount($cntColum = false)
    {
        $cntStr = $cntColum ? "COUNT({$cntColum}) AS " : str_replace('SELECT', '', $this->qb->getCountString());
        $cntStr .= $this->qb->escape_identifiers('numrows');

        return $this->select($cntStr, false)
                ->exec()
                ->getRow()
            ->numrows ?? 0;
    }

    public function getMasterDb()
    {
        // set masterDb
        if ($this->masterDb === false) {
            $this->masterDb = getForbiz()->import('db.master');
        }

        return $this->masterDb;
    }

    public function getSlaveDb()
    {
        // set slaveDb
        if ($this->slaveDb === false) {
            $this->slaveDb = getForbiz()->import('db.slave');
        }

        return $this->slaveDb;
    }

    public function getTableList($table)
    {
        $rows   = $this->exec("show tables like '{$table}%'")->getResultArray();
        $tables = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $tables[] = array_values($row)[0];
            }
        }
        return $tables;
    }

    /**
     * excute sql
     * @param string $sql
     * @return NunaResult | boolean
     */
    public function exec($sql = '')
    {
        $queryType = $this->queryType;

        if ($sql == '') {
            $sql = $this->toStr();
        } else {
            $queryType = strtolower(substr(ltrim($sql), 0, 6));
            switch ($queryType) {
                case 'insert':
                case 'update':
                case 'delete':
                case 'replac':
                case 'trunca':
                case 'show t':
                case 'show d':
                case 'create':
                case 'drop t':
                case 'alter ':
                    break;
                case 'call p':
                    $queryType = 'procedure';
                    break;
                default:
                    $queryType = $this->queryType;
                    break;
            }
        }

        $backLog = debug_backtrace(0)[0];
        $url = ($_SERVER['REQUEST_URI'] ?? 'CLI');
        $sql = "/*\n* url : {$url}\n* file : {$backLog['file']}\n* line : {$backLog['line']}\n*/\n{$sql}";

        if ($this->database === false) {
            if ($this->enableMaster === true) {
                $this->database = $this->getMasterDb();
            } else {
                $this->database = ($queryType == 'select' ? $this->getSlaveDb() : $this->getMasterDb());
            }
        }

        $this->lastQury = $sql;

        $result = $this->database->query($sql, false);

        if ($result === false && ($this->database->useTryCatch ?? false)) {

            $db_error = $this->database->error();

            log_message('error', 'Database error! Error Code ['.$db_error['code'].'] Error: '.$db_error['message']);

            // Database 재설정
            $this->resetDatabase();

            return false;
        }

        if ($queryType == 'select') {
            $res         = new NunaResult($result);
            $this->total = $res->num_rows();
        } else if($queryType == 'procedure') {
            $res = new NunaResult($result);
        }else {
            if ($queryType == 'insert') {
                if ($this->database->dbdriver == 'mysqli' || $this->database->dbdriver == 'postgre') {
                    $this->insertId = $this->database->insert_id();
                    $res            = $this->insertId;
                } else {
                    $res = true;
                }
            } else if ($queryType == 'show t' || $queryType == 'show d') {
                $res = new NunaResult($result);
            } else {
                $res = true;
            }
        }

        // Database 재설정
        $this->resetDatabase();

        return $res;
    }

    public function queryBind($sql, $binds = FALSE)
    {
        return $this->qb->compile_binds($sql, $binds);
    }

    public function startSubQuery($alias = '')
    {
        $subQuery = new CI_Qb($this->params);

        $subQuery->subQryAlias = $alias ? $alias : '';
        $subQuery->subQuery    = $subQuery;

        return $subQuery;
    }

    public function endSubQuery()
    {
        $sql = '('.$this->subQuery->toStr().')'.($this->subQryAlias ? (' AS '.$this->subQryAlias) : '');
        unset($this->subQuery);

        return $sql;
    }

    public function callProcedureResultArray($sql)
    {
        $result = $this->exec($sql);
        $res = $result->getResultArray();
        $this->freeProcedure($result);
        return $res;
    }

    public function callProcedureRowArray($sql)
    {
        $result = $this->exec($sql);
        $res = $result->getRowArray();
        $this->freeProcedure($result);
        return $res;
    }

    private function freeProcedure(&$result)
    {
        $result->freeResult();
        $result->nextResult();
    }

    /**
     * Select
     *
     * Generates the SELECT portion of the query
     *
     * @param    string
     * @param    mixed
     * @return    CI_Qb
     */
    public function select($select = '*', $escape = NULL)
    {
        $this->qb->select($select, $escape);

        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Select Max
     *
     * Generates a SELECT MAX(field) portion of a query
     *
     * @param    string    the field
     * @param    string    an alias
     * @return    CI_Qb
     */
    public function selectMax($select = '', $alias = '')
    {
        $this->qb->select_max($select, $alias);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Select Min
     *
     * Generates a SELECT MIN(field) portion of a query
     *
     * @param    string    the field
     * @param    string    an alias
     * @return    CI_Qb
     */
    public function selectMin($select = '', $alias = '')
    {
        $this->qb->select_min($select, $alias);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Select Average
     *
     * Generates a SELECT AVG(field) portion of a query
     *
     * @param    string    the field
     * @param    string    an alias
     * @return    CI_Qb
     */
    public function selectAvg($select = '', $alias = '')
    {
        $this->qb->select_avg($select, $alias);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Select Sum
     *
     * Generates a SELECT SUM(field) portion of a query
     *
     * @param    string    the field
     * @param    string    an alias
     * @return    CI_Qb
     */
    public function selectSum($select = '', $alias = '')
    {
        $this->qb->select_sum($select, $alias);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * DISTINCT
     *
     * Sets a flag which tells the query string compiler to add DISTINCT
     *
     * @param    bool $val
     * @return    CI_Qb
     */
    public function distinct($val = TRUE)
    {
        $this->qb->distinct($val);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * From
     *
     * Generates the FROM portion of the query
     *
     * @param    mixed $from can be a string or array
     * @return    CI_Qb
     */
    public function from($from, $escape = true)
    {
        if ($this->queryType !== 'select') {
            throw new Exception('Must call the "exec()" or "toStr()" method after calling '.$this->queryType);
        } else {
            $this->qb->from($from, $escape);

            return $this;
        }
    }
    // --------------------------------------------------------------------

    /**
     * JOIN
     *
     * Generates the JOIN portion of the query
     *
     * @param    string
     * @param    string    the join condition
     * @param    string    the type of join
     * @param    string    whether not to try to escape identifiers
     * @return    CI_Qb
     */
    public function join($table, $cond, $type = '', $escape = NULL)
    {
        $this->qb->join($table, $cond, $type, $escape);

        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * WHERE
     *
     * Generates the WHERE portion of the query.
     * Separates multiple calls with 'AND'.
     *
     * @param    mixed
     * @param    mixed
     * @param    bool
     * @return    CI_Qb
     */
    public function where($key, $value = NULL, $escape = NULL)
    {
        $this->qb->where($key, $value, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * OR WHERE
     *
     * Generates the WHERE portion of the query.
     * Separates multiple calls with 'OR'.
     *
     * @param    mixed
     * @param    mixed
     * @param    bool
     * @return    CI_Qb
     */
    public function orWhere($key, $value = NULL, $escape = NULL)
    {
        $this->qb->or_where($key, $value, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * WHERE IN
     *
     * Generates a WHERE field IN('item', 'item') SQL query,
     * joined with 'AND' if appropriate.
     *
     * @param    string $key The field to search
     * @param    array $values The values searched on
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function whereIn($key = NULL, $values = NULL, $escape = NULL)
    {
        $this->qb->where_in($key, $values, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * OR WHERE IN
     *
     * Generates a WHERE field IN('item', 'item') SQL query,
     * joined with 'OR' if appropriate.
     *
     * @param    string $key The field to search
     * @param    array $values The values searched on
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function orWhereIn($key = NULL, $values = NULL, $escape = NULL)
    {
        $this->qb->or_where_in($key, $values, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * WHERE NOT IN
     *
     * Generates a WHERE field NOT IN('item', 'item') SQL query,
     * joined with 'AND' if appropriate.
     *
     * @param    string $key The field to search
     * @param    array $values The values searched on
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function whereNotIn($key = NULL, $values = NULL, $escape = NULL)
    {
        $this->qb->where_not_in($key, $values, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * OR WHERE NOT IN
     *
     * Generates a WHERE field NOT IN('item', 'item') SQL query,
     * joined with 'OR' if appropriate.
     *
     * @param    string $key The field to search
     * @param    array $values The values searched on
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function orWhereNotIn($key = NULL, $values = NULL, $escape = NULL)
    {
        $this->qb->or_where_not_in($key, $values, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * LIKE
     *
     * Generates a %LIKE% portion of the query.
     * Separates multiple calls with 'AND'.
     *
     * @param    mixed $field
     * @param    string $match
     * @param    string $side
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function like($field, $match = '', $side = 'both', $escape = NULL)
    {
        $this->qb->like($field, $match, $side, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * NOT LIKE
     *
     * Generates a NOT LIKE portion of the query.
     * Separates multiple calls with 'AND'.
     *
     * @param    mixed $field
     * @param    string $match
     * @param    string $side
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function notLike($field, $match = '', $side = 'both', $escape = NULL)
    {
        $this->qb->not_like($field, $match, $side, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * OR LIKE
     *
     * Generates a %LIKE% portion of the query.
     * Separates multiple calls with 'OR'.
     *
     * @param    mixed $field
     * @param    string $match
     * @param    string $side
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function orLike($field, $match = '', $side = 'both', $escape = NULL)
    {
        $this->qb->or_like($field, $match, $side, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * OR NOT LIKE
     *
     * Generates a NOT LIKE portion of the query.
     * Separates multiple calls with 'OR'.
     *
     * @param    mixed $field
     * @param    string $match
     * @param    string $side
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function orNotLike($field, $match = '', $side = 'both', $escape = NULL)
    {
        $this->qb->or_not_like($field, $match, $side, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Starts a query group.
     *
     * @param    string $not (Internal use only)
     * @param    string $type (Internal use only)
     * @return    CI_Qb
     */
    public function groupStart($not = '', $type = 'AND ')
    {
        $this->qb->group_start($not, $type);

        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Starts a query group, but ORs the group
     *
     * @return    CI_Qb
     */
    public function orGroupStart()
    {
        $this->qb->or_group_start();
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Starts a query group, but NOTs the group
     *
     * @return    CI_Qb
     */
    public function notGroupStart()
    {
        $this->qb->not_group_start();
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Starts a query group, but OR NOTs the group
     *
     * @return    CI_Qb
     */
    public function orNotGroupStart()
    {
        $this->qb->or_not_group_start();
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Ends a query group
     *
     * @return    CI_Qb
     */
    public function groupEnd()
    {
        $this->qb->group_end();

        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * GROUP BY
     *
     * @param    string $by
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function groupBy($by, $escape = NULL)
    {
        $this->qb->group_by($by, $escape);

        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * HAVING
     *
     * Separates multiple calls with 'AND'.
     *
     * @param    string $key
     * @param    string $value
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function having($key, $value = NULL, $escape = NULL)
    {
        $this->qb->having($key, $value, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * OR HAVING
     *
     * Separates multiple calls with 'OR'.
     *
     * @param    string $key
     * @param    string $value
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function orHaving($key, $value = NULL, $escape = NULL)
    {
        $this->qb->or_having($key, $value, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * ORDER BY
     *
     * @param    string $orderby
     * @param    string $direction ASC, DESC or RANDOM
     * @param    bool $escape
     * @return    CI_Qb
     */
    public function orderBy($orderby, $direction = '', $escape = NULL)
    {
        $this->qb->order_by($orderby, $direction, $escape);

        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * LIMIT
     *
     * @param    int $value LIMIT value
     * @param    int $offset OFFSET value
     * @return    CI_Qb
     */
    public function limit($value, $offset = 0)
    {
        $this->qb->limit($value, $offset);
        return $this;
    }

    /**
     * Excute last query string
     * @return string
     */
    public function lastQuery()
    {
        return $this->lastQury;
    }
    // --------------------------------------------------------------------

    /**
     * Sets the OFFSET value
     *
     * @param    int $offset OFFSET value
     * @return    CI_Qb
     */
    public function offset($offset)
    {
        $this->qb->offset($offset);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * The "set" function.
     *
     * Allows key/value pairs to be set for inserting or updating
     *
     * @param    mixed
     * @param    string
     * @param    bool
     * @return    CI_Qb
     */
    public function set($key, $value = '', $escape = NULL)
    {
        $this->qb->set($key, $value, $escape);
        return $this;
    }
    // --------------------------------------------------------------------

    /**
     * Insert
     *
     * Compiles an insert string and runs the query
     *
     * @param    string    the table to insert data into
     * @param    array    an associative array of insert values
     * @param    bool $escape Whether to escape values and identifiers
     * @return    CI_Qb
     */
    public function insert($table, $set = NULL, $escape = NULL)
    {
        if ($this->queryType !== 'select') {
            throw new Exception('Must call the "exec()" or "toStr()" method after calling '.$this->queryType);
        } else {
            $this->table     = $table ? $table : $this->table;
            $this->queryType = 'insert';

            if ($set !== NULL) {
                foreach ($set as $sKey => $sVal) {
                    $this->set($sKey, $sVal, $escape);
                }
            }

            return $this;
        }
    }
    // --------------------------------------------------------------------

    /**
     * UPDATE
     *
     * Compiles an update string and runs the query.
     *
     * @param string $table
     * @param array $set An associative array of update values
     * @param mixed $where
     * @param int $limit
     * @param null $offset
     * @return    CI_Qb
     * @throws Exception
     */
    public function update($table, $set = NULL, $where = NULL, $limit = NULL, $offset = NULL)
    {
        if ($this->queryType !== 'select') {
            throw new Exception('Must call the "exec()" or "toStr()" method after calling '.$this->queryType);
        } else {

            $this->table     = $table ? $table : $this->table;
            $this->queryType = 'update';

            if ($set !== NULL) {
                foreach ($set as $sKey => $sVal) {
                    $this->set($sKey, $sVal);
                }
            }

            if ($where !== NULL) {
                $this->where($where);
            }

            if (!empty($limit)) {
                $this->limit($limit, $offset);
            }

            return $this;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Delete
     *
     * Compiles a delete string and runs the query
     *
     * @param    strint    the table(s) to delete from.
     * @param    mixed    the where clause
     * @param    mixed    the limit clause
     * @return    CI_Qb
     */
    public function delete($table, $where = '', $limit = NULL)
    {
        if ($this->queryType !== 'select') {
            throw new Exception('Must call the "exec()" or "toStr()" method after calling '.$this->queryType);
        } elseif (!is_string($table)) {
            throw new Exception('The table name is not a string.');
        } else {

            $this->table     = $table ? $table : $this->table;
            $this->queryType = 'delete';

            if ($where != '') {
                $this->where($where);
            }

            if (!empty($limit)) {
                $this->limit($limit);
            }

            return $this;
        }
    }
    // --------------------------------------------------------------------

    /**
     * toStr
     *
     * @return    string
     */
    public function toStr()
    {
        switch ($this->queryType) {
            case 'update':
                if ($this->qb->hasWhare()) {
                    $sql = $this->qb->get_compiled_update($this->table);
                } else {
                    throw new Exception('Update must call where() function!');
                }
                break;
            case 'insert':
                $sql = $this->qb->get_compiled_insert($this->table);
                break;
            case 'delete':
                if ($this->qb->hasWhare()) {
                    $sql = $this->qb->get_compiled_delete($this->table);
                } else {
                    throw new Exception('Delete must call where() function!');
                }
                break;
            default:
                $sql = $this->qb->get_compiled_select();
                break;
        }

        $this->queryType = 'select';

        return $sql;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSql()
    {
        return $this->toStr();
    }

    /**
	 * insert multy.
	 *
	 * Generates a platform-specific insert string from the supplied data.
	 *
	 * @param	string	$table	INSERT table
	 * @param	array	$set	INSERT values
     * @param	boolean	$escape	
     * @param	int	    $batch_size
	 * @return	string
	 */
	public function insert_multy($table, $set, $escape = false, $batch_size = 100)
	{
        // 초기 변수 세팅.
        $this->table = $table;
        $queryValues = '';
        $loopCnt = 0;
        $batchCnt = 1;
        $batchArr = [];
        $keys = array_keys($set[0]);

        // batch 돌릴 데이터 쿼리화.
        $batchArr = array_chunk($set, $batch_size);

        for ($i=0, $len=count($batchArr); $i < $len; $i ++) {
            foreach ($batchArr[$i] as $v) {
                // filed 갯수와 values의 개수가 틀리면 에러.
                if (count($keys) != count($v)) {
                    throw new Exception('The number of keys to insert and the number of values are different. Please check the $set variable again.');
                }
                
                if($escape) {
                    $v = $this->qb->escape($v);
                }

                $queryValues .= '("'.implode('", "', $v).'")';
                
                if ($batchCnt != count($set)) {
                    $queryValues .= ', ';
                }

                $batchCnt++;
                $loopCnt++;

            }
            $this->exec('INSERT INTO '.$this->table.' (`'.implode('`, `', $keys).'`) VALUES '.$queryValues);
        }
        
        return $loopCnt;
        
        
    }

}