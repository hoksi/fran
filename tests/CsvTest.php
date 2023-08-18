<?php

class CsvTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \League\Csv\Writer
     */
    public function testCsvWriterLoad()
    {
        $csv = qb()
            ->select('id, code')
            ->from('common_user')
            ->where('id', 1)
            ->exec()
            ->toCsv();

        $this->assertEquals('"id","code"'."\n", $csv);
    }


}