<?php declare (strict_types=1);

namespace Sabre\Xml\Deserializer;

use
    Sabre\Xml\Reader;

class ValueObjectTest extends \PHPUnit\Framework\TestCase {

    function testDeserializeValueObject() {

        $input = <<<XML
<?xml version="1.0"?>
<person xmlns="urn:person" sex="male">
   <firstName>Harry</firstName>
   <lastName>Turtle</lastName>
</person>
XML;

        $reader = new Reader();
        $reader->xml($input);
        $reader->elementMap = [
            '{urn:person}person' => function(Reader $reader) {
                return valueObject($reader, 'Sabre\\Xml\\Deserializer\\PersonVo', 'urn:person');
            }
        ];

        $output = $reader->parse();

        $vo = new PersonVo();
        $vo->firstName = 'Harry';
        $vo->lastName = 'Turtle';

        $expected = [
            'name'       => '{urn:person}person',
            'value'      => $vo,
            'attributes' => []
        ];

        $this->assertEquals(
            $expected,
            $output
        );

    }

    function testDeserializeValueObjectIgnoredElement() {

        $input = <<<XML
<?xml version="1.0"?>
<foo xmlns="urn:foo">
   <firstName>Harry</firstName>
   <lastName>Turtle</lastName>
   <email>harry@example.org</email>
</foo>
XML;

        $reader = new Reader();
        $reader->xml($input);
        $reader->elementMap = [
            '{urn:foo}foo' => function(Reader $reader) {
                return valueObject($reader, 'Sabre\\Xml\\Deserializer\\PersonVo', 'urn:foo');
            }
        ];

        $output = $reader->parse();

        $vo = new PersonVo();
        $vo->firstName = 'Harry';
        $vo->lastName = 'Turtle';

        $expected = [
            'name'       => '{urn:foo}foo',
            'value'      => $vo,
            'attributes' => []
        ];

        $this->assertEquals(
            $expected,
            $output
        );

    }

    function testDeserializeValueObjectAutoArray() {

        $input = <<<XML
<?xml version="1.0"?>
<foo xmlns="urn:foo">
   <firstName>Harry</firstName>
   <lastName>Turtle</lastName>
   <link>http://example.org/</link>
   <link>http://example.net/</link>
</foo>
XML;

        $reader = new Reader();
        $reader->xml($input);
        $reader->elementMap = [
            '{urn:foo}foo' => function(Reader $reader) {
                return valueObject($reader, 'Sabre\\Xml\\Deserializer\\PersonVo', 'urn:foo');
            }
        ];

        $output = $reader->parse();

        $vo = new PersonVo();
        $vo->firstName = 'Harry';
        $vo->lastName = 'Turtle';
        $vo->link = [
            'http://example.org/',
            'http://example.net/',
        ];


        $expected = [
            'name'       => '{urn:foo}foo',
            'value'      => $vo,
            'attributes' => []
        ];

        $this->assertEquals(
            $expected,
            $output
        );

    }
    function testDeserializeValueObjectEmpty() {

        $input = <<<XML
<?xml version="1.0"?>
<foo xmlns="urn:foo" />
XML;

        $reader = new Reader();
        $reader->xml($input);
        $reader->elementMap = [
            '{urn:foo}foo' => function(Reader $reader) {
                return valueObject($reader, 'Sabre\\Xml\\Deserializer\\PersonVo', 'urn:foo');
            }
        ];

        $output = $reader->parse();

        $vo = new PersonVo();

        $expected = [
            'name'       => '{urn:foo}foo',
            'value'      => $vo,
            'attributes' => []
        ];

        $this->assertEquals(
            $expected,
            $output
        );

    }

}

class PersonVo {

    public $firstName;
    public $lastName;

    public $link = [];

}
