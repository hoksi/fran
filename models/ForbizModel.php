<?php
namespace Forbiz\Model;
/**
 * Description of ForbizModel
 *
 * @author hoksi
 *
 * @property CI_Qb $qb
 */
class ForbizModel
{
    public $qb;

    public function __construct()
    {
        $this->qb = qb();
    }

    public function import($resource, $params = null, $return = false)
    {
        return fb_import($resource, $params, $return);
    }
}