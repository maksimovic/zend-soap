<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../_files/commontypes.php';

class Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest extends TestCase
{
    private $wsdl;
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = new Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex();
        $this->wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testNestingObjectsDeepMakesNoSenseThrowingException(): void
    {
        $this->expectException(Zend_Soap_Wsdl_Exception::class);
        $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[][]');
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException(): void
    {
        $this->expectException(Zend_Soap_Wsdl_Exception::class);
        $this->wsdl->addComplexType('Zend_Soap_Wsdl_UnknownClass[]');
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfSimpleObject()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexTest", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexTest"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexTest[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertStringContainsString(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testThatOverridingStrategyIsReset()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexTest", $return);
        #$this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplexStrategy);

        $wsdl = $this->wsdl->toXML();
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfComplexObjects()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectStructure[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertStringContainsString(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexObjectStructure"><xsd:all><xsd:element name="boolean" type="xsd:boolean"/><xsd:element name="string" type="xsd:string"/><xsd:element name="int" type="xsd:int"/><xsd:element name="array" type="soap-enc:Array"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testArrayOfObjectWithObject()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertStringContainsString(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertStringContainsString(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexObjectWithObjectStructure"><xsd:all><xsd:element name="object" type="tns:Zend_Soap_Wsdl_ComplexTest" nillable="true"/></xsd:all></xsd:complexType>',
            $wsdl
        );

        $this->assertStringContainsString(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    /**
     * @group ZF-4937
     */
    public function testAddingTypesMultipleTimesIsSavedOnlyOnce()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest">')
        );
    }

    /**
     * @group ZF-4937
     */
    public function testAddingSingularThenArrayTypeIsRecognizedCorretly()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure');
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest">')
        );
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndNotThrowingException(): void
    {
        $return = $this->wsdl->addComplexType("Zend_Soap_Wsdl_ComplexTypeA");
        $wsdl = $this->wsdl->toXml();
        $this->assertStringContainsString('Zend_Soap_Wsdl_ComplexTypeA', $wsdl);
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndAddsAllTypesRecursivly()
    {
        $return = $this->wsdl->addComplexType("Zend_Soap_Wsdl_ComplexTypeA");
        $wsdl = $this->wsdl->toXml();

        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTypeA">'),
            'No definition of complex type A found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexTypeB">'),
            'No definition of complex type B array found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexTypeB[]"'),
            'No usage of Complex Type B array found.'
        );
    }

    /**
     * @group ZF-5754
     * @group ZF-8948
     */
    public function testNestingOfSameTypesDoesNotLeadToInfiniteRecursionButWillThrowException(): void
    {
        $return = $this->wsdl->addComplexType("Zend_Soap_AutoDiscover_Recursion");
        $this->assertStringContainsString('Zend_Soap_AutoDiscover_Recursion', $return);
    }
}
