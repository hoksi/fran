<?php defined('FRAN') or exit('No direct script access allowed');

return [
    'master' => [
        'host' => get_env_value('fran_db_master_host'),
        'user' => get_env_value('fran_db_master_user'),
        'pass' => get_env_value('fran_db_master_pass'),
        'name' => get_env_value('fran_db_master_name'),
        'port' => get_env_value('fran_db_master_port'),
        'dbdriver' => get_env_value('fran_db_master_dbdriver'),
        'char_set' => get_env_value('fran_db_master_char_set'),
        'dbcollat' => get_env_value('fran_db_master_dbcollat'),
    ],
    'slave' => [
        'host' => get_env_value('fran_db_slave_host'),
        'user' => get_env_value('fran_db_slave_user'),
        'pass' => get_env_value('fran_db_slave_pass'),
        'name' => get_env_value('fran_db_slave_name'),
        'port' => get_env_value('fran_db_slave_port'),
        'dbdriver' => get_env_value('fran_db_slave_dbdriver'),
        'char_set' => get_env_value('fran_db_slave_char_set'),
        'dbcollat' => get_env_value('fran_db_slave_dbcollat'),
    ],
];
