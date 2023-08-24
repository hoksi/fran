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
            ->exec()
            ->saveCsv('test.csv');

        $this->assertEquals('"id","code"'."\n", $csv);
    }


}