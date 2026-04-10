<?php

use PHPUnit\Framework\TestCase;

class Zend_Soap_Wsdl_ArrayOfTypeSequenceStrategyTest extends TestCase
{
    private $wsdl;
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = new Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence();
        $this->wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testFunctionReturningSimpleArrayOfInts()
    {
        $this->wsdl->addComplexType('int[]');

        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfInt">'.
                '<xsd:sequence><xsd:element name="item" type="xsd:int" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence>'.
            '</xsd:complexType>',
            $this->wsdl->toXML()
        );
    }

    public function testFunctionReturningSimpleArrayOfString()
    {
        $this->wsdl->addComplexType('string[]');

        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfString">'.
                '<xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence>'.
            '</xsd:complexType>',
            $this->wsdl->toXML()
        );
    }

    public function testFunctionReturningNestedArrayOfString()
    {
        $return = $this->wsdl->addComplexType('string[][]');
        $this->assertEquals("tns:ArrayOfArrayOfString", $return);

        $wsdl = $this->wsdl->toXML();

        // Check for ArrayOfArrayOfString
        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="tns:ArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfString
        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfString"><xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
    }

    public function testFunctionReturningMultipleNestedArrayOfType()
    {
        $return = $this->wsdl->addComplexType('string[][][]');
        $this->assertEquals("tns:ArrayOfArrayOfArrayOfString", $return);

        $wsdl = $this->wsdl->toXML();

        // Check for ArrayOfArrayOfArrayOfString
        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="tns:ArrayOfArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfArrayOfString
        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="tns:ArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfString
        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfString"><xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
    }


    public function testAddComplexTypeObject()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_SequenceTest');

        $this->assertEquals("tns:Zend_Soap_Wsdl_SequenceTest", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertStringContainsString(
            '<xsd:complexType name="Zend_Soap_Wsdl_SequenceTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testAddComplexTypeArrayOfObject()
    {

         $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_SequenceTest[]');

         $this->assertEquals("tns:ArrayOfZend_soap_wsdl_sequencetest", $return);

         $wsdl = $this->wsdl->toXML();

         $this->assertStringContainsString(
            '<xsd:complexType name="Zend_Soap_Wsdl_SequenceTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
         );

         $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfZend_soap_wsdl_sequencetest"><xsd:sequence><xsd:element name="item" type="tns:Zend_Soap_Wsdl_SequenceTest" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
         );
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        $this->expectException("Zend_Soap_Wsdl_Exception");

        $this->wsdl->addComplexType('Zend_Soap_Wsdl_UnknownClass[]');
    }
}

class Zend_Soap_Wsdl_SequenceTest
{
    /**
     * @var int
     */
    public $var = 5;
}
