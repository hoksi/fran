<?php

class FormValidationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \CodeIgniter\Lib\FormValidation
     */
    public function testFormValidationLoad()
    {
        $validation = get_fran('formValidation');

        $this->assertInstanceOf('\CodeIgniter\Lib\FormValidation', $validation);
    }

    /**
     * @covers form_validation()
     */
    public function testFormValidationTrue()
    {
        $_POST['id'] = 1;
        $this->assertTrue(form_validation(['id']));
    }

    /**
     * @covers form_validation()
     */
    public function testFormValidationFalse()
    {
        $res = form_validation(['id']);
        $this->assertFalse($res);
    }
}